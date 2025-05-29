<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hewan;
use App\Models\User;

class HewanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('role', ['user', 'shelter'])
            ->inRandomOrder()
            ->take(2)
            ->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users with role user or shelter found, skipping hewan seeding.');
            return;
        }

        $hewanData = [
            [
                'image' => 'hewan_images/kucing.jpg',
                'nama' => 'Bubu',
                'jenis_kelamin' => 'jantan',
                'warna' => 'Oren',
                'jenis_hewan' => 'kucing',
                'umur' => 2,
                'status' => 'tersedia',
                'deskripsi' => 'Kucing lucu dan aktif, sangat cocok untuk keluarga.'
            ],
            [
                'image' => 'hewan_images/anjing.jpg',
                'nama' => 'Rocky',
                'jenis_kelamin' => 'betina',
                'warna' => 'Coklat',
                'jenis_hewan' => 'anjing',
                'umur' => 4,
                'status' => 'tersedia',
                'deskripsi' => 'Anjing setia dan pintar, siap diadopsi.'
            ]
        ];

        foreach ($users as $index => $user) {
            if (isset($hewanData[$index])) {
                Hewan::create(array_merge(
                    ['user_id' => $user->id],
                    $hewanData[$index]
                ));
            }
        }
    }

}
