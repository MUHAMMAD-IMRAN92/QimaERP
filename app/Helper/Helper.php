<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

function timeago($ptime)
{
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

function sendSuccess($message, $data)
{
    return Response::json(array('status' => 'success', 'message' => $message, 'data' => $data), 200, [], JSON_NUMERIC_CHECK);
}

function sendError($error_message, $code, $data = null)
{
    return Response::json(array('status' => 'error', 'message' => $error_message, 'data' => $data), $code);
}

function containerType()
{
    $arr = array(
        1 => array(
            'id' => 1,
            'code' => 'BS',
            'type' => 'Basket',
            'user_role' => 2,
        ),
        2 => array(
            'id' => 2,
            'code' => 'DT',
            'type' => 'Drying Tables',
            'user_role' => 0,
        ),
        3 => array(
            'id' => 3,
            'code' => 'SC',
            'type' => 'Special Process barrel',
            'user_role' => 6,
        ),
        4 => array(
            'id' => 4,
            'code' => 'DM',
            'type' => 'Drying Machine (Future)',
            'user_role' => 0,
        ),
        5 => array(
            'id' => 5,
            'code' => 'DS',
            'type' => 'Dry Coffee Bag',
            'user_role' => 0,
        ),
        6 => array(
            'id' => 6,
            'code' => 'GSA',
            'type' => 'Pre Defect removal Export Coffee (Size 1) bag',
            'user_role' => 0,
        ),
        7 => array(
            'id' => 7,
            'code' => 'ESA',
            'type' => 'Defect Free Export coffee (Size 1) bag',
            'user_role' => 0,
        ),
        8 => array(
            'id' => 8,
            'code' => 'PS',
            'type' => 'Peaberry Coffee Bag',
            'user_role' => 0,
        ),
        9 => array(
            'id' => 9,
            'code' => 'SS',
            'type' => 'Grade 2 Coffee (small and big beans)',
            'user_role' => 0,
        ),
        10 => array(
            'id' => 10,
            'code' => 'LS',
            'type' => 'Grade 3 (defect) Coffee',
            'user_role' => 0,
        ),
        11 => array(
            'id' => 11,
            'code' => 'HS',
            'type' => 'Grade 1 husk  Bag',
            'user_role' => 0,
        ),
        12 => array(
            'id' => 12,
            'code' => 'QS',
            'type' => 'Grade 2 husk Bag',
            'user_role' => 0,
        ),
        13 => array(
            'id' => 13,
            'code' => 'KS',
            'type' => 'Grade 3 husk bag',
            'user_role' => 0,
        ),
        14 => array(
            'id' => 14,
            'code' => 'VB',
            'type' => '5kg Vacuum Bag for export',
            'user_role' => 0,
        ),
        15 => array(
            'id' => 15,
            'code' => 'PB',
            'type' => '15kg Premium Bag for export',
            'user_role' => 0,
        ),
        16 => array(
            'id' => 16,
            'code' => 'VP',
            'type' => '15kg Shipping Box',
            'user_role' => 0,
        ),
        17 => array(
            'id' => 17,
            'code' => 'PP',
            'type' => '30kg Shipping Box',
            'user_role' => 0,
        ),
        18 => array(
            'id' => 18,
            'code' => 'SM',
            'type' => 'Sample Bag 1',
            'user_role' => 0,
        ),
        19 => array(
            'id' => 19,
            'code' => 'SPS',
            'type' => 'Special Processed Sack',
            'user_role' => 6,
        ),
        20 => array(
            'id' => 20,
            'code' => 'EB',
            'type' => 'Elephent Beans Bag',
            'user_role' => 0,
        ),
        21 => array(
            'id' => 21,
            'code' => 'SB',
            'type' => 'Small Beans Bag',
            'user_role' => 0,
        ),
        22 => array(
            'id' => 22,
            'code' => 'SGO',
            'type' => 'Size 1 Green Coffee Bag',
            'user_role' => 0,
        ),
        23 => array(
            'id' => 23,
            'code' => 'SGT',
            'type' => 'Size 2 Green Coffee Bag',
            'user_role' => 0,
        ),
        24 => array(
            'id' => 24,
            'code' => 'GSB',
            'type' => 'Pre Defect removal Export Coffee (Size 2) bag',
            'user_role' => 0,
        ),
        25 => array(
            'id' => 25,
            'code' => 'ESB',
            'type' => 'Defect Free Export coffee (Size 2) bag',
            'user_role' => 0,
        ),
        26 => array(
            'id' => 26,
            'code' => 'PDC',
            'type' => 'Part Dry Cherry',
            'user_role' => 0,
        ),
        27 => array(
            'id' => 27,
            'code' => 'PW',
            'type' => 'Pallet',
            'user_role' => 0,
        ),
    );

    return $arr;
}

function searcharray($value, $key, $array)
{
    foreach ($array as $k => $val) {
        if ($val[$key] == $value) {
            return $k;
        }
    }
    return null;
}

function getFileExtensionForBase64($file)
{

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

function toSqlDT($string)
{
    return Carbon::parse($string)->toDateTimeString();
}

function lotGen($maxId)
{
    return 'LOT-' . now()->year . '-' . Str::padLeft($maxId, 4, 0);
}
