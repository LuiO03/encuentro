<?php

require('../../archives/fpdf/fpdf.php');
require("../bd.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../../img/logos/logo_black_horizontal.png', 10, 6, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode('Lista de Usuarios'), 0, 1, 'C');
        $this->Ln(10);

        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(10, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('Nombre'), 1, 0, 'C', true);
        $this->Cell(20, 10, 'Apellido', 1, 0, 'C', true);
        $this->Cell(20, 10, 'DNI', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Correo', 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('Teléfono'), 1, 0, 'C', true);
        $this->Cell(40, 10, utf8_decode('Dirección'), 1, 0, 'C', true);
        $this->Cell(20, 10, 'Rol', 1, 0, 'C', true);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Row($data, $widths)
    {
        $maxHeight = 0;
        $heights = [];
        for ($i = 0; $i < count($data); $i++) {
            $heights[$i] = $this->NbLines($widths[$i], $data[$i]);
            $maxHeight = max($maxHeight, $heights[$i]);
        }

        $rowHeight = 6 * $maxHeight;
        $this->CheckPageBreak($rowHeight);

        for ($i = 0; $i < count($data); $i++) {
            $w = $widths[$i];
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $rowHeight);
            $this->MultiCell($w, 6, utf8_decode($data[$i]), 0, 'C');
            $this->SetXY($x + $w, $y);
        }

        $this->Ln($rowHeight);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c] ?? 500; // fallback por seguridad
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
}

// Obtener los usuarios con rol 'Administrador' o 'Empleado'
$consulta = "SELECT id_usuario, nombre, apellido, dni, correo, telefono, direccion, rol 
             FROM usuarios 
             WHERE rol IN ('Administrador', 'Empleado')";
$resultado = $conect->query($consulta);

// Inicializar PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
$pdf->SetAutoPageBreak(true, 15); // Ajustar la ruptura de página automáticamente

// Definir los anchos de las columnas
$widths = [10, 20, 20, 20, 40, 20, 40, 20];

// Iterar a través de los datos de los usuarios
while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
    $data = [
        $row['id_usuario'],
        utf8_decode($row['nombre']),
        utf8_decode($row['apellido']),
        utf8_decode($row['dni']),
        utf8_decode($row['correo']),
        utf8_decode($row['telefono']) ?? 'N/A',
        utf8_decode($row['direccion']),
        utf8_decode($row['rol'])
    ];
    $pdf->Row($data, $widths);
}

$nombreArchivo = "Usuarios.pdf";
$pdf->Output('I', $nombreArchivo);

?>
