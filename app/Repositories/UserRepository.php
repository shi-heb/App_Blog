<?php
namespace App\Repositories;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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




}
