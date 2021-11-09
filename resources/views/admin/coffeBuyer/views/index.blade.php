<div class="row ml-2">
    <div class="col-md-12 p-0">

        <div class="card shadow-none">
            <div class="table-responsive text-uppercase letter-spacing-2 coffee_buyeers_table">

                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0 text-left">
                                <div class="text-uppercase pl-0">
                                    Coffee Buying Managers
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="border-0"></th>
                            <th class="border border-dark font-weight-lighter">Name</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">First Purchase</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">Last Purchase</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">City</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                        <tr>
                            @if ($coffeeBuyerManger->picture_id == null)
                            <td class="border-0"> <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}" alt="">
                            </td>
                            @else
                            <td class="border-0"> <img class="famerimg" src="{{ asset('public/storage/images/' . $coffeeBuyerManger->image) }}" alt=""></td>
                            @endif
                            <td class="border border-dark border-top-0">{{ $coffeeBuyerManger->first_name }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->first_purchase }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->last_purchase }}</td>
                            <td class="border border-dark border-left-0 border-top-0">-</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0">
                                <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                    Specialty
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="border border-dark font-weight-lighter">CHREEY BOUGHT</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                        <tr>
                            <td class="border border-dark border-top-0">{{ $coffeeBuyerManger->special_weight }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->special_price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0">
                                <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                    Non Specialty
                                </div>
                            </th>
                        </tr>
                        <th class="border border-dark font-weight-lighter"> <span>DRY COFFEE</span> BOUGHT</th>
                        <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>
                        <th class="border border-dark border-left-0 font-weight-lighter">Total Price Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                        <tr>
                            <td class="border border-dark border-top-0">{{ $coffeeBuyerManger->non_special_weight }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->non_special_price }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->special_price + $coffeeBuyerManger->non_special_price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0 invisible">
                                <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                    View Details
                                </div>
                            </th>
                        </tr>
                        <th class="border border-dark font-weight-lighter">View Details</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                        <tr>
                            <td class="border border-dark border-top-0"> <a href="{{ route('coffeBuyer.profile', $coffeeBuyerManger) }}"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>

        <div class="card shadow-none">
            <div class="table-responsive text-uppercase letter-spacing-2 coffee_buyeers_table">
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0 text-left">
                                <div class="text-uppercase pl-0">
                                    Coffee Buyers
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="border-0"></th>
                            <th class="border border-dark font-weight-lighter">Name</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">First Purchase</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">Last Purchase</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">City</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyers as $coffeeBuyer)
                        <tr>

                            @if ($coffeeBuyer->picture_id == null)
                            <td class="border-0"> <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}" alt="">
                            </td>
                            @else
                            <td class="border-0"> <img class="famerimg" src="{{ asset('public/storage/images/' . $coffeeBuyer->image) }}" alt=""></td>
                            @endif
                            <td class="border border-dark border-top-0">{{ $coffeeBuyer->first_name }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->first_purchase }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->last_purchase }}</td>
                            <td class="border border-dark border-left-0 border-top-0">-</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0">
                                <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                    Specialty
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="border border-dark font-weight-lighter">CHREEY BOUGHT</th>
                            <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyers as $coffeeBuyer)
                        <tr>
                            <td class="border border-dark border-top-0">{{ $coffeeBuyer->special_weight }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->special_price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>
                        <tr>
                            <th colspan="12" class="border-0 px-0">
                                <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                    Non Specialty
                                </div>
                            </th>
                        </tr>
                        <th class="border border-dark font-weight-lighter"> <span>DRY COFFEE</span> BOUGHT</th>
                        <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>
                        <th class="border border-dark border-left-0 font-weight-lighter">Total Price Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyers as $coffeeBuyer)
                        <tr>

                            <td class="border border-dark border-top-0">{{ $coffeeBuyer->non_special_weight }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->non_special_price }}</td>
                            <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->special_price + $coffeeBuyer->non_special_price }}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                    <thead>

                        <tr>
                            <th colspan="12" class="border-0 px-0 invisible">
                                <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                    View Details
                                </div>
                            </th>
                        </tr>
                        <th class="border border-dark font-weight-lighter">View Details</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyers as $coffeeBuyer)
                        <tr>
                            <td class="border border-dark border-top-0"> <a href="{{ route('coffeBuyer.profile', $coffeeBuyer) }}"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
