<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;



use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;


use App\Models\User;
use mysql_xdevapi\Table;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
          $start_date =  Carbon::parse($request->start_date)->startOfDay()->toDateTimeString();
          $end_date = Carbon::parse($request->end_date)->endOfDay()->toDateTimeString();


          $users = User::select(DB::raw('users.*, count(*) as total_posts'))
          ->join('posts', 'users.id', '=', 'posts.user_id')
          ->whereBetween('posts.created_at',[$start_date,$end_date])
          ->groupBy('user_id')
          ->orderBy('total_posts', 'desc')

          ->get();

    return($users);
    }



    public function getPosts(Request $request)
    {
          $start_date =  Carbon::parse($request->start_date)->startOfDay()->toDateTimeString();
          $end_date = Carbon::parse($request->end_date)->endOfDay()->toDateTimeString();


          $posts = Post::select(DB::raw('posts.*, count(*) as total_posts'))
          ->join('users', 'posts.users_id', '=', 'users.id')
          ->whereBetween('posts.created_at',[$start_date,$end_date])
          ->groupBy('post_id')
          ->orderBy('total_posts', 'desc')
          ->get();

    return($posts);
    }

    public function getUsersbyPostsAndComments(Request $request){

          $authUserID = auth('api')->user()->id;
          $start_date =  Carbon::parse($request->start_date)->startOfDay()->toDateTimeString();
          $end_date = Carbon::parse($request->end_date)->endOfDay()->toDateTimeString();

          $users = User::withCount(['posts', 'comments'])
              // ->orderByRaw("CASE WHEN  users.id = $i THEN 1 Else 2 END")
           ->join('posts', 'users.id', '=', 'posts.user_id')
           ->join('comments', 'users.id', '=', 'comments.user_id')
           ->whereBetween('posts.created_at',[$start_date,$end_date])
           ->whereBetween('comments.created_at',[$start_date,$end_date])

           ->groupBy ('users.id')

           ->orderBy ( DB::raw("`posts_count`+`comments_count`"), 'desc')
           ->get();
          $indexAuthUser = $users->search(function($user) {
                  return $user->id === Auth::id();
                                               });
                  $authUserID = auth('api')->user()->id;

                $ListOfSortedUsers=$users->toQuery()->orderByRaw("CASE WHEN  users.id = $authUserID  THEN 1 Else 2 END")->get();

                $sortedUsers = collect([$users[$indexAuthUser]]);
                //dd($sortedUsers);



            foreach( $users as $user) {
                  if ($user->id !==Auth::id()) {
                        $sortedUsers->push($user);
                  }
            }

                return ([$indexAuthUser,$ListOfSortedUsers]);
           }

           public function mapUsersWithPosts()
           {
              $users=DB::table('users')
                  ->orderby('created_at','desc')

                  ->get();
                  $collection=collect([$users]);

                  /* $keyed = $collection->mapWithKeys(function ($item, $key) {
                   return [$item['email'] => $item['name']];
               });*/
             // $sortedUsers=collect($users)->orderBy('created_at','desc');
             return ($users);


           }


           public function UserCommentOn()
           {
               $users=DB::table("users")
                   ->join("posts","users.id",'=','posts.user_id')
                   ->groupBy('users.id')
                   ->select('name as Mr.','posts.title','posts.created_at')
                   ->get();
               //$collection=collect([$users]);

               return ($users);


           }



















}
