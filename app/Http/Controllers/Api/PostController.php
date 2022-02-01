<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Http\Requests\User\PostUpdateRequest;


use App\Models\Post;

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
           $post = Post::find($id);
           if (is_null($post)) {
           //return $this->sendError('Post not found.');
           return response()->json([
            'status' => 'fail',
            'post' => $post
        ], 404);
}
      return response()->json([
       "success" => true,
       "message" => "Product retrieved successfully.",
       "data" => $post
    ]);
}

public function destroy($id)
{
$post = Post::find($id);
$post->delete();
return response()->json([
"success" => true,
"message" => "Product deleted successfully.",
"data" => $post
]);
}

public function updatePost(PostUpdateRequest $request)
{
    
    $title = $request->input('title', null);
    $description  = $request->input('description', null);
    $source  = $request->input('source', null);
    $postRepository = new UserRepository($authUser);
    $postRepository->update($title, $description,$source);

    return response()->json([ 'data' => $authUser, ]);
}



}
