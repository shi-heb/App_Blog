<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Http\Requests\User\PostUpdateRequest;
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

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 * @Consumes( {MediaType.APPLICATION_JSON}),
 * @Produces ( {MediaType.APPLICATION_JSON}),
 */
class PostController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/posts/create",
     * summary="create post",
     * description="create a new post",
     * operationId="createPost",
     * tags={"post"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="put fields related to a new post",
     *    @OA\JsonContent(
     *       required={"titel,description","source"},
     *       @OA\Property(property="title", type="string",  example="here is a title"),
     *       @OA\Property(property="decription", type="string", example="this,id description "),
     *       @OA\Property(property="source", type="string",  example="sourcesource"),
     *
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry,  Please try again")
     *        )
     *     ),
     *       @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="Post", type="object"),
     *     )
     *  ),
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/posts/{id}",
     *      operationId="getPostById",
     *      tags={"post"},
     *      summary="Get post information",
     *      description="Returns post data",
     *     security={{ "apiAuth": {} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
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
            "message" => "Post retrieved successfully.",
            "data" => $post
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/posts/?id={id}",
     *      operationId="deletePost",
     *      tags={"post"},
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
    public function destroy(Request $request)
    {
        //$post = Post::query()->findOrFail($request->input('id'));

        $user_id = auth('api')->id();

        $user = User::query()->findOrFail($user_id);
        $post = $user->posts()->findOrFail($request->input('id'));


        $res = (new PostRepository($post))->delete();
        if ($res) {
            return response()->json(['status' => "success"], 200);
        } else {
            return response()->json(['status' => "fail"], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/posts/update",
     *      operationId="updatePost",
     *      tags={"post"},
     *      summary="Update existing post",
     *      description="Returns updated post data",
     *       security={{ "apiAuth": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          description="put fields related to a  post to update",
     *       @OA\JsonContent(
     *       required={"id,titel,description","source"},
     *       @OA\Property(property="id", type="integer",  example="here is an id"),
     *       @OA\Property(property="title", type="string",  example="here is a title"),
     *       @OA\Property(property="decription", type="string", example="this,id description "),
     *       @OA\Property(property="source", type="string",  example="sourcesource"),
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
    public function updatePost(Request $request)
    {

        $validator = null;
        try {
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
                'source' => ['required', 'string'],
            ]);
            if ($validator->fails()) {
                throw new ValidationException();
            }
        } catch (ValidationException $e) {
            return response()->json($validator->errors(), 422);
        }
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
     * return all comments related to all posts
     */
    public function postGetAllComments($id)
    {
        $post = Post::query()->findOrFail($id);
        $com = $post->comments()->get();

        if (is_null($com)) {
            //return $this->sendError('Post not found.');
            return response()->json([
                'status' => 'fail',
                'post' => $com
            ], 404);
        }
        return response()->json([
            "success" => true,
            "message" => "comments retrieved successfully.",
            "data" => $com
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/getTopPosts",
     *      operationId="getTopPosts",
     *      tags={"post"},
     *      summary="Get posts",
     *      description="Returns sorted posts",
     *          @OA\Parameter(
     *          name="start_date",
     *          description="top posts",
     *          in="query",
     *          @OA\Schema(
     *              type="date",
     *
     *          ),
     *
     *      ),
     *      @OA\Parameter(
     *          name="end_date",
     *          description="end date",
     *          in="query",
     *          @OA\Schema(
     *              type="date",
     *
     *          ),
     *
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
    public function getTopPosts(Request $request)
    {
        $topPosts = (new PostRepository)->filterPosts($request->get('start'),$request->get('end'));
        return response()->json([
            'status' => 'success',
            'topPosts ' => $topPosts
        ], 200);

    }

    /*
     * search comments that contain text given as parameter
     */
    public function serachIntoComments(Request $request)
    {
        $item = $request->text;

        $posts = Post::select(DB::raw('posts.*'))
            ->join('comments', 'posts.id', '=', 'comments.post_id')
            ->whereHas('comments')
            ->where('comment', 'like', $item . '%')
            ->groupby('id')
            ->get();
        return ($posts);
    }

    /*
     * return only posts who are commented
     */
    public function CommentedPosts()
    {

        $posts = Post::select(DB::raw('posts.*'))
            ->whereHas('comments')
            ->groupby('id')
            ->get();
        return ($posts);
    }

    /*
     * return posts who with such number of comments
     */
    public function postsByNumberOfComments(Request $request)
    {
        $nb_comments = $request->nb;

        $posts = Post::whereHas('comments', function (Builder $query) {

        }, '>=', $nb_comments)->get();

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
    /**
     * @OA\Get(
     *      path="/api/getTopPostsSorted",
     *      operationId="getTopPosts Sorted",
     *      tags={"post"},
     *      summary="Get posts by interval of date day,month,week ...",
     *      description="Returns sorted posts by day , week,month",
     *          @OA\Parameter(
     *          name="type",
     *          description="0:day , 1:week , 2:month",
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *       ),
     *      ),
     *          @OA\Parameter(
     *          name="value",
     *          description="0: current, 1:previous",
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *       ),
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
     *          response=404,
     *          description="not found"
     *      )
     * )
     */
    public function getTopPostsSorted(PostgetTopPostsSortedRequest  $request)
    {

        $topPost = (new PostRepository)->filterByTime($request->get('type'),$request->get('value'));
        return response()->json([
            'status' => 'success',
            'topPost ' => $topPost->get()->first()
        ], 200);




    }




}
