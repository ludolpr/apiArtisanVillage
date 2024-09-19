<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contactEmail(Request $request)
    {
        // Validation des données
        $request->validate([
            'subject' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
            'recipientEmail' => 'required|email',
            'recipientType' => 'required|string'
        ]);

        $details = [
            'subject' => $request->input('subject'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
            'recipientType' => $request->input('recipientType')
        ];

        // Utiliser l'adresse e-mail du destinataire spécifiée
        $recipientEmail = $request->input('recipientEmail');

        // Envoyer l'e-mail au destinataire
        Mail::to($recipientEmail)->send(new ContactMail($details));

        return response()->json(['message' => 'Email envoyé avec succès']);
    }
}
