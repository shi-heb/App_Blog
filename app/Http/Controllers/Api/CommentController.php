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
    /**
     * @OA\Post(
     *      path="/api/posts/{post_id}/comment",
     *      operationId="Comment a Post",
     *      tags={"comment"},
     *      summary="Comment a  post",
     *      description="Return commented post",
     *       security={{ "apiAuth": {} }},
     *         @OA\Parameter(
     *          name="post_id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="put your comment",
     *       @OA\JsonContent(
     *       required={"comment"},
     *       @OA\Property(property="comment", type="string",  example="put your comment please!"),
     *      ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
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
        // Mail::to($user->email)->send(new MyTestMail($user->name,$data['comment']));


        return response()->json([
            'status' => 'success',
            'comment' => $commentaire
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/posts/comment/{id}",
     *      operationId="getCommentById",
     *      tags={"comment"},
     *      summary="Get comment text",
     *      description="Returns comment data",
     *      security={{ "apiAuth": {} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="comment id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
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

    /**
     * @OA\Delete(
     *      path="/api/posts/comment/?id={id}",
     *      operationId="deleteComment",
     *      tags={"comment"},
     *      summary="Delete existing post",
     *      description="Deletes a record and returns no content",
     *      security={{ "apiAuth": {} }},
     *    @OA\RequestBody(
     *    required=true,
     *    description="put fields related to a  post to update",
     *    @OA\JsonContent(
     *       required={"id"},
     *     @OA\Property(property="id", type="integer",  example="1"),
     *
     *    ),
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
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
