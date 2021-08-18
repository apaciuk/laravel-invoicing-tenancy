@extends('themes.default1.layouts.master')
@section('title')
Edit
@stop
@section('content-header')
    <div class="col-sm-6">
        <h1>Edit Script Code</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{url('settings')}}"><i class="fa fa-dashboard"></i> Settings</a></li>
            <li class="breadcrumb-item"><a href="{{url('chat')}}"><i class="fa fa-dashboard"></i> Script</a></li>
            <li class="breadcrumb-item active">Edit Script</li>
        </ol>
    </div><!-- /.col -->
@stop

@section('content')
<div class="card card-secondary card-outline">


        {!! Form::model($chat,['url'=>'chat/'.$chat->id,'method'=>'patch']) !!}



    <div class="card-body">

        <div class="row">

            <div class="col-md-12">

                

                <div class="row">

                    <div class="col-md-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <!-- first name -->
                        {!! Form::label('name',Lang::get('message.name'),['class'=>'required']) !!}
                        {!! Form::text('name',null,['class' => 'form-control']) !!}

                    </div>

                  
                  

                </div>


                 <div class="row">

                    <div class="col-md-6 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <!-- first name -->
                        {!! Form::label('script','Show script',['class'=>'required']) !!}
                        <br>
                        {!! Form::label('on_registration','On registration') !!}
                        {!! Form::radio('on_registration',1,true) !!}
                        &nbsp; &nbsp; &nbsp; 
                        {!! Form::label('on_every_page','On every page') !!}
                        {!! Form::radio('on_registration',0,false) !!}

                    </div>

                     <div class="col-md-3 form-group">
                        <!-- first name -->
                        {!! Form::label('analytics','Google analytics') !!}
                        {{Form::hidden('google_analytics',0,['id'=>'hidden_analytic'])}}
                        {!! Form::checkbox('google_analytics',$chat->google_analytics,null, array('id'=>'analytics')) !!}
                        <!-- <input type="checkbox" name="google_analytics" id="analytics"> -->
                    </div>
                        <br>
                    <div class="col-md-3 form-group analytics_tag" hidden>
                        {!! Form::label('tag','Google analytics tag',['class'=>'required']) !!}
                        {!! Form::text('google_analytics_tag',null,['class' => 'form-control']) !!}
                    </div>

                   

                   


                </div>




                <div class="row">
                    <div class="col-md-12 form-group">

           

                        {!! Form::label('data',Lang::get('message.content'),['class'=>'required']) !!}
                        {!! Form::textarea('script',null,['class'=>'form-control','id'=>'textarea']) !!}

                    </div>


                </div>

            </div>

        </div>
        <button type="submit" class="btn btn-primary pull-right" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-sync-alt">&nbsp;&nbsp;</i>Update</button>

    </div>

</div>


{!! Form::close() !!}
<script>
     $('ul.nav-sidebar a').filter(function() {
        return this.id == 'setting';
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.id == 'setting';
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

    $(document).ready(function(){
        var analytics = $('#analytics').val();
        console.log(analytics)
        if(analytics == 1) {
            $('#analytics').prop('checked',true)
            $('.analytics_tag').attr('hidden',false)
        } else {
           $('#analytics').prop('checked',false)
            $('.analytics_tag').attr('hidden',true) 
        }


        $('#analytics:checkbox').on('change',function(){
        if($(this).is(':checked')){
           $('#analytics').val(1);
           $('.analytics_tag').attr('hidden',false)
        } else {
             $('#analytics').val(0);
           $('.analytics_tag').attr('hidden',true)
        }
    })
    })
</script>
@stop