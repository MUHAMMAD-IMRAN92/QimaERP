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
                    <div class="col-md-12">
                        <form action="">
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="customer">Select a Customer</label>
                                        <select class="form-control" id="customer" name="customer">
                                            <option :value="customer.id" v-for="customer in customers">@{{ customer.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
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
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script>
    const app = new Vue({
        el: '#app',
        data: {
            customers: [],
            selectCustomerId: 0
        },
        methods: {
            getCustomers : function(){
                fetch('/admin/customers')
                    .then(res => res.json())
                    .then(data => {
                        console.log(data.customers);
                        this.customers = data.customers;
                    })
            }
        },
        created(){
            this.getCustomers();
        }
    });
</script>
@endsection