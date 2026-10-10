<?php
    Class Fungsi {
        function PdfGenerator($html, $filename){
            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream($filename, array('Attachment' => 0));
        }
    }

?>