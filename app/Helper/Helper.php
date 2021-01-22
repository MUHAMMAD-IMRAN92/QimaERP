<?php

use Illuminate\Support\Facades\Response;

function timeago($ptime) {
    $difference = time() - strtotime($ptime);
    if ($difference) {
        $periods = array("sec", "min", "hr", "day", "week", "month", "years", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
        for ($j = 0; $difference >= $lengths[$j]; $j++)
            $difference /= $lengths[$j];

        $difference = round($difference);
        if ($difference != 1)
            $periods[$j] .= "s";

        $text = "$difference $periods[$j]";


        return $text;
    } else {
        return 'Just Now';
    }
}

function sendSuccess($message, $data) {
    return Response::json(array('status' => 'success', 'message' => $message, 'data' => $data), 200, [], JSON_NUMERIC_CHECK);
}

function sendError($error_message, $code, $data = null) {
    return Response::json(array('status' => 'error', 'message' => $error_message, 'data' => $data), $code);
}

function containerType() {
    $arr = array(
        1 => array(
            'code' => 'BS',
            'type' => 'Basket',
        ),
        2 => array(
            'code' => 'DT',
            'type' => 'Drying Tables',
        ),
        3 => array(
            'code' => 'SC',
            'type' => 'Special Process barrel',
        ),
        4 => array(
            'code' => 'DM',
            'type' => 'Drying Machine (Future)',
        ),
        5 => array(
            'code' => 'DS',
            'type' => 'Dry Coffee Bag',
        ),
        6 => array(
            'code' => 'GS',
            'type' => 'Pre Defect removal Export Coffee (Size 1 and Size 2) bag',
        ),
        7 => array(
            'code' => 'ES',
            'type' => 'Defect Free Export coffee (Size 1 and Size 2) bag',
        ),
        8 => array(
            'code' => 'PS',
            'type' => 'Peaberry Coffee Bag',
        ),
        9 => array(
            'code' => 'SS',
            'type' => 'Grade 2 Coffee (small and big beans)',
        ),
        10 => array(
            'code' => 'LS',
            'type' => 'Grade 3 (defect) Coffee',
        ),
        11 => array(
            'code' => 'HS',
            'type' => 'Grade 1 husk  Bag',
        ),
        12 => array(
            'code' => 'QS',
            'type' => 'Grade 2 husk Bag',
        ),
        13 => array(
            'code' => 'KS',
            'type' => 'Grade 3 husk bag',
        ),
        14 => array(
            'code' => 'VB',
            'type' => '5kg Vacuum Bag for export',
        ),
        15 => array(
            'code' => 'PB',
            'type' => '15kg Premium Bag for export',
        ),
        16 => array(
            'code' => 'VP',
            'type' => '10kg Shipping Box',
        ),
        17 => array(
            'code' => 'PP',
            'type' => '30kg Shipping Box',
        ),
        18 => array(
            'code' => 'SM',
            'type' => 'Sample Bag 1',
        )
    );
    return $arr;
}

function searcharray($value, $key, $array) {
    foreach ($array as $k => $val) {
        if ($val[$key] == $value) {
            return $k;
        }
    }
    return null;
}

function getFileExtensionForBase64($file) {

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $ext = $finfo->buffer($file) . "\n";
    $ext = strtolower($ext);

    if (strpos($ext, 'png') !== false) {
        return ".png";
    } else if (strpos($ext, 'jpg') !== false) {
        return ".jpg";
    } else if (strpos($ext, 'jpeg') !== false) {
        return ".jpeg";
    } else if (strpos($ext, 'gif') !== false) {
        return ".gif";
    } else if (strpos($ext, 'svg') !== false) {
        return ".svg";
    } else if (strpos($ext, 'bmp') !== false) {
        return ".bmp";
    } else if (strpos($ext, 'webp') !== false) {
        return ".webp";
    } else {
        return ".no-extension";
    }
}
