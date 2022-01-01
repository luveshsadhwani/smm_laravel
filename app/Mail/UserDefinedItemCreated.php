<?php

namespace App\Mail;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserDefinedItemCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The item instance.
     *
     * @var \App\Models\Item
     */
    
    public $item;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        // make item available to blade template
        $this->item = $item;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('smm.aws01@gmail.com', 'SMM')
                    ->subject('New Item Added')
                    ->view('userDefinedItemCreate');
    }
}
