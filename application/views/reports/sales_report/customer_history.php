<?php $this->load->view('includes/header'); ?>
<link href="<?=base_url();?>assets/app/css/modules-widgets.css" rel="stylesheet" type="text/css">  

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end" style="width:40%;">
                        <div class="input-group">
                            <div class="input-group-append" style="width:40%;">
                                <select id="party_id" class="form-control select2">
                                <option value="0">Select Customer</option>
                                    <?=getPartyListOption($customerData)?>
                                </select>
                            </div>
                            <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                                <i class="fas fa-sync-alt"></i> Load
                            </button>
                            </div>										
                        </div>	
                    </div>									
                </div>
            </div>
        </div>
        <div class="row mt-3">              
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 text-sm-right">
                <div class="widget widget-activity-five no-border party_activity">                             
                    <div class="salesLogDiv mt-2" style="padding-left:15px;">
                    </div>
                </div>
            </div>   
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 text-sm-right">                            
                <div class="widget widget-activity-five no-border party_activity">                            
                    <div class="activityDiv mt-2" style="padding-left:15px;">
                    </div>
                </div>
            </div>    
        </div>
    </div>		
</div>
                                        
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
    
    $(document).on('click','.loadData',function(){
		$(".error").html("");
        var valid = 1;
        var party_id = $('#party_id').val();
        if($("#party_id").val() == 0){$(".party_id").html("Customer is required.");valid=0;}

        if(valid){
            $.ajax({
            url: base_url + controller + '/getCustomerHistory',
            data: { party_id:party_id },
            type: "POST",
            dataType:'json',
                success:function(data){
                    $(".salesLogDiv").html("");
                    $(".activityDiv").html("");
                    $(".salesLogDiv").html(data.html);
                    $(".activityDiv").html(data.html2);
                }
            });
        }
	});
    
    // $('.party_activity').each((index, element) => {
    //     new PerfectScrollbar(element);
    // });
});
</script>