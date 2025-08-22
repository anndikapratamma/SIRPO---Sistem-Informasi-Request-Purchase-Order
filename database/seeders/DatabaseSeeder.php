<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pbs;
use App\Models\Template;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if not exists
        if (!User::where('nik', 'admin')->exists()) {
            User::create([
                'name' => 'Administrator',
                'nik' => 'admin',
                'email' => 'admin@sirpo.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'divisi' => 'IT'
            ]);
        }

        // Create sample users
        $users = [
            [
                'name' => 'John Doe',
                'nik' => '12345',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'divisi' => 'OP'
            ],
            [
                'name' => 'Jane Smith',
                'nik' => '67890',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'divisi' => 'AKT'
            ]
        ];

        foreach ($users as $userData) {
            if (!User::where('nik', $userData['nik'])->exists()) {
                User::create($userData);
            }
        }

        // Create sample PBs
        $adminUser = User::where('nik', 'admin')->first();
        $john = User::where('nik', '12345')->first();
        $jane = User::where('nik', '67890')->first();

        $pbsData = [
            [
                'nomor_pb' => 'PB-001-2025',
                'no_pb' => 'PB-001',
                'tanggal' => now(),
                'penginput' => $john->name,
                'nominal' => 1500000,
                'keterangan' => 'Pembelian peralatan kantor',
                'keperluan' => 'Printer, kertas, tinta untuk operasional divisi OP',
                'divisi' => 'OP',
                'user_id' => $john->id,
                'status' => 'pending'
            ],
            [
                'nomor_pb' => 'PB-002-2025',
                'no_pb' => 'PB-002',
                'tanggal' => now()->subDays(1),
                'penginput' => $jane->name,
                'nominal' => 2000000,
                'keterangan' => 'Software akuntansi',
                'keperluan' => 'Lisensi software akuntansi untuk 1 tahun',
                'divisi' => 'AKT',
                'user_id' => $jane->id,
                'status' => 'approved'
            ]
        ];

        foreach ($pbsData as $pbData) {
            if (!Pbs::where('nomor_pb', $pbData['nomor_pb'])->exists()) {
                Pbs::create($pbData);
            }
        }

        // Create sample templates
        $templatesData = [
            [
                'name' => 'Template PB Peralatan Kantor',
                'description' => 'Template untuk permintaan pembelian peralatan kantor',
                'file_path' => 'templates/pb-peralatan-kantor.docx',
                'is_active' => true,
                'created_by' => $adminUser->id
            ],
            [
                'name' => 'Template PB Software',
                'description' => 'Template untuk permintaan pembelian software dan lisensi',
                'file_path' => 'templates/pb-software.docx',
                'is_active' => true,
                'created_by' => $adminUser->id
            ]
        ];

        foreach ($templatesData as $templateData) {
            if (!Template::where('name', $templateData['name'])->exists()) {
                Template::create($templateData);
            }
        }
    }
}
