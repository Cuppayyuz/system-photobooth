<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class KirimFotoTefa extends Mailable
{
    use Queueable, SerializesModels;

    public $namaPelanggan;
    public $pathFoto;

    public function __construct($namaPelanggan, $pathFoto)
    {
        $this->namaPelanggan = $namaPelanggan;
        $this->pathFoto = $pathFoto;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[CETAK] Foto Photobooth - " . $this->namaPelanggan,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kirim_foto', // Kita akan buat view-nya nanti
            with: [
            'namaPelanggan' => $this->namaPelanggan,
        ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pathFoto),
        ];
    }
}