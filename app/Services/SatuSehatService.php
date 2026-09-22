<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SatuSehatService
{
    protected $baseUrl;
    protected $baseUrlWilayah;
    protected $authUrl;
    protected $clientId;
    protected $clientSecret;
    protected $orgID;
    public function __construct()
    {

        $this->authUrl = config('services.satusehat.auth_url');
        $this->baseUrl = config('services.satusehat.base_url');
        $this->baseUrlWilayah = 'https://api-satusehat.kemkes.go.id/masterdata/v1/';
        // $this->baseUrlWilayah = 'https://api-satusehat-stg.kemkes.go.id/masterdata/v1/';
        // $this->clientId = $data_org[0]->SATUSEHAT_CLIENT_ID;
        // $this->clientId = config('services.satusehat.client_id');
        // $this->clientSecret = config('services.satusehat.client_secret');
        // $this->clientSecret = $data_org[0]->SATUSEHAT_CLIENT_SECRET;
        // $this->get_idorg() = $data_org[0]->id_org_satu_sehat;
        if (!$this->authUrl) {
            throw new \Exception("Konfigurasi SATUSEHAT_AUTH_URL tidak ditemukan.");
        }
    }
    public function get_idorg()
    {
        $id = auth()->user()->client_id;
        $client = db::table('mt_client')->where('id',$id)->get();
        return $client[0]->id_org_satu_sehat;
    }
    public function getToken()
    {
        // Bersihkan cache jika sebelumnya menyimpan null
        // if (Cache::get('satusehat_token') === null) {
        //     Cache::forget('satusehat_token');
        // }
        // return Cache::remember('satusehat_token', 100, function () {
            $id = auth()->user()->client_id;
            $client = db::table('mt_client')->where('id',$id)->get();
            // dd($client);
            $clientid = $client[0]->SATUSEHAT_CLIENT_ID;
            $clientSecret = $client[0]->SATUSEHAT_CLIENT_SECRET;
            $response = Http::asForm()->post($this->authUrl, [
                'client_id'     => $clientid,
                'client_secret' => $clientSecret,
                'grant_type'    => 'client_credentials', // Wajib diisi untuk OAuth SATUSEHAT
            ]);
            if ($response->failed()) {
                throw new \Exception('OAuth Failed: ' . $response->body());
            }
            return $response->json('access_token');
        // });
    }
    // Mencari IHS Number Pasien berdasarkan NIK
    public function getPatientByNik($nik)
    {
        $token = $this->getToken();
        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/Patient?identifier=https://fhir.kemkes.go.id/id/nik|$nik");

        return $response->json();
    }
    public function getProvinsi()
    {
        try {
            $token = $this->getToken();
            $response = Http::withToken($token)->get("{$this->baseUrlWilayah}/provinces?codes");

            if ($response->failed()) {
                return [];
            }

            $json = $response->json();
            return $json['data'] ?? $json;
        } catch (\Exception $e) {
            return [];
        }
    }
    public function getKabKota($codesProv)
    {
        // dd('ok');
        try {
            $token = $this->getToken();
            $response = Http::withToken($token)->get("{$this->baseUrlWilayah}/cities", [
                'province_codes' => $codesProv,
            ]);

            if ($response->failed()) {
                return [];
            }

            $json = $response->json();
            return $json['data'] ?? $json;
        } catch (\Exception $e) {
            return [];
        }
    }
    public function getKecamatan($codesKabKota)
    {
        try {
            $token = $this->getToken();
            $response = Http::withToken($token)->get("{$this->baseUrlWilayah}/districts", [
                'city_codes' => $codesKabKota,
            ]);

            if ($response->failed()) {
                return [];
            }

            $json = $response->json();
            return $json['data'] ?? $json;
        } catch (\Exception $e) {
            return [];
        }
    }
    public function getKelurahan($codesKec)
    {
        try {
            $token = $this->getToken();
            $response = Http::withToken($token)->get("{$this->baseUrlWilayah}/sub-districts?district_codes", [
                'district_codes' => $codesKec,
            ]);

            if ($response->failed()) {
                return [];
            }

            $json = $response->json();
            return $json['data'] ?? $json;
        } catch (\Exception $e) {
            return [];
        }
    }
    public function createOrganization(array $payload)
    {
        try {
            // 1. Ambil Access Token OAuth SATUSEHAT
            $token = $this->getToken();
            // dd($token);
            if ($payload['tipe'] == 'dept') {
                $codetipe = 'dept';
                $display = 'Hospital Department';
            } else {
                $codetipe = 'prov';
                $display = 'Healthcare Provider';
            }
            if (!$token) {
                return [
                    'status'  => false,
                    'message' => 'Gagal mendapatkan token autentikasi SATUSEHAT.'
                ];
            }
            // 2. Format Body JSON Sesuai Standar FHIR R4 Organization
            $body = [
                'resourceType' => 'Organization',
                'active'       => isset($payload['active']) ? (bool) $payload['active'] : true,
                'identifier'   => [
                    [
                        'use'    => 'official',
                        'system' => 'http://sys-ids.kemkes.go.id/organization/' . ($payload['part_of_id'] ?? ''),
                        'value'  => $payload['name'] ?? '',
                    ]
                ],
                'type' => [
                    [
                        'coding' => [
                            [
                                'system'  => 'http://terminology.hl7.org/CodeSystem/organization-type',
                                'code'    => $codetipe,
                                'display' => $display
                            ]
                        ]
                    ]
                ],
                'name' => $payload['name'] ?? '',
            ];

            // Induk Organisasi (Part Of / Faskes Utama)
            if (!empty($payload['part_of_id'])) {
                $body['partOf'] = [
                    'reference' => 'Organization/' . $payload['part_of_id']
                ];
            }

            // Kontak (Telepon & Email)
            $telecom = [];
            if (!empty($payload['phone'])) {
                $telecom[] = [
                    'system' => 'phone',
                    'value'  => $payload['phone'],
                    'use'    => 'work'
                ];
            }
            if (!empty($payload['email'])) {
                $telecom[] = [
                    'system' => 'email',
                    'value'  => $payload['email'],
                    'use'    => 'work'
                ];
            }
            if (!empty($telecom)) {
                $body['telecom'] = $telecom;
            }

            // Alamat Detail
            if (!empty($payload['address'])) {
                $body['address'] = [
                    [
                        'use'        => 'work',
                        'type'       => 'both',
                        'line'       => [$payload['address']],
                        'postalCode' => $payload['postal_code'] ?? '',
                        'country'    => 'ID'
                    ]
                ];
            }

            // 3. Kirim Request HTTP POST ke API SATUSEHAT
            // $this->baseUrl ditujukan ke https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->baseUrl}/Organization", $body);

            // 4. Return Response Result
            if ($response->successful()) {
                return $response->json(); // Mengembalikan array response asli dari SATUSEHAT (berisi 'id', 'resourceType', dll)
            }

            Log::error('SATUSEHAT Create Organization Failed: ' . $response->body());

            return [
                'status'  => false,
                'message' => $response->json()['issue'][0]['details']['text'] ?? 'Gagal membuat Organization di SATUSEHAT.',
                'error'   => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Service Create Organization Exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan pada Service SATUSEHAT: ' . $e->getMessage()
            ];
        }
    }
    public function createLocation(array $payload)
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return [
                    'status'  => false,
                    'message' => 'Gagal mendapatkan token autentikasi SATUSEHAT.'
                ];
            }

            // Map Physical Type Display Name
            $physicalTypeMap = [
                'ro' => 'Room',
                'bd' => 'Bed',
                'bu' => 'Building',
                'wi' => 'Wing',
                've' => 'Vehicle',
                'ho' => 'House',
                'ca' => 'Cabinet',
                'rd' => 'Road',
                'area' => 'Area',
            ];

            $physicalTypeCode = $payload['physical_type'] ?? 'ro';
            $physicalTypeDisplay = $physicalTypeMap[$physicalTypeCode] ?? 'Room';
            $code     = $this->get_idorg();
            // $code = 'a9256dc3-ca8e-4f7c-a167-ea980e4446f0';
            // 1. Base Payload FHIR R4 Location
            $body = [
                'resourceType' => 'Location',
                'status'       => $payload['status'] ?? 'active',
                'name'         => $payload['name'] ?? '',
                'description'  => $payload['description'] ?? '',
                'mode'         => 'instance',

                // Identifier resmi lokasi di bawah Organization pengelola
                'identifier' => [
                    [
                        'system' => 'http://sys-ids.kemkes.go.id/location/' . $code,
                        'value'  => $payload['identifier_value'] ?? $payload['name'] // Gunakan kode internal jika ada
                    ]
                ],

                // Tipe Fisik Lokasi
                'physicalType' => [
                    'coding' => [
                        [
                            'system'  => 'http://terminology.hl7.org/CodeSystem/location-physical-type', // <-- PERBAIKAN DI SINI
                            'code'    => $physicalTypeCode,
                            'display' => $physicalTypeDisplay
                        ]
                    ]
                ],

                // Referensi ke Organization Pengelola
                'managingOrganization' => [
                    'reference' => 'Organization/' . $payload['managing_organization_id']
                ]
            ];

            // 2. Tambahan Kontak Telepon/Email (Opsional)
            if (!empty($payload['phone'])) {
                $body['telecom'][] = [
                    'system' => 'phone',
                    'value'  => $payload['phone'],
                    'use'    => 'work'
                ];
            }

            // 3. Request HTTP POST ke API SATUSEHAT
            $response = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/Location", $body);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('SATUSEHAT Create Location Failed: ' . $response->body());

            return [
                'status'  => false,
                'message' => $response->json()['issue'][0]['details']['text'] ?? 'Gagal membuat Location di SATUSEHAT.',
                'error'   => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Service Create Location Exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan pada Service SATUSEHAT: ' . $e->getMessage()
            ];
        }
    }
    public function createPatient(array $payload)
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return [
                    'status'  => false,
                    'message' => 'Gagal mendapatkan token autentikasi SATUSEHAT.'
                ];
            }

            // 1. Mapping Gender
            $genderMap = [
                'L' => 'male',
                'P' => 'female',
            ];
            $gender = $genderMap[$payload['jenis_kelamin'] ?? ''] ?? 'unknown';

            // 2. Mapping Marital Status (SATUSEHAT FHIR Standards)
            // S = Never Married, M = Married, D = Divorced, W = Widowed
            $maritalStatusMap = [
                'S' => ['code' => 'S', 'display' => 'Never Married'],
                'M' => ['code' => 'M', 'display' => 'Married'],
                'D' => ['code' => 'D', 'display' => 'Divorced'],
                'W' => ['code' => 'W', 'display' => 'Widowed'],
            ];
            $maritalCode = $payload['status_pernikahan'] ?? 'S';
            $maritalData = $maritalStatusMap[$maritalCode] ?? ['code' => 'S', 'display' => 'Never Married'];

            // 3. Base Payload FHIR R4 Patient
            $body = [
                'resourceType' => 'Patient',
                'active'       => true,

                // Identifier Wajib: NIK
                'identifier' => [
                    [
                        'use'    => 'official',
                        'system' => 'https://fhir.kemkes.go.id/id/nik',
                        'value'  => $payload['nik'] ?? ''
                    ]
                ],

                // Nama Pasien
                'name' => [
                    [
                        'use' => 'official',
                        'text' => trim(($payload['gelar_depan'] ?? '') . ' ' . ($payload['nama_lengkap'] ?? '') . ' ' . ($payload['gelar_belakang'] ?? ''))
                    ]
                ],

                'gender'    => $gender,
                'birthDate' => $payload['tanggal_lahir'] ?? null,

                // MANDATORI: Mengatasi Error 10167 (Multiple Birth Status)
                // Diisi false jika pasien bukan anak kembar / tidak ada data urutan kelahiran kembar
                'multipleBirthBoolean' => false,

                // MANDATORI: Status Pernikahan (SatuSehat HL7 NullFlavor / Marital Status)
                'maritalStatus' => [
                    'coding' => [
                        [
                            'system'  => 'http://terminology.hl7.org/CodeSystem/v3-MaritalStatus',
                            'code'    => $maritalData['code'],
                            'display' => $maritalData['display']
                        ]
                    ]
                ]
            ];

            // 4. Tambahkan No. BPJS jika ada
            if (!empty($payload['no_bpjs'])) {
                $body['identifier'][] = [
                    'use'    => 'official',
                    'system' => 'https://fhir.kemkes.go.id/id/pasien-bpjs',
                    'value'  => $payload['no_bpjs']
                ];
            }

            // 5. Tambahkan Kontak
            if (!empty($payload['no_hp'])) {
                $body['telecom'][] = [
                    'system' => 'phone',
                    'value'  => $payload['no_hp'],
                    'use'    => 'mobile'
                ];
            }

            if (!empty($payload['email'])) {
                $body['telecom'][] = [
                    'system' => 'email',
                    'value'  => $payload['email'],
                    'use'    => 'home'
                ];
            }
            // 6. Tambahkan Alamat Lengkap
            if (!empty($payload['alamat'])) {
                $address = [
                    'use'     => 'home',
                    'line'    => [$payload['alamat']],
                    'country' => 'ID',
                ];

                if (!empty($payload['kode_pos'])) {
                    $address['postalCode'] = (string) $payload['kode_pos'];
                }

                // Extension Kode Wilayah Kemendagri
                $addressExtensions = [];

                // 1. Wilayah Administrasi (Menggunakan valueCode)
                $admFields = [
                    'province' => $payload['provinsi_code'] ?? null,
                    'city'     => $payload['kabkota_code'] ?? null,
                    'district' => $payload['kecamatan_code'] ?? null,
                    'village'  => $payload['kelurahan_code'] ?? null,
                ];

                foreach ($admFields as $urlKey => $val) {
                    if (!empty($val)) {
                        $addressExtensions[] = [
                            'url'       => $urlKey,
                            'valueCode' => (string) $val
                        ];
                    }
                }

                // 2. RT & RW (Menggunakan valueString, bukan valueCode)
                if (!empty($payload['rt'])) {
                    $addressExtensions[] = [
                        'url'         => 'rt',
                        'valueString' => (string) $payload['rt']
                    ];
                }

                if (!empty($payload['rw'])) {
                    $addressExtensions[] = [
                        'url'         => 'rw',
                        'valueString' => (string) $payload['rw']
                    ];
                }

                if (!empty($addressExtensions)) {
                    // PERBAIKAN: URL wajib 'AdministrativeCode' (A kapital)
                    $address['extension'] = [
                        [
                            'url'       => 'https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode',
                            'extension' => $addressExtensions
                        ]
                    ];
                }

                $body['address'][] = $address;
            }
            // 7. Request HTTP POST ke SATUSEHAT
            $response = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/Patient", $body);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('SATUSEHAT Create Patient Failed: ' . $response->body());

            return [
                'status'  => false,
                'message' => $response->json()['issue'][0]['details']['text'] ?? 'Gagal mendaftarkan Patient di SATUSEHAT.',
                'error'   => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Service Create Patient Exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan pada Service SATUSEHAT: ' . $e->getMessage()
            ];
        }
    }
    public function storeEncounter($kunjungan)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['status' => false, 'message' => 'Gagal mendapatkan token SATUSEHAT'];
        }

        // Pastikan Format ISO8601 untuk Waktu
        $startTime = Carbon::parse($kunjungan->tgl_masuk)->toIso8601String();
        $satusehatStatus = $kunjungan->satusehat_status ?? 'arrived';
        $satusehatClass  = $kunjungan->satusehat_class ?? 'AMB';
        // Mapping Display Class Standard HL7
        $classDisplayMap = [
            'AMB' => 'ambulatory',
            'IMP' => 'inpatient encounter',
            'EMER' => 'emergency',
        ];
        $classDisplay = $classDisplayMap[$satusehatClass] ?? 'ambulatory';
        // Build Base Body Encounter FHIR R4
        $body = [
            'resourceType' => 'Encounter',
            'status'       => $satusehatStatus,

            // CORRECTION: 'class' di FHIR R4 berupa Object Coding langsung (bukan Array)
            'class' => [
                'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code'    => $satusehatClass,
                'display' => $classDisplay
            ],

            // Identifier Lokal Faskes
            'identifier' => [
                [
                    // 'system' => 'http://sys-ids.kemkes.go.id/encounter/a9256dc3-ca8e-4f7c-a167-ea980e4446f0',
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . $this->get_idorg(),
                    'value'  => $kunjungan->no_registrasi
                ]
            ],

            // Subject (Pasien)
            'subject' => [
                'reference' => 'Patient/' . $kunjungan->pasien->ihs_number,
                'display'   => $kunjungan->pasien->nama_lengkap ?? $kunjungan->pasien->nama
            ],

            // Period
            'period' => [
                'start' => $startTime
            ],

            // History Status Awal
            'statusHistory' => [
                [
                    'status' => 'arrived',
                    'period' => [
                        'start' => $startTime
                    ]
                ]
            ],

            // Organization Faskes
            'serviceProvider' => [
                // 'reference' => 'Organization/a9256dc3-ca8e-4f7c-a167-ea980e4446f0'
                'reference' => 'Organization/' . $this->get_idorg()
            ]
        ];

        // Tambahkan Dokter (Participant) jika Memiliki IHS Number
        if (!empty($kunjungan->dokter->ihs_number)) {
            $body['participant'] = [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code'    => 'ATND',
                                    'display' => 'attending'
                                ]
                            ]
                        ]
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/' . $kunjungan->dokter->ihs_number,
                        'display'   => $kunjungan->dokter->nama_dokter ?? $kunjungan->dokter->nama
                    ]
                ]
            ];
        }

        // Tambahkan Location Poli jika Memiliki SATUSEHAT Location ID
        if (!empty($kunjungan->poli->satusehat_location_id)) {
            $body['location'] = [
                [
                    'location' => [
                        // 'reference' => 'Location/5a03621d-4755-42be-a23f-04e30b97ba1b',
                        'reference' => 'Location/' . $kunjungan->poli->satusehat_location_id,
                        'display'   => 'Ruang periksa dokter baru'
                    ]
                ]
            ];
        }
        // dd($body);
        // Send HTTP POST
        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/Encounter", $body);

        if ($response->successful()) {
            $resData = $response->json();
            return [
                'status' => true,
                'id'     => $resData['id'] ?? null,
                'data'   => $resData
            ];
        }

        Log::error('SATUSEHAT Store Encounter Error: ' . $response->body());

        return [
            'status'  => false,
            'message' => $response->json()['issue'][0]['details']['text'] ?? 'Gagal membuat Encounter di SATUSEHAT.',
            'error'   => $response->json()
        ];
    }
    public function updateInprogress($kunjungan)
    {
        try {
            // 1. Ambil Bearer Token SATUSEHAT
            $accessToken = $this->getToken();

            if (!$accessToken) {
                return [
                    'status'  => false,
                    'message' => 'Gagal mendapatkan Access Token SATUSEHAT'
                ];
            }

            // 2. Ambil ID Encounter SATUSEHAT
            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;
            if (!$satusehatEncounterId) {
                return [
                    'status'  => false,
                    'message' => 'Encounter ID SATUSEHAT tidak ditemukan pada data kunjungan.'
                ];
            }

            $waktuPemeriksaan = Carbon::now()->toIso8601String();
            $orgId = $this->get_idorg();

            // 3. Susun Payload (Sertakan identifier)
            $payload = [
                'resourceType' => 'Encounter',
                'id'           => $satusehatEncounterId,
                'identifier'   => [
                    [
                        'system' => "http://sys-ids.kemkes.go.id/encounter/{$orgId}",
                        'value'  => (string) ($kunjungan->no_kunjungan ?? $kunjungan->id)
                    ]
                ],
                'status' => 'in-progress',
                'class'  => [
                    'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                    'code'    => 'AMB',
                    'display' => 'ambulatory'
                ],
                'subject' => [
                    'reference' => 'Patient/' . $kunjungan->pasien->ihs_number,
                    'display'   => $kunjungan->pasien->nama_lengkap ?? $kunjungan->pasien->nama
                ],
                'participant' => [
                    [
                        'type' => [
                            [
                                'coding' => [
                                    [
                                        'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                        'code'    => 'PPRF',
                                        'display' => 'primary performer'
                                    ]
                                ]
                            ]
                        ],
                        'individual' => [
                            'reference' => 'Practitioner/' . $kunjungan->dokter->ihs_number,
                            'display'   => $kunjungan->dokter->nama_dokter ?? $kunjungan->dokter->nama
                        ]
                    ]
                ],
                'period' => [
                    'start' => $kunjungan->tgl_masuk ? Carbon::parse($kunjungan->tgl_masuk)->toIso8601String() : $waktuPemeriksaan,
                ],
                'location' => [
                    [
                        'location' => [
                            'reference' => 'Location/' . ($kunjungan->poli->satusehat_location_id ?? config('satusehat.default_location_id')),
                            'display'   => $kunjungan->poli->nama_poli ?? 'Poli Umum'
                        ]
                    ]
                ],
                'statusHistory' => [
                    [
                        'status' => 'arrived',
                        'period' => [
                            'start' => $kunjungan->tgl_masuk ? Carbon::parse($kunjungan->tgl_masuk)->toIso8601String() : $waktuPemeriksaan,
                            'end'   => $waktuPemeriksaan
                        ]
                    ],
                    [
                        'status' => 'in-progress',
                        'period' => [
                            'start' => $waktuPemeriksaan
                        ]
                    ]
                ],
                'serviceProvider' => [
                    'reference' => "Organization/{$orgId}"
                ]
            ];

            // 4. Kirim Request
            $baseUrl  = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put("{$baseUrl}/Encounter/{$satusehatEncounterId}", $payload);

            // 5. Response Handling
            if ($response->successful()) {

                DB::table('kunjungans')
                    ->where('id', $kunjungan->id)
                    ->update(['satusehat_status' => 'in-progress']);

                return [
                    'status'  => true,
                    'message' => 'Status Encounter berhasil diperbarui menjadi in-progress',
                    'data'    => $response->json()
                ];
            }
            Log::error('SATUSEHAT Update In-Progress Failed: ' . $response->body());

            return [
                'status'  => false,
                'message' => 'Gagal update status Encounter ke SATUSEHAT: ' . $response->reason(),
                'error'   => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Exception SATUSEHAT Update In-Progress: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
    public function conditionDiagnosis($kunjungan, $ermData)
    {
        try {
            // 1. Ambil Bearer Token
            $accessToken = $this->getToken();

            if (!$accessToken) {
                return [
                    'status'  => false,
                    'message' => 'Gagal mendapatkan Access Token SATUSEHAT'
                ];
            }

            // 2. Validasi Kebutuhan ID & Data ICD-10
            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;
            $patientIhs           = $kunjungan->pasien->ihs_number ?? null;
            $icdCode              = strtoupper(trim($ermData->icd10_code ?? ''));
            $icdDisplay           = $ermData->icd10_display ?? '';

            if (!$satusehatEncounterId) {
                return [
                    'status'  => false,
                    'message' => 'Encounter ID SATUSEHAT tidak ditemukan pada data kunjungan.'
                ];
            }

            if (!$patientIhs) {
                return [
                    'status'  => false,
                    'message' => 'Nomer IHS Pasien tidak ditemukan.'
                ];
            }

            if (empty($icdCode) || empty($icdDisplay)) {
                return [
                    'status'  => false,
                    'message' => 'Kode atau Display ICD-10 tidak boleh kosong.'
                ];
            }

            $waktuPencatatan = Carbon::now()->toIso8601String();

            // 3. Susun Payload FHIR Condition SATUSEHAT
            $payload = [
                'resourceType' => 'Condition',
                'clinicalStatus' => [
                    'coding' => [
                        [
                            'system'  => 'http://terminology.hl7.org/CodeSystem/condition-clinical',
                            'code'    => 'active',
                            'display' => 'Active'
                        ]
                    ]
                ],
                'category' => [
                    [
                        'coding' => [
                            [
                                'system'  => 'http://terminology.hl7.org/CodeSystem/condition-category',
                                'code'    => 'encounter-diagnosis',
                                'display' => 'Encounter Diagnosis'
                            ]
                        ]
                    ]
                ],
                'code' => [
                    'coding' => [
                        [
                            'system'  => 'http://hl7.org/fhir/sid/icd-10',
                            'code'    => $icdCode,
                            'display' => $icdDisplay
                        ]
                    ]
                ],
                'subject' => [
                    'reference' => 'Patient/' . $patientIhs,
                    'display'   => $kunjungan->pasien->nama_lengkap ?? $kunjungan->pasien->nama
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $satusehatEncounterId
                ],
                'onsetDateTime' => $waktuPencatatan,
                'recordedDate'  => $waktuPencatatan,
                'note'          => !empty($ermData->diagnosa_catatan) ? [
                    [
                        'text' => $ermData->diagnosa_catatan
                    ]
                ] : []
            ];

            // 4. Kirim HTTP POST ke Endpoint /Condition
            $baseUrl  = rtrim(env('SATUSEHAT_BASE_URL'), '/');
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/Condition", $payload);

            // 5. Response Handling
            if ($response->successful()) {
                $responseData = $response->json();
                $conditionId  = $responseData['id'] ?? null;

                // Simpan Condition ID ke tabel erm_records jika kolom ketersediaan ada
                if ($conditionId && isset($ermData->id)) {
                    DB::table('erm_records')
                        ->where('id', $ermData->id)
                        ->update(['satusehat_condition_id' => $conditionId]);
                }
                return [
                    'status'       => true,
                    'message'      => 'Condition Diagnosis berhasil dikirim ke SATUSEHAT',
                    'condition_id' => $conditionId,
                    'data'         => $responseData
                ];
            }

            Log::error('SATUSEHAT Condition Diagnosis Failed: ' . $response->body());

            return [
                'status'  => false,
                'message' => 'Gagal mengirim Condition Diagnosis ke SATUSEHAT: ' . $response->reason(),
                'error'   => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Exception SATUSEHAT Condition Diagnosis: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
    public function updateTTV($kunjungan, $ermData)
    {
        try {
            // 1. Ambil Bearer Token
            $accessToken = $this->getToken();

            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;

            // Ambil IHS pasien dari relasi kunjungan ATAU cari dari tabel pasiens via pasien_id
            $patientIhs = $kunjungan->pasien->ihs_number
                ?? DB::table('pasiens')->where('id', $kunjungan->pasien_id ?? $ermData->pasien_id)->value('ihs_number');

            if (!$satusehatEncounterId) {
                return [
                    'status'  => false,
                    'message' => 'Encounter ID SATUSEHAT (satusehat_encounter_id) tidak ditemukan pada data kunjungan.'
                ];
            }

            if (!$patientIhs) {
                return [
                    'status'  => false,
                    'message' => 'IHS Pasien (ihs_number) tidak ditemukan pada database pasien.'
                ];
            }
            $waktuPemeriksaan = Carbon::now()->toIso8601String();
            $baseUrl          = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            // 3. Pemetaan Kode LOINC untuk Tanda-Tanda Vital (TTV)
            $ttvConfigs = [
                'td_sistole' => [
                    'code'    => '8480-6',
                    'display' => 'Systolic blood pressure',
                    'unit'    => 'mmHg',
                    'ucum'    => 'mm[Hg]'
                ],
                'td_diastole' => [
                    'code'    => '8462-4',
                    'display' => 'Diastolic blood pressure',
                    'unit'    => 'mmHg',
                    'ucum'    => 'mm[Hg]'
                ],
                'nadi' => [
                    'code'    => '8867-4',
                    'display' => 'Heart rate',
                    'unit'    => 'beats/min',
                    'ucum'    => '/min'
                ],
                'suhu' => [
                    'code'    => '8310-5',
                    'display' => 'Body temperature',
                    'unit'    => 'C',
                    'ucum'    => 'Cel'
                ],
                'spo2' => [
                    'code'    => '2708-6',
                    'display' => 'Oxygen saturation in Arterial blood by Pulse oximetry',
                    'unit'    => '%',
                    'ucum'    => '%'
                ],
                'respirasi' => [
                    'code'    => '9279-1',
                    'display' => 'Respiratory rate',
                    'unit'    => 'breaths/min',
                    'ucum'    => '/min'
                ],
            ];

            $responses = [];
            $successCount = 0;

            // 4. Loop dan Kirim Setiap Data TTV yang Terisi
            foreach ($ttvConfigs as $field => $config) {
                $value = $ermData->$field ?? null;

                // Lewati jika field TTV tidak diisi/kosong
                if ($value === null || $value === '') {
                    continue;
                }

                // Susun Payload Observation FHIR
                $payload = [
                    'resourceType' => 'Observation',
                    'status'       => 'final',
                    'category'     => [
                        [
                            'coding' => [
                                [
                                    'system'  => 'http://terminology.hl7.org/CodeSystem/observation-category',
                                    'code'    => 'vital-signs',
                                    'display' => 'Vital Signs'
                                ]
                            ]
                        ]
                    ],
                    'code' => [
                        'coding' => [
                            [
                                'system'  => 'http://loinc.org',
                                'code'    => $config['code'],
                                'display' => $config['display']
                            ]
                        ]
                    ],
                    'subject' => [
                        'reference' => 'Patient/' . $patientIhs,
                        'display'   => $kunjungan->pasien->nama_lengkap ?? $kunjungan->pasien->nama
                    ],
                    'encounter' => [
                        'reference' => 'Encounter/' . $satusehatEncounterId
                    ],
                    'effectiveDateTime' => $waktuPemeriksaan,
                    'issued'            => $waktuPemeriksaan,
                    'valueQuantity'     => [
                        'value'  => (float) $value,
                        'unit'   => $config['unit'],
                        'system' => 'http://unitsofmeasure.org',
                        'code'   => $config['ucum']
                    ]
                ];

                // Kirim HTTP POST ke API SATUSEHAT (/Observation)
                $response = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$baseUrl}/Observation", $payload);

                if ($response->successful()) {
                    $successCount++;
                    $responses[$field] = [
                        'status' => true,
                        'id'     => $response->json('id')
                    ];
                } else {
                    $responses[$field] = [
                        'status' => false,
                        'error'  => $response->json()
                    ];
                    Log::error("SATUSEHAT Observation TTV ($field) Failed: " . $response->body());
                }
            }

            return [
                'status'        => $successCount > 0,
                'message'       => "Berhasil mengunggah {$successCount} data TTV ke SATUSEHAT.",
                'details'       => $responses
            ];
        } catch (\Exception $e) {
            Log::error('Exception SATUSEHAT Observation TTV: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
    public function sendAllergyIntolerance($kunjungan, $ermData)
    {
        try {
            // 1. Ambil Bearer Token & Konfigurasi Base URL
            $accessToken = $this->getToken();
            $baseUrl     = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            // 2. Ambil Variable Pendukung (Encounter, Pasien, Dokter)
            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;

            $patientIhs = $kunjungan->pasien->ihs_number
                ?? DB::table('pasiens')->where('id', $kunjungan->pasien_id ?? $ermData->pasien_id)->value('ihs_number');

            $practitionerIhs = $kunjungan->dokter->ihs_number
                ?? DB::table('dokters')->where('id', $kunjungan->dokter_id ?? $ermData->dokter_id)->value('ihs_number');

            // 3. Validasi Prasyarat
            if (!$satusehatEncounterId) {
                return [
                    'status'  => false,
                    'message' => 'Encounter ID SATUSEHAT (satusehat_encounter_id) tidak ditemukan pada data kunjungan.'
                ];
            }

            if (!$patientIhs) {
                return [
                    'status'  => false,
                    'message' => 'IHS Pasien (ihs_number) tidak ditemukan pada database pasien.'
                ];
            }

            // Cek apakah data alergi diisi
            if (empty($ermData->detail_alergi) || empty($ermData->kategori_alergi)) {
                return [
                    'status'  => false,
                    'message' => 'Data detail atau kategori alergi tidak diisi.'
                ];
            }

            $waktuPemeriksaan = Carbon::now()->toIso8601String();

            // 4. Susun Payload FHIR AllergyIntolerance
            $payload = [
                'resourceType' => 'AllergyIntolerance',
                'clinicalStatus' => [
                    'coding' => [
                        [
                            'system'  => 'http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical',
                            'code'    => 'active',
                            'display' => 'Active'
                        ]
                    ]
                ],
                'verificationStatus' => [
                    'coding' => [
                        [
                            'system'  => 'http://terminology.hl7.org/CodeSystem/allergyintolerance-verification',
                            'code'    => 'confirmed',
                            'display' => 'Confirmed'
                        ]
                    ]
                ],
                'type'     => 'allergy',
                'category' => [
                    $ermData->kategori_alergi // Pilihan standar: 'food', 'medication', 'environment', 'biologic'
                ],
                'code' => [
                    'coding' => [
                        [
                            'system'  => 'http://snomed.info/sct',
                            'code'    => '418038007', // Code SNOMED untuk "Proprietary drug (substance)" atau "Allergic reaction"
                            'display' => $ermData->detail_alergi
                        ]
                    ],
                    'text' => $ermData->detail_alergi
                ],
                'patient' => [
                    'reference' => 'Patient/' . $patientIhs,
                    'display'   => $kunjungan->pasien->nama_lengkap ?? $kunjungan->pasien->nama ?? 'Pasien'
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $satusehatEncounterId
                ],
                'recordedDate' => $waktuPemeriksaan
            ];

            // Tambahkan Dokter/Nakes Recorder jika IHS Dokter tersedia
            if ($practitionerIhs) {
                $payload['recorder'] = [
                    'reference' => 'Practitioner/' . $practitionerIhs
                ];
            }

            // 5. Kirim Request HTTP POST ke EndPoint /AllergyIntolerance
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/AllergyIntolerance", $payload);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'id'     => $response->json('id'),
                    'data'   => $response->json()
                ];
            }

            Log::error('SATUSEHAT AllergyIntolerance Failed: ' . $response->body());

            return [
                'status' => false,
                'error'  => $response->json(),
                'body'   => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception SATUSEHAT AllergyIntolerance: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
    public function sendProcedure($kunjungan, $tindakanItem)
    {
        try {
            $accessToken = $this->getToken();
            $baseUrl     = rtrim(env('SATUSEHAT_BASE_URL'), '/');

            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;
            $patientIhs          = $kunjungan->pasien->ihs_number
                ?? DB::table('pasiens')->where('id', $kunjungan->pasien_id)->value('ihs_number');
            $practitionerIhs     = $kunjungan->dokter->ihs_number
                ?? DB::table('dokters')->where('id', $kunjungan->dokter_id)->value('ihs_number');

            if (!$satusehatEncounterId || !$patientIhs) {
                return ['status' => false, 'message' => 'Missing Encounter ID or Patient IHS'];
            }

            $waktuPemeriksaan = Carbon::now()->toIso8601String();

            // Single Procedure Payload
            $payload = [
                'resourceType' => 'Procedure',
                'status'       => 'completed',
                'category'     => [
                    'coding' => [
                        [
                            'system'  => 'http://snomed.info/sct',
                            'code'    => '103693007',
                            'display' => 'Diagnostic procedure'
                        ]
                    ]
                ],
                'code' => [
                    'coding' => [
                        [
                            'system'  => 'http://hl7.org/fhir/sid/icd-9-cm', // Sistem Kode ICD-9-CM
                            'code'    => $tindakanItem['icd9_code'],
                            'display' => $tindakanItem['icd9_display'] ?? $tindakanItem['nama_tindakan']
                        ]
                    ],
                    'text' => $tindakanItem['icd9_display'] ?? $tindakanItem['nama_tindakan']
                ],
                'subject' => [
                    'reference' => 'Patient/' . $patientIhs,
                    'display'   => $kunjungan->pasien->nama_lengkap ?? 'Pasien'
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $satusehatEncounterId
                ],
                'performedDateTime' => $waktuPemeriksaan
            ];

            if ($practitionerIhs) {
                $payload['performer'] = [
                    [
                        'actor' => [
                            'reference' => 'Practitioner/' . $practitionerIhs
                        ]
                    ]
                ];
            }

            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/Procedure", $payload);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'id'     => $response->json('id')
                ];
            }

            Log::error('SATUSEHAT Procedure Failed: ' . $response->body());
            return ['status' => false, 'error' => $response->json()];
        } catch (\Exception $e) {
            Log::error('Exception SATUSEHAT Procedure: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    public function sendMedicationRequest($kunjungan, $itemObat)
    {
        try {
            $accessToken = $this->getToken();
            $baseUrl     = rtrim(config('services.satusehat.base_url', env('SATUSEHAT_BASE_URL')), '/');
            $orgId       = config('services.satusehat.organization_id', $this->get_idorg());

            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;
            $patientIhs          = $kunjungan->pasien->ihs_number
                ?? DB::table('pasiens')->where('id', $kunjungan->pasien_id)->value('ihs_number');
            $practitionerIhs     = $kunjungan->dokter->ihs_number
                ?? DB::table('dokters')->where('id', $kunjungan->dokter_id)->value('ihs_number');

            if (!$satusehatEncounterId || !$patientIhs) {
                return ['status' => false, 'message' => 'Missing Encounter ID or Patient IHS'];
            }

            // ==========================================
            // CEK & BRIDGING MEDICATION ID JIKA KOSONG
            // ==========================================
            $obatId       = $itemObat['obat_id'] ?? null;
            $medicationId = $itemObat['satusehat_medication_id'] ?? null;

            if (!$medicationId && $obatId) {
                $medicationId = DB::table('master_obats')
                    ->where('id', $obatId)
                    ->value('satusehat_medication_id');
            }

            // PERBAIKAN: Deklarasikan kfaDisplay di awal agar siap dipakai oleh Medication maupun MedicationRequest
            $kfaDisplay = !empty($itemObat['kfa_display']) ? $itemObat['kfa_display'] : ($itemObat['nama_obat'] ?? 'Obat');

            // Jika Medication ID belum ada, buat Resource Medication ke SATUSEHAT
            if (empty($medicationId)) {
                $kfaCode = !empty($itemObat['kfa_code'])
                    ? (string) $itemObat['kfa_code']
                    : DB::table('master_obats')->where('id', $obatId)->value('kfa_code');

                if (empty($kfaCode)) {
                    return [
                        'status'  => false,
                        'message' => 'Kode KFA untuk obat ' . ($itemObat['nama_obat'] ?? '') . ' belum diisi!'
                    ];
                }

                $medicationPayload = [
                    'resourceType' => 'Medication',
                    'meta' => [
                        'profile' => [
                            'https://fhir.kemkes.go.id/r4/StructureDefinition/Medication'
                        ]
                    ],
                    'identifier' => [
                        [
                            'system' => 'http://sys-ids.kemkes.go.id/medication/' . $orgId,
                            'value'  => (string) ($obatId ?? time()) . '-' . uniqid()
                        ]
                    ],
                    'code' => [
                        'coding' => [
                            [
                                'system'  => 'http://sys-ids.kemkes.go.id/kfa',
                                'code'    => (string) $kfaCode,
                                'display' => $kfaDisplay
                            ]
                        ]
                    ],
                    'status' => 'active',
                    'extension' => [
                        [
                            'url' => 'https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType',
                            'valueCodeableConcept' => [
                                'coding' => [
                                    [
                                        'system'  => 'http://terminology.kemkes.go.id/CodeSystem/medication-type',
                                        'code'    => 'NC',
                                        'display' => 'Non-Compound'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                $resMedication = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$baseUrl}/Medication", $medicationPayload);

                if (!$resMedication->successful()) {
                    Log::error('SATUSEHAT Medication Failed: ' . $resMedication->body());
                    return ['status' => false, 'error' => $resMedication->json(), 'step' => 'Medication'];
                }

                $medicationId = $resMedication->json('id');

                // Simpan ID baru ke tabel master_obats
                if ($obatId && $medicationId) {
                    DB::table('master_obats')
                        ->where('id', $obatId)
                        ->update([
                            'satusehat_medication_id' => $medicationId,
                            'updated_at'              => now()
                        ]);
                }
            }

            // ==========================================
            // KIRIM RESOURCE "MedicationRequest"
            // ==========================================
            $payload = [
                'resourceType' => 'MedicationRequest',
                'status'       => 'completed',
                'intent'       => 'order',

                'identifier' => [
                    [
                        'system' => 'http://sys-ids.kemkes.go.id/prescription/' . $orgId,
                        'value'  => 'RESEP-' . ($kunjungan->id ?? time()) . '-' . ($obatId ?? rand(100, 999))
                    ]
                ],

                'category' => [
                    [
                        'coding' => [
                            [
                                'system'  => 'http://terminology.hl7.org/CodeSystem/medicationrequest-category',
                                'code'    => 'outpatient',
                                'display' => 'Outpatient'
                            ]
                        ]
                    ]
                ],
                'medicationReference' => [
                    'reference' => 'Medication/' . $medicationId,
                    'display'   => $itemObat['nama_obat'] ?? $kfaDisplay
                ],
                'subject' => [
                    'reference' => 'Patient/' . $patientIhs,
                    'display'   => $kunjungan->pasien->nama_lengkap ?? 'Pasien'
                ],
                'encounter' => [
                    'reference' => 'Encounter/' . $satusehatEncounterId
                ],
                'authoredOn' => now()->toIso8601String(),
                'requester' => [
                    'reference' => 'Practitioner/' . ($practitionerIhs ?? '10000001'),
                    'display'   => $kunjungan->dokter->nama_dokter ?? 'Dokter'
                ],
                'dosageInstruction' => [
                    [
                        'text' => $itemObat['signa'] ?? 'Sesuai petunjuk dokter',
                        'timing' => [
                            'repeat' => [
                                'frequency'  => (int) ($itemObat['frequency'] ?? 3),
                                'period'     => 1,
                                'periodUnit' => 'd'
                            ]
                        ]
                    ]
                ],
                'dispenseRequest' => [
                    'quantity' => [
                        'value'  => (int) ($itemObat['qty'] ?? 1),
                        'unit'   => 'TAB',
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                        'code'   => 'TAB'
                    ]
                ]
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/MedicationRequest", $payload);

            if ($response->successful()) {
                return [
                    'status'        => true,
                    'medication_id' => $medicationId,
                    'id'            => $response->json('id')
                ];
            }

            Log::error('SATUSEHAT MedicationRequest Failed: ' . $response->body());
            return ['status' => false, 'error' => $response->json(), 'step' => 'MedicationRequest'];
        } catch (\Exception $e) {
            Log::error('Exception MedicationRequest: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    public function sendMedicationDispense($kunjungan, $itemObat)
    {
        $locationId = '03b9c8dc-5e06-4fba-b4d7-172e382dd877 ';
        // $locationId = 'ed180ca4-4af0-4315-ab59-fdf99e71dd4e';
         
        if (!$locationId) {
            return [
                'status'  => false,
                'message' => 'Location ID Farmasi/Apotek SATUSEHAT belum dikonfigurasi di .env (SATUSEHAT_PHARMACY_LOCATION_ID)'
            ];
        }
        try {
            $accessToken = $this->getToken();
            $baseUrl     = rtrim(config('services.satusehat.base_url', env('SATUSEHAT_BASE_URL')), '/');
            $orgId       = config('services.satusehat.organization_id', $this->get_idorg());
            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;
            $patientIhs          = $kunjungan->pasien->ihs_number
                ?? DB::table('pasiens')->where('id', $kunjungan->pasien_id)->value('ihs_number');

            $pharmacistIhs = '10002626108';

            // =========================================================================
            // CARA BACA YANG BENAR (Dukung Object Eloquent Model & Array)
            // =========================================================================
            $medicationRequestId = is_array($itemObat)
                ? ($itemObat['satusehat_medication_request_id'] ?? $itemObat['medication_request_id'] ?? null)
                : ($itemObat->satusehat_medication_request_id ?? $itemObat->medication_request_id ?? null);

            $itemId     = is_array($itemObat) ? ($itemObat['id'] ?? null) : $itemObat->id;
            $obatId     = is_array($itemObat) ? ($itemObat['obat_id'] ?? null) : $itemObat->obat_id;
            $namaObat   = is_array($itemObat) ? ($itemObat['nama_obat'] ?? 'Obat') : ($itemObat->nama_obat ?? $itemObat->obat->nama_obat ?? 'Obat');
            $qty        = is_array($itemObat) ? ($itemObat['qty'] ?? 1) : ($itemObat->qty ?? 1);
            $signa      = is_array($itemObat) ? ($itemObat['aturan_pakai'] ?? 'Sesuai petunjuk') : ($itemObat->aturan_pakai ?? 'Sesuai petunjuk');

            // Fallback jika di object/array masih null, query ulang ke DB
            if (!$medicationRequestId && $itemId) {
                $medicationRequestId = DB::table('billing_details')
                    ->where('id', $itemId)
                    ->value('satusehat_medication_request_id');
            }

            if (!$satusehatEncounterId || !$patientIhs) {
                return ['status' => false, 'message' => 'Missing Encounter ID or Patient IHS'];
            }

            if (!$medicationRequestId) {
                return ['status' => false, 'message' => 'Missing MedicationRequest ID pada item obat: ' . $namaObat];
            }

            // ==========================================
            // CEK & BRIDGING MEDICATION ID JIKA KOSONG
            // ==========================================
            $medicationId = is_array($itemObat)
                ? ($itemObat['satusehat_medication_id'] ?? null)
                : ($itemObat->satusehat_medication_id ?? null);

            if (!$medicationId && $obatId) {
                $medicationId = DB::table('master_obats')
                    ->where('id', $obatId)
                    ->value('satusehat_medication_id');
            }

            // KFA Code Fallback
            if (empty($medicationId)) {
                $kfaCode = DB::table('master_obats')->where('id', $obatId)->value('kfa_code');

                if (empty($kfaCode)) {
                    return ['status' => false, 'message' => 'Kode KFA untuk obat ' . $namaObat . ' belum diisi!'];
                }

                $medicationPayload = [
                    'resourceType' => 'Medication',
                    'meta' => ['profile' => ['https://fhir.kemkes.go.id/r4/StructureDefinition/Medication']],
                    'identifier' => [
                        [
                            'system' => 'http://sys-ids.kemkes.go.id/medication/' . $orgId,
                            'value'  => (string) ($obatId ?? time()) . '-' . uniqid()
                        ]
                    ],
                    'code' => [
                        'coding' => [
                            [
                                'system'  => 'http://sys-ids.kemkes.go.id/kfa',
                                'code'    => (string) $kfaCode,
                                'display' => $namaObat
                            ]
                        ]
                    ],
                    'status' => 'active',
                    'extension' => [
                        [
                            'url' => 'https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType',
                            'valueCodeableConcept' => [
                                'coding' => [
                                    [
                                        'system'  => 'http://terminology.kemkes.go.id/CodeSystem/medication-type',
                                        'code'    => 'NC',
                                        'display' => 'Non-Compound'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                $resMedication = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$baseUrl}/Medication", $medicationPayload);

                if (!$resMedication->successful()) {
                    Log::error('SATUSEHAT Medication Failed: ' . $resMedication->body());
                    return ['status' => false, 'error' => $resMedication->json(), 'step' => 'Medication'];
                }

                $medicationId = $resMedication->json('id');

                if ($obatId && $medicationId) {
                    DB::table('master_obats')->where('id', $obatId)->update([
                        'satusehat_medication_id' => $medicationId,
                        'updated_at'              => now()
                    ]);
                }
            }

            // ==========================================
            // KIRIM RESOURCE "MedicationDispense"
            // ==========================================
            $now = now()->toIso8601String();

            $payload = [
                'resourceType' => 'MedicationDispense',
                'status'       => 'completed',
                'identifier' => [
                    [
                        'system' => 'http://sys-ids.kemkes.go.id/prescription/' . $orgId,
                        'value'  => 'DISPENSE-' . ($kunjungan->id ?? time()) . '-' . ($itemId ?? rand(100, 999))
                    ]
                ],

                'medicationReference' => [
                    'reference' => 'Medication/' . $medicationId,
                    'display'   => $namaObat
                ],
                'subject' => [
                    'reference' => 'Patient/' . $patientIhs,
                    'display'   => $kunjungan->pasien->nama_lengkap ?? 'Pasien'
                ],
                'context' => [
                    'reference' => 'Encounter/' . $satusehatEncounterId
                ],
                'authorizingPrescription' => [
                    [
                        'reference' => 'MedicationRequest/' . $medicationRequestId
                    ]
                ],
                'performer' => [
                    [
                        'actor' => [
                            'reference' => 'Practitioner/' . ($pharmacistIhs ?? '10000001'),
                            'display'   => auth()->user()->name ?? 'Apoteker'
                        ]
                    ]
                ],
                'location' => [
                    'reference' => 'Location/' . $locationId,
                    'display'   => 'Apotek / Depo Farmasi'
                ],
                'quantity' => [
                    'value'  => (int) $qty,
                    'unit'   => 'TAB',
                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                    'code'   => 'TAB'
                ],
                'daysSupply' => [
                    'value'  => 3,
                    'unit'   => 'Days',
                    'system' => 'http://unitsofmeasure.org',
                    'code'   => 'd'
                ],
                'whenPrepared'   => $now,
                'whenHandedOver' => $now,
                'dosageInstruction' => [
                    [
                        'text' => $signa,
                        'timing' => [
                            'repeat' => [
                                'frequency'  => 3,
                                'period'     => 1,
                                'periodUnit' => 'd'
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/MedicationDispense", $payload);

            if ($response->successful()) {
                return [
                    'status'        => true,
                    'medication_id' => $medicationId,
                    'id'            => $response->json('id')
                ];
            }

            Log::error('SATUSEHAT MedicationDispense Failed: ' . $response->body());
            return ['status' => false, 'error' => $response->json(), 'step' => 'MedicationDispense'];
        } catch (\Exception $e) {
            Log::error('Exception MedicationDispense: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    public function updateEncounterFinished($kunjungan)
    {
        try {
            $satusehatEncounterId = $kunjungan->satusehat_encounter_id ?? null;
            if (!$satusehatEncounterId) {
                return ['status' => false, 'message' => 'Encounter ID tidak ditemukan'];
            }
            $accessToken = $this->getToken();
            $baseUrl     = rtrim(config('services.satusehat.base_url', env('SATUSEHAT_BASE_URL')), '/');

            // 1. GET data Encounter lama
            $getResponse = Http::withToken($accessToken)->get("{$baseUrl}/Encounter/{$satusehatEncounterId}");

            if (!$getResponse->successful()) {
                return ['status' => false, 'message' => 'Gagal mengambil data Encounter dari SATUSEHAT'];
            }

            $encounterData = $getResponse->json();
            $nowIso        = now()->format('Y-m-d\TH:i:sP');

            // 2. Set status utama & period end
            $encounterData['status'] = 'finished';

            if (!isset($encounterData['period'])) {
                $encounterData['period'] = ['start' => $nowIso];
            }
            $encounterData['period']['end'] = $nowIso;

            // 3. SUSUN ULANG statusHistory SESUAI STRUKTUR RESMI FHIR
            // Ambil history lama jika ada, lalu tambahkan status 'finished'
            $existingHistory = $encounterData['statusHistory'] ?? [];

            $existingHistory[] = [
                'status' => 'finished',
                'period' => [
                    'start' => $encounterData['period']['start'] ?? $nowIso,
                    'end'   => $nowIso
                ]
            ];

            // Pastikan nama key-nya camelCase: 'statusHistory'
            $encounterData['statusHistory'] = $existingHistory;

            // 4. Kirim HTTP PUT
            $patchPayload = [
                [
                    'op'    => 'replace',
                    'path'  => '/status',
                    'value' => 'finished'
                ],
                [
                    'op'    => 'replace',
                    'path'  => '/period/end',
                    'value' => $nowIso
                ]
            ];

            $putResponse = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json-patch+json'])
                ->patch("{$baseUrl}/Encounter/{$satusehatEncounterId}", $patchPayload);
            if ($putResponse->successful()) {
                return ['status' => true, 'data' => $putResponse->json()];
            }

            Log::error('Update Encounter Finished Failed: ' . $putResponse->body());
            return ['status' => false, 'error' => $putResponse->json()];
        } catch (\Exception $e) {
            Log::error('Exception Update Encounter Finished: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    public function searchKfa(string $keyword)
    {
        $token = $this->getToken();

        // Base URL Staging SATUSEHAT
        $baseUrl = 'https://api-satusehat-stg.dto.kemkes.go.id';

        // Endpoint resmi KFA v2: /kfa-v2/products/v2/search
        $response = Http::withToken($token)
            ->get("https://api-satusehat-stg.dto.kemkes.go.id/kfa-v2/products/all", [
                'page'         => 1,
                'size'         => 20,
                'product_type' => 'farmasi',
                'keyword'      => $keyword,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('API SATUSEHAT KFA Error (' . $response->status() . '): ' . $response->body());
        return null;
    }
}
