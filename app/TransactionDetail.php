<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{

    protected $primaryKey = 'transaction_detail_id';
    protected $fillable = ['transaction_id', 'container_number', 'created_by', 'is_local', 'local_code', 'container_weight', 'weight_unit', 'container_status', 'center_id', 'reference_id'];
    protected $casts = [
        'is_local' => 'boolean',
    ];

    function transection()
    {
        return $this->hasOne(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function metas()
    {
        return $this->hasMany(Meta::class, 'transaction_detail_id', 'transaction_detail_id');
    }

    public static function createFromArray($details, $userId, $transactionId, $referenceId)
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

            foreach ($detailObj['metas'] as $metaData) {

                $meta = new Meta();
                $meta->key = $metaData['key'];
                $meta->value = $metaData['value'];
                $detail->metas()->save($meta);
            }
        }

        return $savedDetails;
    }
}
