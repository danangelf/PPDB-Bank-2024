<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => '9104e031-2976-4d68-99f3-1d57214beb18',
                'name' => 'John Doe 2',
                'username' => 'admin',
                'email_verified_at' => NULL,
                'password' => '$2y$10$KooePhrgadbrndLkZxe83enINF3GMO88kISE0RolcoU5A/J7RlQMS',
                'current_role' => 'bcd0b4ce-2cf8-4fc4-af88-4573c59a2377',
                'is_active' => true,
                'remember_token' => NULL,
                'created_at' => '2022-02-11 03:54:42',
                'updated_at' => '2022-03-24 12:59:18',
            ),
            1 => 
            array (
                'id' => '296c478c-3e40-4d05-83f5-a1e97e92aaf5',
                'name' => 'Ronny Priatama',
                'username' => 'superadmin',
                'email_verified_at' => '2022-02-01 02:10:52',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'current_role' => 'e4510500-70b4-44a9-968c-3e66fecc6fb9',
                'is_active' => true,
                'remember_token' => 'JdcVhnjmXoM1C8aCOFTcarcJRS89k289gigDowWKkdAVqnr3gH5jOseaTBmT',
                'created_at' => '2022-02-01 02:10:52',
                'updated_at' => '2023-10-23 04:14:38',
            ),
        ));
        
        
    }
}