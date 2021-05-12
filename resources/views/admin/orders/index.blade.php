@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

<style type="text/css">
    .nav.nav-tabs {
        float: left;
        display: block;
        margin-right: 20px;
        border-bottom: 0;
        border-right: 1px solid #ddd;
        padding-right: 15px;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        background: #ccc;
    }

    .nav-tabs .nav-link.active {
        color: #495057;

        border-color: transparent !important;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0rem !important;
        border-top-right-radius: 0rem !important;
    }

    .tab-content>.active {
        display: block;
        /*background: #007bff;*/
        min-height: 165px;
    }

    .nav.nav-tabs {
        float: left;
        display: block;
        margin-right: 20px;
        border-bottom: 0;
        border-right: 1px solid transparent;
        padding-right: 15px;
    }

    #custom_tab li.nav-item a {
        color: #000;
        margin-bottom: 0px;
    }

    .batchnumber thead tr {
        border-bottom: 1px solid black;
    }

    .batchnumber tbody tr {
        border-bottom: 1px solid black;
    }

    .set-padding {
        padding: 10px;
    }

    .top-margin-set {
        margin-top: 10px;
    }
</style>
<script>
    $(document).ready(function() {
            $('#myTable').DataTable();
        });

</script>
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
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div id="app">
                <div class="row">
                    <div class="col-md-3" v-for="product in inventory">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">@{{ product.name }}</h5>
                                <p class="card-text">Weight: <b>@{{ product.weight }}</b> KG</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Create Customer</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" id="customer" name="customer" v-model="selectCustomerId">
                                    <option value="0" selected class="text-indigo">Create Customer</option>
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
                            <div class="col-md-12">
                                Create Orders
                            </div>
                            <div class="form-group col-md-2">
                                <label for="order_product">Products</label>
                                <select class="form-control" name="order_product" id="order_product"
                                    v-model="orderProduct">
                                    <option value="0" selected disabled>Select a Product</option>
                                    <option :value="product.id" v-for="product in products">
                                        @{{ product.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="order_is_special">Variant</label>
                                <select class="form-control" name="order_is_product" id="order_is_special"
                                    v-model="orderIsSpecial">
                                    <option value="0" selected>Reguler</option>
                                    <option value="1">Special</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="order_weight">Weight</label>
                                <input v-model="orderWeight" class="form-control" type="number" min="0"
                                    name="order_weight" id="order_weight">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="order_price">Price per KG</label>
                                <input v-model="orderPrice" class="form-control" type="number" min="0"
                                    name="order_price" id="order_price">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="order_total">Price</label>
                                <input :value="orderTotal" class="form-control" type="number" name="order_total"
                                    id="order_total" disabled>
                            </div>
                            <div class="form-group col-md-2">
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
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<script>
    // setTimeout(function(){
    //     document.getElementById('alert').remove();
    // }, 3000)
</script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script>
    const app = new Vue({
        el: '#app',
        data: {
            customers: [],
            selectCustomerId: 0,
            customer: {
                name: '',
                phone: '',
                email: '',
                address: ''
            },
            inventory: [],
            products: [],
            orderProduct: 0,
            orderIsSpecial: 0,
            orderWeight: 0,
            orderPrice: 0,
            orders: [],
            errors: []
        },
        methods: {
            getCustomers: function(){
                axios.get('/admin/customers')
                    .then(res => {
                        this.customers = res.data.customers;
                    });
            },
            getInventory: function(){
                axios.get('/admin/local_inventory')
                    .then(res => {
                        this.inventory = res.data.inventory;
                    });
            },
            getLocalProducts: function(){
                axios.get('/admin/local_products')
                    .then(res => {
                        this.products = res.data.products;
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
                    this.errors.push('Weight should be more than zero.')
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
            }
        },
        watch: {
            selectCustomerId: function(customerId){
                let customer = this.customers.find(customer => customer.id == customerId);

                if(customer){
                    this.customer = customer;
                } else {
                    this.customer = {
                        name: '',
                        phone: '',
                        email: '',
                        address: ''
                    };
                }
            }
        },
        computed: {
            newCustomer: function(){
                return this.selectCustomerId == 0;
            },
            orderTotal: function(){
                return this.orderWeight * this.orderPrice;
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