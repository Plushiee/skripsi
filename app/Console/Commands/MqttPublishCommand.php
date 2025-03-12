<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\TabelPompaModel;
use PhpMqtt\Client\Exceptions\MqttClientException;
use Illuminate\Support\Facades\Log;

class MqttPublishCommand extends Command
{
    protected $signature = 'mqtt:publish';
    protected $description = 'Mengirimkan status pompa via MQTT setiap 5 detik';

    protected $oldId = 0;

    public function handle()
    {
        try {
            $mqtt = MQTT::connection('default');

            echo sprintf("MQTT Publisher Started...");

            while (true) {
                $statusPompa = TabelPompaModel::latest('created_at')->first();

                if ($statusPompa) {
                    if ($statusPompa->otomatis) {
                        // Jika otomatis aktif, selalu kirim status terbaru
                        $this->publishPumpStatus($mqtt, $statusPompa->status);
                    } elseif ($statusPompa->id != $this->oldId) {
                        // Jika otomatis tidak aktif, hanya kirim sekali jika ada perubahan
                        $this->oldId = $statusPompa->id;
                        $this->publishPumpStatus($mqtt, $statusPompa->status);
                    }
                }

                sleep(1);
            }
        } catch (MqttClientException $e) {
            Log::error("MQTT error: " . $e->getMessage());
            sleep(5);
            $this->handle(); // Restart jika error
        }
    }

    protected function publishPumpStatus($mqtt, $status)
    {
        Log::info("Publishing Pump Status: $status");
        $mqtt->publish('72210456/pump', $status, 0);
    }
}
