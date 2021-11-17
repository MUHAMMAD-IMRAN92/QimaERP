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
                        <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                                alt="">
                        </td>
                    @else
                        <td> <img class="famerimg"
                                src="{{ Storage::disk('s3')->url('images/' . $farmer->image) }}" alt="">
                        </td>
                    @endif
                    <td>{{ $farmer->farmer_id }}</td>
                    <td>{{ $farmer->farmer_name }}</td>
                    <td>{{ $farmer->farmer_code }}</td>
                    <td>{{ $farmer->first_purchase }}</td>
                    <td>{{ $farmer->last_purchase }}</td>
                    <td>{{ $farmer->governerate_title }}</td>
                    <td>{{ $farmer->region_title }}</td>
                    <td>{{ $farmer->village_title }}</td>
                    <td>{{ number_format($farmer->quantity) }}</td>
                    @if ($farmer->price_per_kg == null)
                        <td>{{ number_format($farmer->price * $farmer->quantity) }}</td>
                    @else
                        <td>{{ number_format($farmer->price_per_kg * $farmer->quantity) }}
                        </td>
                    @endif
                    <td>{{ $farmer->id }}</td>
                    <td>{{ $farmer->id }}</td>
                    <td>{{ $farmer->id }}</td>
                    <td>{{ $farmer->id }}</td>
                    <td> <a href="{{ route('farmer.profile', $farmer) }}"><i class="fas fa-eye"></i></a></td>


                </tr>

            @endforeach

        </tbody>

    </table>
</div>
