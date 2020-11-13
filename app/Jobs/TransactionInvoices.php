<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\FileSystem;
use App\TransactionInvoice;

class TransactionInvoices implements ShouldQueue {

    protected $transactionId;
    protected $invoice;
    protected $createdBy;
    protected $incNumber;

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $invoice, $createdBy, $incNumber) {
        $this->transactionId = $transactionId;
        $this->invoice = $invoice;
        $this->createdBy = $createdBy;
        $this->incNumber = $incNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $destinationPath = 'storage/app/images/';
        $file = base64_decode($this->invoice);
        $file_name = time() . $this->incNumber . getFileExtensionForBase64($file);
        file_put_contents($destinationPath . $file_name, $file);
        $userProfileImage = FileSystem::create([
                    'user_file_name' => $file_name,
        ]);
        TransactionInvoice::create([
            'transaction_id' => $this->transactionId,
            'created_by' => $this->createdBy,
            'invoice_id' => $userProfileImage->file_id,
        ]);
    }

}
