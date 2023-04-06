<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
class UserIsNewStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Is New Status Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }
    public function build()
    {
        $newUsers = User::where('is_new', 0)->get();
        
        return $this->view('emails.users')
                    ->with('newUsers', $newUsers);
    }
    
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
    public function handle()
{
    //  $twoDaysAgo = now()->subDays(2);
    // $newUsers = User::where('created_at', '>=', $twoDaysAgo)->get();
 $newUsers = User::where('created_at', '<',Carbon::now()->subMinutes(5))->get();
    foreach ($newUsers as $user) {
        $user->is_new = false;
        $user->save();
    }
    
    if ($newUsers->count() > 0) {
        $delay = now()->addMinutes(5); // Add a 5-minute delay
        Mail::to('hello@example.com')->later($delay, new UserIsNewStatusUpdated);
    }
}
}