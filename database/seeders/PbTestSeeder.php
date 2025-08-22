<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pbs;
use App\Models\User;

class PbTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user or create admin user
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'nik' => '12345'
            ]);
        }

        // Create sample PB data
        $pbData = [
            [
                'nomor_pb' => 'PB-2025-001',
                'tanggal' => '2025-01-15',
                'penginput' => 'John Doe',
                'nominal' => 5000000,
                'keterangan' => 'Pembelian peralatan kantor',
                'divisi' => 'E-CHANNEL',
                'status' => 'active',
                'user_id' => $user->id
            ],
            [
                'nomor_pb' => 'PB-2025-002',
                'tanggal' => '2025-01-16',
                'penginput' => 'Jane Smith',
                'nominal' => 3000000,
                'keterangan' => 'Biaya operasional',
                'divisi' => 'TREASURY OPERASIONAL',
                'status' => 'active',
                'user_id' => $user->id
            ],
            [
                'nomor_pb' => 'PB-2025-003',
                'tanggal' => '2025-01-17',
                'penginput' => 'Bob Wilson',
                'nominal' => 2500000,
                'keterangan' => 'Maintenance sistem',
                'divisi' => 'LAYANAN OPERASIONAL',
                'status' => 'cancelled',
                'user_id' => $user->id
            ],
            [
                'nomor_pb' => 'PB-2025-004',
                'tanggal' => '2025-01-18',
                'penginput' => 'Alice Brown',
                'nominal' => 4500000,
                'keterangan' => 'Konsultasi pajak',
                'divisi' => 'AKUNTANSI & TAX MANAGEMENT',
                'status' => 'active',
                'user_id' => $user->id
            ]
        ];

        foreach ($pbData as $data) {
            Pbs::create($data);
        }

        $this->command->info('Test PB data created successfully!');
    }
}
