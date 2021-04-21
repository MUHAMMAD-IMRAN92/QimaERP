<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\CenterUser;
use App\Environment;
use App\Transaction;
use App\MetaTransation;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CoffeeDryingController extends Controller
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

    function getCoffeeDryingPendingCoffee(Request $request)
    {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->whereHas('log', function ($q) use ($centerId) {
            $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen'])->where('entity_id', $centerId);
        })->whereHas('transactionDetail', function ($q) use ($centerId) {
            $q->where('container_status', 0);
        }, '>', 0)
            //->doesntHave('isReference')
            ->with(['transactionDetail' => function ($query) {
                $query->where('container_status', 0);
            }])->with('meta')->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transactionMata = $transaction->meta;
            $transactionDetailArray = array();
            foreach ($transactionDetail as $key => $transactionDet) {
                $transactionDet->is_local = FALSE;
                $transactionDet->update_meta = FALSE;
                array_push($transactionDetailArray, $transactionDet);
            }
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            if ($transaction->sent_to != 12) {
                $transaction->is_sent = 0;
            }
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetailArray, 'transactionMeta' => $transactionMata];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function receivedCoffeeDryingCoffee(Request $request)
    {
        // return $request->transactions;
        $validator = Validator::make($request->all(), [
            'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $userId = $this->userId;
        $receivedCofffee = array();
        $receivedTransactions = json_decode($request['transactions']);
        DB::beginTransaction();
        try {
            foreach ($receivedTransactions as $key => $receivedTransaction) {
                if ($receivedTransaction->transaction->is_local == FALSE && $receivedTransaction->transaction->update_meta == TRUE) {
                    $updateCoffees = Transaction::where('transaction_id', $receivedTransaction->transaction->transaction_id)->first();
                    if ($updateCoffees) {
                        $updateCoffees->is_sent = $receivedTransaction->transaction->is_sent;
                        $updateCoffees->is_in_process = $receivedTransaction->transaction->is_in_process;
                        $updateCoffees->local_updated_at = toSqlDT($receivedTransaction->transaction->local_updated_at);
                        $updateCoffees->update();
                    }
                } else {

                    if ($receivedTransaction->transaction && $receivedTransaction->transaction && $receivedTransaction->transaction->sent_to == 10) {
                        //::Recevied coffee transations
                        if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
                            $trans = true;
                            if ($receivedTransaction->transaction->is_server_id == false) {
                                $trans = false;
                            }

                            if ($trans) {
                                $parentTransaction = Transaction::where('transaction_id', $receivedTransaction->transaction->reference_id)->first();

                                if (!$parentTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] is not found 10 server');
                                }
                            } else {
                                $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
                                $parentTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();

                                if (!$parentTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] is not found 10 local');
                                }
                            }

                            $transaction = Transaction::create([
                                'batch_number' => $receivedTransaction->transaction->batch_number,
                                'is_parent' => $receivedTransaction->transaction->is_parent,
                                'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => 1,
                                'local_code' => $receivedTransaction->transaction->local_code,
                                'is_special' => $parentTransaction->is_special,
                                'transaction_status' => 'received',
                                'reference_id' => $receivedTransaction->transaction->reference_id,
                                'is_server_id' => 1,
                                'is_new' => 0,
                                'sent_to' => 10,
                                'is_sent' => 1,
                                'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                'session_no' => $receivedTransaction->transaction->session_no,
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at)
                            ]);
                            $receivedTransId = $receivedTransaction->transaction->reference_id;
                            $transactionLog = TransactionLog::create([
                                'transaction_id' => $transaction->transaction_id,
                                'action' => 'received',
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'entity_id' => $receivedTransaction->transaction->center_id,
                                'center_name' => '',
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at),
                                'type' => 'coffee_drying',
                            ]);
                            $transactionContainers = $receivedTransaction->transactionMeta;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                if (strstr($transactionContainer->key, 'BS') || strstr($transactionContainer->key, 'DT') || strstr($transactionContainer->key, 'SC') || strstr($transactionContainer->key, 'DM') || strstr($transactionContainer->key, 'DS') || strstr($transactionContainer->key, 'GS') || strstr($transactionContainer->key, 'ES') || strstr($transactionContainer->key, 'PS') || strstr($transactionContainer->key, 'SS') || strstr($transactionContainer->key, 'LS') || strstr($transactionContainer->key, 'HS') || strstr($transactionContainer->key, 'QS') || strstr($transactionContainer->key, 'KS') || strstr($transactionContainer->key, 'VB') || strstr($transactionContainer->key, 'PB') || strstr($transactionContainer->key, 'VP') || strstr($transactionContainer->key, 'PP') || strstr($transactionContainer->key, 'SM')) {
                                    $basketArray = explode("_", $transactionContainer->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    $transationsExplodeId = $basketArray[2];
                                    TransactionDetail::create([
                                        'transaction_id' => $transaction->transaction_id,
                                        'container_number' => $basket,
                                        'created_by' => $userId,
                                        'is_local' => FALSE,
                                        'container_weight' => $weight,
                                        'weight_unit' => 'kg',
                                        'center_id' => $receivedTransaction->transaction->center_id,
                                        'reference_id' => $receivedTransaction->transaction->reference_id,
                                    ]);

                                    if ($trans == true) {
                                        TransactionDetail::where('transaction_id', $transationsExplodeId)->where('container_number', $basket)->update(['container_status' => 1]);
                                    } else {
                                        $code = $transationsExplodeId . '_' . $userId . '-T';
                                        $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();
                                        $receivedTransIdCheck = $checkTransaction->transaction_id;
                                        TransactionDetail::where('transaction_id', $receivedTransIdCheck)->where('container_number', $basket)->update(['container_status' => 1]);
                                    }
                                }
                            }
                        }
                        //::Process start transactions
                        if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
                            $processTransaction = Transaction::create([
                                'batch_number' => $receivedTransaction->transaction->batch_number,
                                'is_parent' => $receivedTransaction->transaction->is_parent,
                                'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => 2,
                                'local_code' => $receivedTransaction->transaction->local_code,
                                'is_special' => $parentTransaction->is_special,
                                'transaction_status' => 'sent',
                                'reference_id' => $receivedTransaction->transaction->reference_id,
                                'is_server_id' => 1,
                                'is_new' => 0,
                                'sent_to' => 10,
                                'is_sent' => 1,
                                'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                'session_no' => $receivedTransaction->transaction->session_no,
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at)
                            ]);
                            $receivedTransId = $receivedTransaction->transaction->reference_id;
                            $transactionLog = TransactionLog::create([
                                'transaction_id' => $processTransaction->transaction_id,
                                'action' => 'sent',
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'entity_id' => $receivedTransaction->transaction->center_id,
                                'center_name' => '',
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at),
                                'type' => 'coffee_drying_received',
                            ]);
                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $processTransaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $userId,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => 'kg',
                                    'center_id' => $receivedTransaction->transaction->center_id,
                                    'reference_id' => $receivedTransaction->transaction->reference_id,
                                ]);
                            }
                            array_push($receivedCofffee, $processTransaction->transaction_id);

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                MetaTransation::create([
                                    'transaction_id' => $processTransaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                ]);
                            }
                        }
                    }
                    if ($receivedTransaction->transaction && $receivedTransaction->transaction && $receivedTransaction->transaction->sent_to == 11) {

                        if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
                            if ($receivedTransaction->transaction->is_server_id == true) {
                                $receivedTransId = $receivedTransaction->transaction->reference_id;

                                $parentTransaction = Transaction::where('transaction_id', $receivedTransaction->transaction->reference_id)->first();

                                if (!$parentTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] is not found 11');
                                }
                            } else {
                                $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
                                $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();
                                $receivedTransId = $checkTransaction->transaction_id;

                                $parentTransaction = $checkTransaction;
                            }

                            $processTransaction = Transaction::create([
                                'batch_number' => $receivedTransaction->transaction->batch_number,
                                'is_parent' => $receivedTransaction->transaction->is_parent,
                                'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => 2,
                                'local_code' => $receivedTransaction->transaction->local_code,
                                'is_special' => $parentTransaction->is_special,
                                'transaction_status' => 'sent',
                                'reference_id' => $receivedTransId,
                                'is_server_id' => 1,
                                'is_new' => 0,
                                'sent_to' => 11,
                                'is_sent' => 1,
                                'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                'session_no' => $receivedTransaction->transaction->session_no,
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at)
                            ]);
                            array_push($receivedCofffee, $processTransaction->transaction_id);
                            $transactionLog = TransactionLog::create([
                                'transaction_id' => $processTransaction->transaction_id,
                                'action' => 'sent',
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'entity_id' => $receivedTransaction->transaction->center_id,
                                'center_name' => '',
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at),
                                'type' => 'coffee_drying_send',
                            ]);
                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $processTransaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $userId,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => 'kg',
                                    'center_id' => $receivedTransaction->transaction->center_id,
                                    'reference_id' => $receivedTransaction->transaction->reference_id,
                                ]);
                                TransactionDetail::where('transaction_id', $receivedTransId)->where('container_number', $transactionContainer->container_number)->update(['container_status' => 1]);
                            }
                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                MetaTransation::create([
                                    'transaction_id' => $processTransaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                ]);
                            }
                        }
                    }
                    if ($receivedTransaction->transaction && $receivedTransaction->transaction->sent_to == 12) {

                        if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {

                            $receivedTransIds = [];
                            if ($receivedTransaction->transaction->is_server_id == true) {

                                $parentTransaction = Transaction::where('transaction_id', explode(',', $receivedTransaction->transaction->reference_id)[0])->first();

                                if (!$parentTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] is not found 12');
                                }

                                $receivedTransIds = explode(',', $receivedTransaction->transaction->reference_id);

                                $receivedTransIdsString = $receivedTransaction->transaction->reference_id;
                            } else {

                                $localCodes = explode(',', $receivedTransaction->transaction->reference_id);

                                $parentTransactions = Transaction::whereIn('local_code', $localCodes)->get();

                                $receivedTransIds = $parentTransactions->pluck('transaction_id')->toArray();

                                $receivedTransIdsString = implode(',', $receivedTransIds);

                                $parentTransaction = $parentTransactions->first();

                                if (!$parentTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] is not found in sent_to = 12 and search with local code');
                                }
                            }

                            $processTransaction = Transaction::create([
                                'batch_number' => $receivedTransaction->transaction->batch_number,
                                'is_parent' => $receivedTransaction->transaction->is_parent,
                                'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => 2,
                                'local_code' => $receivedTransaction->transaction->local_code,
                                'is_special' => $parentTransaction->is_special,
                                'transaction_status' => 'sent',
                                'reference_id' => $receivedTransIdsString,
                                'is_server_id' => 1,
                                'is_new' => 0,
                                'sent_to' => 12,
                                'is_sent' => $receivedTransaction->transaction->is_sent,
                                'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                'session_no' => $receivedTransaction->transaction->session_no,
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at)
                            ]);
                            array_push($receivedCofffee, $processTransaction->transaction_id);
                            $transactionLog = TransactionLog::create([
                                'transaction_id' => $processTransaction->transaction_id,
                                'action' => 'sent',
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'entity_id' => $receivedTransaction->transaction->center_id,
                                'center_name' => '',
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at),
                                'type' => 'sent_to_yemen',
                            ]);
                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $processTransaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $userId,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => 'kg',
                                    'center_id' => $receivedTransaction->transaction->center_id,
                                    'reference_id' => $receivedTransIdsString,
                                ]);
                            }
                            TransactionDetail::whereIn('transaction_id', $receivedTransIds)->update(['container_status' => 1]);
                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                MetaTransation::create([
                                    'transaction_id' => $processTransaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                ]);
                            }
                        }
                    }
                    if ($receivedTransaction->transaction && $receivedTransaction->transaction->sent_to == 0) {
                        if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
                            if ($receivedTransaction->transaction->is_server_id == true) {
                                $receivedTransId = $receivedTransaction->transaction->reference_id;

                                $parentTransaction = Transaction::where('transaction_id', $receivedTransaction->transaction->reference_id)->first();

                                if (!$parentTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] is not found 0');
                                }
                            } else {
                                $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
                                $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();

                                if (!$checkTransaction) {
                                    throw new Exception('Parent Transaction [' . $receivedTransaction->transaction->reference_id . '] not found 0 [part dry cofee]');
                                }

                                $receivedTransId = $checkTransaction->transaction_id;

                                $parentTransaction = $checkTransaction;
                            }
                            $processTransaction2 = Transaction::create([
                                'batch_number' => $receivedTransaction->transaction->batch_number,
                                'is_parent' => $receivedTransaction->transaction->is_parent,
                                'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => 2,
                                'local_code' => $receivedTransaction->transaction->local_code,
                                'is_special' => $parentTransaction->is_special,
                                'transaction_status' => 'sent',
                                'reference_id' => $receivedTransId,
                                'is_server_id' => 1,
                                'is_new' => 0,
                                'sent_to' => 0,
                                'is_sent' => 1,
                                'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                'session_no' => $receivedTransaction->transaction->session_no,
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at)
                            ]);
                            array_push($receivedCofffee, $processTransaction2->transaction_id);

                            $transactionLog = TransactionLog::create([
                                'transaction_id' => $processTransaction2->transaction_id,
                                'action' => 'sent',
                                'created_by' => $receivedTransaction->transaction->created_by,
                                'entity_id' => $receivedTransaction->transaction->center_id,
                                'center_name' => '',
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                'local_updated_at' => toSqlDT($receivedTransaction->transaction->local_updated_at),
                                'type' => 'coffee_drying',
                            ]);
                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $processTransaction2->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $userId,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => 'kg',
                                    'center_id' => $receivedTransaction->transaction->center_id,
                                    'reference_id' => $receivedTransaction->transaction->reference_id,
                                ]);
                            }
                            TransactionDetail::where('transaction_id', $receivedTransId)->update(['container_status' => 1]);
                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                MetaTransation::create([
                                    'transaction_id' => $processTransaction2->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        }

        $allTransactions = array();
        //        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $receivedCofffee)->with('transactionDetail', 'log', 'meta')->get();
        //        foreach ($currentlyReceivedCoffees as $key => $transaction) {
        //            $transactionDetail = $transaction->transactionDetail;
        //
        //            $transactionDetailArray = array();
        //            foreach ($transactionDetail as $key => $transactionDet) {
        //                $transactionDet->is_local = FALSE;
        //                $transactionDet->update_meta = FALSE;
        //                array_push($transactionDetailArray, $transactionDet);
        //            }
        //            $transactionMeta = $transaction->meta;
        //            $transaction->center_id = $transaction->log->entity_id;
        //            $transaction->center_name = $transaction->log->center_name;
        //            $transaction->is_sent = 0;
        //            $transaction->makeHidden('transactionDetail');
        //            $transaction->makeHidden('log');
        //            $transaction->makeHidden('meta');
        //            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetailArray, 'transactionMeta' => $transactionMeta];
        //            array_push($allTransactions, $data);
        //        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function getReceivedCoffeeDryingCoffee(Request $request)
    {
        $userId = $this->userId;

        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        $centerId = 0;
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $currentlyReceivedCoffees = Transaction::where('is_parent', 0)->where('created_by', $userId)->where('transaction_status', 'sent')->whereHas('transactionLog', function ($q) use ($centerId) {
            $q->where('action', 'sent')->where('type', 'coffee_drying_received')->where('entity_id', $centerId);
        })->with('transactionDetail', 'log', 'meta')->orderBy('transaction_id', 'desc')->get();
        foreach ($currentlyReceivedCoffees as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transactionDetailArray = array();
            foreach ($transactionDetail as $key => $transactionDet) {
                $transactionDet->is_local = FALSE;
                $transactionDet->update_meta = FALSE;
                array_push($transactionDetailArray, $transactionDet);
            }
            $transactionMeta = $transaction->meta;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transaction->is_sent = 0;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');
            $data = ['transaction' => $transaction, 'transactionDetail' => $transactionDetailArray, 'transactionMeta' => $transactionMeta];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function updateMeta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meta' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $userId = $this->userId;
        $allMetaArray = array();
        $receivedMeta = json_decode($request['meta']);

        $transationsIdArray = array();
        foreach ($receivedMeta as $key => $transactionsInformation) {

            if ($transactionsInformation->transactionDetails) {
                if ($transactionsInformation->transactionDetails->is_local == TRUE) {
                    TransactionDetail::create([
                        'transaction_id' => $transactionsInformation->transactionDetails->transaction_id,
                        'container_number' => $transactionsInformation->transactionDetails->container_number,
                        'created_by' => $userId,
                        'is_local' => FALSE,
                        'container_weight' => $transactionsInformation->transactionDetails->container_weight,
                        'weight_unit' => 'kg',
                        'center_id' => $transactionsInformation->transactionDetails->center_id,
                        'reference_id' => $transactionsInformation->transactionDetails->reference_id,
                    ]);
                } else {
                    $alreadyExistTransactionDetail = TransactionDetail::where('transaction_id', $transactionsInformation->transactionDetails->transaction_id)
                        ->where('container_number', $transactionsInformation->transactionDetails->container_number)
                        ->first();

                    $alreadyExistTransactionDetail->container_weight = $transactionsInformation->transactionDetails->container_weight;
                    $alreadyExistTransactionDetail->container_status = $transactionsInformation->transactionDetails->is_sent;
                    $alreadyExistTransactionDetail->save();
                }
                if (!in_array($transactionsInformation->transactionDetails->transaction_id, $transationsIdArray)) {
                    array_push($transationsIdArray, $transactionsInformation->transactionDetails->transaction_id);
                }
            }

            foreach ($transactionsInformation->transactionMeta as $key => $value) {

                if ($value->key == 'moisture_measurement') {

                    // $alreadyMetaExist = MetaTransation::where('transaction_id', $value->transaction_id)
                    //     ->where('key', 'moisture_measurement')
                    //     ->first();

                    // if ($alreadyMetaExist) {
                    //     $alreadyMetaExist->value = $value->value;
                    //     $alreadyMetaExist->save();
                    // } else {
                    //     $newMata = MetaTransation::create([
                    //         'transaction_id' => $value->transaction_id,
                    //         'key' => $value->key,
                    //         'value' => $value->value,
                    //         'local_created_at' => Carbon::parse($value->local_created_at)->toDateTimeString()
                    //     ]);
                    // }

                    $newMata = MetaTransation::create([
                        'transaction_id' => $value->transaction_id,
                        'key' => $value->key,
                        'value' => $value->value,
                        'local_created_at' => Carbon::parse($value->local_created_at)->toDateTimeString()
                    ]);
                } elseif (strstr($value->key, 'BS') || strstr($value->key, 'DT') || strstr($value->key, 'SC') || strstr($value->key, 'DM') || strstr($value->key, 'DS') || strstr($value->key, 'GS') || strstr($value->key, 'ES') || strstr($value->key, 'PS') || strstr($value->key, 'SS') || strstr($value->key, 'LS') || strstr($value->key, 'HS') || strstr($value->key, 'QS') || strstr($value->key, 'KS') || strstr($value->key, 'VB') || strstr($value->key, 'PB') || strstr($value->key, 'VP') || strstr($value->key, 'PP') || strstr($value->key, 'SM')) {

                    $basketArray = explode("_", $value->key);

                    $basket = $basketArray[0];
                    $weight = $basketArray[1];

                    $alreadyExistBasketMeta = MetaTransation::where('transaction_id', $value->transaction_id)
                        ->where('key', 'like', "$basket%")
                        ->first();

                    if ($alreadyExistBasketMeta) {
                        $alreadyExistBasketMeta->key = $value->key;
                        $alreadyExistBasketMeta->value = $value->value;
                        $alreadyExistBasketMeta->save();
                    } else {
                        $newMata = MetaTransation::create([
                            'transaction_id' => $value->transaction_id,
                            'key' => $value->key,
                            'value' => $value->value,
                            'local_created_at' => Carbon::parse($value->local_created_at)->toDateTimeString()
                        ]);
                    }
                } else {

                    $alreadyExist = MetaTransation::where('transaction_id', $value->transaction_id)
                        ->where('key', $value->key)
                        ->first();

                    if ($alreadyExist) {

                        $alreadyExist->key = $value->key;
                        $alreadyExist->value = $value->value;
                        $alreadyExist->save();
                    } else {

                        $newMata = MetaTransation::create([
                            'transaction_id' => $value->transaction_id,
                            'key' => $value->key,
                            'value' => $value->value,
                            'local_created_at' => Carbon::parse($value->local_created_at)->toDateTimeString()
                        ]);
                    }
                }
            }
        }

        $allTransationsDetail = array();
        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $transationsIdArray)
            ->with('transactionDetail', 'log', 'meta')
            ->get();

        foreach ($currentlyReceivedCoffees as $key => $currentlyReceivedCof) {
            $transactionDetailRec = $currentlyReceivedCof->transactionDetail()->first();
            $transactionMetaRec = $currentlyReceivedCof->meta;
            $data = ['transactionDetails' => $transactionDetailRec, 'transactionMeta' => $transactionMetaRec];
            array_push($allTransationsDetail, $data);
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransationsDetail);
    }

    //    function sendCoffeeDryingCoffee(Request $request) {
    //        $validator = Validator::make($request->all(), [
    //                    'transactions' => 'required',
    //        ]);
    //        if ($validator->fails()) {
    //            $errors = implode(', ', $validator->errors()->all());
    //            return sendError($errors, 400);
    //        }
    //        $userId = $this->userId;
    //        $sentCofffeeArray = array();
    //        $receivedTransactions = json_decode($request['transactions']);
    //        foreach ($receivedTransactions as $key => $receivedTransaction) {
    //            //::Process start transactions
    //            if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
    //                if ($receivedTransaction->transaction->is_server_id == True) {
    //                    $receivedTransId = $receivedTransaction->transaction->reference_id;
    //                } else {
    //                    $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
    //                    $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();
    //                    $receivedTransId = $checkTransaction->transaction_id;
    //                }
    //
    //                $processTransaction = Transaction::create([
    //                            'batch_number' => $receivedTransaction->transaction->batch_number,
    //                            'is_parent' => $receivedTransaction->transaction->is_parent,
    //                            'is_mixed' => $receivedTransaction->transaction->is_mixed,
    //                            'created_by' => $receivedTransaction->transaction->created_by,
    //                            'is_local' => FALSE,
    //                            'transaction_type' => 2,
    //                            'local_code' => $receivedTransaction->transaction->local_code,
    //                            'transaction_status' => 'sent',
    //                            'reference_id' => $receivedTransId,
    //                            'is_server_id' => 1,
    //                            'is_new' => 0,
    //                            'sent_to' => 11,
    //                            'is_sent' => 1,
    //                ]);
    //                array_push($sentCofffeeArray, $processTransaction->transaction_id);
    //                $transactionLog = TransactionLog::create([
    //                            'transaction_id' => $processTransaction->transaction_id,
    //                            'action' => 'sent',
    //                            'created_by' => $receivedTransaction->transaction->created_by,
    //                            'entity_id' => $receivedTransaction->transaction->center_id,
    //                            'center_name' => '',
    //                            'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
    //                            'type' => 'coffee_drying_send',
    //                ]);
    //                $transactionContainers = $receivedTransaction->transactionDetails;
    //                foreach ($transactionContainers as $key => $transactionContainer) {
    //                    TransactionDetail::create([
    //                        'transaction_id' => $processTransaction->transaction_id,
    //                        'container_number' => $transactionContainer->container_number,
    //                        'created_by' => $userId,
    //                        'is_local' => FALSE,
    //                        'container_weight' => $transactionContainer->container_weight,
    //                        'weight_unit' => 'kg',
    //                        'center_id' => $receivedTransaction->transaction->center_id,
    //                        'reference_id' => $receivedTransaction->transaction->reference_id,
    //                    ]);
    //                    TransactionDetail::where('transaction_id', $receivedTransId)->where('container_number', $transactionContainer->container_number)->update(['container_status' => 1]);
    //                }
    //                $transactionMeta = $receivedTransaction->transactionMeta;
    //                foreach ($transactionMeta as $key => $transactionMe) {
    //                    MetaTransation::create([
    //                        'transaction_id' => $processTransaction->transaction_id,
    //                        'key' => $transactionMe->key,
    //                        'value' => $transactionMe->value,
    //                    ]);
    //                }
    //            }
    //        }
    //        $allTransactions = array();
    //        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $sentCofffeeArray)->with('transactionDetail', 'log', 'meta')->get();
    //        foreach ($currentlyReceivedCoffees as $key => $transaction) {
    //            $transactionDetail = $transaction->transactionDetail;
    //
    //            $transactionDetailArray = array();
    //            foreach ($transactionDetail as $key => $transactionDet) {
    //                $transactionDet->is_local = FALSE;
    //                $transactionDet->update_meta = FALSE;
    //                array_push($transactionDetailArray, $transactionDet);
    //            }
    //            $transactionMeta = $transaction->meta;
    //            $transaction->center_id = $transaction->log->entity_id;
    //            $transaction->center_name = $transaction->log->center_name;
    //            $transaction->is_sent = 0;
    //            $transaction->makeHidden('transactionDetail');
    //            $transaction->makeHidden('log');
    //            $transaction->makeHidden('meta');
    //            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetailArray, 'transactionMeta' => $transactionMeta];
    //            array_push($allTransactions, $data);
    //        }
    //        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.SENT_COFFEE"), $allTransactions);
    //    }
    //
    //    function coffeeSentToYemen(Request $request) {
    //        $validator = Validator::make($request->all(), [
    //                    'transactions' => 'required',
    //        ]);
    //        if ($validator->fails()) {
    //            $errors = implode(', ', $validator->errors()->all());
    //            return sendError($errors, 400);
    //        }
    //        $userId = $this->userId;
    //        $sentCofffeeArray = array();
    //        $receivedTransactions = json_decode($request['transactions']);
    //        foreach ($receivedTransactions as $key => $receivedTransaction) {
    //            //::Process start transactions
    //            if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
    //                if ($receivedTransaction->transaction->is_server_id == True) {
    //                    $receivedTransId = $receivedTransaction->transaction->reference_id;
    //                } else {
    //                    $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
    //                    $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();
    //                    $receivedTransId = $checkTransaction->transaction_id;
    //                }
    //                $processTransaction = Transaction::create([
    //                            'batch_number' => $receivedTransaction->transaction->batch_number,
    //                            'is_parent' => $receivedTransaction->transaction->is_parent,
    //                            'is_mixed' => $receivedTransaction->transaction->is_mixed,
    //                            'created_by' => $receivedTransaction->transaction->created_by,
    //                            'is_local' => FALSE,
    //                            'transaction_type' => 2,
    //                            'local_code' => $receivedTransaction->transaction->local_code,
    //                            'transaction_status' => 'sent',
    //                            'reference_id' => $receivedTransId,
    //                            'is_server_id' => 1,
    //                            'is_new' => 0,
    //                            'sent_to' => 12,
    //                            'is_sent' => 1,
    //                ]);
    //                array_push($sentCofffeeArray, $processTransaction->transaction_id);
    //                $transactionLog = TransactionLog::create([
    //                            'transaction_id' => $processTransaction->transaction_id,
    //                            'action' => 'sent',
    //                            'created_by' => $receivedTransaction->transaction->created_by,
    //                            'entity_id' => $receivedTransaction->transaction->center_id,
    //                            'center_name' => '',
    //                            'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
    //                            'type' => 'sent_to_yemen',
    //                ]);
    //                $transactionContainers = $receivedTransaction->transactionDetails;
    //                foreach ($transactionContainers as $key => $transactionContainer) {
    //                    TransactionDetail::create([
    //                        'transaction_id' => $processTransaction->transaction_id,
    //                        'container_number' => $transactionContainer->container_number,
    //                        'created_by' => $userId,
    //                        'is_local' => FALSE,
    //                        'container_weight' => $transactionContainer->container_weight,
    //                        'weight_unit' => 'kg',
    //                        'center_id' => $receivedTransaction->transaction->center_id,
    //                        'reference_id' => $receivedTransaction->transaction->reference_id,
    //                    ]);
    //                }
    //                TransactionDetail::where('transaction_id', $receivedTransId)->update(['container_status' => 1]);
    //                $transactionMeta = $receivedTransaction->transactionMeta;
    //                foreach ($transactionMeta as $key => $transactionMe) {
    //                    MetaTransation::create([
    //                        'transaction_id' => $processTransaction->transaction_id,
    //                        'key' => $transactionMe->key,
    //                        'value' => $transactionMe->value,
    //                    ]);
    //                }
    //            }
    //        }
    //        $allTransactions = array();
    //        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $sentCofffeeArray)->with('transactionDetail', 'log', 'meta')->get();
    //        foreach ($currentlyReceivedCoffees as $key => $transaction) {
    //            $transactionDetail = $transaction->transactionDetail;
    //
    //            $transactionDetailArray = array();
    //            foreach ($transactionDetail as $key => $transactionDet) {
    //                $transactionDet->is_local = FALSE;
    //                $transactionDet->update_meta = FALSE;
    //                array_push($transactionDetailArray, $transactionDet);
    //            }
    //            $transactionMeta = $transaction->meta;
    //            $transaction->center_id = $transaction->log->entity_id;
    //            $transaction->center_name = $transaction->log->center_name;
    //            $transaction->is_sent = 0;
    //            $transaction->makeHidden('transactionDetail');
    //            $transaction->makeHidden('log');
    //            $transaction->makeHidden('meta');
    //            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetailArray, 'transactionMeta' => $transactionMeta];
    //            array_push($allTransactions, $data);
    //        }
    //        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.SENT_COFFEE"), $allTransactions);
    //    }
    //
    //    function partDryCoffee(Request $request) {
    //        $validator = Validator::make($request->all(), [
    //                    'transactions' => 'required',
    //        ]);
    //        if ($validator->fails()) {
    //            $errors = implode(', ', $validator->errors()->all());
    //            return sendError($errors, 400);
    //        }
    //        $userId = $this->userId;
    //        $sentCofffeeArray = array();
    //        $receivedTransactions = json_decode($request['transactions']);
    //        foreach ($receivedTransactions as $key => $receivedTransaction) {
    //            //::Process start transactions
    //            if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
    //                if ($receivedTransaction->transaction->is_server_id == True) {
    //                    $receivedTransId = $receivedTransaction->transaction->reference_id;
    //                } else {
    //                    $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
    //                    $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();
    //                    $receivedTransId = $checkTransaction->transaction_id;
    //                }
    //                $processTransaction = Transaction::create([
    //                            'batch_number' => $receivedTransaction->transaction->batch_number,
    //                            'is_parent' => $receivedTransaction->transaction->is_parent,
    //                            'is_mixed' => $receivedTransaction->transaction->is_mixed,
    //                            'created_by' => $receivedTransaction->transaction->created_by,
    //                            'is_local' => FALSE,
    //                            'transaction_type' => 2,
    //                            'local_code' => $receivedTransaction->transaction->local_code,
    //                            'transaction_status' => 'sent',
    //                            'reference_id' => $receivedTransId,
    //                            'is_server_id' => 1,
    //                            'is_new' => 0,
    //                            'sent_to' => 0,
    //                            'is_sent' => 1,
    //                ]);
    //                array_push($sentCofffeeArray, $processTransaction->transaction_id);
    //                $transactionLog = TransactionLog::create([
    //                            'transaction_id' => $processTransaction->transaction_id,
    //                            'action' => 'sent',
    //                            'created_by' => $receivedTransaction->transaction->created_by,
    //                            'entity_id' => $receivedTransaction->transaction->center_id,
    //                            'center_name' => '',
    //                            'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
    //                            'type' => 'coffee_drying',
    //                ]);
    //                $transactionContainers = $receivedTransaction->transactionDetails;
    //                foreach ($transactionContainers as $key => $transactionContainer) {
    //                    TransactionDetail::create([
    //                        'transaction_id' => $processTransaction->transaction_id,
    //                        'container_number' => $transactionContainer->container_number,
    //                        'created_by' => $userId,
    //                        'is_local' => FALSE,
    //                        'container_weight' => $transactionContainer->container_weight,
    //                        'weight_unit' => 'kg',
    //                        'center_id' => $receivedTransaction->transaction->center_id,
    //                        'reference_id' => $receivedTransaction->transaction->reference_id,
    //                    ]);
    //                }
    //                TransactionDetail::where('transaction_id', $receivedTransId)->update(['container_status' => 1]);
    //                $transactionMeta = $receivedTransaction->transactionMeta;
    //                foreach ($transactionMeta as $key => $transactionMe) {
    //                    MetaTransation::create([
    //                        'transaction_id' => $processTransaction->transaction_id,
    //                        'key' => $transactionMe->key,
    //                        'value' => $transactionMe->value,
    //                    ]);
    //                }
    //            }
    //        }
    //        $allTransactions = array();
    //        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $sentCofffeeArray)->with('transactionDetail', 'log', 'meta')->get();
    //        foreach ($currentlyReceivedCoffees as $key => $transaction) {
    //            $transactionDetail = $transaction->transactionDetail;
    //
    //            $transactionDetailArray = array();
    //            foreach ($transactionDetail as $key => $transactionDet) {
    //                $transactionDet->is_local = FALSE;
    //                $transactionDet->update_meta = FALSE;
    //                array_push($transactionDetailArray, $transactionDet);
    //            }
    //            $transactionMeta = $transaction->meta;
    //            $transaction->center_id = $transaction->log->entity_id;
    //            $transaction->center_name = $transaction->log->center_name;
    //            $transaction->is_sent = 0;
    //            $transaction->makeHidden('transactionDetail');
    //            $transaction->makeHidden('log');
    //            $transaction->makeHidden('meta');
    //            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetailArray, 'transactionMeta' => $transactionMeta];
    //            array_push($allTransactions, $data);
    //        }
    //        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.SENT_COFFEE"), $allTransactions);
    //    }


    function environmentList(Request $request)
    {

        $environments = Environment::Select('environment_id', 'environment_name')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $environments);
    }
}
