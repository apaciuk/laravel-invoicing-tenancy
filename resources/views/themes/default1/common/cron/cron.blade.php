@extends('themes.default1.layouts.master')
@section('title')
Cron Setting
@stop
@section('content-header')
    <div class="col-sm-6">
        <h1>{!! Lang::get('message.cron-setting') !!}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{url('settings')}}"><i class="fa fa-dashboard"></i> Settings</a></li>
            <li class="breadcrumb-item active">{!! Lang::get('message.cron-setting') !!}</li>
        </ol>
    </div><!-- /.col -->
@stop


@section('content')



    <div class="card card-secondary card-outline">
      <div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
  
               
                    @include('themes.default1.common.cron.cron-new')
               
                <!-- /.tab-pane -->


        <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>

</div>




<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
  
     <div class="card card-secondary card-outline">
        
        <!-- /.box-header -->
       
              {!! Form::open(['url' => 'cron-days', 'method' => 'PATCH','id'=>'Form']) !!}
              <?php 
                   $mailStatus = \App\Model\Common\StatusSetting::pluck('expiry_mail')->first();
                   $activityStatus =\App\Model\Common\StatusSetting::pluck('activity_log_delete')->first();
                  ?>
         <div class="card-header">
             <h3 class="card-title">{{Lang::get('message.set_cron_period')}}  </h3>


         </div>

      <div class="card-body">
          <div class="row">
           
            <!-- /.col -->
            <div class="col-md-6">
             
              <!-- /.form-group -->
              <div class="form-group select2">
                <label>{{Lang::get('message.expiry_mail_sent')}}</label>
                <?php 
                 if (count($selectedDays) > 0) {
                foreach ($selectedDays as $selectedDay) {
                    $saved[$selectedDay->days] = 'true';
                }
               }  else {
                    $saved=[];
                }
                 if (count($saved) > 0) {
                   foreach ($saved as $key => $value) {
                     $savedkey[]=$key;
                   }
                   $saved1=$savedkey?$savedkey:[''];
                       }
                       else{
                        $saved1=[];
                       }
                 ?>
                  
                   @if ($mailStatus == 0)
                    <select id ="days" name="expiryday[]" class="form-control selectpicker"   style="width: 100%; color:black;" disabled>
                      <option value="">{{Lang::get('message.enable_mail_cron')}}</option>
                    </select>
                      @else
                <select id ="days" name="expiryday[]" class="form-control selectpicker"  data-live-search="true" data-live-search-placeholder="Search" multiple="true" style="width: 100%; color:black;">

                    
                    @foreach ($expiryDays as $key=>$value)
                  <option value="{{$key}}" <?php echo (in_array($key, $saved1)) ?  "selected" : "" ;  ?>>{{$value}}</option>
                   @endforeach
                   
                </select>
                @endif
              </div>
              <!-- /.form-group -->
            </div>

             <div class="col-md-6">
              <div class="form-group">
                <label>{{Lang::get('message.log_del_days')}}</label>
                  @if ($activityStatus == 0)
                    <select id ="days" name="expiryday[]" class="form-control selectpicker"   style="width: 100%; color:black;" disabled>
                      <option value="">{{Lang::get('message.enable_activityLog_cron')}}</option>
                    </select>
                      @else
                <select name="logdelday" class="form-control selectpicker" data-live-search="true" data-live-search-placeholder="Search" style="width: 100%;">
                    @foreach ($delLogDays as $key=>$value)
                  <option value="{{$key}}" <?php echo (in_array($key, $beforeLogDay)) ?  "selected" : "" ;  ?>>{{$value}}</option>
                  @endforeach
                </select>
                @endif
              </div>
              <!-- /.form-group -->

              <!-- /.form-group -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          @if ( $mailStatus || $activityStatus ==1)
              <button type="submit" class="btn btn-primary pull-right" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-sync-alt">&nbsp;</i>{!!Lang::get('message.update')!!}</button>
          @else
              <button type="submit" class="btn btn-primary pull-right disabled" id="submit"><i class="fa fa-sync-alt">&nbsp;</i>{!!Lang::get('message.update')!!}</button>
          @endif
            {!! Form::close() !!}
        </div>
        <!-- /.box-body -->
       
      </div>
                <!-- /.tab-pane -->
            
         
        <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->
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


