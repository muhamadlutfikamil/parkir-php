<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $data = json_decode(file_get_contents("php://input"));

    if(empty($data->plat_nomor) || empty($data->warna) || empty($data->tipe)) {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create Kendaraan"));
        die();
    }

    $readDataParkiran = file("../slot.txt", FILE_IGNORE_NEW_LINES);
    $lot = "";
    $statusSlotA = 0;
    $kendaraans_arr = array();
    $idSlot = "";
    $addIsi = "";

    foreach ($readDataParkiran as $key => $val) {
        list($slot, $kapasitas, $isi) = array_pad(explode("|---|", $val, 3), 3, null); 
        $sisaSlot = (int) $kapasitas - (int) $isi;

        if($slot == "A1" && $sisaSlot > 0) {
            $lot = "A1";
            $statusSlotA = 1;
            $idSlot = $key;
            $addIsi = (int) $isi + 1;
        } else if($slot == "A2" && $sisaSlot > 0 && $statusSlotA = 0) {
            $lot = "A2";
            $idSlot = $key;
            $addIsi = (int) $isi + 1;
        } 
    }

    if(empty($slot)) {
        http_response_code(400);
        echo json_encode(array("message" => "Parkiran Penuh"));
        die();
    } 

    $isiString = (string) $addIsi;
    $readDataParkiran[$idSlot] = ($lot."|---|".$kapasitas."|---|".$isiString);
    $writeData = implode("\r\n", $readDataParkiran);
    $fileWrite = fopen('../slot.txt', 'w');
    fwrite($fileWrite, $writeData."\r\n"); 
    fclose($fileWrite);

    $plat_nomor = $data->plat_nomor;
    $warna = $data->warna;
    $tipe = $data->tipe;
    $tanggal_masuk = date('Y-m-d H:i:s');

    $fileName = "../kendaraan.txt";
    $data = fopen($fileName, "a");
    fwrite($data, $plat_nomor."|---|".$tipe."|---|".$warna."|---|".$lot."|---|".$tanggal_masuk."\r\n");
    fclose($data);

    $kendaraan_item = array(
        "plat_nomor" => $plat_nomor,
        "parking_lot" => $lot,
        "tanggal_masuk" => $tanggal_masuk
    );

    array_push($kendaraans_arr, $kendaraan_item); 
    http_response_code(201);

    echo json_encode($kendaraans_arr);
?>