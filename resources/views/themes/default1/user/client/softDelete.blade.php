@extends('themes.default1.layouts.master')
@section('title')
Suspended users
@stop

@section('content-header')
    <div class="col-sm-6">
        <h1>Suspended Users</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{url('clients')}}"><i class="fa fa-dashboard"></i> Users</a></li>
            <li class="breadcrumb-item active">Suspended Users</li>
        </ol>
    </div><!-- /.col -->
@stop

@section('content')




<div class="card card-secondary card-outline">

   

    <div id="response"></div>

    <div class="card-body table-responsive">
        <div class="row">

            <div class="col-md-12">
                <table id="deleted-user-table" class="table display " cellspacing="0" width="100%" styleClass="borderless">
                 <button  value="" class="btn btn-secondary btn-sm btn-alldell" id="bulk_delete"><i class= "fa fa-trash"></i>&nbsp;&nbsp;Delete Permanently</button><br /><br />
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


   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
  $('ul.nav-sidebar a').filter(function() {
      console.log('id-=== ', this.id)
        return this.id == 'soft_delete_user';
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.id == 'soft_delete_user';
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

        $('#deleted-user-table').DataTable({

            processing: true,
            serverSide: true,
              
            ajax: {
            "url":  '{!! route('soft-delete') !!}',
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
                "sProcessing": ' <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>'
                    {{--'<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! asset("lb-faveo/media/images/gifloader3.gif") !!}">'--}}
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
 



  
   function checking(e){

          $('#deleted-user-table').find("td input[type='checkbox']").prop('checked', $(e).prop('checked'));
     }


     $(document).on('click','#bulk_delete',function(){
      var id=[];
      if (confirm("Are you sure? Deleting user permanently will delete all invoices, orders, subscriptions and comments related to the user. "))
        {
            $('.user_checkbox:checked').each(function(){
              id.push($(this).val())
            });
            if(id.length >0)
            {
               $.ajax({
                      url:"{!! Url('permanent-delete-client') !!}",
                      method:"delete",
                      data: $('#check:checked').serialize(),
                      beforeSend: function () {
                $('#gif').html( "<img id='blur-bg' class='backgroundfadein' style='top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;' src='{!! asset('lb-faveo/media/images/gifloader3.gif') !!}'>");
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
