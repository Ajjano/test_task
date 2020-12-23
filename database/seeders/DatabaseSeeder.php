<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //create fake data
        //task 4
        Image::factory();
        User::factory(10)->hasPosts(3)->create();
        Comment::factory(10)->create();

    }
}
