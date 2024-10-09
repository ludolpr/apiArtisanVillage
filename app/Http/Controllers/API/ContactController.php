<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * @OA\Post(
     *     path="/contact",
     *     summary="Envoyer un e-mail de contact",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject","name","email","message","recipientEmail","recipientType"},
     *             @OA\Property(property="subject", type="string", example="Demande de renseignements"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="message", type="string", example="Bonjour, j'ai besoin d'informations sur..."),
     *             @OA\Property(property="recipientEmail", type="string", format="email", example="artisan@example.com"),
     *             @OA\Property(property="recipientType", type="string", example="artisan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email envoyé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation des données"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de l'envoi de l'e-mail"
     *     )
     * )
     */
    public function contactEmail(Request $request)
    {
        // Validation des données
        $request->validate([
            'subject' => 'required|string|max:255',
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|max:5000',
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

        try {
            // Utiliser l'adresse e-mail du destinataire spécifiée
            $recipientEmail = $request->input('recipientEmail');

            // Envoyer l'e-mail au destinataire
            Mail::to($recipientEmail)->send(new ContactMail($details));

            return response()->json(['message' => 'Email envoyé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'envoi de l\'e-mail'], 500);
        }
    }
}