@extends('layouts.default')
@section('title', 'Add Container')
@section('content')
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Container</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Container</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">

              <div class="card-header">
                <h3 class="card-title">Add</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="{{URL::to('')}}/admin/addregion">
                
                {{--  @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </li>

                          @endforeach
                      </ul>
                      
                  </div>
              @endif --}}
                {{ csrf_field() }}
                <div class="card-body">
                  <div class="form-group">
                   <label for="country_name">Type</label>
                   
                    <select class="form-control input-add-inception" name="container_type">
                      <option value="1">Basket</option>
                      <option value="2">Drying Tables</option>
                      <option value="3">Special Process barrel</option>
                      <option value="4">Drying Machine (Future)</option>
                      <option value="5">Dry Coffee Bag</option>
                      <option value="6">Pre Defect removal Export Coffee (Size 1 and Size 2) bag</option>
                      <option value="7">Defect Free Export coffee (Size 1 and Size 2) bag</option>
                      <option value="8">Peaberry Coffee Bag</option>
                      <option value="9">Grade 2 Coffee (small and big beans)</option>
                      <option value="10">Grade 3 (defect) Coffee</option>
                      <option value="11">Grade 1 husk  Bag</option>
                      <option value="12">Grade 2 husk Bag</option>
                      <option value="13">Grade 3 husk bag</option>
                      <option value="14">5kg Vacuum Bag for export</option>
                      <option value="15">15kg Premium Bag for export</option>
                      <option value="16">10kg Shipping Box</option>
                      <option value="17">30kg Shipping Box</option>
                      <option value="18">Sample Bag 1</option>
                     
                    </select>
                  </div>
                    
                  <div class="form-group">
                    <label for="exampleInputPassword1">Capacity</label>
                    <input type="text" id="capacity" class="form-control" id="exampleInputPassword1" name="capacity" placeholder="Capacity" @error('capacity') is-invalid @enderror>
                    @error('capacity')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                 
                 </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

           
            </div>
            <!-- /.card -->

          </div>
         
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
@endsection