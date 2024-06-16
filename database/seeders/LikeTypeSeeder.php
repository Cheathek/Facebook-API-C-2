<?php

namespace Database\Seeders;

use App\Models\LikeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run()
    {
        $likeTypes = [
            ['name' => 'like', 'icon' => '👍'],
            ['name' => 'love', 'icon' => '❤️'],
            ['name' => 'laugh', 'icon' => '😄'],
            ['name' => 'angry', 'icon' => '😡'],
            ['name' => 'sad', 'icon' => '😢'],
        ];

        foreach ($likeTypes as $likeType) {
            LikeType::create($likeType);
        }
    }
}
