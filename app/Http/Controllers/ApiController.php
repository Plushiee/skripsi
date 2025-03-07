<?php

namespace App\Http\Controllers;

use App\Models\TabelArusAirModel;
use App\Models\TabelPingModel;
use Illuminate\Http\RedirectResponse;
use App\Events\SSEUpdateEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\TabelPHModel;
use App\Models\TabelPompaModel;
use App\Models\TabelTDSModel;
use App\Models\TabelTempHumModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event as FacadesEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function getDashboard()
    {
        $ph = optional(TabelPHModel::latest()->first())->ph ?? 0;
        $tds = optional(TabelTDSModel::latest()->first())->ppm ?? 0;
        $tempHum = [
            'temperature' => optional(TabelTempHumModel::latest()->first())->temperature ?? 0,
            'humidity' => optional(TabelTempHumModel::latest()->first())->humidity ?? 0,
        ];
        $arusAir = optional(TabelArusAirModel::latest()->first())->debit ?? 0;
        $pompa = TabelPompaModel::latest()->first();
        $ping = optional(TabelPingModel::latest()->first())->ping ?? 0;

        $formattedData = [
            'ph' => $ph,
            'ping' => $ping,
            'tds' => $tds,
            'tempHum' => $tempHum,
            'arusAir' => $arusAir,
            'pompa' => $pompa,
        ];

        return response()->json($formattedData);
    }

    public function getUser()
    {
        $data = User::all();

        $formattedData = [
            'total' => $data->count(),
            'totalNotFiltered' => User::count(),
            'rows' => $data
                ->map(function ($item) {
                    $jam = json_decode($item->jam, true);

                    return [
                        'id' => $item->id,
                        'email' => $item->email,
                        'nama' => $item->nama,
                        'role' => $item->role,
                        'hari' => $item->hari,
                        'jam' => $jam,
                        'fakultas' => $item->fakultas,
                        'prodi' => $item->prodi,
                        'semester' => $item->semester,
                        'foto' => $item->foto,
                        'nomor_telepon' => $item->nomor_telepon,
                    ];
                })
                ->toArray(),
        ];

        return response()->json($formattedData);
    }

    public function sendMqtt(Request $request)
    {
        $topic = $request->input('topic');
        $message = $request->input('message');

        MQTT::publish($topic, $message);

        // Kembalikan respon JSON
        return response()->json(['success' => 'Pesan MQTT berhasil dikirim!']);
    }

    private function validDate($date)
    {
        $date = strtotime($date);
        return date('Y-m-d H:i:s', $date);
    }

    public function getPH(Request $request)
    {
        $query = TabelPHModel::query();

        if ($request->has('start_time') && $request->has('end_time')) {
            $startTime = $this->validDate($request->input('start_time'));
            $endTime = $this->validDate($request->input('end_time'));
            $query->whereBetween('created_at', [$startTime, $endTime]);

            $ph = $query->get();
        } else {
            $ph = TabelPHModel::all();
        }

        $formattedData = [
            'total' => $ph->count(),
            'totalNotFiltered' => TabelPHModel::count(),
            'rows' => $ph
                ->map(function ($item) {
                    return [
                        'timestamp' => $item->created_at->format('Y-m-d H:i:s'),
                        'id_area' => $item->id_area,
                        'ph' => $item->ph,
                    ];
                })
                ->toArray(),
        ];

        return response()->json($formattedData);
    }

    public function getTDS(Request $request)
    {
        $query = TabelTDSModel::query();

        if ($request->has('start_time') && $request->has('end_time')) {
            $startTime = $this->validDate($request->input('start_time'));
            $endTime = $this->validDate($request->input('end_time'));
            $query->whereBetween('created_at', [$startTime, $endTime]);

            $ph = $query->get();
        } else {
            $ph = TabelTDSModel::all();
        }

        $formattedData = [
            'total' => $ph->count(),
            'totalNotFiltered' => TabelTDSModel::count(),
            'rows' => $ph
                ->map(function ($item) {
                    return [
                        'timestamp' => $item->created_at->format('Y-m-d H:i:s'),
                        'id_area' => $item->id_area,
                        'ppm' => $item->ppm,
                    ];
                })
                ->toArray(),
        ];

        return response()->json($formattedData);
    }

    public function getUdara(Request $request)
    {
        $query = TabelTempHumModel::query();

        if ($request->has('start_time') && $request->has('end_time')) {
            $startTime = $this->validDate($request->input('start_time'));
            $endTime = $this->validDate($request->input('end_time'));
            $query->whereBetween('created_at', [$startTime, $endTime]);

            $ph = $query->get();
        } else {
            $ph = TabelTempHumModel::all();
        }

        $formattedData = [
            'total' => $ph->count(),
            'totalNotFiltered' => TabelTempHumModel::count(),
            'rows' => $ph
                ->map(function ($item) {
                    return [
                        'timestamp' => $item->created_at->format('Y-m-d H:i:s'),
                        'id_area' => $item->id_area,
                        'temperature' => $item->temperature,
                        'humidity' => $item->humidity,
                    ];
                })
                ->toArray(),
        ];

        return response()->json($formattedData);
    }

    public function getPing(Request $request)
    {
        $query = TabelPingModel::query();

        if ($request->has('start_time') && $request->has('end_time')) {
            $startTime = $this->validDate($request->input('start_time'));
            $endTime = $this->validDate($request->input('end_time'));
            $query->whereBetween('created_at', [$startTime, $endTime]);

            $ph = $query->get();
        } else {
            $ph = TabelPingModel::all();
        }

        $formattedData = [
            'total' => $ph->count(),
            'totalNotFiltered' => TabelPingModel::count(),
            'rows' => $ph
                ->map(function ($item) {
                    return [
                        'timestamp' => $item->created_at->format('Y-m-d H:i:s'),
                        'id_area' => $item->id_area,
                        'ping' => $item->ping,
                    ];
                })
                ->toArray(),
        ];

        return response()->json($formattedData);
    }

    public function getArusAir(Request $request)
    {
        $query = TabelArusAirModel::query();

        if ($request->has('start_time') && $request->has('end_time')) {
            $startTime = $this->validDate($request->input('start_time'));
            $endTime = $this->validDate($request->input('end_time'));
            $query->whereBetween('created_at', [$startTime, $endTime]);

            $ph = $query->get();
        } else {
            $ph = TabelArusAirModel::all();
        }

        $formattedData = [
            'total' => $ph->count(),
            'totalNotFiltered' => TabelArusAirModel::count(),
            'rows' => $ph
                ->map(function ($item) {
                    return [
                        'timestamp' => $item->created_at->format('Y-m-d H:i:s'),
                        'id_area' => $item->id_area,
                        'debit' => $item->debit,
                    ];
                })
                ->toArray(),
        ];

        return response()->json($formattedData);
    }

    public function postPompa(Request $request)
    {
        $request->validate([
            'status' => 'required|in:nyala,mati',
        ]);

        $pompa = new TabelPompaModel();
        $pompa->id_area = 1;
        $pompa->status = $request->input('status');
        if ($request->has('suhu')) {
            $pompa->suhu = $request->input('suhu');
        } else {
            $pompa->suhu = null;
        }
        $pompa->otomatis = $request->boolean('otomatis', false);
        $pompa->save();

        return response()->json(['success' => 'Status pompa berhasil diubah!']);
    }

    public function updateAdmin(Request $request)
    {
        // Map field ke key yang benar
        if ($request->has('field') && $request->has('value')) {
            $request[$request->input('field')] = $request->input('value');
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:akun,id',
            'email' => 'sometimes|email',
            'role' => 'sometimes|in:admin,admin-master',
            'nama' => 'sometimes|string|max:255',
            'hari' => 'sometimes|string|max:50',
            'jam' => 'sometimes|string|max:50',
            'fakultas' => 'sometimes|string|max:255',
            'prodi' => 'sometimes|string|max:255',
            'semester' => 'sometimes|integer|min:1|max:14',
            'foto' => 'sometimes|string',
            'nomor_telepon' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8|required_with:password',
        ]);

        $isMasterAdmin = Auth::user()->role === 'admin-master';

        if (!$isMasterAdmin) {
            $validator->after(function ($validator) use ($request) {
                if ($request->id != Auth::user()->id) {
                    $validator->errors()->add('id', 'ID tidak sesuai dengan pengguna yang sedang login.');
                }
            });
        }

        if ($request->has('password')) {
            $validator->after(function ($validator) use ($request) {
                if ($request->id != Auth::user()->id) {
                    $validator->errors()->add('id', 'Password tidak dapat diubah untuk pengguna lain.');
                }
            });
        }

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'messages' => $validator->errors(),
            ], 422);
        }

        $user = User::where('id', $request->input('id'))->first();

        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan!'], 404);
        }

        $fields = $request->only([
            'email',
            'role',
            'nama',
            'hari',
            'jam',
            'fakultas',
            'prodi',
            'semester',
            'foto',
            'nomor_telepon'
        ]);

        foreach ($fields as $key => $value) {
            $user->{$key} = $value;
        }

        if (!$user->save()) {
            return response()->json(['error' => 'Gagal menyimpan perubahan data!'], 500);
        }

        return response()->json(['success' => 'Data user berhasil diubah!']);
    }

    public function updateAdminPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:akun,id',
            'photo' => 'required|image|max:3072', // Maksimal 3MB
        ]);

        $isMasterAdmin = Auth::user()->role === 'admin-master';

        if (!$isMasterAdmin) {
            $validator->after(function ($validator) use ($request) {
                if ($request->id != Auth::user()->id) {
                    $validator->errors()->add('id', 'ID tidak sesuai dengan pengguna yang sedang login.');
                }
            });
        }

        $userId = $request->input('id');
        $image = $request->file('photo');

        // Simpan gambar ke folder storage
        $imagePath = $image->storeAs('public/photos/' . $userId, 'user_' . $userId . '.' . $image->getClientOriginalExtension());

        // Simpan path ke database
        $user = User::where('id', $userId)->first();
        if ($user) {
            $user->foto = str_replace('public/', '', $imagePath); // Simpan path relatif
            $user->save();
        }

        $image = asset('storage/' . $user->foto);

        return response()->json(['message' => 'Photo updated successfully!', 'image' => $image], 200);
    }

    public function updateJamKerja(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:akun,id',
            'start' => 'required|string|max:50',
            'end' => 'required|string|max:50',
        ]);


        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->jam = json_encode(['s' => $request->start, 'e' => $request->end]);
            $user->save();
        } else {
            return response()->json(['message' => 'User tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'Jam kerja berhasil diupdate!'], 200);
    }

    public function updateHariKerja(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:akun,id',
            'hari' => 'required|string|max:50',
        ]);

        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->hari = $request->hari;
            $user->save();
        } else {
            return response()->json(['message' => 'User tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'Hari kerja berhasil diupdate!'], 200);
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:akun,id',
            'role' => 'required|in:admin,admin-master',
        ]);

        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->role = $request->role;
            $user->save();
        } else {
            return response()->json(['message' => 'User tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'Role berhasil diupdate!'], 200);
    }

    public function updateAdminNama(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:akun,id',
            'nama' => 'required|string|max:255',
        ]);

        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->nama = $request->nama;
            $user->save();
        } else {
            return response()->json(['message' => 'User tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'Nama berhasil diupdate!'], 200);
    }

    public function getPhoto(Request $request)
    {
        $userId = $request->id;

        $user = User::where('id', $userId)->first();

        if ($user && $user->foto) {
            $imagePath = asset('storage/' . $user->foto);
        } else {
            $imagePath = asset('main/img/default-avatar.jpg');
        }

        return response()->json(['image' => $imagePath]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:akun,id',
        ]);

        $user = User::where('id', $request->id)->first();

        if ($user) {
            $password = substr(md5(uniqid()), 0, 16);
            $user->password = bcrypt($password);
            $user->save();
        } else {
            return response()->json(['message' => 'User tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'Password berhasil direset!', 'new_password' => $password], 200);
    }

    public function postUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:akun,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|in:admin,admin-master',
            'nama' => 'required|string|max:255',
            'hari' => 'required|array',
            's' => 'required|string|max:50',
            'e' => 'required|string|max:50',
            'nomor_telepon' => 'required|string|max:20',
        ]);

        $formattedHari = '["' . implode('","', $request->input('hari')) . '"]';

        $user = new User();
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->role = $request->input('role');
        $user->nama = $request->input('nama');
        $user->hari = $formattedHari;
        $user->jam = json_encode(['s' => $request->input('s'), 'e' => $request->input('e')]);
        $user->nomor_telepon = $request->input('nomor_telepon');
        $user->save();

        return response()->json(['message' => 'User berhasil ditambahkan!'], 200);
    }

    public function deleteAdmin(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:akun,id',
        ]);

        if ($request->id == Auth::user()->id) {
            return response()->json(['message' => 'User yang digunakan tidak dapat dihapus!'], 403);
        }

        $user = User::where('id', $request->id)->first();

        if ($user) {
            $user->delete();
        } else {
            return response()->json(['message' => 'User tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'User berhasil dihapus!'], 200);
    }

    public function getSSE()
    {
        // Set unlimited execution time
        set_time_limit(0);

        return response()->stream(
            function () {
                while (!connection_aborted()) {
                    // Ambil data dari cache atau gunakan default jika tidak ada
                    $cachedData = cache('sse-update-event', []);

                    // Validasi format data dan gunakan default jika tidak sesuai
                    $formattedData = [
                        'ph' => $cachedData['ph'] ?? 0,
                        'ping' => $cachedData['ping'] ?? 0,
                        'tds' => $cachedData['tds'] ?? 0,
                        'tempHum' => [
                            'temperature' => $cachedData['tempHum']['temperature'] ?? 0,
                            'humidity' => $cachedData['tempHum']['humidity'] ?? 0,
                        ],
                        'arusAir' => $cachedData['arusAir'] ?? 0,
                        'status'=> $cachedData['status'] ?? 0,
                    ];

                    // Kirim data sebagai event SSE
                    echo 'data: ' . json_encode($formattedData) . "\n\n";

                    // Flush buffer untuk menghindari tumpukan data
                    @ob_flush();
                    flush();

                    // Tunggu sebelum mengirim data berikutnya (interval 1 detik)
                    sleep(1);
                }

                // Hentikan script jika koneksi ditutup
                exit;
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ],
        );
    }
}
