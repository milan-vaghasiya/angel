<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:30%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:40%;">
                                <select name="target_month" id="target_month" class="form-control select2">
                                    <option value="">Month</option>
                                    <?php   
                                        foreach($monthData as $row): 
                                            echo '<option value="'.$row['val'].'">'.$row['label'].'</option>';
                                        endforeach; 
                                    ?>
                                </select>
                                <div class="error target_month"></div>
                            </div>    
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf_type = "0" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button> 
                                <button type="button" class="btn waves-effect waves-light btn-primary float-right loadData" data-pdf_type= "1" title="Load Data">
                                    <i class="fas fa-print"></i> PDF
                                </button>
                            </div>
                        </div>  
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark">
                                       <tr>
                                        <th class="checkbox-column" rowspan="2"> # </th>
                                        <th rowspan="2">Sales Executive</th>
										<th colspan="3" class="text-center">Visit Target</th>
										<th colspan="3" class="text-center">Lead Target</th>
										<th colspan="3" class="text-center">Amount Target</th>
									</tr>
									<tr>
										<th>Target</th>
										<th>Achievement</th>
										<th>Achievement Ratio</th>
										<th>Target</th>
										<th>Achievement</th>
										<th>Achievement Ratio</th>
										<th>Target</th>
										<th>Achievement</th>
										<th>Achievement Ratio</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                </table>
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
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var pdf_type = $(this).data('pdf_type'); 
        var target_month = $("#target_month").val();
        var postData = {target_month:target_month,pdf_type:pdf_type};

		if(valid){
            if(pdf_type == 0){
                $.ajax({
                    url: base_url + controller + '/getTargetVsAchieveData',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").DataTable().clear().destroy();
                        $("#tbodyData").html(data.tbody);
                        reportTable();
                    }
                });
            }else{
				window.open(base_url + controller + '/getTargetVsAchieveData/'+encodeURIComponent(window.btoa(JSON.stringify(postData))));
            }
        }
    });   
});
</script>