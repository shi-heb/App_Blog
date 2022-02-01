<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    /**
     * @var Reclamation|null
     */
    protected $Post;

    /**
     * ReclamationRepository constructor.
     * @param Reclamation|null $reclamation
     */
    public function __construct(Post $post = null)
    {
        $this->post = $post;
    }

    /**
     * @param $type
     * @param $subject
     * @param $message
     * @return Reclamation
     * @throws \Throwable
     */
    public static function create($user_id, $title, $description, $source)
    {
        $post = new Post();


        $post->title = $title;
        $post->description = $description;
        $post->source = $source;
        $post->user_id = $user_id;
        $post->saveOrFail();
        return $post;
    }


    public function update($title = null, $description = null, $source = null)
    {
        if ($title) {
            $this->post->title = $title;
        }

        if ($description) {
            $this->post->description = $description;
        }

        if ($source) {
            $this->post->source = $source;
        }

          $this->user->save();
    }


}
