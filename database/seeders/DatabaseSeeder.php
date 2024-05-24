<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $testOrganizer = User::create([
            'name' => 'Organizer User',
            'email' => 'ivanjimenezl@outlook.com',
            'phone' => 8340928421,
            'password' => Hash::make('12345'),
            'type' => 'organizer',
        ]);

        $testOrganizer->createToken('authToken')->plainTextToken;

        $testVisitor = User::create([
            'name' => 'Visitor User',
            'email' => 'ivaneduardojimenezleon@gmail.com',
            'phone' => 7549273292,
            'password' => Hash::make('12345'),
            'type' => 'visitor',
        ]);

        $testVisitor->createToken('authToken')->plainTextToken;
    }
}
