<?php

use App\Lot;
use App\Governerate;
use App\Region;
use App\Farmer;
use App\MetaTransation;
use App\Village;
use Carbon\Carbon;
use App\Transaction;
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
        28 => array(
            'id' => 28,
            'code' => '000',
            'type' => 'Accumulation Container',
            'user_role' => 0,
        ),
        29 => array(
            'id' => 29,
            'code' => 'SMP-VB',
            'type' => 'Sample Container Number',
            'user_role' => 0,
        ),
        30 => array(
            'id' => 30,
            'code' => 'SMP-PB',
            'type' => 'Sample Container Number',
            'user_role' => 0,
        ),
        31 => array(
            'id' => 31,
            'code' => 'EE',
            'type' => 'Elephent Beans',
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

function lotNumberGen()
{
    $maxLotNumber = Lot::max('lot_number');

    $currentYear = now()->year;
    $year = $currentYear;
    $serial = 1;

    if ($maxLotNumber) {
        $maxLotNumber = explode('-', $maxLotNumber);
        $year = $maxLotNumber[1];
        $serial = $maxLotNumber[2] + 1;

        if ($currentYear > $year) {
            $year = $currentYear;
            $serial = 1;
        }
    }
    return 'LOT-' . $year . '-' . Str::padLeft($serial, 4, 0);
}

function mixFarmes($id)
{
    $farmers = collect();
    $transaction = Transaction::where('transaction_id', $id)->first();
    if ($transaction['sent_to'] == 2) {
        if (explode('-', $transaction->batch_number)[3] != '000') {
            $farmerCode = explode('-', $transaction->batch_number)[3];
            $farmer =  Farmer::where('farmer_code', 'LIKE', '%' . $farmerCode  . '%')->first();
            $farmers->push(['farmerCode' =>  $farmer->farmer_code]);
            return $farmers;
        }
    } else {
        $id = $transaction->reference_id;
        mixFarmes($id);
    }
}

function checkBatchNumber($farmerCode)
{
    // return $farmerCode;
    $farmer = Farmer::where('farmer_code', $farmerCode)->first();

    if ($farmer) {
        $batchArr = explode('-', $farmerCode);
        $lastnumber =  array_pop($batchArr);
        $newNumber = $lastnumber + 1;
        $newFarmerCode =  array_shift($batchArr) . '-' .  array_shift($batchArr) . '-' .  array_shift($batchArr) . '-' . $newNumber;
        return   checkBatchNumber($newFarmerCode);
    } else {

        return $farmerCode;
        exit;
    }
}
function getGov($code)
{
    $govCode = Str::before($code, '-');
    $governrate = Governerate::where('governerate_code', $govCode)->first();
    if ($governrate) {
        return $governrate->governerate_title;
    }
}
function getRegion($code)
{
    $regCode = explode('-', $code)[0] . '-' . explode('-', $code)[1];
    $region = Region::where('region_code', $regCode)->first();
    if ($region) {
        return $region->region_title;
    }
}
function getVillage($code)
{
    $villageCode = explode('-', $code)[0] . '-' . explode('-', $code)[1] . '-' . explode('-', $code)[2];
    $village = Village::where('village_code', $villageCode)->first();
    if ($village) {
        return $village->village_title;
    }
}
function getFarmer($code)
{
    $farmerCode = explode('-', $code)[0] . '-' . explode('-', $code)[1] . '-' . explode('-', $code)[2] . '-' . explode('-', $code)[3];
    $farmer = Farmer::where('farmer_code', $farmerCode)->first();
    if ($farmer) {
        return $farmer->farmer_name;
    }
}
function farmerPricePerKg($code)
{
    $farmerCode = explode('-', $code)[0] . '-' . explode('-', $code)[1] . '-' . explode('-', $code)[2] . '-' . explode('-', $code)[3];
    $farmer = Farmer::where('farmer_code', $farmerCode)->first();
    if ($farmer) {
        if ($farmer->price_per_kg != null) {
            return $farmer->price_per_kg;
        } else {
            $villageCode = explode('-', $code)[0] . '-' . explode('-', $code)[1] . '-' . explode('-', $code)[2];
            $village = Village::where('village_code', $villageCode)->first();
            if ($village) {
                return $village->price_per_kg;
            }
        }
    }
}
function regDescrption($govName)
{
    $region = Region::where('region_title', $govName)->first();
    return $region->description;
}
function govDescrption($regName)
{

    $gov = Governerate::where('governerate_title', $regName)->first();
    return $gov->description;
}
function stagesOfSentTo($value)
{
    $sentTo = [
        2 => 'Coffee Buyer',
        3 => 'Center Manager',
        4 => 'Processing Manager',
        5 => 'Special Processing',
        6 => 'pending in Drying Coffee',
        8 => 'Recieved By Special Processing',
        7 => 'Special processing',
        9 => 'Sent By Special Processing',
        10 => 'On Drying Beds',
        11 => 'Recieved By Coffee Drying',
        12 => 'Dry Coffee',
        13 => 'Sent For Approve Milling',
        14 => 'Ready To Be Milled',
        15 => 'Mill Operative',
        17 => 'Mill Operative Received',
        // 20 => 'Local Market',
        // 21 => 'Milled',
        // 22 => 'sorting Pending',
        // 23 => 'sorting Rec',
        // 201 => 'sorting sent',
        140 => 'Ready For Approve',
        20 => 'local market from Mill op',
        21 => 'sorting pending',
        22 =>  'sorting Received',
        23 =>  'Yemen Pack Coffee',
        201 =>  'Local Market from sorting Op',
        191 =>  'local market Received',
        193 =>  'sent to inventory',
        195 =>  'order Prepared',
        24 =>  'Pack Coffee For Mixing(Admin)',
        26 =>  'Yemen Pack Coffee Mixed',
        27 =>  'Yemen Pack Coffee Re-Pack',
        29 =>  'Packaging Approval',
        30 => 'Yemen Pack Coffee Prepared',
        31 => 'Yemen Packaging Op Pending',
        33 => 'Yemen Packaging Op Received',
        34 => 'Yemen Packaging Bags',
        36 => 'Yemen Packaging Cartons',
        39 => 'Sent Approval',
        40 => 'Approved For Shipping',
        41 => 'Sent To UK Quality Op',
        197 => 'Order Received',
        198 => 'Order Delivered',
        43 => 'UK warehouse',
        44 => 'Uk Warehouse (set prices)',
        472 => 'Allocate To Ukwarehouse',
        473 => 'China Pending',
        474 => 'China Received',
    ];
    if (array_key_exists($value, $sentTo)) {
        $value = $sentTo[$value];
        if ($value) {
            return $value;
        } else {
            return  ' ';
        }
    } else {
        return  ' ';
    }
}
function regionOfVillage($id)
{
    $village = Village::find($id);

    $regionCode = Str::beforeLast($village->village_code, '-');
    // dd($regionCode);
    $region =  Region::where('region_code', $regionCode)->first();
    if ($region) {
        return $region->region_title;
    } else {
        return ' ';
    }
}
function parentBatch($batch)
{
    $batchNUmber = $batch;
    $farmers = collect();

    $transaction = Transaction::where('batch_number', $batchNUmber)->first();
    // return $transaction->batch_number . !Str::contains($transaction->batbatch_numberch, '000');
    if ($transaction->sent_to == 2 && !Str::contains($transaction->batch_number, '000')) {
        $farmer = Farmer::where('farmer_code', Str::beforeLast($transaction->batch_number, '-'))->first();
        // return $farmer;
        $farmers->push($farmer);
        // return 'here';
    } else {
        $childTransaction =  Transaction::where('is_parent',  $transaction->transaction_id)->get();

        foreach ($childTransaction as $childTran) {
            if ($childTran->sent_to == 2 && !Str::contains($childTran->batch, '000')) {
                // return $childTran->batch_number;
                // return Str::beforeLast($childTran->batch_number, '-');
                $farmer = Farmer::where('farmer_code', Str::beforeLast($childTran->batch_number, '-'))->first();
                // return $farmer;
                $farmers->push($farmer);
            } else {
                $batch_number = $childTran->batch_number;
                // return $batch_number;
                $farmers =  parentBatch($batch_number);
            }
        }
    }
    return $farmers;
}

function allSentTo()
{
    return   $sentTo = [
        2 => 'Coffee Buyer',
        3 => 'Center Manager',
        4 => 'Processing Manager',
        5 => 'Special Processing',
        6 => 'pending in Drying Coffee',
        8 => 'Recieved By Special Processing',
        7 => 'Special processing',
        9 => 'Sent By Special Processing',
        10 => 'On Drying Beds',
        11 => 'Recieved By Coffee Drying',
        12 => 'Dry Coffee',
        13 => 'Sent For Approve Milling',
        14 => 'Ready To Be Milled',
        15 => 'Mill Operative',
        17 => 'Mill Operative Received',
        // 20 => 'Local Market',
        // 21 => 'Milled',
        // 22 => 'sorting Pending',
        // 23 => 'sorting Rec',
        // 201 => 'sorting sent',
        140 => 'Ready For Approve',
        20 => 'local market from Mill op',
        21 => 'sorting pending',
        22 =>  'sorting Received',
        23 =>  'Yemen Pack Coffee',
        24 =>  'Pack Coffee For Mixing(Admin)'
    ];
}


function parentMeta($transactionId, $referenceIds)
{
    $referenceIdsArr = explode('-', $referenceIds);
    $referenceTransactionMetas = MetaTransation::where('key', 'special_process')
        ->whereIn('transaction_id', $referenceIdsArr)->get();
    $referenceTransactionMetas->each(function ($meta) use ($transactionId) {
        $meta->replicate()->fill([
            'transaction_id' => $transactionId,
        ])->save();
    });
}
