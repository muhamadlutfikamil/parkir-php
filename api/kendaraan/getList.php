<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    $readData = file("../kendaraan.txt", FILE_IGNORE_NEW_LINES);
    $kendaraans_arr = array();
    $kendaraans_arr["result"] = array();

    if (count($readData) < 0) { 
        http_response_code(404);
        echo json_encode(
            array("message" => "No Kendaraan found.")
        );
    }

    $cnt = 1;

    foreach ($readData as $key => $val) {
        list($plat_nomor,  $warna,  $type, $parking_lot, $tanggal_masuk) = array_pad(explode("|---|", $val, 5), 5, null); 

        $kendaraan_item = array(
            "id" => $cnt,
            "plat_nomor" => $plat_nomor,
            "parking_lot" => $parking_lot,
            "tanggal_masuk" => $tanggal_masuk
        );
 
        array_push($kendaraans_arr["result"], $kendaraan_item); 
        
        $cnt++;
    }

    http_response_code(200);

    echo json_encode($kendaraans_arr);
?>
