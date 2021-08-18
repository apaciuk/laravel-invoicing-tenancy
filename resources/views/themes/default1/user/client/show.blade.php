@extends('themes.default1.layouts.master')
@section('title')
User
@stop
@section('content-header')
    <div class="col-sm-6">
        <h1>User Details</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{url('clients')}}"> All Users</a></li>
            <li class="breadcrumb-item active">View User</li>
        </ol>
    </div><!-- /.col -->

@stop
<style type="text/css">
    .read-more-show{
      cursor:pointer;
      color: #ed8323;
    }
    .read-more-hide{
      cursor:pointer;
      color: #ed8323;
    }
    .hide_content{
      display: none;
    }
    .btn-xs{
        padding:.300rem!important;
    }
 
}
</style>
@section('content')

    <!-- Widget: user widget style 1 -->
    <div class="card card-widget widget-user">

        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-info">


            <h3 class="widget-user-username">{{ucfirst($client->first_name)}}  {{ucfirst($client->last_name)}}</h3>
            {{($client->email)}}



        </div>

        <div class="widget-user-image">
            <img class="img-circle" src="{{ $client->profile_pic }}" alt="User Avatar">
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">{{currency_format($invoiceSum,$code=$currency)}}</h5>
                        <span class="description-text">INVOICE TOTAL</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">{{currency_format($amountReceived,$code=$currency)}}</h5>
                        <span class="description-text">PAID</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    @if($pendingAmount)
                    <div class="description-block" style="color:red;">
                        @else
                         <div class="description-block">
                            @endif
                        <h5 class="description-header" >{{currency_format($pendingAmount,$code=$currency)}}</h5>
                        <span class="description-text">BALANCE</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>

    </div>







    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">

                <h3 class="widget-user-username">
                    <a class="btn btn-sm btn-secondary" href="{{url('invoice/generate?clientid='.$client->id)}}"> <i class="fa fa-credit-card"></i> &nbsp;{{Lang::get('message.create-invoice')}}</a>
                    <a class="btn btn-sm btn-secondary" href="{{url('newPayment/receive?clientid='.$client->id)}}"> <i class="fa fa-bars"></i> &nbsp;{{Lang::get('message.create-payment')}}</a>
                     <a class="btn btn-sm btn-secondary" href="{{url('clients/'.$client->id.'/edit')}}"> <i class="fas fa-edit"></i> Edit Details</a>
                    @if($is2faEnabled)
                        <button id="disable2fa" value="{{$client->id}}" class="btn btn-sm btn-secondary"><i class="fa fa-ban"></i>&nbsp;
                            Disable 2FA
                        </button>

                    @endif
                </h3>
            </h3>
        </div>
        <div class="card-body table-responsive">
            <div class="row">
                <div class="col-md-2 col-lg-2 col-sm-2">
                    <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab myTab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="invoice" data-toggle="pill" href="#activity" role="tab"  aria-selected="true">Invoice Detail</a>
                        <a class="nav-link" id="customer_detail" data-toggle="pill" href="#settings" role="tab"  aria-selected="false">{{Lang::get('message.customer_detail')}}</a>
                        <a class="nav-link" id="payment" data-toggle="pill" href="#timeline" role="tab"  aria-selected="false">{{Lang::get('message.payment_detail')}}</a>
                        <a class="nav-link" id="orderdetail" data-toggle="pill" href="#order" role="tab" aria-controls="vert-tabs-settings" aria-selected="false">{{Lang::get('message.order_detail')}}</a>
                        <a class="nav-link" id="vert-tabs-comment-tab" data-toggle="pill" href="#comment" role="tab" aria-controls="vert-tabs-settings" aria-selected="false">{{Lang::get('message.comment')}}&nbsp;<span class="badge bg-green">{{count($comments)}}</span></a>

                    </div>
                </div>
                <div class="col-md-10 col-lg-10 col-sm-10">
                    <div class="tab-content" id="vert-tabs-tabContent">

                         <div id="response"></div>
                        <!--------------------Invoice detail tab starts here-------------------------------->

                        <div class="tab-pane text-left fade show active" id="activity" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
                            <table id="invoice-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                                <button  value="" class="btn btn-secondary btn-sm btn-alldell" id="bulk_invoice_delete"><i class= "fa fa-trash"></i>&nbsp;&nbsp;Delete Selected</button><br /><br />
                                <thead><tr>
                                    <th class="no-sort"><input type="checkbox" name="select_all" onchange="checkinginvoice(this)"></th>
                                    <th style="width: 100px;">Date</th>
                                    <th>Invoice No</th>
                                    <th>Order No</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr></thead>
                            </table>
                        </div>


                        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">

                        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
                        <script type="text/javascript">
                            $(document).ready(function(){//Stay On the Selected Tab after Page Refresh
                                $("#invoice").trigger('click');
                                $('a[data-toggle="pill"]').on('show.bs.tab',function(e){
                                    sessionStorage .setItem('activeTab',$(e.target).attr('href'));
                                });
                                var activeTab = sessionStorage .getItem('activeTab');
                                if(activeTab){
                                    $('#myTab a[href="' + activeTab + '"]').tab('show');
                                }
                                if(activeTab == "#timeline") {
                                    $('#payment').trigger('click');
                                } else if(activeTab == "#order") {
                                    $('#orderdetail').trigger('click');
                                } else if(activeTab == "#settings") {
                                    $('#customer_detail').trigger('click');
                                }  else if(activeTab == "#activity") {
                                    $('#invoice').trigger('click');
                                }  else if(activeTab == "#comment") {
                                    $('#comment').trigger('click');
                                }
                            });


                            $("#invoice").on('click',function(){
                                $('#orderdetail-table').DataTable().clear().destroy();
                                $('#payment-table').DataTable().clear().destroy();

                                $('#invoice-table').DataTable({

                                    processing: true,
                                    "bDestroy": true,
                                    serverSide: true,
                                    stateSave: false,
                                    order: [[ 0, "desc" ]],
                                    ajax: {
                                    "url":  '{{url("get-client-invoice/".$id)}}',
                                       error: function(xhr) {
                                       if(xhr.status == 401) {
                                        alert('Your session has expired. Please login again to continue.')
                                        window.location.href = '/login';
                                       }
                                    }

                                    },
                                   


                                    "oLanguage": {
                                        "sLengthMenu": "_MENU_ Records per page",
                                        "sSearch"    : "Search: ",
                                        "sProcessing": ' <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>'
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
                                        {data: 'date', name: 'date'},
                                        {data: 'invoice_no', name: 'invoice_no'},
                                        {data: 'order_no', name: 'order_no'},
                                        {data: 'total', name: 'total'},
                                        {data: 'paid', name: 'paid'},
                                        {data: 'balance', name: 'balance'},
                                        {data: 'status', name: 'status'},
                                        {data: 'action', name: 'action'}
                                    ],
                                    "fnDrawCallback": function( oSettings ) {
                                         $(function () {
                                              $('[data-toggle="tooltip"]').tooltip({
                                                container : 'body'
                                              });
                                            });
                                        $('.loader').css('display', 'none');
                                    },
                                    "fnPreDrawCallback": function(oSettings, json) {
                                        $('.loader').css('display', 'block');
                                    },
                                });
                            });

                        </script>
                        <script>
                         function checkinginvoice(e){
                          $('#invoice-table').find("td input[type='checkbox']").prop('checked', $(e).prop('checked'));
                         }

                         $(document).on('click','#bulk_invoice_delete',function(){
                              var id=[];
                              if (confirm("Are you sure you want to delete this?"))
                                {
                                    $('.invoice_checkbox:checked').each(function(){
                                      id.push($(this).val())
                                    });
                                    if(id.length >0)
                                    {
                                       $.ajax({
                                              url:"{!! Url('invoice-delete') !!}",
                                              method:"delete",
                                              data: $('#check:checked').serialize(),
                                              beforeSend: function () {
                                        $('#gif').show();
                                        },
                                        success: function (data) {
                                        $('#gif').hide();
                                        $('#response').html(data);
                                        setTimeout(function(){
                                            location.reload();
                                        },2000);
                                        }
                                       })
                                    }
                                    else
                                    {
                                        alert("Please select at least one checkbox");
                                    }
                                    }  
                                     });
                                    
                                    </script>




                 <!---------------------- Customer detail Tab starts here  -------------------------------->


                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                            <div class="row">
                                <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                                    <div class="row">



                                    <!-- /.description-block -->

                                </div>
                                <!-- /.col -->
                                <div class="card-body table-responsive">
                                    <div id="response"></div>
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.email')}}</b>: <span class="pull-right clientemail" style="float:right;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.company')}}</b>: <span class="pull-right clientcompanyname" style="float:right;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>User Name</b>: <span class="pull-right clientuser_name" style="float:right;"></span>
                                        </li>



                                        <li class="list-group-item">
                                            <b>Business</b>: <span class="pull-right clientbusiness" style="float:right;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.mobile')}}</b>: <span class="clientmobile" style="float:right;">  @if($client->mobile_code)<b>+</b>{{$client->mobile_code}}@endif{{$client->mobile}}</span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.address')}}</b>: <span class="pull-right clientaddress" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.town')}}</b>: <span class="pull-right clienttown" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.state')}}</b>: <span class="pull-right clientstate" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.country')}}</b>: <span class="pull-right clientcountry" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.zip')}}</b>: <span class="pull-right clientzip" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.role')}}</b>: <span class="pull-right clientrole" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>{{Lang::get('message.currency')}}</b>: <span class="pull-right clientcurrency" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>Company Type</b>: <span class="pull-right clientcompany" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>Company Size</b>: <span class="pull-right clientcomsize" style="float:right;"></span>
                                        </li>

                                        <li class="list-group-item">
                                            <b>IP</b>: <span class="pull-right clientip" style="float:right;"></span>
                                        </li>
                                        @if($client && $client->skype)
                                        <li class="list-group-item">
                                            <b>Skype</b>: <span class="pull-right clientskype" style="float:right;"></span>
                                        </li>
                                        @endif

                                        <?php $manager = $client->manager()->select('id', 'first_name', 'last_name')->first(); ?>
                                        <?php $actManager = $client->accountManager()->select('id', 'first_name', 'last_name')->first(); ?>
                                        @if($client && $manager)
                                            <li class="list-group-item">
                                                <b>Sales Manager</b>: <span class="pull-right clientmanager" style="float:right;"><a href="{{url('clients/'.$manager->id)}}">{{$manager->first_name}} {{$manager->last_name}}</a></span>
                                            </li>
                                        @endif
                                        @if($client && $actManager)
                                        <li class="list-group-item">
                                                <b>Account Manager</b>: <span class="pull-right actmanager" style="float:right;"><a href="{{url('clients/'.$actManager->id)}}">{{$actManager->first_name}} {{$actManager->last_name}}</a></span>
                                            </li>
                                        @endif
                                        <li class="list-group-item">
                                            <b>Referrer</b>: <span class="pull-right referrer" style="float:right;"></span>
                                        </li>


                                </ul>
                                </div>  
                            </div>
                            </div>
                            </div>


                            <!---------------------- Payment Detail tab---------------------------------------->
                        <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
                            <table id="payment-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                                <button  value="" class="btn btn-secondary btn-sm btn-alldell" id="bulk_payment_delete"><i class= "fa fa-trash"></i>&nbsp;&nbsp;Delete Selected</button><br /><br />
                                <thead><tr>
                                    <th class="no-sort"><input type="checkbox" name="select_all" onchange="checkingpayment(this)"></th>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr></thead>
                            </table>
                        </div>

                        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
                        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
                        <script type="text/javascript">
                            $("#payment").on('click',function(){
                                $('#orderdetail-table').DataTable().clear().destroy();
                                $('#invoice-table').DataTable().clear().destroy();
                                $('#payment-table').DataTable({

                                    processing: true,
                                    "bDestroy": true,
                                    serverSide: true,
                                    stateSave: false,
                                    order: [[ 0, "desc" ]],
                                    ajax: {
                                    "url":  '{{url("getPaymentDetail/".$client->id)}}',
                                       error: function(xhr) {
                                       if(xhr.status == 401) {
                                        alert('Your session has expired. Please login again to continue.')
                                        window.location.href = '/login';
                                       }
                                    }

                                    },
                                  

                                    "oLanguage": {
                                        "sLengthMenu": "_MENU_ Records per page",
                                        "sSearch"    : "Search: ",
                                        "sProcessing": ' <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>'
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
                                        {data: 'invoice_no', name: 'invoice_no'},
                                        {data: 'date', name: 'date'},
                                        {data: 'payment_method', name: 'payment_method'},
                                        {data: 'total', name: 'total'},
                                        {data: 'status', name: 'status'},
                                        {data: 'action', name: 'action'},
                                    ],
                                    "fnDrawCallback": function( oSettings ) {
                                         $(function () {
                                              $('[data-toggle="tooltip"]').tooltip({
                                                container : 'body'
                                              });
                                            });
                                        $('.loader').css('display', 'none');
                                    },
                                    "fnPreDrawCallback": function(oSettings, json) {
                                        $('.loader').css('display', 'block');
                                    },
                                });
                            });
                        </script>
                        <script>
                            function checkingpayment(e){

                                $('#payment-table').find("td input[type='checkbox']").prop('checked', $(e).prop('checked'));
                            }


                            $(document).on('click','#bulk_payment_delete',function(){
                                var id=[];
                                if (confirm("Are you sure you want to delete this?"))
                                {
                                    $('.payment_checkbox:checked').each(function(){
                                        id.push($(this).val())
                                    });
                                    if(id.length >0)
                                    {
                                        $.ajax({
                                            url:"{!! Url('payment-delete') !!}",
                                            method:"delete",
                                            data: $('#checkpayment:checked').serialize(),
                                            beforeSend: function () {
                                                $('#gif').show();
                                            },
                                            success: function (data) {
                                                $('#gif').hide();
                                                $('#response').html(data);
                                                setTimeout(function(){
                                                location.reload();
                                                },2000);
                                            }
                                        })
                                    }
                                    else
                                    {
                                        alert("Please select at least one checkbox");
                                    }
                                }

                            });

                        </script>



                        <!------------------------------------------Order Detail Tab--------------------------------- -->

                        <div class="tab-pane fade" id="order" role="tabpanel" aria-labelledby="vert-tabs-settings-tab">
                            <table id="orderdetail-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                                <button  value="" class="btn btn-secondary btn-sm btn-alldell" id="bulk_order_delete"><i class= "fa fa-trash"></i>&nbsp;&nbsp;Delete Selected</button><br /><br />
                                <thead><tr>
                                    <th class="no-sort"><input type="checkbox" name="select_all" onchange="checkingorder(this)"></th>
                                    <th>Order Date</th>
                                    <th>Product</th>
                                    <th>Order No</th>
                                    <th>Version</th>
                                    <th>Expiry</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr></thead>
                            </table>

                            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
                            <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
                            <script type="text/javascript">
                                $("#orderdetail").on('click',function(){
                                    $('#payment-table').DataTable().clear().destroy();
                                    $('#invoice-table').DataTable().clear().destroy();

                                    $('#orderdetail-table').DataTable({

                                        processing: true,
                                        "bDestroy": true,
                                        serverSide: true,
                                        stateSave: true,
                                        order: [[ 0, "desc" ]],
                                        ajax: {
                                        "url":  '{{url("getOrderDetail/".$client->id)}}',
                                           error: function(xhr) {
                                           if(xhr.status == 401) {
                                            alert('Your session has expired. Please login again to continue.')
                                            window.location.href = '/login';
                                           }
                                        }

                                        },

                                        "oLanguage": {
                                            "sLengthMenu": "_MENU_ Records per page",
                                            "sSearch"    : "Search: ",
                                            "sProcessing": ' <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>'
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
                                            {data: 'date', name: 'date'},
                                            {data: 'product', name: 'product'},
                                            {data: 'number', name: 'number'},
                                            {data: 'version', name: 'version'},
                                            {data: 'expiry', name: 'expiry'},
                                            {data: 'status', name: 'status'},
                                            {data: 'action', name: 'action'}
                                        ],
                                        "fnDrawCallback": function( oSettings ) {
                                             $(function () {
                                                  $('[data-toggle="tooltip"]').tooltip({
                                                    container : 'body'
                                                  });
                                                });
                                            $('.loader').css('display', 'none');
                                        },
                                        "fnPreDrawCallback": function(oSettings, json) {
                                            $('.loader').css('display', 'block');
                                        },
                                    });
                                });
                            </script>
                            <script>
                                function checkingorder(e){

                                    $('#orderdetail-table').find("td input[type='checkbox']").prop('checked', $(e).prop('checked'));
                                }


                                $(document).on('click','#bulk_order_delete',function(){
                                    var id=[];
                                    if (confirm("Are you sure you want to delete this?"))
                                    {
                                        $('.order_checkbox:checked').each(function(){
                                            id.push($(this).val())
                                        });
                                        if(id.length >0)
                                        {
                                            $.ajax({
                                                url:"{!! Url('orders-delete') !!}",
                                                method:"delete",
                                                data: $('#checkorder:checked').serialize(),
                                                beforeSend: function () {
                                                    $('#gif').show();
                                                },
                                                success: function (data) {
                                                    $('#gif').hide();
                                                    $('#response').html(data);
                                                    setTimeout(function(){
                                                    location.reload();
                                                    },2000);
                                                    }
                                                })
                                            }
                                            else
                                            {
                                                alert("Please select at least one checkbox");
                                            }
                                        }

                                });

                            </script>

                        </div>
                       
                        <div class="tab-pane fade" id="comment" role="tabpanel" aria-labelledby="vert-tabs-settings-tab">
                            <a href="#comment" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#createComment">
                                <span class="fas fa-plus"></span>&nbsp;&nbsp;{{Lang::get('message.add_comment')}}</a>
                            @include('themes.default1.user.client.createComment')

                            <br/> <br/> <br/>
                           
                                <!-- The timeline -->
                              

                                   
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    @forelse($comments as $comment)
                                     <div class="timeline">
                                        <?php
                                        $userId = $comment ->updated_by_user_id;
                                         $user = \App\User::where('id', $userId)->first();
                                         if(\App\User::onlyTrashed()->find($userId)) {
                                             $user = \App\User::onlyTrashed()->find($userId);
                                        }
                                        
                                       ?>
                                        <div>
                                      

                                            <i class="fas fa-comments bg-yellow" title="Posted by {{$user->role}}"></i>

                                            <div class="timeline-item">
                                                   @include('themes.default1.user.client.editComment')
                                                <h3 class="timeline-header"><a href="{{url('clients/'.$user->id)}}"><img src = "{{$user->profile_pic}}" class="img-circle img-bordered-sm" alt="User Image" width="35" height="35">&nbsp;{{$user->first_name}} {{$user->last_name}}</a> commented on
                                                 <b> {!! getDateHtml($comment->created_at) !!}</b>
                                             </h3>
                                                   
                                                 

                                                <div class="timeline-body" id="longdesc" >

                                                  
                                                        @if(strlen($comment->description) > 100)
                                                            {{substr($comment->description,0,100)}}
                                                            <span class="read-more-show hide_content">More&nbsp;<i class="fa fa-angle-down"></i></span>
                                                            <span class="read-more-content"> {{substr($comment->description,100,strlen($comment->description))}}
                                <span class="read-more-hide hide_content">Less <i class="fa fa-angle-up"></i></span> </span>
                                                    @else
                                                        {{$comment->description}}
                                                    @endif


                                                    <!-- {{$comment->description}} -->

                                                   
                                                    <br/>
                                                    </div>
                                                    <div id="response"></div>
                                                    <div class="timeline-footer">
                                                        <button type="submit" class="btn btn-secondary btn-sm btn-xs edit-comment"data-description="{{$comment->description}}" data-comment-id="{{$comment->id}}" data-user_id="{{$comment->user_id}}" data-admin_id="{{$comment->updated_by_user_id}}"><i class='fa fa-edit' style='color:white;'{!! tooltip('Edit') !!} </i></button>

                                                        <button type="submit" class="btn btn-danger btn-sm btn-xs"  id="deleteComment" data-id="{{$comment->id}}"> <i class='fa fa-trash' style='color:white;' {!! tooltip('Delete') !!} </i></button>

                                                    </div>
                                                </div>
                                        
                                    </div>
                                    @empty
                                        <tr>
                                            <td>
                                                {{Lang::get('message.no-comments')}}
                                            </td>
                                        </tr>
                                         </div>
                                    @endforelse
                               
                                
                             </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.card -->
    </div>

    <script type="text/javascript">

    $('ul.nav-sidebar a').filter(function() {
        return this.id == 'all_user';
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.id == 'all_user';
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
    // Hide the extra content initially, using JS so that if JS is disabled, no problemo:
                $('.read-more-content').addClass('hide_content')
                $('.read-more-show, .read-more-hide').removeClass('hide_content')
                // Set up the toggle effect:
                $('.read-more-show').on('click', function(e) {
                  $(this).next('.read-more-content').removeClass('hide_content');
                  $(this).addClass('hide_content');
                  e.preventDefault();
                });
                // Changes contributed by @diego-rzg
                $('.read-more-hide').on('click', function(e) {
                  var p = $(this).parent('.read-more-content');
                  p.addClass('hide_content');
                  p.prev('.read-more-show').removeClass('hide_content'); // Hide only the preceding "Read More"
                  e.preventDefault();
                });
    </script>









<div class='modal fade' id=editinvoice23>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                <h4 class='modal-title'>Invoice</h4>
            </div>
            <div class='modal-body'>
                body
            </div>
            <div class='modal-footer'>
                <button type=button id=close class='btn btn-default pull-left' data-dismiss=modal>Close</button>
                <input type=submit class='btn btn-primary' value=Save>
            </div>
        </div>
    </div>
</div>
  <div class="modal fade" id="disable2fa-modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Turn off Two-Factor-Authentication</h4>
             
            </div>
            
            <div class="modal-body">
               <div id="alertMessage"></div>
              
                  Turning off 2-Step Verification will remove the extra security on your account, and you’ll only use your password to sign in.
           
            </div>
            <div class="modal-footer">
              <button class="btn btn-danger pull-right float-right" id="turnoff2fa"><i class="fa fa-power-off"></i> TURN OFF</button>
              <button type="button" class="btn btn-default pull-left closeandrefresh" data-dismiss="modal"><i class="fa fa-times">&nbsp;&nbsp;</i>Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
        </div>

@stop

@section('icheck')

<script>
    $('.closeandrefresh').on('click',function(){
            location.reload();
        })
    $('#disable2fa').on('click',function(){
        $('#disable2fa-modal').modal('show');
    })
    $('#turnoff2fa').on('click',function(){
                $("#turnoff2fa").attr('disabled',true);
                $("#turnoff2fa").html("<i class='fas fa-circle-notch fa-spin'></i>Please Wait..");
                var user = $('#disable2fa').val();
                $.ajax({

                    url : "{{url('2fa/disable')}}",
                    method : 'post',
                    data : {
                        'userId' : user,
                    },
                    success: function(response){
                        $("#turnoff2fa").attr('disabled',false);
                        $("#turnoff2fa").html("<i class='fa fa-power-off'></i>TURNED OFF");
                         var result =  '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong></strong>'+response.message+'.</div>';
                            $('#alertMessage').html(result+ ".");
                            setTimeout(function(){
                                location.reload();
                            },2000);
                    },
                })
            })

    $('#deleteComment').on('click',function(){
            var id=[];
      if (confirm("Are you sure you want to delete this?"))
        {
          var id = $(this).attr('data-id');
          $.ajax({
                      url:"{!! route('comment-delete') !!}",
                      method:"delete",
                      data: {'comment-id':id},
                success: function (data) {
                $('#response').show();
                  var result =  '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Success! </strong>'+data.message+'!</div>';
                  document.getElementById('response').innerHTML = result;
                setTimeout(function(){
                location.reload();
                },2000);
                },error: function(data) {
                  $('#response').show();
                  var result =  '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Whoops! </strong>'+data.message+'!</div>';
                  document.getElementById('response').innerHTML = result;
                location.reload();
                }
               })
        }  
    })
  


    $(function () {
        //Enable check and uncheck all functionality
        $(".checkbox-toggle").click(function () {
            var clicks = $(this).data('clicks');
            if (clicks) {
                //Uncheck all checkboxes
                $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
            } else {
                //Check all checkboxes
                $(".mailbox-messages input[type='checkbox']").iCheck("check");
                $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
            }
            $(this).data("clicks", !clicks);
        });


    });

     
</script>
<script>

    $(".invoice-total").blur(function () {
        var data = $(this).text().trim();
        var number = $(this).siblings('.invoice-number').find('a').text().trim();


        $.ajax({
        type: "POST",
                url: "{{route('change-invoiceTotal')}}",
                data: {'total':data,'number':number},
                success: function () {
                    alert('Invoice Total Updated');
                },
                error: function () {
                    alert('Invalid URL');
                }

        });
    });
</script>
<script>
    $(".payment-total").blur(function (){
        var id = $(this).attr("data-count");
        var data = $(this).text().trim();

        $.ajax({
        type: "POST",
                url: "{{route('change-paymentTotal')}}",
                data: {'total':data,'id':id},
                success: function () {
                    alert('Payment Total Updated');
                },
                error: function () {
                    alert('Invalid URL');
                }

        });
    })
</script>
<script>


    $('.edit-comment').click(function(){
        var commentDescription = $(this).attr('data-description');
        var commentid = $(this).attr('data-comment-id');
        var userid  = $(this).attr('data-user_id');
        var adminid = $(this).attr('data-admin_id');

        $('#edit-comment').modal('show');
        $('#desc').val(commentDescription);
        $('#user-id').val(userid);
        $('#admin-id').val(adminid);
        var url = "{{url('comment')}}"+ "/"+ commentid
        $('#comment-edit-form').attr('action', url)
    });

   
   </script>
<script type="text/javascript">
// Hide the extra content initially, using JS so that if JS is disabled, no problemo:
            $('.read-more-content').addClass('hide_content')
            $('.read-more-show, .read-more-hide').removeClass('hide_content')

            // Set up the toggle effect:
            $('.read-more-show').on('click', function(e) {
              $(this).next('.read-more-content').removeClass('hide_content');
              $(this).addClass('hide_content');
              e.preventDefault();
            });

            // Changes contributed by @diego-rzg
            $('.read-more-hide').on('click', function(e) {
              var p = $(this).parent('.read-more-content');
              p.addClass('hide_content');
              p.prev('.read-more-show').removeClass('hide_content'); // Hide only the preceding "Read More"
              e.preventDefault();
            });
</script>
  <script>

//Do your stuff, JS!

  $("#customer_detail").on('click',function(){
   $.ajax({
          url: '{{url("getClientDetail/".$client->id)}}',
          type: 'get',
           beforeSend: function () {
                 $('#cus_detail').html( "<img id='blur-bg' class='backgroundfadein' style='left:50%; width: 50px; height:50 px; display: block; position:    fixed;' src='{!! asset('lb-faveo/media/images/gifloader3.gif') !!}'>");

                },
           success: function (response) {
            console.log(response)
            $('#cus_detail').html('');
            $('.clientemail').html((response.client).email);
            $('.clientcompanyname').html((response.client).company);
              $('.clientuser_name').html((response.client).user_name);
              $('.clientbusiness').html((response.client).bussiness);
              $('.clientmobile').val((response.client).clientmobile);
              $('.clientaddress').html((response.client).address);
              $('.clienttown').html((response.client).town);
              $('.clientstate').html((response.client).state);
               $('.clientcountry').html((response.client).country);
               $('.clientzip').html((response.client).zip);
               $('.clientrole').html((response.client).role);
               $('.clientcurrency').html((response.client).currency);
               $('.clientcompany').html((response.client).company_type);
               $('.clientcomsize').html((response.client).company_size);
               $('.clientip').html((response.client).ip);
               $('.referrer').html((response.client).referrer);
               $('.clientskype').html((response.client).skype);
               $('.clientmanager').val((response.client).clientmanager);
               $('.actmanager').val((response.client).account_manager);
         }
      })
 })

  </script>


@stop