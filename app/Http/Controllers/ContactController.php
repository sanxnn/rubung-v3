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
        ], [
            'name.required' => __('storefront.validation_name'),
            'name.string' => __('storefront.validation_name'),
            'name.max' => __('storefront.validation_length'),
            'email.required' => __('storefront.validation_email'),
            'email.string' => __('storefront.validation_email'),
            'email.email' => __('storefront.validation_email'),
            'email.max' => __('storefront.validation_length'),
            'message.*' => __('storefront.validation_message'),
            'website.prohibited' => __('storefront.validation_request'),
        ]);

        $recipient = config('services.contact.email');
        if (! $recipient) {
            return back()->withErrors(['contact' => __('storefront.contact_unavailable')])->withInput();
        }

        try {
            Mail::to($recipient)->send(new ContactMessage($data));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim pesan kontak.', ['error' => $e->getMessage()]);

            return back()->withErrors(['contact' => __('storefront.contact_failed')])->withInput();
        }

        return back()->with('contact_success', __('storefront.contact_success'));
    }
}
