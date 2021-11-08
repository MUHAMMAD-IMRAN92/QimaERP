<div class="row ml-2 text-uppercase mb-2">
    <strong>
        <b>COFFEE PURCHASE TRANSACTIONS</b>
    </strong>
</div>
@foreach ($newTransactions as $newTransaction)
    @php
        $user = App\User::find($newTransaction->created_by);
        if ($user) {
            $name = $user->fisrt_name . $user->last_name;
        }
        $batch = explode('-', $newTransaction->batch_number);
        $gov = array_shift($batch);
        $reg = array_shift($batch);
        $regionCode = $gov . '-' . $reg;
        $region = App\Region::where('region_code', $regionCode)->first();
        if ($region) {
            $regionName = $region->region_title;
        }
        // echo $regionName ;
    @endphp
    <p class="ml-2 letter-spacing-1 btn-color-darkRed">{{ $newTransaction->created_at }}/
        {{ $name }}
        /
        {{ $regionName }}/
        {{ round($newTransaction->details->sum('container_weight'), 2) }} </p>
@endforeach
