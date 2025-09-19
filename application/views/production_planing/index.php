<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesOrderTable',0,'getProductionDtHeader','productionPlanningSo');" id="pending_so" class="nav-tab btn waves-effect waves-light btn-outline-danger active pending_so" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending Orders</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('salesOrderTable',1,'getProductionDtHeader','productionPlanning');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Planned Orders</button> 
                            </li>
                        </ul>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='salesOrderTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function() {
	setTimeout(() => {
		initbulkPlButton();
	}, 1000);
	$(document).on('click', '.pending_so', function() {
		setTimeout(() => {
			initbulkPlButton();
		}, 1000);
	});
	$(document).on('click', '.bulkPlan', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkPl").show();
				
				$("input[name='so_trans_id[]']").prop('checked', true);
			} else {
				$(".bulkPl").hide();
				$("input[name='so_trans_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='so_trans_id[]']").not(':checked').length != $("input[name='so_trans_id[]']").length) {
				$(".bulkPl").show();
				
				$("#masterSelect").prop('checked', false);
			} else {
				$(".bulkPl").hide();
			}

			if ($("input[name='so_trans_id[]']:checked").length == $("input[name='so_trans_id[]']").length) {
				$("#masterSelect").prop('checked', true);
				$(".bulkPl").show();
				
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});
	
	$(document).on('click', '.bulkPl', function() {
		var so_trans_id = []; var qty = []; var sendData = [];
		$("input[name='so_trans_id[]']:checked").each(function() {
			so_trans_id = $(this).val();
			sendData.push(so_trans_id);//08-04-25
		});
        var postdata = {so_trans_id:sendData}
		var call_function ='addBulkPlan';
        var fnsave = 'savePlan';
        var modal_id = 'bs-right-xl-modal';
        var form_id = 'addBulkPlan';
        var js_store_fn = 'customStore';
        var controllerName = controller;
        var data = {call_function:call_function,fnsave:fnsave,modal_id:modal_id,form_id:form_id};
        var ajaxParam = {
            url: base_url + controllerName + '/' + call_function,   
            type: "POST",   
            data: postdata
        }; 
       
        $.ajax(ajaxParam).done(function(response){
            initModal(data,response);
        })
		
	});

	
});

function initbulkPlButton() {
	var bulkPlBtn = '<button class="btn btn-outline-dark bulkPl" tabindex="0" aria-controls="salesOrderTable" type="button"><span>Bulk Plan</span></button>';
	$("#salesOrderTable_wrapper .dt-buttons").append(bulkPlBtn);
	$(".bulkPl").hide();
}

function getPlanningResponse(data,formId = ""){
	if(data.status==1){
		initTable();
		if(formId){
			$('#'+formId)[0].reset(); closeModal(formId);
		} 
		
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
	setTimeout(() => {
		initbulkPlButton();
	}, 1000);
}
</script>