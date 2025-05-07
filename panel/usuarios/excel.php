<?php
require '../../archives/vendor/autoload.php';
include("../bd.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$consulta = "
    SELECT 
        id_usuario, 
        nombre, 
        apellido, 
        dni, 
        correo, 
        telefono, 
        direccion, 
        rol, 
        creado_en 
    FROM usuarios 
    WHERE rol IN ('Administrador', 'Empleado')
";

try {
    $stmt = $conect->prepare($consulta);
    $stmt->execute();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'REPORTE DE USUARIOS (ADMINISTRADORES Y EMPLEADOS)');

    // Encabezados
    $sheet->setCellValue('A2', 'ID');
    $sheet->setCellValue('B2', 'NOMBRE');
    $sheet->setCellValue('C2', 'APELLIDO');
    $sheet->setCellValue('D2', 'DNI');
    $sheet->setCellValue('E2', 'CORREO');
    $sheet->setCellValue('F2', 'TELÉFONO');
    $sheet->setCellValue('G2', 'DIRECCIÓN');
    $sheet->setCellValue('H2', 'ROL');
    $sheet->setCellValue('I2', 'FECHA DE CREACIÓN');

    // Estilo
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:I2')->applyFromArray($headerStyle);

    // Ancho de columnas
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(30);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(20);

    // Llenado de datos
    $row = 3;
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sheet->setCellValue('A' . $row, $data['id_usuario']);
        $sheet->setCellValue('B' . $row, $data['nombre']);
        $sheet->setCellValue('C' . $row, $data['apellido']);
        $sheet->setCellValue('D' . $row, $data['dni']);
        $sheet->setCellValue('E' . $row, $data['correo']);
        $sheet->setCellValue('F' . $row, $data['telefono'] ?? 'N/A');
        $sheet->setCellValue('G' . $row, $data['direccion'] ?? 'N/A');
        $sheet->setCellValue('H' . $row, $data['rol']);
        $sheet->setCellValue('I' . $row, $data['creado_en']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $nombre_archivo = "Empleados.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$nombre_archivo\"");
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;

} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}
?>
