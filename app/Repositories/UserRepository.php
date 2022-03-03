<?php
namespace App\Repositories;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserRepository
{

    /**
     * @var User|null
     */
    protected $user;

    /**
     * UserRepository constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }


    public static function getUserByEmail($email)
    {
        return User::query()->where('email', '=', $email)->firstOrFail();
    }


    public static function alreadySigned($email)
    {
        $user = User::query()->where('email', '=', $email)->firstOrFail();
        if ($user) {
            $user->delete();
        }



    }



    public static function create($name,$email, $password)
    {

        $user = new User();
        $user->name = $name;
        $user->email = strtolower($email);
        $user->password = Hash::make($password);

        $user->saveOrFail();

        return $user;
    }



    public function update($email = null, $name = null)
    {
        if ($email) {
            $this->user->email = $email;
        }

        if ($name) {
            $this->user->name = $name;
        }

        $this->user->save();
    }


    public function sortUsers($start_date,$end_date){

        $authUserID = auth('api')->user()->id;
        $start =  Carbon::parse($start_date)->startOfDay()->toDateTimeString();
        $end = Carbon::parse($end_date)->endOfDay()->toDateTimeString();

        $users = User::withCount(['posts', 'comments'])
            // ->orderByRaw("CASE WHEN  users.id = $i THEN 1 Else 2 END")
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->join('comments', 'users.id', '=', 'comments.user_id')
            ->whereBetween('posts.created_at',[$start,$end])
            ->whereBetween('comments.created_at',[$start,$end])

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


        /*
         * you can work with a different methode to get sorted users list
         * by creating a list of users
         */
        foreach( $users as $user) {
            if ($user->id !==Auth::id()) {
                $sortedUsers->push($user);
            }
        }

        return ([$indexAuthUser,$ListOfSortedUsers]);
    }


    public function filterPosts($start,$end)
    {
        $start_date = Carbon::parse($start)->startOfDay()->toDateTimeString();
        $end_date = Carbon::parse($end)->endOfDay()->toDateTimeString();

        $posts = Post::select(DB::raw('posts.*, count(*) as total_posts'))
            ->join('users', 'posts.users_id', '=', 'users.id')
            ->whereBetween('posts.created_at',[$start_date,$end_date])
            ->groupBy('post_id')
            ->orderBy('total_posts', 'desc')
            ->get();

        return($posts);
    }

    public function getUsersList($start,$end)
    {
        $start_date =  Carbon::parse($start)->startOfDay()->toDateTimeString();
        $end_date = Carbon::parse($end)->endOfDay()->toDateTimeString();


        $users = User::select(DB::raw('users.*, count(*) as total_posts'))
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->whereBetween('posts.created_at',[$start_date,$end_date])
            ->groupBy('user_id')
            ->orderBy('total_posts', 'desc')


            ->get();

        return($users)->toQuery()->paginate(1);
    }






}
