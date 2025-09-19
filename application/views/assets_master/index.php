<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
					<div class="float-start">
						<ul class="nav nav-pills">    
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/0") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 0)?'active':''?>">New Inward</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/1") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 1)?'active':''?>">In Stock</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/2") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 2)?'active':''?>">Issued</a> </li>
							<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/3") ?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 3)?'active':''?>">Rejected</a> </li>
						</ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='assetsMasterTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectAssetsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Reject Asset [Asset Code: <span id="assets_code"></span>]</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="rejectAssets">
                <input type="hidden" name="assets_id" id="assets_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="reject_reason">Reject Reason</label>
                            <textarea name="reject_reason" id="reject_reason" class="form-control req" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary close" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success" onclick="saveRejectAsset('rejectAssets');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    initBulkChallanButton();
    $(document).on('click','.rejectAssets',function(){
        var id = $(this).data('id');
        var assets_code = $(this).data('assets_code');
        $(".error").html("");
		$("#rejectAssetsModal").modal('show');
		$("#assets_code").html(assets_code);
		$("#assets_id").val(id);
    });
    
	$(document).on('click', '.BulkAssetChallan', function() {
		if ($(this).attr('id') == "masterAssetSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkChallan").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkChallan").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkChallan").show();
				$("#masterAssetSelect").prop('checked', false);
			} else {
				$(".bulkChallan").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterAssetSelect").prop('checked', true);
				$(".bulkChallan").show();
			}
			else{$("#masterAssetSelect").prop('checked', false);}
		}
	});
	
	$(document).on('click', '.bulkChallan', function() {
		var ref_id = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id.push(this.value);
		});
		var ids = ref_id.join("~");
		var send_data = {
			ids
		};
		Swal.fire({
			title: 'Are you sure?',
			text: 'Are you sure want to generate Challan?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Do it!',
		}).then(function(result) {
			if (result.isConfirmed){				
				window.open(base_url + controller + '/createChallan/' + ids, '_self');
			}
		});
	});

});

function saveRejectAsset(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveRejectAsset',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status==1){
			$("#gauge_id").val(""); $("#assets_code").html(""); $(".modal").modal('hide');
			initTable(); $('#'+formId)[0].reset(); closeModal(formId);
			Swal.fire({ icon: 'success', title: data.message});
			$(".modal-select2").select2();
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				initTable();
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}		
	});
}

function initBulkChallanButton() {
	var bulkChallanBtn = '<button class="btn btn-outline-dark bulkChallan" tabindex="0" aria-controls="instrumentTable" type="button"><span>Bulk Challan</span></button>';
	$("#assetsMasterTable_wrapper .dt-buttons").append(bulkChallanBtn);
	$(".bulkChallan").hide();
}
</script>