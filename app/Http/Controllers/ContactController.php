<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'],
        ]);

        $recipient = config('services.contact.email');
        if (! $recipient) {
            return back()->withErrors(['contact' => 'Formulir kontak belum tersedia.'])->withInput();
        }

        try {
            Mail::to($recipient)->send(new ContactMessage($data));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim pesan kontak.', ['error' => $e->getMessage()]);

            return back()->withErrors(['contact' => 'Pesan belum terkirim. Silakan coba lagi.'])->withInput();
        }

        return back()->with('contact_success', 'Pesan berhasil dikirim.');
    }
}
