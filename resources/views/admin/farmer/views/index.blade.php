<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
<div class="table-responsive">
    <table class="table" id="myTable">
        <thead>
            <tr style="font-size:13px;">
                <th></th>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>First Purchase</th>
                <th>Last Purchase</th>
                <th>Governorate</th>
                <th>Region</th>
                <th>Village</th>
                <th>Quantity</th>
                <th>Coffe Bought</th>
                <th>Price Paid
                </th>
                <th>Reward</th>
                <th>Money Owed</th>
                <th>Cupping Score</th>
                <th>Cup Profile</th>

                <th>View Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($farmers as $farmer)
            <tr>
                @if ($farmer->picture_id == null)
                    <td class="border-0"> <img class="famerimg mr-2"
                            src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                            alt="">
                    </td>
                @else
                    <td class="border-0"> <img class="famerimg mr-2"
                            src="{{ Storage::disk('s3')->url('images/' . $farmer->image) }}"
                            alt=""></td>
                @endif

                <!-- <td class="border border-dark border-top-0">{{ $farmer->farmer_id }}</td> -->
                <td class="border border-dark border-top-0">
                    {{ $farmer->farmer_name }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->farmer_code }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->first_purchase }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->last_purchase }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->governerate_title }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->region_title }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->village_title }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ number_format($farmer->quantity) }}</td>
                @if ($farmer->price_per_kg == null || $farmer->price_per_kg == 0)
                    <td class="border border-dark border-left-0 border-top-0">
                        {{ number_format($farmer->price * $farmer->quantity) }}</td>
                @else
                    <td class="border border-dark border-left-0 border-top-0">
                        {{ number_format($farmer->price_per_kg * $farmer->quantity) }}
                    </td>
                @endif
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->paidprice }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->reward }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->id }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->cupping_score }}</td>
                <td class="border border-dark border-left-0 border-top-0">
                    {{ $farmer->cup_profile }}</td>
                <td class="border border-dark border-left-0 border-top-0"> <a
                        href="{{ route('farmer.profile', $farmer) }}"><i
                            class="fas fa-eye"></i></a></td>


            </tr>

        @endforeach


        </tbody>

    </table>
</div>
