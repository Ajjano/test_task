<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $posts = Post::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        $date=array(null, Carbon::now());
        return [
            'content' => $this->faker->text,
            'post_id' => $posts[array_rand($posts)],
            'commentator_id' => $users[array_rand($users)],
            'deleted_at' => $date[array_rand($date)],
        ];
    }
}
