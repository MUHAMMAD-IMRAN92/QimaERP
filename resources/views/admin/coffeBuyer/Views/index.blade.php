<div class="row ml-2"   >
    <div class="col-md-12">
        <div class="row ">
            <div class="col-md-6">
                <caption> <b> Coffee Buying Manger</b></caption>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Name</td>
                            <td>First Purchase</td>
                            <td>Last Purchase</td>
                            <td>City</td>
                            <td class="gap"></td>
                            <td>CHREEY BOUGHT</td>
                            <td>PRICE PAID</td>
                            <td class="gap"></td>
                            <td> <span>DRY COFFEE</span> BOUGHT</td>
                            <td>PRICE PAID</td>
                            <td class="gap"></td>
                            <td>Firstname</td>
                            <td>View Details</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                            <tr>

                                @if ($coffeeBuyerManger->picture_id == null)
                                    <td> <img class="famerimg"
                                            src="{{ asset('public/dist/img/farmericon.png') }}"
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

                                <td>John</td>

                                <td> <a
                                        href="{{ route('coffeBuyer.profile', $coffeeBuyerManger) }}"><i
                                            class="fas fa-eye"></i></a></td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- <div class="col-md-2 ml-2">
                    <caption>Specialty</caption>
                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <td>CHREEY BOUGHT</td>
                                <td>PRICE PAID</td>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>Doe</td>

                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-md-2 mi-2">
                    <caption>Non-Specialty</caption>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td> <span>DRY COFFEE</span> BOUGHT</td>
                                <td>PRICE PAID</td>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>Doe</td>

                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-md-1 ml-2">
                    <caption>&nbsp;</caption>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Firstname</td>


                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>


                            </tr>

                        </tbody>
                    </table>
                </div> --}}
        </div>
        <hr>

        <div class="row ">
            <div class="col-md-6">
                <caption> <b> Coffee Buyer</b></caption>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Name</td>
                            <td>First Purchase</td>
                            <td>Last Purchase</td>
                            <td>City</td>
                            <td class="gap"></td>
                            <td>CHREEY BOUGHT</td>
                            <td>PRICE PAID</td>
                            <td class="gap"></td>
                            <td> <span>DRY COFFEE</span> BOUGHT</td>
                            <td>PRICE PAID</td>
                            <td class="gap"></td>
                            <td>Firstname</td>
                            <td>View Details</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coffeeBuyers as $coffeeBuyer)
                            <tr>

                                @if ($coffeeBuyer->picture_id == null)
                                    <td> <img class="famerimg"
                                            src="{{ asset('public/dist/img/farmericon.png') }}"
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

                                <td>John</td>

                                <td> <a href="{{ route('coffeBuyer.profile', $coffeeBuyer) }}"><i
                                            class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- <div class="col-md-2 ml-2">
                    <caption>Specialty</caption>
                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <td>CHREEY BOUGHT</td>
                                <td>PRICE PAID</td>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>Doe</td>

                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-md-2 mi-2">
                    <caption>Non-Specialty</caption>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td> <span>DRY COFFEE</span> BOUGHT</td>
                                <td>PRICE PAID</td>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>Doe</td>

                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-md-1 ml-2">
                    <caption>&nbsp;</caption>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Firstname</td>


                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>


                            </tr>

                        </tbody>
                    </table>
                </div> --}}
        </div>
    </div>
</div>