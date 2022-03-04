<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Http\Requests\PostupdatePostRequest;
use App\Http\Requests\PostgetTopPostsRequest;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Dotenv\Exception\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\PostgetTopPostsSortedRequest;


use App\Models\Post;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class PostController extends Controller
{

    public function store(Request $request)
    {
        $messages = [
            "title.required" => "vous devez specifier un titre",
            "description.required" => "vous devez specifier une description",
            "source.required" => "vous devez specifier une source",

        ];
        $data = $request->validate([
            'title' => [
                'required',
                'string',

            ],
            'description' => [
                'required',
                'string',
                'min:3',
                'max:200'
            ],
            'source' => [
                'required',
                'string',
                'min:3',
                'max:500'
            ],
        ], $messages);

        $user_id = auth('api')->id();
        $post = PostRepository::create($user_id, $data['title'], $data['description'], $data['source']);

        return response()->json([
            'status' => 'success',
            'post' => $post
        ], 200);
    }


    public function show($id)
    {
        $post = Post::findOrFail($id);

        return response()->json([
            "success" => true,
            "message" => "Post retrieved successfully.",
            "data" => $post
        ]);
    }


    public function destroy(Request $request,$id_post)
    {
       /*
        * get the id of the current user
        */
        $user_id = auth('api')->id();

        $user = User::query()->findOrFail($user_id);
        $post = $user->posts()->findOrFail($id_post);


        $res = (new PostRepository($post))->delete();
        if ($res) {
            return response()->json(['status' => "success"], 200);
        } else {
            return response()->json(['status' => "fail"], 400);
        }
    }


    public function updatePost(PostupdatePostRequest $request)
    {


        $user_id = auth('api')->id();
        //$post = $user->posts->findOrFail($id);
        $user = User::query()->findOrFail($user_id);
        $post = $user->posts()->findOrFail($request->input('id'));

        // $post = Post::query()->findOrFail($request->input('id'));
        $title = $request->input('title');
        $description = $request->input('description');
        $source = $request->input('source');
        $new_post = (new PostRepository($post))->update($title, $description, $source);

        return response()->json(['status' => 'success', 'post' => $new_post], 200);
    }


    /*
     * return all comments related to a posts given by id
     */
    public function postGetAllComments($id)
    {
        $post = Post::query()->findOrFail($id);
        $commentaires = $post->comments()->get();

        return response()->json([
            "success" => true,
            "message" => "comments retrieved successfully.",
            "data" => $commentaires
        ]);
    }


    public function getTopPosts(PostgetTopPostsRequest $request)
    {
        $topPosts = (new PostRepository)->filterPosts($request->get('start'),$request->get('end'));
        return response()->json([
            'status' => 'success',
            'topPosts ' => $topPosts
        ], 200);

    }

    /*
     * search posts that contain text (part of text comment) given as parameter
     */
    public function serachIntoComments(Request $request)
    {
        $Posts = (new PostRepository)->serachIntoComments($request->get('text'));
        return response()->json([
            'status' => 'success',
            'Posts ' => $Posts
        ], 200);
    }

    /*
     * return only posts who are commented
     */
    public function CommentedPosts()
    {
        $Posts = (new PostRepository)->CommentedPosts();
        return response()->json([
            'status' => 'success',
            'Posts ' => $Posts
        ], 200);
    }


    /*
     * return posts who with such number of comments
     */
    public function postsByNumberOfComments(Request $request)
    {
        $posts = (new PostRepository)->postsByNumberOfComments($request->get('nb_comments'));

        if ($posts->count() == 0) {
            return response()->json([
                "message" => "no comments found for this post",
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "posts retrieved successfully.",
            "data" => $posts
        ]);

    }

    public function getTopPostsSorted(PostgetTopPostsSortedRequest  $request)
    {

        $topPost = (new PostRepository)->filterByTime($request->get('type'),$request->get('value'));
        return response()->json([
            'status' => 'success',
            'topPost ' => $topPost->get()->first()
        ], 200);




    }




}
