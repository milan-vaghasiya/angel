<form data-res_function="getReviewResponse">
    <div class="row">
        <div class="col-md-4 form-group">
            <label for="rework_type">Rework</label>
            <select id="rework_type" name="rework_type" class="form-control req">
                <option value="">Select type</option>
                <option value="1">Regular</option>
                <option value="2">Separate</option>
            </select>
            <div class="error rework_type"></div>
        </div>
        <div class="col-md-4 form-group">
            <input type="hidden" name="id">
            <input type="hidden" id="prc_id" name="prc_id" value="<?= (!empty($dataRow->prc_id) ? $dataRow->prc_id : '') ?>">
            <input type="hidden" id="log_id" name="log_id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
            <input type="hidden" id="item_id" name="item_id" value="<?= (!empty($dataRow->item_id) ? $dataRow->item_id : '') ?>">
            <input type="hidden" id="decision_type" name="decision_type" value="2">
            <input type="hidden" id="source" name="source" value="<?=$source?>">
            <label for="qty">Rework Qty</label>
            <input type="text" id="qty" name="qty" class="form-control req numericOnly">
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_reason">Rework Reason</label>
            <select id="rr_reason" name="rr_reason" class="form-control select2 req">
                <option value="">Select Reason</option>
                <?php
                foreach ($reworkComments as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_stage">Rework Process</label>
            <select id="rr_stage" name="rr_stage" class="form-control select2 req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                echo $dataRow->stage;
                                                                                            } ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_by">Rework By <span class="text-danger">*</span></label>
            <select id="rr_by" name="rr_by" class="form-control select2 req">
                <option value="">Select </option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="mc_op_id">Operator / Machine</label>
            <select id="mc_op_id" name="mc_op_id" class="form-control select2 req">
				<option value="">Select</option>
                
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="in_ch_no">In Challan No</label>
            <select id="in_ch_no" name="in_ch_no" class="form-control select2">
				<option value="">Select</option>
                
            </select>
        </div>
        <div class="col-md-4 form-group rwJob" style="display:none">
            <label for="rw_job_id">Rework Job</label>       
            <select name="rw_job_id" id="rw_job_id" class="form-control select2 req">
                <option value="-1">New</option>
                <?php
                if(!empty($rwJobList)){
                    foreach($rwJobList AS $row){
                        
                        ?><option value="<?=$row->id?>"><?=$row->prc_number?></option><?php
                    }
                }
                ?>
            </select>                                                                    
        </div>
        <div class="col-md-4 form-group rwProcess">
            <label for="rw_process">Rework Process</label>
            <select  id="rw_process" name="rw_process[]" class="form-control select2 req" >
                <option value="">Select</option>
                <?php 
                $in_process_key = array_keys(array_column($prcProcessList,'id'), $dataRow->process_id)[0];
                foreach ($prcProcessList as $key => $row) {
                    $reworkType = (($key <= $in_process_key)?'1':2);
                    echo '<option value="' . $row->id . '" data-process_name="' . $row->process_name . '" data-process_id="' . $row->id . '" data-rework_type = "'.$reworkType.'">' . $row->process_name . '</option>';
                    
                }
                ?>
            </select>
            <div class="error rw_process"></div>
        </div>
        <div class="col-md-12 form-group">
            <label for="rr_comment">Note</label>
            <textarea id="rr_comment" name="rr_comment" class="form-control" value=""></textarea>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <h5 >Review Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='rejTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th>Qty.</th>
                        <th>Decision</th>
                        <th>Reason</th>    
                        <th>Rej/Rw Process</th>                        
                        <th>Rej/Rw By</th>                        
                        <th>Operator</th>                        
                        <th>Machine</th>
                        <th>In Challan No</th>
                        <th>Remark</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="rejTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
if(!tbodyData){
    var postData = {'postData':{'log_id':$("#log_id").val(),'source':$("#source").val()},'table_id':"rejTransTable",'tbody_id':'rejTbodyData','tfoot_id':'','fnget':'getReviewHtml'};
    getTransHtml(postData);
    tbodyData = true;
}

$(document).ready(function(){
    $(document).on("change", "#rework_type", function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        var rework_type = $("#rework_type").val();
        if(rework_type == 1){
            $(".rwJob").hide();
            $("#rw_process").removeAttr("multiple");
            $('#rw_process option').filter('[data-rework_type="2"]').prop('disabled', true).parent().val('');

        }else{
            $(".rwJob").show();
                $("#rw_process").attr("multiple","multiple");
                $('#rw_process option').prop('disabled', false).filter('[data-rework_type="2"], [value=""]').prop('disabled', false).parent().val('');
        }
        $("#rw_process").val("");
        $("#rw_process").select2();
    });
    $(document).on("change", "#rw_job_id", function() {
        var rw_job_id = $("#rw_job_id").val();
        if(rw_job_id == -1){
            $(".rwProcess").show();
        }else{
            $(".rwProcess").hide();  
        }
        
    });
});
</script>