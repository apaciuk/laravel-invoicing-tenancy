<div class="modal fade" id="domainModal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="defaultModalLabel">Are you sure?</h4>
			<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		</div>
		<div class="modal-body">
			<div id="response1"></div>
			   <input type="hidden" name="orderId" id="orderId">

          <div class="form-group">
			<span style="color:red;">*&nbsp By reissuing the license, all Installation on the current domain will be aborted. </span>
		</div>  
		</div>
		
		  <div class="modal-footer justify-content-between">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Close</button>
			  <button type="submit" id="domainSave"  class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;Save</button>
            </div>
	</div>
</div>
</div>
@section('script')
@stop