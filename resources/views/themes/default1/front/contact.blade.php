@extends('themes.default1.layouts.front.master')
@section('title')
Contact Us
@stop
@section('page-header')
Cart
@stop
@section('page-heading')
What can we help you with?
@stop
@section('breadcrumb')
 @if(Auth::check())
<li><a href="{{url('my-invoices')}}">Home</a></li>
  @else
  <li><a href="{{url('login')}}">Home</a></li>
  @endif
<li class="active">Contact Us</li>
@stop
@section('main-class') "main shop" @stop
@section('content')   
<style>
    .required:after{ 
        content:'*'; 
        color:red; 
        padding-left:5px;
    }
</style>
<?php 
$set = new \App\Model\Common\Setting();
$set = $set->findOrFail(1);
?>

<div class="row">
    <div class="col-md-6">

       <h2 class="mb-3 mt-2"><strong>Contact</strong> Us</h2>
        {!! Form::open(['url'=>'contact-us']) !!}
            <div class="form-row">
                <div class="form-group col-lg-6">
                  
                        <label class="required">Your name</label>
                        <input type="text" value="" data-msg-required="Please enter your name." maxlength="100" class="form-control" name="name" id="name" required>
                    </div>
                   <div class="form-group col-lg-6">
                        <label class="required">Your email address</label>
                        <input type="email" value="" data-msg-required="Please enter your email address." data-msg-email="Please enter a valid email address." maxlength="100" class="form-control" name="email" id="email" required>
                    </div>
                </div>
           
            <div class="form-row">
                    <div class="form-group col">
                        <label class="required">Mobile No</label>
                        {!! Form::hidden('mobile',null,['id'=>'mobile_code_hidden','name'=>'country_code']) !!}
                        <input type="text" value=""  id="mobilenum" data-msg-required="Please enter the mobile No." maxlength="10" class="form-control" name="Mobile"  required>
                       
                    
                </div>
            </div>
           <div class="form-row">
               <div class="form-group col">
                    
                        <label class="required">Message</label>
                        <textarea maxlength="5000" data-msg-required="Please enter your message." rows="10" class="form-control" name="message" id="message" required></textarea>
                    </div>
                
            </div>
              <div class="form-row">
                <div class="form-group col">
                    <input type="submit" value="Send Message" class="btn btn-primary btn-lg mb-xlg" data-loading-text="Loading...">
                </div>
            </div>
        {!! Form::close() !!}
    </div>
    <div class="col-md-6">

        <hr>

        <h4 class="heading-primary">Our Office</h4>
       <ul class="list list-icons list-icons-style-3 mt-4">
                                <li><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> {{$set->address}}</li>
                                <li><i class="fas fa-phone"></i> <strong>Phone:</strong> {{$set->phone}}</li>
                                <li><i class="far fa-envelope"></i> <strong>Email:</strong> <a href="mailto:{{$set->company_email}}">{{$set->company_email}}</a></li>
                            </ul>
        <hr>

      <!--  <h4 class="heading-primary">Business <strong>Hours</strong></h4>
                            <ul class="list list-icons list-dark mt-4">
                                <li><i class="far fa-clock"></i> Monday to Friday 09:30AM to 06:30PM IST</li>
                                
                            </ul> -->

    </div>

</div>
@stop
@section('script')
<script type="text/javascript">
    var telInput = $('#mobilenum');
    telInput.intlTelInput({
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () {}, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        initialCountry: "auto",
        separateDialCode: true,
        utilsScript: "{{asset('common/js/utils.js')}}"
    });
    $('.intl-tel-input').css('width', '100%');

    telInput.on('blur', function () {
        if ($.trim(telInput.val())) {
            if (!telInput.intlTelInput("isValidNumber")) {
                telInput.parent().addClass('has-error');
            }
        }
    });
    $('input').on('focus', function () {
        $(this).parent().removeClass('has-error');
    });

    $('form').on('submit', function (e) {
        $('input[name=country_code]').attr('value', $('.selected-dial-code').text());
    });

</script>
@stop