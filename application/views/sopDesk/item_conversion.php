<form data-res_function="getPrcMovementResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-peach btn flex-fill">Currunt Process : <?=!empty($dataRow->current_process)?$dataRow->current_process:(!empty($semiFinish)?'Semi Finished':'')?></span>
            <span class="badge bg-light-cream btn flex-fill" id="pending_movement_qty">Pending Qty :  </span>
        </div>                                       
    </div>
    <div class="row">
        <input type="hidden" id="pending_qty" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=!empty($dataRow->process_id)?$dataRow->process_id:(!empty($semiFinish)?1:'')?>">
        <input type="hidden" name="finish_wt" id="finish_wt" value="<?=!empty($dataRow->finish_wt)?$dataRow->finish_wt:''?>">
        <input type="hidden" name="move_from" id="move_from" value="<?=!empty($move_type)?$move_type:1?>"> 
        <input type="hidden" name="process_from" id="process_from" value="<?=!empty($process_from)?$process_from:0?>"> 
        <div class="col-md-4 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
      
        <div class="col-md-4 form-group">
            <label for="qty"> Qty(pcs)</label>
            <input type="text" id="qty" name="qty" class="form-control numericOnly req qtyCal" value="">
        </div>
        <div class="col-md-4 form-group">
            <label for="convert_item">Convert Item</label>
            <select name="convert_item" id="convert_item" class="form-control select2">
                <option value="">Select Item</option>
                <?php
                if(!empty($itemList)){
                    foreach($itemList AS $row){
                        ?><option value="<?=$row->id?>"><?=$row->item_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-12 form-group remarkDiv">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'itemConversion','fnsave':'saveConversion','res_function':'getPrcMovementResponse'}";
                    ?>
                    <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right btn-block" onclick="storeSop(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='movementTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Qty.</th>
                        <th>Converted Item</th>
                        <th>Remark</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="movementTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
  
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'trans_type':$("#move_from").val(),'process_from':$("#process_from").val(),'send_to':4},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCConversionHtml'};
        getPRCMovementHtml(postData);
        tbodyData = true;
    }
});
function getPrcMovementResponse(data,formId="itemConversion"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
       var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'trans_type':$("#move_from").val(),'process_from':$("#process_from").val(),'send_to':4},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCConversionHtml'};
        getPRCMovementHtml(postData);
        currLoc = $(location).prop('href');
        if (currLoc.indexOf('/sopDesk/productionLog/') > 0) { 
			initTable();
		}
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}
</script>