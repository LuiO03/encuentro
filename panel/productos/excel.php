<?php
require '../../archives/vendor/autoload.php';
include("../bd.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$consulta = "
    SELECT 
        p.id_producto, 
        p.nombre_producto, 
        p.descripcion, 
        p.precio, 
        p.ingredientes, 
        p.disponible, 
        p.destacado, 
        p.fecha_creacion, 
        c.nombre_categoria 
    FROM productos p 
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
";

try {
    $stmt = $conect->prepare($consulta);
    $stmt->execute();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'REPORTE DE PRODUCTOS');

    $sheet->setCellValue('A2', 'ID');
    $sheet->setCellValue('B2', 'PRODUCTO');
    $sheet->setCellValue('C2', 'DESCRIPCIÓN');
    $sheet->setCellValue('D2', 'PRECIO');
    $sheet->setCellValue('E2', 'INGREDIENTES');
    $sheet->setCellValue('F2', 'DISPONIBLE');
    $sheet->setCellValue('G2', 'DESTACADO');
    $sheet->setCellValue('H2', 'CREADO');
    $sheet->setCellValue('I2', 'CATEGORÍA');

    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:I2')->applyFromArray($headerStyle);

    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('D')->setWidth(10);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(12);
    $sheet->getColumnDimension('G')->setWidth(12);
    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->getColumnDimension('I')->setWidth(20);

    $row = 3;
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sheet->setCellValue('A' . $row, $data['id_producto']);
        $sheet->setCellValue('B' . $row, $data['nombre_producto']);
        $sheet->setCellValue('C' . $row, $data['descripcion']);
        $sheet->setCellValue('D' . $row, $data['precio']);
        $sheet->setCellValue('E' . $row, $data['ingredientes']);
        $sheet->setCellValue('F' . $row, $data['disponible'] ? 'Sí' : 'No');
        $sheet->setCellValue('G' . $row, $data['destacado'] ? 'Sí' : 'No');
        $sheet->setCellValue('H' . $row, $data['fecha_creacion']);
        $sheet->setCellValue('I' . $row, $data['nombre_categoria'] ?? 'Sin categoría');
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $nombre_archivo = "Reporte_Productos.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$nombre_archivo\"");
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;

} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}
?>
