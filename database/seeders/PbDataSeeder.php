<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pbs;
use App\Models\PbCounter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PbDataSeeder extends Seeder
{
    public function run()
    {
        // Pastikan counter direset dan mulai dari 4514
        DB::statement('DELETE FROM pb_counters');

        // Data PB sesuai dengan gambar yang diberikan
        $pbData = [
            [
                'tanggal' => '2025-08-13',
                'penginput' => 'Faruq Kemal',
                'nominal' => 806281080.00,
                'keperluan' => 'telkompay',
                'divisi' => 'LAYANAN OPERASIONAL'
            ],
            [
                'tanggal' => '2025-08-13',
                'penginput' => 'M.Iqbal Fadhulurrahan',
                'nominal' => 10000000000.00,
                'keperluan' => 'MEMORANDUM',
                'divisi' => 'LAYANAN OPERASIONAL'
            ],
            [
                'tanggal' => '2025-08-13',
                'penginput' => 'M.Ikhsan Nugrad',
                'nominal' => 57407315.00,
                'keperluan' => 'Operasional',
                'divisi' => 'LAYANAN OPERASIONAL'
            ],
            [
                'tanggal' => '2025-08-13',
                'penginput' => 'M.Ikhsan Nugrad',
                'nominal' => 1070000.00,
                'keperluan' => 'Operasional',
                'divisi' => 'LAYANAN OPERASIONAL'
            ]
        ];

        // Get first user untuk user_id
        $user = User::first();
        if (!$user) {
            echo "Error: Tidak ada user di database. Buat user terlebih dahulu.\n";
            return;
        }

        foreach ($pbData as $data) {
            // Generate nomor PB menggunakan PbCounter
            $nomorPb = PbCounter::getNextNumber($data['tanggal']);

            // Buat PB dengan nomor yang benar
            $pb = Pbs::create([
                'nomor_pb' => $nomorPb,
                'tanggal' => Carbon::parse($data['tanggal']),
                'penginput' => $data['penginput'],
                'nominal' => $data['nominal'],
                'keperluan' => $data['keperluan'],
                'divisi' => $data['divisi'],
                'user_id' => $user->id,
                'status' => 'active',
                'input_date' => Carbon::today()
            ]);

            echo "✅ PB-{$pb->nomor_pb} berhasil dibuat: {$data['penginput']} - " . number_format($data['nominal'], 0, ',', '.') . "\n";
        }

        echo "\n🎉 Seeder berhasil! Total " . count($pbData) . " PB dibuat dengan nomor mulai dari 4515.\n";
    }
}
            $user = User::whereRaw('LOWER(name) = ?', [strtolower($data['penginput'])])->first();

            // Jika user tidak ditemukan, buat user baru
            if (!$user) {
                $user = User::create([
                    'name' => $data['penginput'],
                    'nik' => $this->generateNik(),
                    'role' => 'user',
                    'password' => bcrypt('password123') // default password
                ]);
            }

            // Buat data PB
            Pbs::create([
                'nomor_pb' => $data['nomor_pb'],
                'tanggal' => $data['tanggal'],
                'penginput' => $data['penginput'],
                'nominal' => $data['nominal'],
                'keterangan' => $data['keterangan'],
                'divisi' => $data['divisi'],
                'user_id' => $user->id,
                'status' => 'active',
                'no_pb' => $data['nomor_pb'],
                'keperluan' => $data['keterangan'],
                'input_date' => $data['tanggal']
            ]);
        }

        echo "Data PB berhasil ditambahkan!\n";
    }

    private function generateNik()
    {
        // Generate NIK random 16 digit
        do {
            $nik = '';
            for ($i = 0; $i < 16; $i++) {
                $nik .= rand(0, 9);
            }
        } while (User::where('nik', $nik)->exists());

        return $nik;
    }
}
