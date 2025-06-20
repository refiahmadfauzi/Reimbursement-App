<?php

namespace App\Mail;

use App\Models\Reimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewReimbursementNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $reimbursement;

    public function __construct(Reimbursement $reimbursement)
    {
        $this->reimbursement = $reimbursement;
    }

    public function build()
    {
        return $this->subject('Pengajuan Reimbursement Baru')
            ->markdown('emails.reimbursement')
            ->with([
                'reimbursement' => $this->reimbursement,
            ]);
    }
}
