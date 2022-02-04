<?php

namespace App\Http\Controllers\Api;
use App\Models\Post;

use App\Models\User;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Http\Controllers\Controller;
use App\Mail\MyTestMail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function comment (Request $request,$id)
    {
        $messages = [
            "comment.required" => "vous devez specifier votre commentaire",
           

        ];
        $data = $request->validate([
            'comment' => [
                'required',
                'string',
              
            ],
            
        ], $messages);

        $user_id = auth('api')->id();
       
        $post = Post::query()->findOrFail($id);
        $post_id=$post->id;
      
      
        $commentaire = CommentRepository::create($user_id, $post_id,$data['comment']);
        $user = auth('api')->user();
        Mail::to($user->email)->send(new MyTestMail($user->name,$data['comment']));


        return response()->json([
            'status' => 'success',
            'comment' => $commentaire
        ], 200);
    }


    public function show($id)
    {
               $comment = Comment::find($id);
               if (is_null($comment)) {
               //return $this->sendError('Post not found.');
               return response()->json([
                'status' => 'fail',
                'post' => $comment
            ], 404);
    }
          return response()->json([
           "success" => true,
           "message" => "comment retrieved successfully.",
           "data" => $comment
        ]);
    }


    public function deleteComment(Request $request)
{
   

    $user_id = auth('api')->id();
   
    
    $user = User::query()->findOrFail($user_id);
    $comment = $user->comments()->findOrFail($request->input('id'));

    
    
    
    $res = (new CommentRepository($comment))->delete();
    if ($res) {
        return response()->json(['status'=>"success"],200);
    } else {
        return response()->json(['status'=>"fail"],400);
    }
}





    
}
