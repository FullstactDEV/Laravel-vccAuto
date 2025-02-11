<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionMails extends Mailable
{
    use Queueable, SerializesModels;

    protected $parent_course_name;
    protected $child_course_name;
    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($parent_course_name, $child_course_name, $link)
    {
        //
        $this->parent_course_name = $parent_course_name;
        $this->child_course_name = $child_course_name;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('accupin@test.com')
                ->view('emails.subscription_mails')
                ->with([
                    'parent_course_name' => $this->parent_course_name,
                    'child_course_name' => $this->child_course_name,
                    'link' => $this->link
                ]);
    }
}
