@extends('themes.default1.layouts.master')
@section('title')
User Profile
@stop

@section('content-header')
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.scrollit {
    overflow:scroll;
    height:600px;
}
</style>

    <div class="col-sm-6">
        <h1>User Profile</h1>
    </div>
    <div class="col-sm-6">

        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>
    </div><!-- /.col -->


<style>

        .bootstrap-select.btn-group .dropdown-menu li a {
    margin-left: -12px !important;
}
 .btn-group>.btn:first-child {
    margin-left: 0;
    background-color: white;

   }
.bootstrap-select.btn-group .dropdown-toggle .filter-option {
    color:#555;
}


</style>
@endsection
@section('content')
<div class="row">

    <div class="col-md-6">


        {!! Form::model($user,['url'=>'profile', 'method' => 'PATCH','files'=>true]) !!}


        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Edit Profile</h3>


            </div>
            <div class="card-body">

                <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                    <!-- first name -->
                    {!! Form::label('first_name',null,['class' => 'required'],Lang::get('message.first_name')) !!}
                    {!! Form::text('first_name',null,['class' => 'form-control']) !!}

                </div>

                <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                    <!-- last name -->
                    {!! Form::label('last_name',null,['class' => 'required'],Lang::get('message.last_name')) !!}
                    {!! Form::text('last_name',null,['class' => 'form-control']) !!}

                </div>
                <div class="form-group {{ $errors->has('user_name') ? 'has-error' : '' }}">
                    <!-- mobile -->
                    {!! Form::label('user_name',null,['class' => 'required'],Lang::get('message.user_name')) !!}
                    {!! Form::text('user_name',null,['class' => 'form-control']) !!}
                </div>


                <div class="form-group">
                    <!-- email -->
                    {!! Form::label('email',null,['class' => 'required'],Lang::get('message.email')) !!}
                     {!! Form::text('email',null,['class' => 'form-control']) !!}

                </div>

                <div class="form-group {{ $errors->has('company') ? 'has-error' : '' }}">
                    <!-- company -->
                    {!! Form::label('company',null,['class' => 'required'],Lang::get('message.company')) !!}
                    {!! Form::text('company',null,['class' => 'form-control']) !!}

                </div>


                <div class="form-group {{ $errors->has('mobile_code') ? 'has-error' : '' }}">
                  {!! Form::label('mobile',null,Lang::get('message.mobile')) !!}
                     {!! Form::hidden('mobile_code',null,['id'=>'mobile_code_hidden']) !!}
                     <!--  <input class="form-control selected-dial-code"  id="mobile_code" value="{{$user->mobile}}" name="mobile" type="tel"> -->
                        
                    {!! Form::text('mobile',$user->mobile,['class'=>'form-control selected-dial-code', 'type'=>'tel','id'=>'mobile_code']) !!}
                    <span id="valid-msg" class="hide"></span>
                       <span id="error-msg" class="hide"></span>
                </div>


                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                    <!-- phone number -->
                    {!! Form::label('address',null,['class' => 'required'],Lang::get('message.address')) !!}
                    {!! Form::textarea('address',null,['class' => 'form-control']) !!}

                </div>

                <div class="row">

                    <div class="col-md-6 form-group {{ $errors->has('town') ? 'has-error' : '' }}">
                        <!-- mobile -->
                        {!! Form::label('town',Lang::get('message.town')) !!}
                        {!! Form::text('town',null,['class' => 'form-control']) !!}

                    </div>

                    <div class="col-md-6 form-group {{ $errors->has('timezone_id') ? 'has-error' : '' }}">
                        <!-- mobile -->
                        {!! Form::label('timezone_id',Lang::get('message.timezone'),['class' => 'required']) !!}
                        <!-- {!! Form::select('timezone_id',[''=>'Select','Timezones'=>$timezones],null,['class' => 'form-control']) !!} -->
                        {!! Form::select('timezone_id', [Lang::get('message.choose')=>$timezones],null,['class' => 'form-control selectpicker','data-live-search'=>'true','required','data-live-search-placeholder' => 'Search','data-dropup-auto'=>'false','data-size'=>'10']) !!}


                    </div>

                </div>

                <div class="row">
                    <?php $countries = \App\Model\Common\Country::pluck('nicename', 'country_code_char2')->toArray(); ?>
                    <div class="col-md-6 form-group {{ $errors->has('country') ? 'has-error' : '' }}">
                         {!! Form::label('country',Lang::get('message.country'),['class' => 'required']) !!}



                        {!! Form::select('country',[Lang::get('message.choose')=>$countries],null,['class' => 'form-control select2','id'=>'country','onChange'=>'getCountryAttr(this.value)','data-live-search'=>'true','required','data-live-search-placeholder' => 'Search','data-dropup-auto'=>'false','data-size'=>'10','disabled'=>'disabled']) !!}
                        <!-- name -->




                    </div>
                    <div class="col-md-6 form-group {{ $errors->has('state') ? 'has-error' : '' }}">
                        <!-- name -->
                        {!! Form::label('state',Lang::get('message.state'),['class' => 'required']) !!}
                        <!--{!! Form::select('state',[],null,['class' => 'form-control','id'=>'state-list']) !!}-->
                        <select name="state" id="state-list" class="form-control">
                            @if(count($state)>0)
                            <option value="{{$state['id']}}">{{$state['name']}}</option>
                            @endif
                            <option value="">Select State</option>
                            @foreach($states as $key=>$value)
                            <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>

                    </div>


                </div>
                 <div class="row">
                <div class="col-md-6 form-group {{ $errors->has('zip') ? 'has-error' : '' }}">
                    <!-- mobile -->
                    {!! Form::label('zip',null,Lang::get('message.zip')) !!}
                    {!! Form::text('zip',null,['class' => 'form-control']) !!}

                </div>

                <div class="col-md-6 form-group" id= "gstin">
                    <!-- mobile -->
                    {!! Form::label('GSTIN',null,'GSTIN') !!}
                    {!! Form::text('gstin',null,['class' => 'form-control']) !!}

                </div>
              </div>

                <div class="form-group {{ $errors->has('profile_pic') ? 'has-error' : '' }}">
                    <!-- profile pic -->
                    {!! Form::label('profile_pic',Lang::get('message.profile-picture')) !!}
                    {!! Form::file('profile_pic') !!}
                    <br>
                    @if($user->profile_pic) 
                    <img src='{{ asset("$user->profile_pic")}}' class="img-thumbnail" style="height: 50px;">
                    @endif

                </div>
                <button type="submit" class="btn btn-primary pull-right" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-save">&nbsp;&nbsp;</i>{!!Lang::get('message.save')!!}</button></h4>

                {!! Form::token() !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">



        {!! Form::model($user,['url'=>'password' , 'method' => 'PATCH']) !!}



        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{Lang::get('message.change-password')}}</h3>


            </div>




            <div class="card-body">
                @if(Session::has('success1'))
                <div class="alert alert-success alert-dismissable">
                    <i class="fa fa-ban"></i>
                    <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.success')}}.
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('success1')}}
                </div>
                @endif
                <!-- fail message -->
                @if(Session::has('fails1'))
                <div class="alert alert-danger alert-dismissable">
                    <i class="fa fa-ban"></i>
                    <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('fails1')}}
                </div>
                @endif
                <!-- old password -->
                <div class="form-group has-feedback {{ $errors->has('old_password') ? 'has-error' : '' }}">
                    {!! Form::label('old_password',null,['class' => 'required'],Lang::get('message.old_password')) !!}
                    {!! Form::password('old_password',['placeholder'=>'Password','class' => 'form-control']) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <!-- new password -->
                <div class="form-group has-feedback {{ $errors->has('new_password') ? 'has-error' : '' }}">
                    {!! Form::label('new_password',null,['class' => 'required'],Lang::get('message.new_password')) !!}
                    {!! Form::password('new_password',['placeholder'=>'New Password','class' => 'form-control']) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <!-- cofirm password -->
                <div class="form-group has-feedback {{ $errors->has('confirm_password') ? 'has-error' : '' }}">
                    {!! Form::label('confirm_password',null,['class' => 'required'],Lang::get('message.confirm_password')) !!}
                    {!! Form::password('confirm_password',['placeholder'=>'Confirm Password','class' => 'form-control']) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                    <button type="submit" class="btn btn-primary pull-right" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-save">&nbsp;&nbsp;</i>{!!Lang::get('message.save')!!}</button>
                {!! Form::close() !!}
            </div>
        </div>



   


 @include('themes.default1.user.2faModals')


        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{Lang::get('message.setup_2fa')}}</h3>


            </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-10">
                <h5>
                    @if($is2faEnabled ==0)
                    
                    <img src="{{asset('common/images/authenticator.png')}}" alt="Authenticator" style="margin-top: -6px!important;" class="img-responsive img-circle img-sm">&nbsp;Authenticator App
                    @else
                    <img src="{{asset('common/images/authenticator.png')}}" alt="Authenticator" style="margin-top: -6px!important;" class="img-responsive img-circle img-sm">&nbsp;2-Step Verification is ON since {{getTimeInLoggedInUserTimeZone($dateSinceEnabled)}}
                    <br><br><br>
                    <div class="row">
                 <div class="col-md-6">
                     <button class="btn btn-primary" id="viewRecCode">View Recovery Code</button>
                 </div>
             </div>
                    @endif
                </h5>
                </div>
                <div class="col-md-2">
                  <label class="switch toggle_event_editing pull-right">

                         <input type="checkbox" value="{{$is2faEnabled}}"  name="modules_settings"
                          class="checkbox" id="2fa">
                          <span class="slider round"></span>
                    </label>
                 </div>

            </div>
        </div>
        </div>
    </div>
</div>


{!! Form::close() !!}
<script src="{{asset('common/js/2fa.js')}}"></script>
<script>
// get the country data from the plugin
     $(document).ready(function(){
         $(function () {
             //Initialize Select2 Elements
             $('.select2').select2()
         });
    var country = $('#country').val();
    if(country == 'IN') {
        $('#gstin').show()
    } else {
        $('#gstin').hide();
    }
    getCode(country);
    var telInput = $('#mobile_code');
    addressDropdown = $("#country");
     errorMsg = document.querySelector("#error-msg"),
    validMsg = document.querySelector("#valid-msg");
    var errorMap = [ "Invalid number", "Invalid country code", "Number Too short", "Number Too long", "Invalid number"];
     let currentCountry="";
    telInput.intlTelInput({
        initialCountry: "auto",
        geoIpLookup: function (callback) {

            $.get("http://ipinfo.io", function () {}, "jsonp").always(function (resp) {
                resp.country = country;

                var countryCode = (resp && resp.country) ? resp.country : "";
                    currentCountry=countryCode.toLowerCase()
                    callback(countryCode);
            });
        },
        separateDialCode: true,
       utilsScript: "{{asset('js/intl/js/utils.js')}}"
    });
     var reset = function() {
      errorMsg.innerHTML = "";
      errorMsg.classList.add("hide");
      validMsg.classList.add("hide");
    };
    setTimeout(()=>{
         telInput.intlTelInput("setCountry", currentCountry);
    },500)
     $('.intl-tel-input').css('width', '100%');
    telInput.on('blur', function () {
      reset();
        if ($.trim(telInput.val())) {
            if (telInput.intlTelInput("isValidNumber")) {
              $('#mobile_code').css("border-color","");
              validMsg.classList.remove("hide");
              $('#submit').attr('disabled',false);
            } else {
              var errorCode = telInput.intlTelInput("getValidationError");
             errorMsg.innerHTML = errorMap[errorCode];
             $('#mobile_code').css("border-color","red");
             $('#error-msg').css({"color":"red","margin-top":"5px"});
             errorMsg.classList.remove("hide");
             $('#submit').attr('disabled',true);
            }
        }
    });

     addressDropdown.change(function() {
     telInput.intlTelInput("setCountry", $(this).val());
             if ($.trim(telInput.val())) {
            if (telInput.intlTelInput("isValidNumber")) {
              $('#mobile_code').css("border-color","");
              errorMsg.classList.add("hide");
              $('#submit').attr('disabled',false);
            } else {
              var errorCode = telInput.intlTelInput("getValidationError");
             errorMsg.innerHTML = errorMap[errorCode];
             $('#mobile_code').css("border-color","red");
             $('#error-msg').css({"color":"red","margin-top":"5px"});
             errorMsg.classList.remove("hide");
             $('#submit').attr('disabled',true);
            }
        }
    });
    $('input').on('focus', function () {
        $(this).parent().removeClass('has-error');
    });

    $('form').on('submit', function (e) {
        $('input[name=mobileds]').attr('value', $('.selected-dial-code').text());
    });


});
</script>
<script>



       function getCountryAttr(val) {
        if(val == 'IN') {
            $('#gstin').show()
        } else {
            $('#gstin').hide()
        }
        getState(val);
        getCode(val);
//        getCurrency(val);

    }

    function getState(val) {


        $.ajax({
            type: "GET",
              url: "{{url('get-state')}}/" + val,
            data: 'country_id=' + val,
            success: function (data) {
                $("#state-list").html(data);
            }
        });
    }
    function getCode(val) {
        $.ajax({
            type: "GET",
            url: "{{url('get-code')}}",
            data: 'country_id=' + val,
            success: function (data) {
                // $("#mobile_code").val(data);
                $("#mobile_code_hidden").val(data);
            }
        });
    }
</script>
@stop