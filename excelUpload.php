<?php
include 'db_config.php';
require 'vendor/autoload.php';
 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

// $inputFileType = 'Xls';
$inputFileType = 'Xlsx';
// $inputFileType = 'Xml';
// $inputFileType = 'Ods';
// $inputFileType = 'Slk';
// $inputFileType = 'Gnumeric';
// $inputFileType = 'Csv';

$uploadFilePath = 'uploads/'.basename($_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

$reader      = IOFactory::createReader($inputFileType);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($uploadFilePath);

$sheet = $spreadsheet->getActiveSheet();

// Store data from the activeSheet to the varibale in the form of Array
$data = $sheet->toArray(null, true, true, true);

// Display the sheet content
// echo json_encode($data);
// $nomor = 1;
echo '<a href="index.php">Back</a><br/>';
foreach($data as $row){
    $kode = $row["A"];
    $desc = $row["B"];

    if(($kode != null && $kode != "") && ($desc != null && $desc != "")){
        try {
            $sql = "INSERT INTO `am_kode`(`kod_kode`, `kod_desc`) VALUES ('$kode','$desc')";
            
            if($mysqli->query($sql)){
                echo $row["A"]." Inserted<br/>";
                // $nomor++;
            }

            if ($mysqli->errno) {
                throw new \Exception($mysqli->error);
            }
        } catch (\Throwable $th) {
            echo $row["A"]." Not Inserted. Problem : ".$th->getMessage()."<br/>";
        }
    }
}

?>