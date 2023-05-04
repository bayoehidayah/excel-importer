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
$sql = "TRUNCATE transaksi";
$mysqli->query($sql);

$sql = "TRUNCATE produk";
$mysqli->query($sql);

echo json_encode(count($data));


function excelToDate($excel_date){
    // $unix_date = ($excel_date - 25569) * 86400;
    // $excel_date = 25569 + ($unix_date / 86400);
    // $unix_date = ($excel_date - 25569) * 86400;
    // return gmdate("Y-m-d", $unix_date);

    // $data = ($excel_date-25569)*86400;
    // return date("Y-m-d", strtotime($data));

    // $UNIX_DATE = ($excel_date - 25569) * 86400;
    // echo gmdate("Y-m-d", $UNIX_DATE);

    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excel_date);
    return $date->format("Y-m-d");
}

echo '<a href="index.php">Back</a><br/>';
$listBarang         = [];
$barangInserted     = [];

$currentDate        = null;
$currentTransaksiID = null;
$currentNumber      = null;

$oldNumber          = 0;
$nomor = 1;
$nomorData = 0;
// echo $data[1]["C"]."<br/>";
foreach($data as $row){
    $no            = $row["A"];
    $tgl           = $row["B"];
    $kodeTransaksi = $row["C"];
    $kodeBarang    = $row["D"];
    $namaBarang    = $row["E"];

    $listBarang[] = $namaBarang;

    if($no != null){
        $currentDate        = $tgl;
        $currentTransaksiID = $kodeTransaksi;
    }

    if($data[$nomor + 1]["A"] != null){
        $barangs     = implode(",", $listBarang);
        // $tglFormated = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($currentDate)->format("Y-m-d");
        $tglFormated = is_int($currentDate) ? excelToDate($currentDate) : excelToDate(\PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($currentDate));

        try {
            $sql = "INSERT INTO `transaksi`(`id`, `transaction_date`, `produk`) VALUES ('$currentTransaksiID','$tglFormated','$barangs')";
            
            if($mysqli->query($sql)){
                echo $currentTransaksiID." | Tanggal ".$tglFormated." | Barang : ".$barangs."<br/>";
                // $barangInserted[] = $kodeBarang;
                // $listBarang[]     = $namaBarang;
                // $nomor++;
                // $nomor = 0;
                // $listBarang = [];
                // $oldNumber = null;
            }

            if ($mysqli->errno) {
                throw new \Exception($mysqli->error);
            }
        } catch (\Throwable $th) {
            echo $currentTransaksiID." Not Inserted. Problem : ".$th->getMessage()."<br/>";
        }

        $listBarang = [];
    }

    $nomor++;
}

?>