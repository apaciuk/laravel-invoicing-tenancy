@extends('themes.default1.layouts.front.master')
@section('title')
Forgot Paswword | Faveo Helpdesk
@stop
@section('page-heading')
 Forgot Password? Reset it Now!
@stop
@section('page-header')
Forgot Password
@stop
@section('breadcrumb')
<li><a href="{{url('home')}}">Home</a></li>
<li><a href="{{url('login')}}">Login</a></li>
<li class="active">Forgot Password</li>
@stop
@section('main-class') 
main
@stop
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="featured-boxes">
         
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                     <div id="alertMessage"></div>
                     <div id="error"></div>
                   <div class="featured-box featured-box-primary text-left mt-5">
                        <div class="box-content">
                          
                           
                         <p>Lost your password? Please enter your email address. You will receive a link to create a new password via email.</p>
                            <div class="form-row">
                                <div class="form-group col">
                                   
                                        <label>Email Address <span style="color: red">*</span></label>
                                          <div class="input-group">
                                      <input type="text" name="email" value="" id="email" class="form-control input-lg">
                                      <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                      </div>
                                  </div>
                                   <h6 id="resetpasswordcheck"></h6>
                                        <!-- {!! Form::text('email',null,['class' => 'form-control input-lg']) !!} -->
                                    
                                </div>
                               
                            </div>
                            <div class="clear"></div>
                                <div class="form-row">
                         <div class="form-group col">
                               <a href="{{url('login')}}" class="pt-left back-login">Click Here To Login</a>
                                <button type="button" class="btn btn-primary mb-xl next-step float-right" name="sendOtp" id="resetmail" onclick="resetpassword()">
                                            Send Email
                                </button>
                              
                                  
                            </div>
                        </div>
                           
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop 
@section('script')
<script>
   $('#email').keyup(function(){
                 verify_mail_check();
            });
           function verify_mail_check(){
              var pattern = new RegExp(/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/);
              if (pattern.test($('#email').val())){
                 $('#resetpasswordcheck').hide();
                  $('#email').css("border-color","");
                 return true;
               
              }
              else{
                 $('#resetpasswordcheck').show();
                $('#resetpasswordcheck').html("Please Enter a valid email");
                 $('#resetpasswordcheck').focus();
                $('#email').css("border-color","red");
                $('#resetpasswordcheck').css({"color":"red","margin-top":"5px"});

                   // mail_error = false;
                return false;
                
              }

            }

                        function resetpassword() 
                        {  
                        	 var mail_error = true;
                           var mobile_error = true;
                           $('#resetpasswordcheck').hide();
                                                        
                          if(verify_mail_check()){
                          $("#resetmail").html("<i class='fa fa-circle-o-notch fa-spin fa-1x fa-fw'></i>Sending...");
                                    var data = {
                                        "email":   $('#email').val(),
                                      
                                    };
                                    $.ajax({
                                        url: '{{url('password/email')}}',
                                        type: 'POST',
                                        data: data,
                                        success: function (response) {
                                        
                                        if(response.type == 'success'){
                                             var result =  '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+response.message+'!</div>';
                                            $('#error').hide(); 
                                            $('#alertMessage').show();
                                            $('#alertMessage').html(result);
                                            // $('#alertMessage2').html(result);
                                            $("#resetmail").html("Send Email");
                                          
                                              // response.success("Success");
                                           }  else {
                                             var result =  '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+response.message+'!</div>';
                                            $('#error').hide(); 
                                            $('#alertMessage').show();
                                            $('#alertMessage').html(result);
                                            // $('#alertMessage2').html(result);
                                            $("#resetmail").html("Send Email");
                                           }
                                        },
                                        error: function (data) {
                                             var html = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong><ul>';
                                            $("#resetmail").html("Send Email");
                                              for (var key in data.responseJSON.errors)
                                            {
                                                html += '<li>' + data.responseJSON.errors + '</li>'
                                            }
                                            html += '</ul></div>';
                                           $('#alertMessage').hide(); 
                                            
                                            $('#error').show();
                                             document.getElementById('error').innerHTML = html;
                                           
                                        }
                                    });
                                  }
                                  else{
                                    return false;
                                  }
                                }
                              </script>
                              @stop