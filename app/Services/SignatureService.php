<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SignatureService
{
    /**
     * Récupère la signature du DG en tant qu'image
     */
    public static function getSignatureAsImage()
    {
        // Chercher d'abord une image PNG
        $pngPath = public_path('assets/img/signature-dg.png');
        if (file_exists($pngPath)) {
            return asset('assets/img/signature-dg.png');
        }

        // Chercher une image JPG
        $jpgPath = public_path('assets/img/signature-dg.jpg');
        if (file_exists($jpgPath)) {
            return asset('assets/img/signature-dg.jpg');
        }

        // Chercher une image SVG
        $svgPath = public_path('assets/img/signature-dg.svg');
        if (file_exists($svgPath)) {
            return asset('assets/img/signature-dg.svg');
        }

        return null;
    }

    /**
     * Vérifie si une signature existe
     */
    public static function hasSignature()
    {
        return self::getSignatureAsImage() !== null;
    }

    /**
     * Récupère le chemin complet de la signature
     */
    public static function getSignaturePath()
    {
        $formats = ['png', 'jpg', 'jpeg', 'gif', 'svg'];

        foreach ($formats as $format) {
            $path = public_path("assets/img/signature-dg.{$format}");
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Récupère la signature en base64
     */
    public static function getSignatureBase64()
    {
        $path = self::getSignaturePath();

        if (!$path) {
            return null;
        }

        $mimeType = mime_content_type($path);
        $fileContent = file_get_contents($path);

        return "data:{$mimeType};base64," . base64_encode($fileContent);
    }

    /**
     * Supprime la signature existante
     */
    public static function deleteSignature()
    {
        $path = self::getSignaturePath();

        if ($path && file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }

    /**
     * Sauvegarde une signature à partir d'un fichier téléchargé
     */
    public static function saveSignatureFromUpload($file)
    {
        // Valider le type de fichier
        $allowedMimes = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Type de fichier non autorisé. Utilisez PNG, JPG, GIF ou SVG.');
        }

        // Supprimer l'ancienne signature
        self::deleteSignature();

        // Déterminer l'extension
        $extension = $file->getClientOriginalExtension();
        $filename = "signature-dg.{$extension}";

        // Sauvegarder le fichier
        $destinationPath = public_path('assets/img');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return true;
