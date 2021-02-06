<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    $readData = file("../kendaraan.txt", FILE_IGNORE_NEW_LINES);
    
    $colorReq = isset($_GET['color']) ? $_GET['color'] : die();
    $kendaraans_arr = array();
    $kendaraans_arr["plat_nomor"] = array();

    if (count($readData) < 0) { 
        http_response_code(404);
        echo json_encode(
            array("message" => "No Kendaraan found.")
        );
    }

    $no = 0;

    foreach ($readData as $key => $val) {
        list($plat_nomor, $type, $color) = array_pad(explode("|---|", $val, 3), 3, null);

        if(strpos($color, $colorReq) !== false) {
            array_push($kendaraans_arr["plat_nomor"], $plat_nomor); 
        }
    }

    http_response_code(200);

    echo json_encode($kendaraans_arr);
?>
