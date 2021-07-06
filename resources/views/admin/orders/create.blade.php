@extends('layouts.default')
@section('title', 'Create Order')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b>Create Order</b></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div id="app" v-cloak>
                <div class="row">
                    <div class="col-md-2" v-for="product in inventory" :key="product.id">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">@{{ product.name }}</h5>
                                <div class="card-text">Regular Weight: <br> <b>@{{ product.regular_weight }}</b> KG
                                </div>
                                <p class="card-text mt--1">Special Weight: <br> <b>@{{ product.special_weight }}</b> KG
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row" v-if="serverErrors.length">
                    <div class="col-md-6">
                        <div class="alert alert-danger" role="alert" v-for="error in serverErrors">
                            @{{ error }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Customer</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" id="customer" name="customer" v-model="selectCustomerId">
                                    <option value="0" selected class="text-indigo">New Customer</option>
                                    <option :value="customer.id" v-for="customer in customers">
                                        @{{ customer.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input v-model="customer.name" v-on:input="resetCustomer()" type="text"
                                    class="form-control" placeholder="Name" required :disabled="!newCustomer">
                            </div>
                            <div class="col-md-3">
                                <input v-model="customer.phone" v-on:input="resetCustomer()" type="text"
                                    class="form-control" placeholder="phone" required :disabled="!newCustomer">
                            </div>
                            <div class="col-md-3">
                                <input v-model="customer.email" v-on:input="resetCustomer()" type="email"
                                    class="form-control" placeholder="email" :disabled="!newCustomer">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <input v-model="customer.address" v-on:input="resetCustomer()" type="text"
                                    class="form-control" placeholder="Address" required :disabled="!newCustomer">
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="form-group col-md-3">
                                <label for="order_product">Products</label>
                                <select class="form-control" name="order_product" id="order_product"
                                    v-model="orderProduct">
                                    <option value="0" selected disabled>Select a Product</option>
                                    <option :value="product.id" v-for="product in products" :key="product.id">
                                        @{{ product.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="order_is_special">Variant</label>
                                <select class="form-control" name="order_is_product" id="order_is_special"
                                    v-model="orderIsSpecial">
                                    <option value="0" selected>Regular</option>
                                    <option value="1">Special</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="order_weight">Weight</label>
                                <input v-on:change="checkTenMulti" v-model="orderWeight" class="form-control"
                                    type="number" min="0" step="10" name="order_weight" id="order_weight">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="order_price">Price per KG</label>
                                <input v-model="orderPrice" class="form-control" type="number" min="0"
                                    name="order_price" id="order_price">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="order_total">Total</label>
                                <input :value="orderTotal" class="form-control" type="number" name="order_total"
                                    id="order_total" disabled>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="order_total">Action</label>
                                <button @click="saveOrder()" class="btn btn-primary form-control">Add</button>
                            </div>
                        </div>

                        <p v-if="errors.length" class="text-danger">
                            <b>Please correct the following error(s):</b>
                            <ul>
                                <li v-for="error in errors">@{{ error }}</li>
                            </ul>
                        </p>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Variant</th>
                                            <th scope="col">Weight</th>
                                            <th scope="col">Price per KG</th>
                                            <th scope="col">Total</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(order, index) in orders" :key="index">
                                            <th scope="row">@{{ index + 1 }}</th>
                                            <td>@{{ findProductName(order.productId) }}</td>
                                            <td>@{{ getVariant(order.isSpecial) }}</td>
                                            <td>@{{ order.weight }}</td>
                                            <td>@{{ formatNumber(order.price) }}</td>
                                            <td>@{{ formatNumber(order.total) }}</td>
                                            <td>
                                                <i @click="removeOrder(index)" class="fa fa-trash text-danger" style="cursor: pointer;"></i>
                                            </td>
                                        </tr>
                                        <tr v-if="!orders.length">
                                            <td colspan="6" class="text-center">No order items yet.</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td>Grand Total</td>
                                            <td>@{{ formatNumber(grandTotal) }}</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5"></div>
                            <div class="col-md-2">
                                <button @click="createOrder()" class="btn btn-success">Create Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<script src="{{ asset('/public/js/vue-toast-notification.js') }}"></script>
<script src="{{ asset('/public/js/vue.js') }}"></script>
<script>
    Vue.use(VueToast);
    const app = new Vue({
        el: '#app',
        data: {
            customers: [],
            selectCustomerId: 0,
            customer: {
                name: null,
                phone: null,
                email: null,
                address: null
            },
            inventory: [],
            products: [],
            orderProduct: 0,
            orderIsSpecial: 0,
            orderWeight: 0,
            orderPrice: 0,
            orders: [],
            errors: [],
            csrfToken: '{{ csrf_token() }}',
            serverErrors: [],
            toastOptions: {
                position: 'top-right',
                duration: 10000
            }
        },
        methods: {
            resetAll: function(){
                this.selectCustomerId = 0;
                this.customer = {
                    name: null,
                    phone: null,
                    email: null,
                    address: null
                };
                this.orderProduct = 0;
                this.orderIsSpecial = 0;
                this.orderWeight = 0;
                this.orderPrice = 0;
                this.orders = [];
                this.errors = [];
                this.serverErrors = [];
            },
            getCustomers: function(){
                fetch('/admin/customers')
                    .then(res => res.json())
                    .then(data => {
                        this.customers = data.customers;
                    })
            },
            getInventory: function(){
                fetch('/admin/local_inventory')
                    .then(res => res.json())
                    .then(data => {
                        this.inventory = data.inventory;
                    });
            },
            getLocalProducts: function(){
                fetch('/admin/local_products')
                    .then(res => res.json())
                    .then(data => {
                        this.products = data.products;
                    })
            },
            resetCustomer: function(){
                this.selectCustomerId = 0;
            },
            saveOrder: function(){
                this.errors = [];

                if(this.orderProduct == 0){
                    this.errors.push('Please select a product');
                }

                if(this.orderWeight == 0){
                    this.errors.push('Weight should be not more than zero.');
                }

                if(this.orderWeight % 10){
                    this.errors.push('Weight should be mutiple of 10 like 10, 20, 60 etc.');
                }

                if(this.orderPrice == 0){
                    this.errors.push('Price should be not more than zero.');
                }

                if(!this.errors.length){
                    let order = {
                        productId: this.orderProduct,
                        isSpecial: this.orderIsSpecial,
                        weight: this.orderWeight,
                        price: this.orderPrice,
                        total: this.orderTotal
                    };

                    this.orders.push(order);

                    this.orderProduct = 0;
                    this.orderIsSpecial = 0;
                    this.orderWeight = 0;
                    this.orderPrice = 0;
                }
            },
            findProduct: function(productId){
                return this.products.find(product => product.id == productId);
            },
            findProductName: function(productId){
                let product = this.products.find(product => product.id == productId);
                return product ? product.name : '';
            },
            getVariant: function(isSpecial){
                return isSpecial ? 'Special' : 'Regular';
            },
            removeOrder: function(index){
                this.orders.splice(index, 1);
            },
            clearErrors: function(){
                this.errors = [];
                this.serverErrors = [];
            },
            createOrder: function(){
                this.errors = [];

                if(this.selectCustomerId == 0){
                    if(!this.customer.name){
                        this.errors.push('Customer name is required.');
                    }

                    if(!this.customer.phone){
                        this.errors.push('Customers phone is required.');
                    }
                    if(!this.customer.address){
                        this.errors.push('Customers address is required.');
                    }
                }

                if(!this.orders.length){
                    this.errors.push('Your order is empty');
                }

                if(!this.errors.length){
                    let body = JSON.stringify({
                            customerId: this.selectCustomerId,
                            customer: this.customer,
                            orders: this.orders
                        });
                    console.log(body);
                    console.log(this.csrfToken);
                    fetch('/admin/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({
                            customerId: this.selectCustomerId,
                            customer: this.customer,
                            orders: this.orders
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.errors){
                            Vue.$toast.error(data.message, this.toastOptions);
                            this.serverErrors = data.errors;
                            setTimeout(() => {
                                this.serverErrors = [];
                            }, 5000);
                        } else {
                            this.resetAll();
                            Vue.$toast.success(data.message, this.toastOptions);
                        }
                    })
                }
            },
            checkTenMulti: function(event){
                let value = event.target.value;
                if(value % 10){
                    this.errors.push('Weight should be mutiple of 10 like 10, 20, 60 etc.');
                } else {
                    this.errors = [];
                }
            },
            formatNumber: function(num) {
                // return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                var formatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                });

                return formatter.format(num).substring(1);
            }
        },
        watch: {
            selectCustomerId: function(customerId){
                let customer = this.customers.find(customer => customer.id == customerId);

                if(customer){
                    this.customer = customer;
                } else {
                    this.customer = {
                        name: null,
                        phone: null,
                        email: null,
                        address: null
                    };
                }
            }
        },
        computed: {
            newCustomer: function(){
                return this.selectCustomerId == 0;
            },
            orderTotal: function(){
                return +(this.orderWeight * this.orderPrice).toFixed(2);
            },
            grandTotal: function() {
                return this.orders.reduce((accumulator, order) => {
                    return accumulator + order.total;
                }, 0)
            }
        },
        created(){
            this.getCustomers();
            this.getInventory();
            this.getLocalProducts();
        }
    });
</script>
@endsection