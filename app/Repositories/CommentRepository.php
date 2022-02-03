<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Exceptions\ModelNotSavedException;

class CommentRepository
{
   
    protected $Comment;

    public function __construct(Comment $comment = null)
    {
        $this->comment = $comment;
    }

  
    public static function create($user_id, $post_id, $comment)
    {
        $commentt = new Comment();


        
        $commentt->user_id = $user_id;
        $commentt->post_id = $post_id;
        $commentt->comment = $comment;
       
       
        $commentt->saveOrFail();
        return $commentt;
    }


    public function delete()
    {
       
        return $this->comment->delete();
    }


}