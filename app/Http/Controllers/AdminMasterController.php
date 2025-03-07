<?php

namespace App\Http\Controllers;

use App\Models\TabelArusAirModel;
use App\Models\TabelPingModel;
use Illuminate\Http\Request;
use App\Models\TabelPompaModel;
use App\Models\TabelTDSModel;
use App\Models\TabelTempHumModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminMasterController extends Controller
{
    private function getRangkumanData($s = null, $e = null)
    {
        // Default range jika tidak ada input
        if (!$s && !$e) {
            $e = Carbon::now()->toDateString();
            $s = Carbon::parse($e)->subDays(32)->toDateString();
        } elseif ($s && !$e) {
            $e = Carbon::now()->toDateString();
        } elseif (!$s && $e) {
            $s = Carbon::parse($e)->subDays(32)->toDateString();
        }

        // Ambil data
        $rawDatta = [
            'arus' => TabelArusAirModel::whereBetween('created_at', [$s, $e])->get(),
            'tds' => TabelTDSModel::whereBetween('created_at', [$s, $e])->get(),
            'udara' => TabelTempHumModel::whereBetween('created_at', [$s, $e])->get(),
            'reservoir' => TabelPingModel::whereBetween('created_at', [$s, $e])->get(),
        ];

        // Helper rentang tanggal
        $dates = collect();
        $current = Carbon::parse($s);
        while ($current->lte(Carbon::parse($e))) {
            $dates->push($current->format('Y-m-d'));
            $current->addDay();
        }

        // Fungsi untuk mengisi data kosong
        $fillMissingDates = function ($groupedData, $key) use ($dates) {
            return $dates->mapWithKeys(function ($date) use ($groupedData, $key) {
                return [$date => $groupedData->get($date, 0)];
            });
        };

        // data
        $data = [
            'arus' => $fillMissingDates(
                $rawDatta['arus']->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                })->map(function ($item) {
                    return round($item->avg('debit'), 3);
                }),
                'debit'
            ),
            'tds' => $fillMissingDates(
                $rawDatta['tds']->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                })->map(function ($item) {
                    return round($item->avg('ppm'), 3);
                }),
                'ppm'
            ),
            'temperature' => $fillMissingDates(
                $rawDatta['udara']->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                })->map(function ($item) {
                    return round($item->avg('temperature'), 3);
                }),
                'temperature'
            ),
            'humidity' => $fillMissingDates(
                $rawDatta['udara']->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                })->map(function ($item) {
                    return round($item->avg('humidity'), 3);
                }),
                'humidity'
            ),
            'reservoir' => $fillMissingDates(
                $rawDatta['reservoir']->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                })->map(function ($item) {
                    return round($item->avg('ping'), 3);
                }),
                'ping'
            ),
        ];

        return $data;
    }

    public function dashboardAdmin()
    {
        // Ambil hari dan waktu sekarang
        $currentDay = Carbon::now()->locale('id')->isoFormat('dddd'); // Contoh: "Sabtu"
        $currentTime = Carbon::now()->format('H:i');

        // Query admin yang berjaga
        $adminJaga = User::select('nama', 'foto', 'hari', 'jam', 'role')->whereRaw('JSON_CONTAINS(hari, JSON_QUOTE(?))', [$currentDay])
            ->where(function ($query) use ($currentTime) {
                $query->whereRaw("TIME(JSON_UNQUOTE(JSON_EXTRACT(jam, '$.s'))) <= ?", [$currentTime])
                    ->whereRaw("TIME(JSON_UNQUOTE(JSON_EXTRACT(jam, '$.e'))) >= ?", [$currentTime]);
            })
            ->get();

        $pompaStatus = TabelPompaModel::latest('created_at')->first();
        if ($pompaStatus == null) {
            $pompaStatus = new TabelPompaModel();
            $pompaStatus->status = 'mati';
            $pompaStatus->otomatis = false;
            $pompaStatus->suhu = 0;
        }
        return view('admin-master.dashboardAdmin', compact('pompaStatus', 'adminJaga'));
    }

    public function rangkuman(Request $request)
    {
        $s = $request->query('s');
        $e = $request->query('e');

        $data = $this->getRangkumanData($s, $e);

        return view('admin-master.rangkuman', ['data' => $data]);
    }

    public function rangkumanCetak(Request $request)
    {
        $s = $request->query('s');
        $e = $request->query('e');

        // Ambil data rangkuman
        $data = $this->getRangkumanData($s, $e);

        if (!$s && !$e) {
            $e = Carbon::now()->format('d-m-Y');
            $s = Carbon::parse($e)->subDays(32)->format('d-m-Y');
        } elseif ($s && !$e) {
            $e = Carbon::now()->format('d-m-Y');
        } elseif (!$s && $e) {
            $s = Carbon::parse($e)->subDays(32)->format('d-m-Y');
        }

        try {
            // Render HTML ke file sementara
            $view = view('PDF.rangkuman', ['data' => $data, 's' => $s, 'e' => $e])->render();
            $htmlPath = storage_path('app\public\temp_rangkuman.html');

            file_put_contents($htmlPath, $view);

            // Encode URL untuk mengatasi spasi atau karakter khusus
            $encodedHtmlPath = 'file:///' . str_replace(' ', '%20', str_replace('\\', '/', $htmlPath));
            Log::info("Encoded HTML path: $encodedHtmlPath");
            $pdfPath = storage_path('app\public\Rangkuman-' . $s . '-to-' . $e . '.pdf');

            // Jalankan perintah untuk menghasilkan PDF
            $command = "node ../node/generate-pdf.js \"$encodedHtmlPath\" \"$pdfPath\"";

            if (!file_exists($htmlPath)) {
                return response()->json(['error' => 'HTML file not found.'], 500);
            }

            exec($command . ' 2>&1', $output, $returnVar);

            // Jika proses gagal
            if ($returnVar !== 0 || !file_exists($pdfPath)) {
                Log::error("Proses generate PDF gagal: " . implode("\n", $output));
                return response()->json(['error' => 'Gagal membuat file PDF.'], 500);
            }

            // Hapus file HTML sementara
            unlink($htmlPath);

            // Kirim file PDF ke client dan hapus setelah dikirim
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Rangkuman-' . $s . '-to-' . $e . '.pdf"',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error("Error saat mencetak rangkuman: " . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mencetak rangkuman.'], 500);
        }
    }

    public function tabelPH()
    {
        return view('admin-master.tabelPH');
    }
    public function tabelTDS()
    {
        return view('admin-master.tabelTDS');
    }
    public function tabelUdara()
    {
        return view('admin-master.tabelUdara');
    }
    public function tabelArus()
    {
        return view('admin-master.tabelArusAir');
    }

    public function tabelReservoir()
    {
        return view('admin-master.tabelReservoir');
    }

    public function pengaturanAkun()
    {
        return view('admin-master.pengaturanAkun');
    }

    public function daftarAdmin()
    {
        return view('admin-master.daftarAdmin');
    }

    public function viewAdmin(Request $request)
    {
        $id = $request->route('id');
        $data = User::where('id', $id)->first();

        if (!$data) {
            // Jika user dengan ID ini tidak ditemukan, kembalikan 404
            abort(404, 'User not found');
        }

        if ($data->jam) {
            $jam = json_decode($data->jam, true);
            $data->jam = $jam;
        }

        return view('admin-master.view-admin', compact('data'));
    }
}
