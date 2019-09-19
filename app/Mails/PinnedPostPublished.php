<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Post;

class PinnedPostPublished extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.pinnedPostPublished')
            ->subject($this->post->title)
            ->with([
                'post' => $this->post,
            ]);
    }
}
