<style>
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        width: 240px;
    }

    .color {

        width: 200px ;
        height: 80px fit-content;
        margin-left: 4px;
    }

    .blacklink a {
        color: black;
    }

    .anchor a {

        color: rgb(204, 38, 38);
        background-color: transparent;
        text-decoration: none;
        font-size: 15px;

    }

    .margin-left {
        margin-left: 306PX;
    }

    #farmerTable {
        border-collapse: separate;
        border-spacing: 3px;
    }

    #farmerTable tr td {
        padding: unset !important;
    }

    .txt-size {
        font-size: 12px;
    }

</style>
<div class="row ml-2">
    <div class="col-sm-1 color bg-danger">
        <h3 style="font-size: 16px !important">{{ $buyer->first_purchase }}</h3>
        <p>First Purchase</p>
    </div>
    <div class="col-sm-1 color bg-primary">
        <h3 style="font-size: 16px !important">{{ $buyer->last_purchase }}</h3>

        <p>Last Purchase</p>
    </div>
  
    <div class="col-sm-1 color bg-info">
        <h3 style="font-size: 16px !important">{{ number_format($buyer->sum) }}</h3>

        <p>Quantity</p>
    </div>
    <div class="col-sm-1 color bg-secondary ">
        <h3 style="font-size: 16px !important">{{number_format($buyer->price) }}</h3>

        <p>yer total coffee purchased</p>
    </div>
    <div class="col-sm-1 color bg-dark"></div>
    <div class="col-sm-1 color bg-danger"></div>
    <div class="col-sm-1 color bg-success"></div>

</div>
