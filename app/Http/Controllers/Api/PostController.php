<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Http\Requests\User\PostUpdateRequest;
use Illuminate\Support\Facades\Validator;
use Dotenv\Exception\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;




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



public function destroy(Request $request)
{
    //$post = Post::query()->findOrFail($request->input('id'));

    $user_id = auth('api')->id();

    $user = User::query()->findOrFail($user_id);
    $post = $user->posts()->findOrFail($request->input('id'));



    $res = (new PostRepository($post))->delete();
    if ($res) {
        return response()->json(['status'=>"success"],200);
    } else {
        return response()->json(['status'=>"fail"],400);
    }
}





public function updatePost(Request $request)
{

    $validator= null;
    try{
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'source' => ['required', 'string'],
        ]);
        if ($validator->fails()){
            throw new ValidationException();
        }
    }catch (ValidationException $e){
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
    $new_post = (new PostRepository($post))->update($title,$description,$source);

    return response()->json(['status'=>'success', 'post'=>$new_post],200);
}




public function postGetAllComments($id)
    { $post = Post::query()->findOrFail($id);
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


    public function getTopPosts(Request $request)
    {
         $start_date =  Carbon::parse($request->start_date)->startOfDay()->toDateTimeString();
          $end_date = Carbon::parse($request->end_date)->endOfDay()->toDateTimeString();

          $posts = Post::withCount('comments')
          ->whereBetween('posts.created_at',[$start_date,$end_date])
          ->groupBy('id')
          ->orderBy('comments_count', 'desc')
          ->get();

          return($posts);
        }

        public function serachIntoComments(Request $request){
            $item=$request->text;

            $posts = Post::select(DB::raw('posts.*'))

            ->join('comments', 'posts.id', '=', 'comments.post_id')
             ->whereHas('comments')

            ->where('comment', 'like', $item.'%')

            ->groupby('id')
            ->get();
            return ($posts);
        }

        public function CommentedPosts(){

           $posts = Post::select(DB::raw('posts.*'))
             ->whereHas('comments')
             ->groupby('id')
            ->get();
            return ($posts);
        }


        public function postsByNumberOfComments(Request $request){
            $nb_comments=$request->nb;

            $posts = Post::whereHas('comments', function (Builder $query) {

            }, '>=',$nb_comments)->get();

            if($posts->count()==0){
                return response()->json([
                    "message"=>"no comments found for this post",
                ],404);
            }

            return response()->json([
                "success" => true,
                "message" => "posts retrieved successfully.",
                "data" => $posts
            ]);

         }



}
