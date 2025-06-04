<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    /**
     * Génère un PDF à partir de contenu HTML et l'enregistre dans le dossier "pdfs".
     *
     * @param string $html Contenu HTML à convertir.
     * @param string $name Nom de la personne (utilisé pour nommer le fichier).
     * @param string $matiere Matière concernée (également utilisée dans le nom du fichier).
     * @return string Le contenu binaire du PDF généré.
     */
    public function generatePdf(string $html, string $name, string $matiere): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if (!is_dir('pdfs')) {
            mkdir('pdfs', 0777, true);
        }
        file_put_contents('pdfs/'. str_replace(' ', '_', $name) .'_'. str_replace(' ', '_', $matiere) .'_diplome.pdf', $dompdf->output());
        return $dompdf->output();
    }
}