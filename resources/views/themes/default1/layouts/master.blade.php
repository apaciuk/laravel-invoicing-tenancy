<!DOCTYPE html>
  <?php
    $set = new \App\Model\Common\Setting();
    $set = $set->findOrFail(1);
    ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href='{{asset("common/images/$set->fav_icon")}}' type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>@yield('title') | {{$set->favicon_title}}</title>
        <link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">

        <link rel="stylesheet" href="{{asset('admin/css/all.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Tempusdominus Bbootstrap 4 -->
        <link rel="stylesheet" href="{{asset('admin/css/tempusdominus-bootstrap-4.min.css')}}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{asset('admin/css/icheck-bootstrap.min.css')}}">
        <!-- JQVMap -->
        <link rel="stylesheet" href="{{asset('admin/css/jqvmap.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('admin/css/adminlte.min.css')}}">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="{{asset('admin/css/OverlayScrollbars.min.css')}}">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="{{asset('admin/css/daterangepicker.css')}}">
        <!-- summernote -->
        <link rel="stylesheet" href="{{asset('admin/css/summernote-bs4.css')}}">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

        <link rel="stylesheet" href="{{asset('admin/css/icheck-bootstrap.min.css')}}">

        <!-- Custom css/js -->
        <link rel="stylesheet" href="{{asset('common/css/intlTelInput.css')}}">

        <script src="{{asset('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js')}}"></script>
        <script src="{{asset('https://code.jquery.com/jquery-3.5.1.min.js')}}"></script>
        <script>
        $(function () {
          $("input[data-bootstrap-switch]").each(function(){
              $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        })
        </script>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


        </script>
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->


         <link rel="stylesheet" href="{{asset('common/css/intlTelInput.css')}}">


    </head>
    <style>
        .required:after {
            content:'*';
            color:red;
            padding-left:5px;
        }
        [type=search] {
        outline-offset: 0px;
        -webkit-appearance: none;
    }
    </style>
    <?php
    $set = new \App\Model\Common\Setting();
    $set = $set->findOrFail(1);
    ?>


    <!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
    <!-- the fixed layout is not compatible with sidebar-mini -->
    <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{url('my-invoices')}}" class="nav-link">Go to Client Panel</a>
                </li>

            </ul>



            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <img src="{{Auth::user()->profile_pic}}" style="width:30px;" class="img-size-50 mr-3 img-circle" alt="User Image" />
                        <span class="hidden-xs">{{ucfirst(Auth::user()->first_name)}} {{ucfirst(Auth::user()->last_name)}}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{Auth::user()->profile_pic}}"  class="img-size-50 mr-3 img-circle" alt="User Image" />
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        {{ucfirst(Auth::user()->first_name)}} {{ucfirst(Auth::user()->last_name)}}
                                    </h3>
                                    <p class="text-sm">{{ucfirst(Auth::user()->role)}}</p>
                                     <a href="{{url('profile')}}" class="btn btn-primary btn-sm btn-flat">{{Lang::get('message.profile')}}</a>
                                     <a href="{{url('auth/logout')}}" class="btn btn-danger btn-sm btn-flat">{{Lang::get('message.signout')}}</a>

                                </div>
                            </div>
                            <!-- Message End -->
                        </div>


                    </div>
                </li>

            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
                @if ($set->title != '')
                    <!-- Brand Logo -->
                        <a href="{{url('/')}}" class="brand-link">

                            <span style="margin-left: 50px;" class="brand-text font-weight-light"><b>{{$set->title}}</b></span>
                        </a>
                @else
                        <a href="{{url('/')}}" class="brand-link">
                <span style="margin-left: 50px;" class="brand-text font-weight-light"><img src='{{ asset("admin/images/$set->admin_logo")}}' alt="Admin-Logo" class="brand-image img-circle elevation-3"
                     style="opacity: .8;"></span>
                        </a>
                @endif

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
               <!--  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <a href="{{url('/clients/'.Auth::user()->id)}}"><img src="{{Auth::user()->profile_pic}}" class="img-circle elevation-2" alt="User Image"></a>
                    </div>
                    <div class="info">
                        <a href="{{url('/clients/'.Auth::user()->id)}}" class="d-block">{{ucfirst(Auth::user()->first_name)}} {{ucfirst(Auth::user()->last_name)}}</a>
                    </div>
                </div>
 -->
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
                             with font-awesome or any other icon font library -->
                            
                        <li class="nav-item has-treeview">
                            <a href="{{url('/')}}" class="nav-link" id="dashboard">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    {{Lang::get('message.dashboard')}}
                                </p>
                            </a>

                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    Users
                                    <i class="fas fa-angle-left right"></i>
                                 </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{url('clients')}}" class="nav-link" id="all_user">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Users</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{url('clients/create')}}" class="nav-link" id="add_new_user">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add New</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{url('deleted-users')}}" class="nav-link" id="soft_delete_user">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Suspended Users</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>
                                    Orders
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{url('orders')}}" class="nav-link" id="all_order">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.all-orders')}}</p>
                                    </a>
                                </li>
                               <!--  <li class="nav-item">
                                    <a href="{{url('invoice/generate')}}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.add-new')}}</p>
                                    </a>
                                </li> -->
                            </ul>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-paperclip"></i>
                                <p>
                                    {{Lang::get('message.invoices')}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{url('invoices')}}" class="nav-link" id="all_invoice">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.all-invoices')}}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{url('invoice/generate')}}" class="nav-link" id="add_invoice">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.add-new')}}</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa fa-fw fa-sticky-note"></i>
                                <p>
                                    {{Lang::get('message.pages')}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{url('pages')}}" class="nav-link" id="all_page">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.all-pages')}}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{url('pages/create')}}" class="nav-link" id="all_new_page">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.add-new')}}</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa fa-fw fa-briefcase"></i>
                                <p>
                                    {{Lang::get('message.products')}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{url('products')}}" class="nav-link" id="all_product">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.all-products')}}</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{url('products/create')}}" class="nav-link" id="add_product">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.add-products')}}</p>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="{{url('plans')}}" class="nav-link" id="plan">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Plans</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{url('promotions')}}" class="nav-link" id="coupon">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.coupons')}}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{url('groups')}}" class="nav-link" id="group">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{Lang::get('message.groups')}}</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="{{url('settings')}}" class="nav-link" id="setting">
                                <i class="nav-icon fa fa-fw fa-cogs"></i>
                                <p>
                                    {{Lang::get('message.settings')}}
                                </p>
                            </a>
                        </li>



                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        @yield('content-header')
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @if (count($errors) > 0)

                        <div class="alert alert-danger alert-dismissable">
                            <strong>Whoops!</strong> There were some problems with your input.
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>



                    @endif

                    @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <i class="fa fa-check"></i>
                            <b>{{Lang::get('message.success')}}!</b>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{Session::get('success')}}
                        </div>
                    @endif

                    @if(Session::has('warning'))
                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{Session::get('warning')}}
                        </div>
                    @endif
                <!-- fail message -->
                    @if(Session::has('fails'))
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-ban"></i>
                            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{Session::get('fails')}}
                        </div>
                    @endif


                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{date('Y')}} <a href="{{$set->website}}">{{$set->company}}</a>.</strong>
            All rights reserved. Powered by <a href="https://ladybirdweb.com" target="_blank"><img src="{{asset('common/images/Ladybird1.png')}}" alt="Ladybird"></a>
            <div class="float-right d-none d-sm-inline-block">
                <b>{{Lang::get('message.version')}}</b> {{Config::get('app.version')}}
            </div>
        </footer>


        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->


    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('admin/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>


    <!-- jQuery -->
    <!-- jQuery UI 1.11.4 -->
    <script src="{{asset('admin/js/jquery-ui.min.js')}}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('admin/js/bootstrap.bundle.min.js')}}"></script>
    <!-- ChartJS -->
    <script src="{{asset('admin/plugins/Chart.min.js')}}"></script>
    <!-- Sparkline -->
    <script src="{{asset('admin/plugins/sparkline.js')}}"></script>
    <!-- JQVMap -->
    <script src="{{asset('admin/plugins/jquery.vmap.min.js')}}"></script>
    <script src="{{asset('admin/plugins/jquery.vmap.usa.js')}}"></script>

    <!-- jQuery Knob Chart -->
    <script src="{{asset('admin/plugins/jquery.knob.min.js')}}"></script>
    <!-- daterangepicker -->
    <script src="{{asset('admin/plugins/moment.min.js')}}"></script>
    <script src="{{asset('admin/plugins/daterangepicker.js')}}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{asset('admin/plugins/tempusdominus-bootstrap-4.min.js')}}"></script>
    <!-- Summernote -->
    <script src="{{asset('admin/plugins/summernote-bs4.min.js')}}"></script>
    <!-- overlayScrollbars -->
    <script src="{{asset('admin/plugins/jquery.overlayScrollbars.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('admin/js/adminlte.js')}}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{asset('admin/js/dashboard.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{asset('admin/js/demo.js')}}"></script>
    <script src="{{asset('admin/plugins/select2.full.min.js')}}"></script>



{{-------------------------------------Custom---------------------------------------------------------}}
    <script src="{{asset('common/js/intlTelInput.js')}}"></script>

    <script src="{{asset('admin/plugins/jquery-file-upload/vendor/jquery.ui.widget.js')}}"></script>

    <script src="{{asset('admin/plugins/jquery-file-upload/jquery.fileupload.js')}}"></script>
    <!-- <script src="{{asset('plugins/jquery-file-upload/main.js')}}"></script> -->
    <script src="{{asset('admin/plugins/jquery-file-upload/jquery.iframe-transport.js')}}"></script>
    <script src="{{asset('admin/plugins/jquery-file-upload/resumable.js')}}"></script>
    <script src="{{asset('admin/plugins/bootstrap-switch.min.js')}}"></script>

    <script>

    // for sidebar menu entirely but not cover treeview
    
    </script>
    
    @yield('icheck')
    @yield('datepicker')
            <script type="text/javascript">
        var csrfToken = $('[name="csrf_token"]').attr('content');

        setInterval(refreshToken, 360000); // 1 hour 

        function refreshToken(){
               $.post('refresh-csrf').done(function(data){
                   csrfToken = data; // the new token
               });
        }


          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });


          </script>



        </div><!-- ./wrapper -->
       
        <!-- Bootstrap 3.3.2 JS -->
       


    </body>
</html>
