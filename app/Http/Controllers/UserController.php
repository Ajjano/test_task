<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserIdRequest;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // 6 - get all active users with their not deleted posts
    public function getActiveUsers(Request $request)
    {
        // 6.1 -  add optional post_limit in $request

        $users = null;
        //6.2 - returns count_of_comments
        //6.3 - order by count of comments for each user
        $users = User::select('id', 'name')->whereActive(true)->with('posts', function($q) {
            $q->select('id', 'author_id', 'content', 'image_id')->whereNull('deleted_at')
                ->with('image')->withCount('comments as count_of_comments')
                ->orderBy('count_of_comments', 'desc');
        })->limit($request->post_limit)->get();

        // delete unnesessary fields and rename image to image_url
        foreach ($users as &$user) {
            foreach ($user->posts as &$p) {
                unset($p->author_id);
                $p->image_url = $p->image['url'];
                unset($p->image);
                unset($p->image_id);
            }
        }

        return response()->json([
            'data' => $users
        ]);
    }

    //7 - returns all comments (with deleted) to according user_id order by desc
    public function getUsersComments(UserIdRequest $request,$user_id)
    {
        //7.1 - Query Builder and raw SQL.

        //7.2 -  eager load - add posts with image (where image_id is not null) within comment
        $comments = Comment::where('commentator_id', $user_id)->with('post.author')->whereHas('post', function($p){
            $p->whereNotNull('image_id');
        })->latest()->get();

        foreach ($comments as &$com){
            //7.2 - get image with lazy eager load
            $com->post->load('image');

            //unset unnesessary fields
            unset($com->post->author_id);

            //7.2.1 - set null if author is not active
            if($com->post->author->active===false){
                unset($com->post->author);
                $com->post->author=null;
            }
        }

        //Raw sql
        $commentsRaw = DB::table('comments')
            ->join('users', 'users.id', 'comments.commentator_id')
            ->join('posts', 'posts.id', 'comments.post_id')
            ->join('images', 'posts.image_id', 'images.id')
            ->select('comments.content', 'comments.created_at', 'posts.content as post_content', 'images.url as image' )
            ->whereNotNull('posts.image_id')
            ->where('comments.commentator_id', $user_id)
            ->orderByDesc('comments.created_at')
            ->get();

        return response()->json([
            'queryBuilder' => $comments,
            'rawQuery' => $commentsRaw
        ]);
    }

    public function startSeed()
    {
        Artisan::call('db:seed');
    }
}
