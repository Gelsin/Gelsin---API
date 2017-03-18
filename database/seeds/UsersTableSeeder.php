<?php
use App\Gelsin\Models\User;
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 09/01/2017
 * Time: 01:02
 */
class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory(User::class)->create([
            'email' => 'admin@gelsin.az',
            'username' => 'admin',
            'is_customer' => 0,
            'password' => app('hash')->make('gelsin135')
        ]);

    }

}