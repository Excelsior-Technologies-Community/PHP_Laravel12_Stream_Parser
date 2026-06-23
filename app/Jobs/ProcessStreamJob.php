<?php

namespace App\Jobs;

use App\Models\UserData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStreamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        if (!file_exists($this->filePath)) {
            return;
        }

        $handle = fopen($this->filePath, 'r');
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $name  = isset($row[0]) ? trim($row[0]) : null;
            $email = isset($row[1]) ? trim($row[1]) : null;
            $age   = isset($row[2]) ? trim($row[2]) : null;

            if (empty($email)) {
                continue;
            }

            UserData::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name ?: 'N/A',
                    'age'  => is_numeric($age) ? (int) $age : null,
                ]
            );
        }

        fclose($handle);
        unlink($this->filePath);
    }
}