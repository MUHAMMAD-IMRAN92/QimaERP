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
<div class="row ml-2">
    <div class="col-sm-1 color bg-danger">
        <h3>{{ $governorates->count() }}</h3>
        <p>Governorate</p>
    </div>
    <div class="col-sm-1 color bg-primary">
        <h3>{{ $regions->count() }}</h3>

        <p>Regions</p>
    </div>
    <div class="col-sm-1 color bg-warning">
        <h3>{{ $villages->count() }}</h3>

        <p>Villages </p>
    </div>
    <div class="col-sm-1 color bg-primary">
        <h3>{{ $farmers->count() }}</h3>

        <p>Farmers </p>
    </div>
    <div class="col-sm-1 color bg-dark">
        <h3>{{ $total_coffee }}</h3>
        <p>Total Coffee </p>
    </div>
    <div class="col-sm-1 color bg-danger">
        <h3>{{ $totalPrice }}</h3>
        <p>Yer Coffee Purchased</p>
    </div>
    <div class="col-sm-1 color bg-warning"></div>
    <div class="col-sm-1 color bg-info"></div>
    <div class="col-sm-1 color bg-dark"></div>
</div>
