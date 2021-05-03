<style>
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        width: 240px;
    }

    .color {

        width: 200px;
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
<div class="row ml-2" >
    <div class="col-sm-1 color bg-danger">
        <h4>{{ count($governorates) }}</h4>
        <p>Governorate</p>
    </div>
    <div class="col-sm-1 color bg-primary">
        <h4>{{ count($regions) }}</h4>

        <p>Regions</p>
    </div>
    <div class="col-sm-1 color bg-warning">
        <h4>{{ count($villages) }}</h4>

        <p>Villages </p>
    </div>
    <div class="col-sm-1 color bg-info">
        <h4>{{ $total_coffee }}</h4>
        <p>Quantity </p>
    </div>
    <div class="col-sm-1 color bg-dark">
        <h4>{{ $totalPrice }}</h4>
        <p>yer coffee bought </p>
    </div>
    <div class="col-sm-1 color bg-danger">
       
    </div>
    <div class="col-sm-1 color bg-warning"></div>
    <div class="col-sm-1 color bg-info"></div>
    <div class="col-sm-1 color bg-dark"></div>
</div>
