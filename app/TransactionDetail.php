<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TransactionDetail extends Model
{

    protected $primaryKey = 'transaction_detail_id';
    protected $fillable = ['transaction_id', 'container_number', 'created_by', 'is_local', 'local_code', 'container_weight', 'weight_unit', 'container_status', 'center_id', 'reference_id'];
    protected $casts = [
        'is_local' => 'boolean',
    ];

    function transaction()
    {
        return $this->hasOne(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function metas()
    {
        return $this->hasMany(Meta::class, 'transaction_detail_id', 'transaction_detail_id');
    }

    public function weight_meta()
    {
        return $this->hasOne(Meta::class, 'transaction_detail_id', 'transaction_detail_id')
            ->where('key', 'rem_weight');
    }

    public static function createFromArray($details, $userId, $transactionId, $referenceId, $is_server_id = false)
    {
        $savedDetails = collect();

        foreach ($details as $detailObj) {

            $detailData = $detailObj['detail'];

            // Start of finding Conatiner
            $container = Container::findOrCreate($detailData['container_number'], $userId);
            // End of finding Conatiner

            // Start of saving one Detail
            $detail = new self();

            $detail->container_number = $container->container_number;
            $detail->transaction_id = $transactionId;
            $detail->created_by = $userId;
            $detail->is_local = FALSE;
            $detail->container_weight = $detailData['container_weight'];
            $detail->weight_unit = $detailData['weight_unit'];
            $detail->center_id = $detailData['center_id'];
            $detail->reference_id = $referenceId;

            $detail->save();

            $savedDetails->push($detail);
            // End of saving one Detail

            TransactionDetail::where('transaction_id', $referenceId)
                ->where('container_number', $detail->container_number)
                ->update(['container_status' => 1]);

            if (array_key_exists('metas', $detailObj)) {
                foreach ($detailObj['metas'] as $metaData) {

                    $meta = new Meta();
                    $meta->key = $metaData['key'];
                    $meta->value = $metaData['value'];
                    $detail->metas()->save($meta);

                    $exploded = explode('_',  $meta->key);

                    if ($exploded[0] == 'last') {
                        $containerNumber  = $exploded[1];
                        $weight = $meta->value;

                        if ($is_server_id) {
                            $weightDetail = TransactionDetail::with('weight_meta')
                                ->where('transaction_id', $referenceId)
                                ->where('container_number', $containerNumber)
                                ->get()
                                ->first();

                            if ($weightDetail->weight_meta) {
                                $weightDetail->weight_meta->value -= $weight;
                                $weightDetail->weight_meta->save();

                                if ($weightDetail->weight_meta->value <= 0) {
                                    $weightDetail->container_status = 1;
                                    $weightDetail->save();
                                }
                            }
                        }
                    }
                }
            }
        }

        return $savedDetails;
    }

    public static function createAccumulated($userId, $transactionId, $containerWeight, $referenceId = 0)
    {
        $accumulationContainer = Container::findOrCreateAccumulated($userId);

        $accumultedDetail = TransactionDetail::create([
            'transaction_id' => $transactionId,
            'container_number' => $accumulationContainer->container_number,
            'created_by' => $userId,
            'container_weight' => $containerWeight,
            'weight_unit' => 'KG',
            'reference_id' => $referenceId
        ]);

        return $accumultedDetail;
    }
}
