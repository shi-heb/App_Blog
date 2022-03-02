<?php

namespace App\Repositories;
use Carbon\Carbon;

use App\Models\Post;
use App\Exceptions\ModelNotSavedException;

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


    public function update($title , $description , $source )
    {


        $this->post->title = $title;
        $this->post->description = $description;
        $this->post->source = $source;

        if (!$this->post->save()) {
            //  throw new ModelNotSavedException();


        }
        return $this->post;
    }

    public function delete()
    {

        return $this->post->delete();
    }

    public function getSortedPostsByDate()
    {
        return (Post::withCount('comments')
            ->groupBy('id')
            ->orderBy('comments_count', 'desc'));
    }
    public function SortByToday(){

        return ($this->getSortedPostsByDate()->whereDate('created_at',Carbon::today() ));
    }
    public function SortByYesterday(){
        return  ($this->getSortedPostsByDate()->whereDate('created_at', Carbon::yesterday()->toDateTimeString()));

    }
    public function SortBYCurrentWeek(){
        return    $this->getSortedPostsByDate()->whereBetween('created_at',[Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()]);

    }
    public function SortByLastWeek(){
        return   $this->getSortedPostsByDate()->whereBetween('created_at',[Carbon::now()->startOfWeek()->subDays(7)->toDateTimeString(), Carbon::now()->startOfWeek()->toDateTimeString()]);

    }
    public function SortByCurrentMonth(){
        return   $this->getSortedPostsByDate()->whereMonth('created_at',Carbon::now()->month);
    }
    public function SortByLastMonth(){
        return   $this->getSortedPostsByDate()->whereBetween('created_at',[Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);

    }
    public function SortByCurrentYear(){
        return   $this->getSortedPostsByDate()->whereBetween('created_at',[Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);

    }
    public function SortBylastYear(){
        return   $this->getSortedPostsByDate()->whereBetween('created_at',[Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()]);

    }

    public function filterByTime($type,$value)
    {
        switch([$type,$value]) {
            case[config('constants.PERIOD.DAY'),config('constants.LASTORCURRENT.CURRENT')]:
                return  (new PostRepository)->SortByToday();
                break;

            case[config('constants.PERIOD.DAY'),config('constants.LASTORCURRENT.PREVIOUS')]:
                return  (new PostRepository)->SortByYesterday();

                break;

            case[config('constants.PERIOD.WEEK'),config('constants.LASTORCURRENT.CURRENT')]:
                return  (new PostRepository)->SortBYCurrentWeek();
                break;
            case[config('constants.PERIOD.WEEK'),config('constants.LASTORCURRENT.PREVIOUS')]:
                return (new PostRepository)->SortByLastWeek();
                break;
            case[config('constants.PERIOD.MONTH'),config('constants.LASTORCURRENT.CURRENT')]:
                return   (new PostRepository)->SortByCurrentMonth();
                break;
            case[config('constants.PERIOD.MONTH'),config('constants.LASTORCURRENT.PREVIOUS')]:
                return  (new PostRepository)->SortByLastMonth();
                break;

            case[config('constants.PERIOD.YEAR'),config('constants.LASTORCURRENT.CURRENT')]:
                return   (new PostRepository)->SortByCurrentYear();
                break;

            case[config('constants.PERIOD.YEAR'),config('constants.LASTORCURRENT.PREVIOUS')]:
                return  (new PostRepository)->SortBylastYear();
                break;

            default:
                $msg ='there are no post.';
        }}

    public function filterPosts($start,$end)
    {
        $start_date = Carbon::parse($start)->startOfDay()->toDateTimeString();
        $end_date = Carbon::parse($end)->endOfDay()->toDateTimeString();

        $posts = Post::withCount('comments')
            ->whereBetween('posts.created_at', [$start_date, $end_date])
            ->groupBy('id')
            ->orderBy('comments_count', 'desc')
            ->get();

        return ($posts);
    }









}
