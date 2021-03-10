<?php

namespace App\Http\Controllers\API;

use App\User;
use App\LoginUser;
use App\CenterUser;
use App\Environment;
use App\Transaction;
use App\MetaTransation;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class YemenOperativeController extends Controller
{

    private $userId;
    private $user;
    private $app_lang;

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';

        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            $this->userId = $request->user()->user_id;

            return $next($request);
        });
    }

    function getYemenOperativeCoffee(Request $request)
    {
        $allTransactions = array();

        $transactions = Transaction::where('is_parent', 0)
            ->whereHas('log', function ($q) {
                $q->whereIn('action', ['sent', 'received'])
                    ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee']);
            })->whereHas(
                'transactionDetail',
                function ($q) {
                    $q->where('container_status', 0);
                },
                '>',
                0
            )->with(['transactionDetail' => function ($query) {
                $query->where('container_status', 0);
            }])->with('meta', 'child')
            ->orderBy('transaction_id', 'desc')
            ->get();

        foreach ($transactions as $key => $transaction) {

            $childTransaction = array();
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;
            $child = $transaction->child;

            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');
            $transaction->makeHidden('child');

            $removeLocalId = explode("-", $transaction->batch_number);
            if ($removeLocalId[3] == '000') {
                $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $transaction->batch_number)->first();
                if ($FindParentTransactions) {
                    $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();

                    foreach ($childTransaction as $key => $childTransactio) {
                        $childTransactio->is_parent = $transaction->transaction_id;
                    }
                }
            }

            $data = [
                'transaction' => $transaction,
                'transactionDetails' => $transactionDetail,
                'transactionMeta' => $transactionMata,
                'child_transactions' => $childTransaction,
                'child' => $child
            ];

            array_push($allTransactions, $data);
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function receivedYemenOperative(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'required',
        ]);

        // return $request->all();

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        $receivedTransactions = json_decode($request['transactions']);

        // return $receivedTransactions;

        DB::beginTransaction();

        try {
            foreach ($receivedTransactions as $key => $sentTransaction) {
                if (isset($sentTransaction->transaction) && $sentTransaction->transaction) {

                    $transaction = Transaction::create([
                        'batch_number' => $sentTransaction->transaction->batch_number,
                        'is_parent' => $sentTransaction->transaction->is_parent,
                        'is_mixed' => $sentTransaction->transaction->is_mixed,
                        'created_by' => $sentTransaction->transaction->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $sentTransaction->transaction->transaction_type,
                        'local_code' => $sentTransaction->transaction->local_code,
                        'transaction_status' => 'received',
                        'reference_id' => $sentTransaction->transaction->reference_id,
                        'is_server_id' => 1,
                        'is_new' => 0,
                        'sent_to' => $sentTransaction->transaction->sent_to,
                        'is_sent' => 1,
                        'session_no' => $sentTransaction->transaction->session_no,
                        'ready_to_milled' => $sentTransaction->transaction->ready_to_milled,
                        'local_created_at' => toSqlDT($sentTransaction->transaction->local_created_at),
                        'local_updated_at' => toSqlDT($sentTransaction->transaction->local_updated_at)
                    ]);

                    $transactionLog = TransactionLog::create([
                        'transaction_id' => $transaction->transaction_id,
                        'action' => 'received',
                        'created_by' => $sentTransaction->transaction->created_by,
                        'entity_id' => $sentTransaction->transaction->center_id,
                        'center_name' => $sentTransaction->transaction->center_name,
                        'local_created_at' => toSqlDT($sentTransaction->transaction->local_created_at),
                        'local_updated_at' => toSqlDT($sentTransaction->transaction->local_updated_at),
                        'type' => 'received_by_yemen',
                    ]);
                    $transactionContainers = $sentTransaction->transactionDetails;
                    foreach ($transactionContainers as $key => $transactionContainer) {
                        TransactionDetail::create([
                            'transaction_id' => $transaction->transaction_id,
                            'container_number' => $transactionContainer->container_number,
                            'created_by' => $transactionContainer->created_by,
                            'is_local' => FALSE,
                            'container_weight' => $transactionContainer->container_weight,
                            'weight_unit' => $transactionContainer->weight_unit,
                            'center_id' => $transactionContainer->center_id,
                            'reference_id' => $transactionContainer->reference_id,
                        ]);

                        TransactionDetail::where('transaction_id', $sentTransaction->transaction->reference_id)->where('container_number', $transactionContainer->container_number)->update(['container_status' => 1]);
                    }
                }
            }

            DB::commit();
        } catch (\PDOException $e) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        }
        $allTransactions = array();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
}
