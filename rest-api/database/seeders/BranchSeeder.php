<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            // Central Region
            ['name' => 'Dhaka Central Branch', 'district' => 'Dhaka', 'region' => 'Central'],
            ['name' => 'Dhaka Gulshan Branch', 'district' => 'Dhaka', 'region' => 'Central'],
            ['name' => 'Dhaka Motijheel Branch', 'district' => 'Dhaka', 'region' => 'Central'],
            ['name' => 'Dhaka Dhanmondi Branch', 'district' => 'Dhaka', 'region' => 'Central'],
            ['name' => 'Gazipur Industrial Branch', 'district' => 'Gazipur', 'region' => 'Central'],
            ['name' => 'Narayanganj Textile Branch', 'district' => 'Narayanganj', 'region' => 'Central'],
            ['name' => 'Manikganj Rural Branch', 'district' => 'Manikganj', 'region' => 'Central'],

            // Eastern Region
            ['name' => 'Chittagong Port Branch', 'district' => 'Chittagong', 'region' => 'Eastern'],
            ['name' => 'Chittagong Steel Mill Branch', 'district' => 'Chittagong', 'region' => 'Eastern'],
            ['name' => 'Comilla Commercial Branch', 'district' => 'Comilla', 'region' => 'Eastern'],
            ['name' => 'Brahmanbaria Textile Branch', 'district' => 'Brahmanbaria', 'region' => 'Eastern'],
            ['name' => 'Noakhali Fisheries Branch', 'district' => 'Noakhali', 'region' => 'Eastern'],
            ['name' => 'Feni Small Business Branch', 'district' => 'Feni', 'region' => 'Eastern'],

            // North-Eastern Region
            ['name' => 'Sylhet Tea Garden Branch', 'district' => 'Sylhet', 'region' => 'North-Eastern'],
            ['name' => 'Sylhet Zindabazar Branch', 'district' => 'Sylhet', 'region' => 'North-Eastern'],
            ['name' => 'Habiganj Gas Field Branch', 'district' => 'Habiganj', 'region' => 'North-Eastern'],
            ['name' => 'Moulvibazar Tea Branch', 'district' => 'Moulvibazar', 'region' => 'North-Eastern'],
            ['name' => 'Sunamganj Haor Branch', 'district' => 'Sunamganj', 'region' => 'North-Eastern'],

            // Northern Region
            ['name' => 'Rajshahi University Branch', 'district' => 'Rajshahi', 'region' => 'Northern'],
            ['name' => 'Rangpur Agricultural Branch', 'district' => 'Rangpur', 'region' => 'Northern'],
            ['name' => 'Dinajpur Agricultural Branch', 'district' => 'Dinajpur', 'region' => 'Northern'],
            ['name' => 'Kurigram Border Branch', 'district' => 'Kurigram', 'region' => 'Northern'],
            ['name' => 'Mymensingh Rural Branch', 'district' => 'Mymensingh', 'region' => 'Northern'],
            ['name' => 'Bogra Agriculture Branch', 'district' => 'Bogra', 'region' => 'Northern'],
            ['name' => 'Pabna Handloom Branch', 'district' => 'Pabna', 'region' => 'Northern'],

            // South-Western Region
            ['name' => 'Khulna Industrial Branch', 'district' => 'Khulna', 'region' => 'South-Western'],
            ['name' => 'Jessore Border Branch', 'district' => 'Jessore', 'region' => 'South-Western'],
            ['name' => 'Kushtia Jute Branch', 'district' => 'Kushtia', 'region' => 'South-Western'],
            ['name' => 'Satkhira Shrimp Branch', 'district' => 'Satkhira', 'region' => 'South-Western'],
            ['name' => 'Narail Rice Branch', 'district' => 'Narail', 'region' => 'South-Western'],

            // Southern Region
            ['name' => 'Barisal River Branch', 'district' => 'Barisal', 'region' => 'Southern'],
            ['name' => 'Patuakhali Coastal Branch', 'district' => 'Patuakhali', 'region' => 'Southern'],
            ['name' => 'Pirojpur Coconut Branch', 'district' => 'Pirojpur', 'region' => 'Southern'],
            ['name' => 'Jhalokati Rural Branch', 'district' => 'Jhalokati', 'region' => 'Southern'],
            ['name' => 'Bhola Island Branch', 'district' => 'Bhola', 'region' => 'Southern']
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
