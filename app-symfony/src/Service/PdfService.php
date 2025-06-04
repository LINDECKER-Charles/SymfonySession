<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
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