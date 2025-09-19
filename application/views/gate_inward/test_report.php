<form enctype="multipart/form-data" data-res_function="getTestReportHtml">
    <div class="col-md-12">
        <table class="table jpExcelTable">
            <tr class="bg-light">
                <th>GRN No</th>
				<th>Party</th>
                <th>Item</th>
                <th>Grade</th>
                <th>Qty.</th>
            </tr>
            <tr>
                <td><?=$giData->trans_number?></td>
				<td><?=$giData->party_name?></td>
                <td><?=(!empty($giData->item_code) ? "[".$giData->item_code."] " : "").$giData->item_name?></td>
                <td><?=$giData->material_grade?></td>
                <td><?=floatval($giData->qty)?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value=""/>
            <input type="hidden" name="grn_id" id="grn_id" value="<?= (!empty($giData->grn_id)) ? $giData->grn_id : ""; ?>"/>
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?= (!empty($giData->id)) ? $giData->id : ""; ?>"/>
            <input type="hidden" name="batch_no" id="batch_no" value="<?= (!empty($giData->batch_no)) ? $giData->batch_no : ""; ?>"/>
            <input type="hidden" name="heat_no" id="heat_no" value="<?= (!empty($giData->heat_no)) ? $giData->heat_no : ""; ?>"/>

            <div class="col-md-5 form-group">
                <label for="name_of_agency">Name Of Agency</label>
                <select name="agency_id" id="agency_id" class="form-control select2 req">
                    <option value="">Select Agency</option>
                    <option value="0">Inhouse</option>
                    <?php
                    if(!empty($partyList)){
                        foreach($partyList as $row){
                            echo '<option value="'.$row->id.'" >'.$row->party_name.'</option>';
                        }
                    }
                    ?>
                </select>
                <input type="hidden" name="name_of_agency" id="name_of_agency" class="form-control req" value="" />
            </div>

            <div class="col-md-3 form-group">
                <label for="test_type">Test Type</label>
                <select name="test_type" id="test_type" class="form-control req select2">
                    <option value="">Select Test Type</option>
                    <?php
                    if (!empty($testTypeList)):
                        foreach ($testTypeList as $row):
                            echo '<option value="'.$row->id.'">'.$row->label.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="sample_qty">Sample Qty/Total Weight</label>
                <div class="input-group">
                    <input type="text" name="sample_qty" id="sample_qty" class="form-control floatOnly req" value="" />
                    <div class="input-group-append">
                        <?php
                            $param = "{'formId':'testReport','fnsave':'saveTestReport','controller':'gateInward','res_function':'getTestReportHtml'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<hr>
<div class="row">
    <h6>Report Details : </h6>
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table id="testReport" class="table jpExcelTable">
                <thead class="thead-info">
                    <tr class="text-center">
                        <th style="min-width:10px;">#</th>
                        <th style="min-width:50px;">Name Of Agency</th>
                        <th style="min-width:50px;">Test Type</th>
                        <th style="min-width:50px;">Test Report No</th>
                        <th style="min-width:50px;">Inspector Name</th>
                        <th style="min-width:30px;">Sample Qty</th>
                        <th style="min-width:50px;">Batch No.</th>
                        <th style="min-width:50px;">Ref./Heat No.</th>
                        <th style="min-width:50px;">Test Result</th>
                        <th style="min-width:30px;">T.C. File</th>
                        <th style="min-width:50px;">Test Remark</th>
                        <th style="min-width:50px;">Special Instruction</th>
                        <th style="min-width:30px;">Status</th>
                        <th style="min-width:150px;">Action</th>
                    </tr>
                </thead>
                <tbody id="testReportBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();	

    $(document).on('change',"#agency_id",function(e){
        e.stopImmediatePropagation();e.preventDefault();
        var party_name = $("#agency_id :selected").text();
        $("#name_of_agency").val(party_name);
    });

    if(!tbodyData){
        var postData = {'postData':{'grn_id':$("#grn_id").val(),'grn_trans_id':$("#grn_trans_id").val()},'table_id':"testReport",'tbody_id':'testReportBody','tfoot_id':'','fnget':'testReportHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function getTestReportHtml(data,formId="testReport"){ 
    if(data.status==1){
        $('#'+formId)[0].reset(); initSelect2();
        var postData = {'postData':{'grn_id':$("#grn_id").val(),'grn_trans_id':$("#grn_trans_id").val()},'table_id':"testReport",'tbody_id':'testReportBody','tfoot_id':'','fnget':'testReportHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function approvalStore(data){
	setPlaceHolder();

	var fnsave = data.fnsave || "save";
	var controllerName = data.controller || controller;

    var fd = data.postData;
    var resFunctionName = data.res_function || "";
    var msg = data.message || "Are you sure want to save this change ?";
    var ajaxParam = {
        url: base_url + controllerName + '/' + fnsave,
        data:fd,
        type: "POST",
        dataType:"json"
    };

	Swal.fire({
		title: 'Are you sure?',
		text: msg,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, Do it!',
	}).then(function(result) {
		if (result.isConfirmed){
			$.ajax(ajaxParam).done(function(response){
				if(resFunctionName != ""){
					window[resFunctionName](response);
				}else{
					if(response.status==1){
						initTable();
						Swal.fire( 'Success', response.message, 'success' );
					}else{
						if(typeof response.message === "object"){
							$(".error").html("");
							$.each( response.message, function( key, value ) {$("."+key).html(value);});
						}else{
							initTable();
							Swal.fire( 'Sorry...!', response.message, 'error' );
						}			
					}	
				}			
			});
		}
	});
}

function editTcReport(data,button){ 
	var row_index = $(button).closest("tr").index();
	$.each(data,function(key, value) {
		if(key != 'tc_file'){
			$("#"+key).val(value);
		}
	});
	setTimeout(function(){ $('#agency_id').trigger('change'); }, 10);
	initSelect2();
}
</script>