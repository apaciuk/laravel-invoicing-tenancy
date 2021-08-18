@extends('themes.default1.layouts.master')
@section('title')
Users
@stop

@section('content-header')
    <div class="col-sm-6">
        <h1>All Users</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">All Users</li>
        </ol>
    </div><!-- /.col -->
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <!-- Default box -->
            <div class="card card-secondary card-outline collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Advance Search</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fas fa-plus"></i></button>
                       
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open(['method'=>'get']) !!}

                    <div class="row">

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('company','Company Name') !!}
                            {!! Form::text('company',$request->company,['class' => 'form-control','id'=>'company']) !!}

                        </div>
                        <?php
                        $countries=DB::table('countries')->pluck('nicename','country_code_char2')->toarray();
                        ?>
                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('country','Country') !!}<br>
                            <select style="width:100%;" name="country" value= "Choose" onChange="getCountryAttr(this.value)" class="form-control select2" data-live-search="true" data-live-search-placeholder="Search" data-dropup-auto="false" data-size="10">
                                <option value="" style="">Choose</option>
                                @foreach($countries as $key=> $country)
                                    @if($key == $request->country)
                                        <option value={{$key}} selected>{{$country}}</option>
                                    @else
                                        <option value={{$key}}>{{$country}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                        {!! Form::label('industry','Industries') !!}<br>

                        <?php $old = ['agriculture_forestry'=>'Agriculture Forestry','safety_security_legal'=>'Safety Security Legal','business_information'=>'Business Information','finance_insurance'=>'Finance Insurance','gaming'=>'Gaming','real_estate_housing'=>'Real Estate Housing','health_services'=>'Health Services','education'=>'Education','food_hospitality'=>'Food Hospitality','personal_services'=>'Personal Services','transportation'=>'Transportation','construction_utilities_contracting'=>'Construction Utilities Contracting','motor_vehicle'=>'Motor Vehicle','animals_pets'=>'Animals & Pets','art_design'=>'Art & Design','auto_transport'=>'Auto & Transport','food_beverage'=>'Food & Beverage','beauty_fashion'=>'Beauty & Fashion','education_childcare'=>'Education & Childcare','environment_green_tech'=>'Environment & Green Tech','events_weddings'=>'Events & Weddings','finance_legal_consulting'=>'Finance, Legal & Consulting','government_municipal'=>'Government & Municipal','home_garden'=>'Home & Garden','internet_technology'=>'Internet & Technology','local_service_providers'=>'Local Service Providers','manufacturing_wholesale'=>'Manufacturing & Wholesale','marketing_advertising'=>'Marketing & Advertising','media_communication'=>'Media & Communication','medical_dental'=>'Medical & Dental','music_bands'=>'Music & Bands','non_profit_charity'=>'Non-Profit & Charity','real_estate'=>'Real Estate','religion'=>'Religion','retail_e-Commerce'=>'Retail & E-Commerce','sports_recreation'=>'Sports & Recreation','travel_hospitality'=>'Travel & Hospitality','other'=>'Other',];
                        $bussinesses =DB::table('bussinesses')->pluck('name','short')->toarray();
                        $acctManagers = DB::table('users')->where('position', 'account_manager')
                            ->pluck('first_name', 'id')->toArray();

                        $salesManagers = DB::table('users')->where('position', 'manager')
                            ->pluck('first_name', 'id')->toArray();
                        ?>
                        <!-- {!! Form::select('industry',['Choose',''=>DB::table('bussinesses')->pluck('name','short')->toarray(),'old'=>$old],null,['class' => 'form-control','data-live-search'=>'true','data-live-search-placeholder'=>'Search','data-dropup-auto'=>'false','data-size'=>'10','id'=>'industry']) !!} -->

                            <select name="industry"  style="width:100%;" class="form-control select2" data-live-search="true",data-live-search-placeholder="Search" data-dropup-auto="false"  data-size="10" id="industry">
                                <option value="">Choose</option>
                                @foreach($bussinesses as $key=>$bussines)
                                    @if($key == $request->industry)
                                        <option value={{$key}} selected>{{$bussines}}</option>
                                    @else
                                        <option value={{$key}}>{{$bussines}}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('Role','Role') !!}
                            {!! Form::select('role',[null => 'Choose']+ ['admin'=>'Admin', 'user'=>'user'], $request->role, ['class' => 'form-control','id'=>'role']) !!}
                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('Position','Position') !!}
                            {!! Form::select('position',[null => 'Choose']+ ['manager'=>'Sales Manager', 'account_manager'=>'Account Manager'], $request->position, ['class' => 'form-control','id'=>'position']) !!}
                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('reg_from','Registered From') !!}
                            <div class="input-group date" id="reservationdate_from" data-target-input="nearest">
                                <input type="text" name="reg_from" class="form-control datetimepicker-input" autocomplete="off" value="{!! $request->reg_from !!}" data-target="#reservationdate_from"/>

                                <div class="input-group-append" data-target="#reservationdate_from" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('from','Registered Till') !!}
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="reg_till" class="form-control datetimepicker-input" autocomplete="off" value="{!! $request->reg_till !!}" data-target="#reservationdate"/>

                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>


                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('from','Users for Account Manager') !!}
                            <select name="actmanager"  class="form-control selectpicker" data-live-search="true",data-live-search-placeholder="Search" data-dropup-auto="false"  data-size="10" id="actmanager" >
                                <option value="">Choose</option>
                                @foreach($acctManagers as $key=>$acct)
                                    @if($key == $request->actmanager)
                                        <option value={{$key}} selected>{{$acct}}</option>
                                    @else
                                        <option value={{$key}}>{{$acct}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <!-- first name -->
                            {!! Form::label('from','Users for Sales Manager') !!}
                            <select name="salesmanager"  class="form-control selectpicker" data-live-search="true",data-live-search-placeholder="Search" data-dropup-auto="false"  data-size="10" id="salesmanager" >
                                <option value="">Choose</option>
                                @foreach($salesManagers as $key=>$sales)
                                    @if($key == $request->salesmanager)
                                        <option value={{$key}} selected>{{$sales}}</option>
                                    @else
                                        <option value={{$key}}>{{$sales}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                </div>
                <!-- /.card-body -->
                    <button name="Search" type="submit"  class="btn btn-secondary"><i class="fa fa-search"></i>&nbsp;{!!Lang::get('Search')!!}</button>
                    &nbsp;
                    <a href="{!! url('clients') !!}" id="reset" class="btn btn-secondary"><i class="fas fa-sync-alt"></i>&nbsp;{!!Lang::get('Reset')!!}</a>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>


<div class="card card-secondary card-outline">

    <div class="card-header">
        <h3 class="card-title">Users</h3>

        <div class="card-tools">
            <a href="{{url('clients/create')}}" class="btn btn-default btn-sm pull-right"><span class="fas fa-plus"></span>&nbsp;Create New User</a>


        </div>
    </div>

    <div id="response"></div>

    <div class="card-body table-responsive">
        <div class="row">

            <div class="col-md-12">
                <table id="user-table" class="table display " cellspacing="0" width="100%" styleClass="borderless">
                 <button  value="" class="btn btn-secondary btn-sm btn-alldell" id="bulk_delete"><i class= "fa fa-trash"></i>&nbsp;&nbsp;Suspend Selected Users</button><br /><br />
                    <thead><tr>
                         <th class="no-sort"><input type="checkbox" name="select_all" onchange="checking(this)"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Country</th>
                            <th>Registered on</th>
                             <th>Status</th>
                            <th>Action</th>
                        </tr></thead>
                     </table>


            </div>
        </div>

    </div>
<div id="gif"></div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
        <!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
                <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script> 

    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
     $('ul.nav-sidebar a').filter(function() {
        return this.id == 'all_user';
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.id == 'all_user';
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

        $('#user-table').DataTable({

            processing: true,
            serverSide: true,
            stateSave: false,
            // if in request sort field is present, it will take that else default order
            // need to stringify the sort_order, else it will be considered as a javascript variable
            order: [[ {!! $request->sort_field ?: 5 !!}, {!! "'".$request->sort_order."'" ?: "'desc'" !!} ]],
            ajax: {
            "url":  '{!! route('get-clients',"company=$request->company&country=$request->country&industry=$request->industry&role=$request->role&position=$request->position&reg_from=$request->reg_from&reg_till=$request->reg_till&actmanager=$request->actmanager&salesmanager=$request->salesmanager" ) !!}',
               error: function(xhr) {
               if(xhr.status == 401) {
                alert('Your session has expired. Please login again to continue.')
                window.location.href = '/login';
               }
            }

            },

          
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch": "Search: ",
                "sProcessing": ' <div class="overlay dataTables_processing"><i class="fas fa-3x fa-sync-alt fa-spin" style=" margin-top: -25px;"></i><div class="text-bold pt-2">Loading...</div></div>'
                   
            },
            columnDefs: [
                {
                    targets: 'no-sort',
                    orderable: false,
                    order: []
                }
            ],
            columns: [
                {data: 'checkbox', name: 'checkbox'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'mobile', name: 'mobile'},
                {data: 'country', name: 'country'},
                {data: 'created_at', name: 'created_at'},
                {data: 'active', name: 'active'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip({
                        container : 'body'
                    });
                });
                $('.loader').css('display', 'none');
            },
            "fnPreDrawCallback": function (oSettings, json) {
                $('.loader').css('display', 'block');
            },
        });
    </script>


@stop

@section('icheck')
<script>
    $(document).ready(function() {
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    });
   function checking(e){

          $('#user-table').find("td input[type='checkbox']").prop('checked', $(e).prop('checked'));
     }


     $(document).on('click','#bulk_delete',function(){
      var id=[];
      if (confirm("Are you sure you want to suspend this user?"))
        {
            $('.user_checkbox:checked').each(function(){
              id.push($(this).val())
            });
            if(id.length >0)
            {
               $.ajax({
                      url:"{!! Url('clients-delete') !!}",
                      method:"delete",
                      data: $('#check:checked').serialize(),
                      beforeSend: function () {
                     '<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>'
                },
                success: function (data) {
                $('#gif').html('');
                $('#response').html(data);
                 setTimeout(function(){
                    window.location.reload();
                },5000);
                }
               })
            }
            else
            {
                alert("Please select at least one checkbox");
            }
        }

     });
   $('#reservationdate').datetimepicker({
       format: 'L'
   });
        $('#reservationdate_from').datetimepicker({
      format: 'L'
    })
</script>
@stop