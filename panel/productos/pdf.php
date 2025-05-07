<?php

require('../../archives/fpdf/fpdf.php');
require("../bd.php");

class PDF extends FPDF

{
    function Header()
    {
        $this->Image('../../img/logos/logo_black_horizontal.png', 10, 6, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode('Lista de Productos'), 0, 1, 'C');
        $this->Ln(10);

        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(10, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Nombre', 1, 0, 'C', true);
        $this->Cell(45, 10, utf8_decode('Descripción'), 1, 0, 'C', true);
        $this->Cell(25, 10, 'Precio (S/)', 1, 0, 'C', true);
        $this->Cell(45, 10, utf8_decode('Ingredientes'), 1, 0, 'C', true);
        $this->Cell(20, 10, 'Disponible', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Destacado', 1, 0, 'C', true);
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

    $consulta = "SELECT id_producto, nombre_producto, descripcion, precio, ingredientes, disponible, destacado FROM productos";
    $resultado = $conect->query($consulta);

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 9);

    $widths = [10, 30, 45, 25, 45, 20, 20];

    while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
        $data = [
            $row['id_producto'],
            $row['nombre_producto'],
            $row['descripcion'],
            number_format($row['precio'], 2),
            $row['ingredientes'],
            $row['disponible'] ? 'Sí' : 'No',
            $row['destacado'] ? 'Sí' : 'No'
        ];
        $pdf->Row($data, $widths);
    }

    $nombreArchivo = "Productos.pdf";
    $pdf->Output('I', $nombreArchivo);
?>
