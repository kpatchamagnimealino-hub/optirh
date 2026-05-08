<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SignatureService;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    /**
     * Affiche la page de gestion de la signature du DG
     */
    public function show()
    {
        $hasSignature = SignatureService::hasSignature();
        $signatureUrl = SignatureService::getSignatureAsImage();

        return view('modules.opti-hr.pages.admin.signature', [
            'hasSignature' => $hasSignature,
            'signatureUrl' => $signatureUrl,
        ]);
    }

    /**
     * Télécharge et sauvegarde la signature du DG
     */
    public function upload(Request $request)
    {
        $request->validate([
            'signature' => 'required|file|mimes:png,jpg,jpeg,gif,svg|max:5120', // Max 5MB
        ], [
            'signature.required' => 'Veuillez sélectionner un fichier de signature',
            'signature.mimes' => 'Le fichier doit être une image (PNG, JPG, GIF ou SVG)',
            'signature.max' => 'La taille du fichier ne doit pas dépasser 5MB',
        ]);

        try {
            SignatureService::saveSignatureFromUpload($request->file('signature'));

            return redirect()->back()->with('success', 'Signature du DG mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du téléchargement: ' . $e->getMessage());
        }
    }

    /**
     * Supprime la signature du DG
     */
    public function delete()
    {
        SignatureService::deleteSignature();

        return redirect()->back()->with('success', 'Signature du DG supprimée');
    }
}
