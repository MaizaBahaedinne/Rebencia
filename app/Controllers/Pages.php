<?php

namespace App\Controllers;

class Pages extends BaseController
{
    /**
     * Page À propos
     */
    public function about()
    {
        $data = [
            'title' => 'À propos de REBENCIA - Immobilier de prestige en Tunisie'
        ];

        return view('public/about', $data);
    }

    /**
     * Page Contact
     */
    public function contact()
    {
        $data = [
            'title' => 'Contactez-nous - REBENCIA'
        ];

        return view('public/contact', $data);
    }

    /**
     * Traitement du formulaire de contact
     */
    public function sendContact()
    {
        $validation = $this->validate([
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'permit_empty|max_length[20]',
            'subject' => 'required|min_length[5]|max_length[200]',
            'message' => 'required|min_length[10]|max_length[1000]'
        ]);

        if (!$validation) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Ici vous pouvez envoyer un email ou enregistrer dans la base de données
        // Pour l'instant, on retourne juste un message de succès
        
        return redirect()->back()
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
    }
}
