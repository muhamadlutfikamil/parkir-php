<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    $readData = file("../kendaraan.txt", FILE_IGNORE_NEW_LINES);

    $typeReq = isset($_GET['type']) ? $_GET['type'] : die();

    if (count($readData) < 0) { 
        http_response_code(404);
        echo json_encode(
            array("message" => "No Kendaraan found.")
        );
    }

    $no = 0;

    foreach ($readData as $key => $val) {
        list($plat_nomor, $type) = array_pad(explode("|---|", $val, 2), 2, null);

        if(strpos($type, $typeReq) !== false) {
            $no = $no + 1;
        }
    }

    http_response_code(200);

    $jumlahkendaraan = array( "jumlah_kendaraan" => $no);

    echo json_encode($jumlahkendaraan);
?>
