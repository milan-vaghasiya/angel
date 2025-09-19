<form data-res_function="getPrcLogResponse">
    
    <div class="row">
        <input type="hidden" id="pending_qty" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="finish_wt" id="finish_wt" value="<?=$dataRow->finish_wt?>">
        <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:0?>">
        <input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:0?>">
        <input type="hidden" name="trans_type" id="trans_type" value="<?=!empty($trans_type)?$trans_type:1?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=!empty($process_from)?$process_from:0?>">
        <div class="error die_error"></div>
        <?php /* if($this->userRole == 8): */ ?>
        <div class="col-md-3 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        <div class="col-md-3 form-group">
            <label for="in_challan_no">Challan No</label>
            <input type="text" name="in_challan_no" id="in_challan_no" class="form-control req">
        </div>
        <input type="hidden" name="process_by" id="process_by" value="<?=$process_by?>">
        <input type="hidden" name="processor_id" id="processor_id" value="<?=$processor_id?>">
       
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <table class="table mb-0 table-borderless" >
                    <thead>
                        <tr>
                            <th>Process</th>
                            <th>OK Qty.</th>
                            <th>Rejection Qty.</th>
                            <th>Without Process</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $processArray = explode(",",$dataRow->challan_process);
                        $processMaster = array_reduce($processList, function($processMaster, $process) { 
                                $processMaster[$process->id] = $process; 
                                return $processMaster; 
                            }, []);
                        foreach($processArray AS $process){ ?>
                            <tr>
                                <td><?=$processMaster[$process]->process_name?></td>
                                <td>
                                    <input type = "text" class="form-control" name="ok_qty[]">
                                    <input type="hidden" name="process_id[]" value="<?=$process?>">
                                    <div class="error ok_qty<?=$process?>">
                                </td>
                                <td>
                                    <input type = "text" class="form-control" name="rej_found[]">
                                </td>
                                <td>
                                    <input type = "text" class="form-control" name="without_process_qty[]">
                                </td>
                            </tr><?php
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12 form-group">
            <?php $param = "{'formId':'addLog','fnsave':'saveLog','res_function':'getPrcLogResponse','controller':'outsource'}";  ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
        <?php /* endif; */ ?>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th>In Challan No</th>
                        <th style="min-width:100px">Date</th>
                        <th>Process</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th>
						<th>Without Process Return</th>
                        <th style="min-width:100px">Created By/At</th> 
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="logTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getReceiveLogHtml','controller':'outsource'};

        getPRCLogHtml(postData);
        tbodyData = true;
    }

    $(document).on("keyup",".qtyLogCal", function(e){
        e.stopImmediatePropagation();e.preventDefault();
        $(".production_qty").html("");
        var rej_qty = $("#rej_found").val()|| 0;
        // var without_process_qty = $("#without_process_qty").val()|| 0;
        var production_qty = parseFloat($("#production_qty").val() )|| 0
		// var okQty=production_qty-(parseFloat(rej_qty) + parseFloat(without_process_qty));
        var okQty=((production_qty > 0)?(production_qty-parseFloat(rej_qty)) : 0);
		$("#ok_qty").val(okQty);
        var finish_wt = parseFloat($("#finish_wt").val()) || 0;
       var qty_kg = 0;
       if(finish_wt > 0){
            qty_kg = production_qty*finish_wt;
       }
       $("#qty_kg").val(qty_kg);
    });

    $(document).on("keyup",".calKg2Pc", function(e){
        e.stopImmediatePropagation();e.preventDefault();
        $(".production_qty").html("");
        var qty_kg = $("#qty_kg").val() || 0;
        var finish_wt = parseFloat($("#finish_wt").val()) || 0;
        var pending_qty = parseFloat($("#pending_qty").val()) || 0;
        var qty_pc = 0;
        if(finish_wt > 0){
                qty_pc = parseInt(qty_kg/finish_wt);
        }
        if(qty_pc > pending_qty){
                var conv_ratio  = parseFloat($("#conv_ratio").val()) || 0;
                var ratioQty = pending_qty + ((pending_qty*conv_ratio)/100);
                if(ratioQty >= qty_pc){
                    qty_pc = pending_qty;
                }else{
                    $(".production_qty").html("Invalid Pcs");
                    qty_pc = 0;
                }
        }
        $("#production_qty").val(qty_pc);
        $("#ok_qty").val(qty_pc);
    });


    $(document).on('change','#process_by',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var process_by = $(this).val();
        if(process_by != 3) {		
            $.ajax({
                url:base_url  + "sopDesk/getProcessorList",
                type:'post',
                data:{process_by:process_by}, 
                dataType:'json',
                success:function(data){
                    $("#processor_id").html("");
                    $("#processor_id").html(data.options);
                }
            });
        }
    });

    

});
function getPrcLogResponse(data,formId="addLog"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getReceiveLogHtml','controller':'outsource'};
        getPRCLogHtml(postData);
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