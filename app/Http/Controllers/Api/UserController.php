<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\UsergetTheMoreActifUsersRequest;




use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;


use App\Models\User;
//use mysql_xdevapi\Table;

/**
 * @OA\Info(
 *     title="My the best API",
 *     version="1.0.0")
 */
class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        $topPosts = (new UserRepository())->getUsersList($request->get('start'),$request->get('end'));
        return response()->json([
            'status' => 'success',
            'topPosts ' => $topPosts
        ], 200);
    }


    /*
     * return all posts posted between two dates gives a parameter
     */
    public function getPosts(Request $request)
    {
        $topPosts = (new UserRepository())->filterPosts($request->get('start'),$request->get('end'));
        return response()->json([
            'status' => 'success',
            'topPosts ' => $topPosts
        ], 200);
    }

    /*
     * get all users sorted with their activities between a range of date
     * the return contain index of the connected user with a list of sorted users
     */
    public function getTheMoreActifUsers(UsergetTheMoreActifUsersRequest $request){

        $topUsers = (new UserRepository())->sortUsers($request->get('start_date'),$request->get('end_date'));
        return response()->json([
            'status' => 'success',
            'topUsers' => $topUsers
        ], 200);



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

    /*
     * map all user with posts who comments
     */
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
