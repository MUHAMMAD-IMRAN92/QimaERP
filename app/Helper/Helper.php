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
