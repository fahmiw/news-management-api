<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\News;

class NewsActivity
{
    use Dispatchable, SerializesModels;

    public $action;
    public $news;
    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($action, News $news, $user)
    {   
        $this->action = $action;
        $this->news = $news;
        $this->user = $user;
    }
}
