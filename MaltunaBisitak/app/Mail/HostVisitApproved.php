<?php

namespace App\Mail;

use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the teacher who hosts an approved visit who is coming and when, with a link
 * to add the visit to their Google Calendar.
 */
class HostVisitApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Visit $visit) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bisita bat duzu: {$this->visit->origin} ({$this->visit->visit_date->format('Y/m/d')})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.host-visit-approved',
            with: [
                'mode' => $this->mode(),
                'visitorNames' => $this->visitorNames(),
                'calendarUrl' => $this->googleCalendarUrl(),
            ],
        );
    }

    /**
     * A Google Calendar "add event" link for the whole day of the visit, since visits have no time.
     */
    public function googleCalendarUrl(): string
    {
        $details = collect([
            'Mota: '.($this->visit->typeNames('eu') ?: '—'),
            'Modua: '.$this->mode(),
            'Bisitariak: '.($this->visitorNames() ?: '—'),
            $this->visit->response_message ? 'Oharrak: '.$this->visit->response_message : null,
        ])->filter()->join("\n");

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => "Bisita: {$this->visit->origin}",
            'dates' => $this->visit->visit_date->format('Ymd').'/'.$this->visit->visit_date->copy()->addDay()->format('Ymd'),
            'details' => $details,
            'ctz' => config('app.timezone'),
        ], encoding_type: PHP_QUERY_RFC3986);
    }

    private function mode(): string
    {
        return $this->visit->is_group ? "Taldea ({$this->visit->group_size} pertsona)" : 'Bakarka';
    }

    private function visitorNames(): string
    {
        return $this->visit->visitors->map(fn (Visitor $visitor) => $visitor->full_name)->join(', ');
    }
}
