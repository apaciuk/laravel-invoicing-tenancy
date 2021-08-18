@extends('themes.default1.layouts.master')
@section('title')
Social Media
@stop
@section('content-header')
    <div class="col-sm-6">
        <h1>Create New Social Media</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{url('settings')}}"><i class="fa fa-dashboard"></i> Settings</a></li>
            <li class="breadcrumb-item"><a href="{{url('social-media')}}"><i class="fa fa-dashboard"></i> Social Media</a></li>
            <li class="breadcrumb-item active">Create Social Media</li>
        </ol>
    </div><!-- /.col -->
@stop

@section('content')

<div class="row">

    <div class="col-md-12">
        <div class="card card-secondary card-outline">




            <div class="card-body">
                {!! Form::open(['url'=>'social-media','method'=>'post']) !!}

                <table class="table table-condensed">



                    <tr>

                        <td><b>{!! Form::label('name',Lang::get('message.name'),['class'=>'required']) !!}</b></td>
                        <td>
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">


                                {!! Form::text('name',null,['class' => 'form-control']) !!}
                                <p><i> {{Lang::get('message.enter-the-name-of-the-social-media')}}</i> </p>


                            </div>
                        </td>

                    </tr>
                    <tr>

                        <td><b>{!! Form::label('class',Lang::get('message.class'),['class'=>'required']) !!}</b></td>
                        <td>

                            <div class="form-group {{ $errors->has('class') ? 'has-error' : '' }}">


                                {!! Form::text('class',null,['class' => 'form-control']) !!}
                                <p><i> {{Lang::get('message.enter-the-css-class-of-the-social-media')}}</i> </p>



                        </td>


                    </tr>
                    <tr>
                        <td><b>{!! Form::label('fa_class',Lang::get('message.fa-class'),['class'=>'required']) !!}</b></td>
                        <td>

                            <div class="form-group {{ $errors->has('fa_class') ? 'has-error' : '' }}">


                                {!! Form::text('fa_class',null,['class' => 'form-control']) !!}
                                <p><i> {{Lang::get('message.enter-the-fa-class-of-the-social-media')}}</i> </p>



                        </td>
                    </tr>

                    <tr>

                        <td><b>{!! Form::label('link',Lang::get('message.link'),['class'=>'required']) !!}</b></td>
                        <td>
                            <div class="form-group {{ $errors->has('link') ? 'has-error' : '' }}">


                                {!! Form::text('link',null,['class' => 'form-control']) !!}
                                <p><i> {{Lang::get('message.enter-the-link-of-the-social-media')}}</i> </p>


                            </div>
                        </td>

                    </tr>



                    {!! Form::close() !!}

                </table>

                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save">&nbsp;&nbsp;</i>{!!Lang::get('message.save')!!}</button>


            </div>

        </div>
        <!-- /.box -->

    </div>


</div>
<script>
     $('ul.nav-sidebar a').filter(function() {
        return this.id == 'setting';
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.id == 'setting';
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
</script>
@stop