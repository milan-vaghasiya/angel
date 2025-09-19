<form data-res_function="getReturnResponse">
    <div class="card">
                <div class="media align-items-center btn-group process-tags">
                    <span class="badge bg-light-peach btn flex-fill">Item : <?=!empty($dataRow->item_name)?$dataRow->item_name:''?></span>
                    <span class="badge bg-light-cream btn flex-fill" id="pending_stock_qty">Available Qty :  </span>
                </div>                                       
    </div>
    <div class="row">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="prc_bom_id" id="prc_bom_id" value="<?=$dataRow->id?>">
        <input type="hidden" name="prc_number" id="prc_number" value="<?=$dataRow->prc_number?>">
        <input type="hidden" name="item_id" id="item_id" value="<?=$dataRow->item_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$dataRow->process_id?>">
        <div class="col-md-4 form-group">
            <label for="return_type">Return Type</label>
            <select name="return_type" id="return_type" class="form-control">
                <option value="1">Return AS Usable</option>
                <option value="2">End Piece Return</option>
                <option value="3">Scrap</option>
                <option value="4">Material Reject</option>
            </select>
        </div>
        <div class="col-md-4 form-group location">
            <label for="location_id">Location</label>
            <?php
            $locations = !empty($dataRow->location_id)?explode(",",$dataRow->location_id):[];
            ?>
            <select id="location_id" name="location_id" class="form-control select2 req">
                <option value="">Select Location</option>
                <?=getLocationListOption($locationList,((!empty($locations[0]))?$locations[0]:0))?>
            </select>  
            
        </div>
        <div class="col-md-4 form-group batchNo">
            <label for="batch_no">Batch No.</label>
            <select id="batch_no" class="form-control select2 req" name="batch_no">
                <?php
                $batchList = !empty($dataRow->batch_no)?explode(",",$dataRow->batch_no):[];
                if(!empty($batchList)){
                    foreach($batchList as $batch_no){
                        ?>
                        <option value="<?=$batch_no?>"><?=$batch_no?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
         <div class="col-md-4 form-group">
            <label for="qty">Qty.(Kg/Pcs)</label>
            <input type="text" name="qty" id="qty" class="form-control floatOnly req" placeholder="Enter Quantity" value="0" min="0" />
        </div>
        <div class="col-md-4 form-group endPcs">
            <label for="end_pcs">End Pcs</label>
            <input type="text" name="end_pcs" id="end_pcs" class="form-control floatOnly" placeholder="Enter Quantity" value="0" min="0" />
        </div>
        <div class="col-md-12 from-group remarkDiv">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" />
        </div>
        <div class="col-md-12 form-group float-end mt-2">
            <?php $param = "{'formId':'materialReturn','fnsave':'storeReturnedMaterial','res_function':'getReturnResponse'}"; ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="storeSop(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Return Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='returnTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:50px">Date</th>
                        <th style="min-width:50px">Location</th>
                        <th style="min-width:50px">Batch No</th>
                        <th>Qty.</th>
                        <th>Remark.</th>
                        <th style="width:50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="returnTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'item_id':$("#item_id").val(),'process_id':$("#process_id").val(),'prc_bom_id':$("#prc_bom_id").val()},'table_id':"returnTable",'tbody_id':'returnTbodyData','tfoot_id':'','fnget':'getReturnHtml'};
        getReturnHtml(postData);
        tbodyData = true;
    }
    setTimeout(function(){  $("#return_type").trigger("change"); }, 30);
    $(document).on('change','#return_type',function(){
		var return_type=$(this).val();
		if(return_type == 1){
            $(".location").show();
            $(".endPcs").hide();
            $(".remarkDiv").removeClass("col-md-12");
            $(".remarkDiv").addClass("col-md-8");
		}else if(return_type == 2){
            $(".location").hide();
            $(".endPcs").show();
            $(".remarkDiv").removeClass("col-md-12");
            $(".remarkDiv").addClass("col-md-8");
		}else if(return_type == 3){
            $(".location").hide();
            $(".endPcs").hide();
            $(".remarkDiv").removeClass("col-md-8");
            $(".remarkDiv").addClass("col-md-12");
		}else if(return_type == 4){
            $(".location").show();
            $(".endPcs").hide();
            $(".remarkDiv").removeClass("col-md-12");
            $(".remarkDiv").addClass("col-md-8");
		}
	});
});
function getReturnResponse(data,formId="materialReturn"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'item_id':$("#item_id").val(),'process_id':$("#process_id").val(),'prc_bom_id':$("#prc_bom_id").val()},'table_id':"returnTable",'tbody_id':'returnTbodyData','tfoot_id':'','fnget':'getReturnHtml'};
        getReturnHtml(postData);
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