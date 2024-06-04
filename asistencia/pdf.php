<?php
session_start();
require_once "../tcpdf/tcpdf.php";

if (isset($_SESSION['datos']) && isset($_SESSION['nombre_empleado'])) {
    $datos = $_SESSION['datos'];
    $nombre_empleado = $_SESSION['nombre_empleado'];
    // Crear formateador de fecha
    $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
    $formatter->setPattern('dd-MM-yyyy EEE'); // Patrón para mostrar la fecha y las tres primeras letras del día

    class MYPDF extends TCPDF {
        private $nombre_empleado;
        
        public function __construct($nombre_empleado) {
            parent::__construct();
            $this->nombre_empleado = $nombre_empleado;
        }

        // Header
        public function Header() {
            // Título
            $this->SetFont('times', 'B', 20);
            $this->SetXY(25, 14);  // Asegurarse de que el título no se superponga con el logo
            $this->Cell(0, 15, 'YOONGOS', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
            
            // Subtítulo
            $this->SetFont('times', 'B', 16);
            $this->SetXY(15, 30);  // Ajustar la posición del subtítulo
            $this->Cell(0, 15, 'Reporte de Asistencia de ' . $this->nombre_empleado, 0, 1, 'L', 0, '', 0, false, 'M', 'M');
        }

        // Footer
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('times', 'B', 20);
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    // create new PDF document
    $pdf = new MYPDF($nombre_empleado);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('YOONGO');
    $pdf->SetTitle('Reporte de Asistencia');
    $pdf->SetSubject('Reporte');
    $pdf->SetKeywords('TCPDF, PDF, reporte, asistencia, YOONGO');

    // set default header data
    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT); // Increase top margin to avoid overlap
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set font
    $pdf->SetFont('Helvetica', '', 12);

    // add a page
    $pdf->AddPage();

    // Start adding content after the initial HTML content
    $html = '<table border="1" cellspacing="3" cellpadding="4">
        <thead>
            <tr>
                <th><strong>Fecha</strong></th>
                <th><strong>Entrada</strong></th>
                <th><strong>Salida</strong></th>
            </tr>
        </thead>
        <tbody>';

    foreach ($datos as $dato) {
        // Convertir la fecha a español con las tres primeras letras del día
        $timestamp = strtotime($dato['fecha']);
        $fecha = $formatter->format($timestamp); // Obtener la fecha en formato español

        // Formatear la fecha con el día en español
        $fecha_formateada = str_replace(array("\r\n", "\r", "\n"), " ", $fecha);
        $fecha_formateada = str_replace(' ', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $fecha_formateada); // Añadir espacios adicionales entre fecha y día

        $html .= '<tr>
            <td>' . $fecha_formateada . '</td>
            <td>' . $dato['entrada'] . '</td>
            <td>' . $dato['salida'] . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';


    $pdf->writeHTML($html, true, false, true, false, '');

    //Salida del documento y su formato de descarga.
    $pdf->Output('Resumen_Pedido_'.date('d_m_y').'.pdf', 'I'); 
    ob_end_clean();
    exit;
} else {
    echo '<script>alert("No data available to generate the PDF.");</script>';
}
?>
