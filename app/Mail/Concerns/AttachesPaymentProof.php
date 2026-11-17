<?php

namespace App\Mail\Concerns;

use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

trait AttachesPaymentProof
{
    /** @return array<int, Attachment> */
    protected function paymentProofAttachments(): array
    {
        $path = $this->order->payment_proof_path;

        if (! is_string($path) || $path === '' || ! Storage::disk('local')->exists($path)) {
            return [];
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'bin';

        return [
            Attachment::fromStorageDisk('local', $path)
                ->as('Zahlungsnachweis-'.$this->order->reference.'.'.$extension),
        ];
    }
}
