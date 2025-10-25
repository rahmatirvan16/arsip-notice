<?php
require 'db.php';

$sql = "SELECT id, nomor_notice as name, file_pdf FROM notices WHERE status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
$result = mysqli_query($conn, $sql);
$pdfs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pdfs[] = [
        'id' => 'notice_' . $row['id'],
        'name' => $row['name'],
        'type' => 'notice'
    ];
}

$sql_dokumen = "SELECT id, nama_dokumen as name, file_pdf FROM dokumen WHERE status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
$result_dokumen = mysqli_query($conn, $sql_dokumen);
while ($row = mysqli_fetch_assoc($result_dokumen)) {
    $pdfs[] = [
        'id' => 'dokumen_' . $row['id'],
        'name' => $row['name'],
        'type' => 'dokumen'
    ];
}

echo json_encode($pdfs);
