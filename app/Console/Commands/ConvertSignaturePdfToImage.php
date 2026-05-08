<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertSignaturePdfToImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signature:convert-pdf-to-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convertit le PDF de signature du DG en image PNG pour l\'intégration dans les documents PDF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pdfPath = public_path('assets/img/signature-dg.pdf');
        $imagePath = public_path('assets/img/signature-dg.png');

        if (!file_exists($pdfPath)) {
            $this->error("Le fichier PDF de signature n'existe pas: {$pdfPath}");
            return 1;
        }

        // Tentative 1: Utiliser ImageMagick (convert)
        if ($this->tryImageMagick($pdfPath, $imagePath)) {
            $this->info("✓ Conversion réussie avec ImageMagick");
            return 0;
        }

        // Tentative 2: Utiliser Poppler (pdftoppm)
        if ($this->tryPoppler($pdfPath, $imagePath)) {
            $this->info("✓ Conversion réussie avec Poppler");
            return 0;
        }

        // Tentative 3: Utiliser Ghostscript
        if ($this->tryGhostscript($pdfPath, $imagePath)) {
            $this->info("✓ Conversion réussie avec Ghostscript");
            return 0;
        }

        // Tentative 4: Utiliser LibreOffice
        if ($this->tryLibreOffice($pdfPath, $imagePath)) {
            $this->info("✓ Conversion réussie avec LibreOffice");
            return 0;
        }

        $this->error("Impossible de convertir le PDF. Aucun outil de conversion trouvé.");
        $this->info("Pour résoudre cela, installez l'un des outils suivants:");
        $this->line("  - ImageMagick (convert)");
        $this->line("  - Poppler (pdftoppm)");
        $this->line("  - Ghostscript (gswin64c)");
        $this->line("  - LibreOffice (soffice)");

        return 1;
    }

    /**
     * Convertir avec ImageMagick
     */
    private function tryImageMagick($pdfPath, $imagePath)
    {
        // Utiliser l'indice [0] pour la première page du PDF
        $command = "convert \"" . str_replace("\\", "/", $pdfPath) . "[0]\" -density 150 -quality 85 \"" . str_replace("\\", "/", $imagePath) . "\"";

        $output = null;
        $returnVar = null;
        exec($command . " 2>&1", $output, $returnVar);

        if (!file_exists($imagePath)) {
            // Essayer sans l'indice
            $command = "convert \"" . str_replace("\\", "/", $pdfPath) . "\" -density 150 -quality 85 \"" . str_replace("\\", "/", $imagePath) . "\"";
            exec($command . " 2>&1", $output, $returnVar);
        }

        return file_exists($imagePath);
    }

    /**
     * Convertir avec Poppler
     */
    private function tryPoppler($pdfPath, $imagePath)
    {
        $imagePathWithoutExt = substr($imagePath, 0, -4); // Enlever .png
        $command = "pdftoppm -png -r 150 \"{$pdfPath}\" \"{$imagePathWithoutExt}\"";

        exec($command);

        // pdftoppm ajoute -1 au nom du fichier pour la première page
        $pdfpmOutput = $imagePathWithoutExt . "-1.png";

        if (file_exists($pdfpmOutput)) {
            rename($pdfpmOutput, $imagePath);
            return true;
        }

        return false;
    }

    /**
     * Convertir avec Ghostscript
     */
    private function tryGhostscript($pdfPath, $imagePath)
    {
        $command = "gswin64c -sDEVICE=pngalpha -o \"{$imagePath}\" -r150 \"{$pdfPath}\"";

        exec($command);

        return file_exists($imagePath);
    }

    /**
     * Convertir avec LibreOffice
     */
    private function tryLibreOffice($pdfPath, $imagePath)
    {
        $outputDir = dirname($imagePath);
        $command = "soffice --headless --convert-to png:writer_pdf_Export \"{$pdfPath}\" --outdir \"{$outputDir}\"";

        exec($command);

        $libreOfficeOutput = substr($imagePath, 0, -4) . ".png";

        if (file_exists($libreOfficeOutput)) {
            return true;
        }

        return false;
    }
}
