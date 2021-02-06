<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    $readData = file("../kendaraan.txt", FILE_IGNORE_NEW_LINES);

    $typeReq = isset($_GET['plat_nomor']) ? $_GET['plat_nomor'] : die();

    if (count($readData) < 0) { 
        http_response_code(404);
        echo json_encode(
            array("message" => "No Kendaraan found.")
        );
    }

    $deleteId = "";
    $arrOut = array();
    $tanggal_keluar = date('Y-m-d H:i:s');
    $tanggal_masuk = "";

    foreach ($readData as $key => $val) {
        list($plat_nomor_list,  $warna,  $type, $parking_lot, $tanggal_masuk_list) = array_pad(explode("|---|", $val, 5), 5, null);

        if(strpos($plat_nomor_list, $typeReq) === false) {
            $arrOut[] = $val;
        }

        if(strpos($plat_nomor_list, $typeReq) !== false) {
            $tanggal_masuk = $tanggal_masuk_list;
            $plat_nomor = $plat_nomor_list;
            $lot = $parking_lot;
        }
    }

    if (empty($tanggal_masuk)) { 
        http_response_code(404);
        echo json_encode(
            array("message" => "Plat Nomor Kendaraan Tidak ada.".$tanggal_masuk)
        );
    }

    $dateOne = DateTime::createFromFormat("Y-m-d H:i:s", $tanggal_keluar);
    $dateTwo = DateTime::createFromFormat("Y-m-d H:i:s", $tanggal_masuk);
    $interval = $dateOne->diff($dateTwo);

    $jumlah_bayar = 0 ; 

    if((int) $interval->format("%h ") > 0) {
        $jumlah_bayar += 25000;
    }

    if((int) $interval->format("%h ") > 1) {
        $jumlah_bayar += 5000 * ((int) $interval->format("%h ") - 1);
    }

    $strArr = implode("\n",$arrOut);
    $fp = fopen('../kendaraan.txt', 'w');
    
    if (count($readData) < 0) {
      fwrite($fp, $strArr."\r\n");
    } else {
      fwrite($fp, $strArr);   
    }

    fclose($fp);

    $readDataParkiran = file("../slot.txt", FILE_IGNORE_NEW_LINES);
    $kapasitas = 10;

    foreach ($readDataParkiran as $key => $val) {
        list($slot, $kapasitasList, $isi) = array_pad(explode("|---|", $val, 3), 3, null); 
        $sisaSlot = (int) $kapasitasList - (int) $isi;

        if(strpos($slot, $lot) !== false) {
            $idSlot = $key;
            $minIsi = (int) $isi - 1;
            $kapasitas = $kapasitasList;
        }

       
    }
    $isiString = (string) $minIsi;
    $readDataParkiran[$idSlot] = ($lot."|---|".$kapasitas."|---|".$isiString);
    $writeData = implode("\r\n", $readDataParkiran);
    $fileWrite = fopen('../slot.txt', 'w');
    fwrite($fileWrite, $writeData."\r\n"); 
    fclose($fileWrite);

    $kendaraans_arr = array();

    $kendaraan_item = array(
        "plat_nomor" => $plat_nomor,
        "parking_lot" => $lot,
        "tanggal_masuk" => $tanggal_masuk,
        "jumlah_bayar" => $jumlah_bayar
    );

    array_push($kendaraans_arr, $kendaraan_item); 
    http_response_code(201);

    echo json_encode($kendaraans_arr);
?>
