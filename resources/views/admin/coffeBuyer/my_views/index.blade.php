<div class="row ml-2" >
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4"> <b>Coffe Buyer Manger</b> </div>
            <div class="col-md-2 ml-2"> <b>Special</b> </div>

            <div class="col-md-2"> <b>Non Special</b> </div>

        </div>
        <div class="row ">
            <div class="col-md-6">

                <table class="table table-bordered" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>First Purchase</th>
                            <th>Last Purchase</th>
                            <th>City</th>
                            <th class="gap"></th>
                            <th>CHREEY BOUGHT</th>
                            <th>PRICE PAID</th>
                            <th class="gap"></th>
                            <th> <span>DRY COFFEE</span> BOUGHT</th>
                            <th>PRICE PAID</th>
                            <th class="gap"></th>
                            <th>Total Price Paid</th>
                            <th>View Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                            <tr>

                                @if ($coffeeBuyerManger->picture_id == null)
                                    <td> <img class="famerimg"
                                            src="{{ asset('public/images/farmericon.png') }}"
                                            alt="">
                                    </td>
                                @else
                                    <td> <img class="famerimg"
                                            src="{{ asset('public/storage/images/' . $coffeeBuyerManger->image) }}"
                                            alt=""></td>
                                @endif
                                <td>{{ $coffeeBuyerManger->first_name }}</td>
                                <td>{{ $coffeeBuyerManger->first_purchase }}</td>
                                <td>{{ $coffeeBuyerManger->last_purchase }}</td>

                                <td>lahore</td>
                                <td class="gap"></td>
                                <td>{{ $coffeeBuyerManger->special_weight }}</td>
                                <td>{{ $coffeeBuyerManger->special_price }}</td>
                                <td class="gap"></td>
                                <td>{{ $coffeeBuyerManger->non_special_weight }}</td>
                                <td>{{ $coffeeBuyerManger->non_special_price }}</td>
                                <td class="gap"></td>

                                <td>{{ $coffeeBuyerManger->special_price + $coffeeBuyerManger->non_special_price }}
                                </td>

                                <td> <a
                                        href="{{ route('coffeBuyer.profile', $coffeeBuyerManger) }}"><i
                                            class="fas fa-eye"></i></a></td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <hr>

        <div class="row ">
            <div class="col-md-4 "> <b> Coffee Buyer</b></div>

            <div class="col-md-2"> <b>Special</b> </div>

            <div class="col-md-2"> <b>Non Special</b> </div>

            <div class="col-md-6">
                <table class="table table-bordered" style="font-size:14px;">
                    <thead>
                        <tr align="center">
                            <th></th>
                            <th>Name</th>
                            <th>First Purchase</th>
                            <th>Last Purchase</th>
                            <th>City</th>
                            <th class="gap"></th>
                            <th>CHREEY BOUGHT</th>
                            <th>PRICE PAID</th>
                            <th class="gap"></th>
                            <th> <span>DRY COFFEE</span> BOUGHT</th>
                            <th>PRICE PAID</th>
                            <th class="gap"></th>
                            <th>Total Price Paid</th>
                            <th>View Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyers as $coffeeBuyer)
                            <tr>

                                @if ($coffeeBuyer->picture_id == null)
                                    <td> <img class="famerimg"
                                            src="{{ asset('public/images/farmericon.png') }}"
                                            alt="">
                                    </td>
                                @else
                                    <td> <img class="famerimg"
                                            src="{{ asset('public/storage/images/' . $coffeeBuyer->image) }}"
                                            alt=""></td>
                                @endif
                                <td>{{ $coffeeBuyer->first_name }}</td>
                                <td>{{ $coffeeBuyer->first_purchase }}</td>
                                <td>{{ $coffeeBuyer->last_purchase }}</td>

                                <td>lahore</td>
                                <td class="gap"></td>
                                <td>{{ $coffeeBuyer->special_weight }}</td>
                                <td>{{ $coffeeBuyer->special_price }}</td>
                                <td class="gap"></td>
                                <td>{{ $coffeeBuyer->non_special_weight }}</td>
                                <td>{{ $coffeeBuyer->non_special_price }}</td>
                                <td class="gap"></td>

                                <td>{{ $coffeeBuyer->special_price + $coffeeBuyer->non_special_price }}
                                </td>

                                <td> <a href="{{ route('coffeBuyer.profile', $coffeeBuyer) }}"><i
                                            class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>