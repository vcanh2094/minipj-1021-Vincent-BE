<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Văn Cảnh',
            'email' => 'vancanh.be@gmail.com',
            'password' => bcrypt('123123'),
            'phone' => '0963232864',
            'gender' => '1',
            'birthday' => '1994-06-20'
        ]);
    }
}
