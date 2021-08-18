<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@if (trim($__env->yieldContent('template_title')))@yield('template_title') | @endif {{ trans('installer_messages.title') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-16x16.png') }}" sizes="16x16"/>
        <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-32x32.png') }}" sizes="32x32"/>
        <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-96x96.png') }}" sizes="96x96"/>
        <link href="{{ asset('installer/css/style.min.css') }}" rel="stylesheet"/>
        @yield('style')
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
      <style>
        .required:after {
            content:'*';
            color:red;
            padding-left:5px;
        }
    </style>
    <body>
        <div class="master">
            <div class="box">
                <div class="header">
                    <h1 class="header__title">@yield('title')</h1>
                </div>
                <ul class="step">
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('AgoraInvoicingInstaller::final') }}">
                        <i class="step__icon fa fa-server" style="background-color: #34a0db;" aria-hidden="true"></i>
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('AgoraInstaller::environment')}} {{ isActive('AgoraInstaller::environmentWizard')}} {{ isActive('AgoraInstaller::environmentClassic')}}">
                        @if(Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') )
                            <a href="{{ route('AgoraInstaller::environment') }}">
                                <i class="step__icon fa fa-cog" style="background-color: #34a0db;" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon fa fa-cog" style="background-color: #34a0db;" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('AgoraInstaller::permissions') }}">
                        @if(Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') )
                            <a href="{{ route('AgoraInstaller::permissions') }}">
                                <i class="step__icon fa fa-key" style="background-color: #34a0db;" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon fa fa-key" style="background-color: #34a0db;" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('AgoraInstaller::requirements') }}">
                        @if(Request::is('install') || Request::is('install/requirements') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') )
                            <a href="{{ route('AgoraInstaller::requirements') }}">
                                <i class="step__icon fa fa-list" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon fa fa-list" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('AgoraInstaller::welcome') }}">
                        @if(Request::is('install') || Request::is('install/requirements') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') )
                            <a href="{{ route('AgoraInstaller::welcome') }}">
                                <i class="step__icon fa fa-home" style="background-color: #34a0db;" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon fa fa-home" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                </ul>
                <div class="main">
                    
                     @if (session('fails'))
                      <div class="alert alert-danger alert-dismissable" id="close">
                            <i class="fa fa-ban"></i>
                            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
                            <button type="button" class="close" id="close_alert" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{Session::get('fails')}}
                        </div>
                       
                    @endif
                    @if(session()->has('errors'))
                        <div class="alert alert-danger" id="error">
                            
                             <div>
                                <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                {{ trans('installer_messages.forms.errorTitle') }}
                            </div>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('container')
                </div>
            </div>
        </div>
        @yield('scripts')
        <script type="text/javascript">
            var x = document.getElementById('error_alert');
            var y = document.getElementById('close_alert');
            var close = document.getElementById('close');
            var validateClose = document.getElementById('error');
            y.onclick = function() {
                close.style.display = "none";
            };
            x.onclick = function() {
                validateClose.style.display = "none";
            };
        </script>
    </body>
</html>
