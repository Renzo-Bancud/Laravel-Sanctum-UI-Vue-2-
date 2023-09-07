<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       User::create([
          'profile' => '/storage/profiles/user.png',
          'name' => 'Renzo Bancud',
          'email' => 'bancudzo3@gmail.com',
          'email_verified_at' => Carbon::now('Asia/Manila'),
          'password' => bcrypt('11111111'),
          'role' => 0,
          'created_at' => Carbon::now('Asia/Manila'),
          'updated_at' => Carbon::now('Asia/Manila'),
        ]);
    }
}
