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
            'email' => 'alirzayev.07@gmail.com',
            'username' => 'alirzayev',
            'password' => app('hash')->make('Alirzayev57')
        ]);

        factory(User::class)->create([
            'email' => 'admin@gelsin.com',
            'username' => 'admin',
            'password' => app('hash')->make('admin1234')
        ]);

        factory(User::class)->create([
            'email' => 'orik@gelsin.com',
            'username' => 'orik',
            'password' => app('hash')->make('admin1234')
        ]);

    }

}