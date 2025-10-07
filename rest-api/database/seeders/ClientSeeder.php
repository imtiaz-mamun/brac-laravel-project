<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $branches = Branch::all();

        // Bangladeshi names
        $maleNames = [
            'Mohammad Rahman',
            'Abdul Karim',
            'Mizanur Rahman',
            'Aminul Islam',
            'Rafiqul Islam',
            'Shahidul Islam',
            'Abdur Rahman',
            'Motiur Rahman',
            'Nurul Islam',
            'Shamsul Haque',
            'Delwar Hossain',
            'Anwar Hossain',
            'Golam Mostafa',
            'Shah Alam',
            'Abu Bakkar'
        ];

        $femaleNames = [
            'Rashida Begum',
            'Fatima Khatun',
            'Salma Begum',
            'Rahima Khatun',
            'Nasreen Akter',
            'Parveen Akter',
            'Shahida Begum',
            'Kamrun Nahar',
            'Sufiya Khatun',
            'Razia Begum',
            'Monowara Begum',
            'Jahanara Begum',
            'Khaleda Khatun',
            'Rokeya Begum',
            'Amena Khatun'
        ];

        for ($i = 0; $i < 200; $i++) {
            $gender = $faker->randomElement(['MALE', 'FEMALE']);
            $name = $gender === 'MALE' ?
                $faker->randomElement($maleNames) :
                $faker->randomElement($femaleNames);

            // Create some clients with auth credentials (about 20% of clients)
            $hasAuth = $i < 40; // First 40 clients will have auth credentials

            Client::create([
                'name' => $name,
                'email' => $hasAuth ? strtolower(str_replace(' ', '.', $name)) . $i . '@microcredit.com' : null,
                'password' => $hasAuth ? bcrypt('password123') : null,
                'phone' => $hasAuth ? $faker->phoneNumber() : null,
                'gender' => $gender,
                'branch_id' => $branches->random()->id,
                'registration_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d')
            ]);
        }
    }
}
