<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:60%;">
                        <div class="input-group">
                            <label for="report_type" style="width:20%;">Report Type</label>
                            <label for="executive_id" style="width:30%;">Sales Executive</label>
                            <label for="from_date" style="width:17%;">From Date</label>
                            <label for="to_date" style="width:10%;">To Date</label>
                        </div>
					    <div class="input-group">
                            <div class="input-group-append" style="width:20%;">
                                <select id="report_type" name="report_type" class="form-control select2">
                                    <option value="1">Yearly</option>
                                    <option value="2">Monthly</option>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:30%;">
                                <select id="executive_id" name="executive_id" class="form-control select2">
                                    <option value="0">All Executive</option>
                                    <?php
                                    if(!empty($executiveList)){
                                        foreach($executiveList as $row){
                                            ?>
                                            <option value="<?=$row->id?>"><?=$row->emp_name?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" style="width:10%;"/>                                    
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" style="width:10%;"/>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf_type = "0" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button> 
                                <button type="button" class="btn waves-effect waves-light btn-primary float-right loadData" data-pdf_type= "1" title="Load Data">
                                    <i class="fas fa-print"></i> PDF
                                </button>
                            </div>
                        </div>
                        <div class="error fromDate"></div>
                        <div class="error toDate"></div>
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
											<th rowspan="2">Party Name</th>
											<th rowspan="2">Contact No.</th>
											<th rowspan="2">Address</th>
											<th rowspan="2">Sales Executive</th>
                                            <th colspan="3" class="text-center">Total</th>
										</tr>
                                        <tr>
                                            <th>Taxable<br>Amount</th>
                                            <th>Budget</th>
                                            <th>Per (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot class="thead-dark" id="tfootData">
                                        <tr>
                                            <th colspan="4">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                        </tr>
                                    </tfoot>
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
        var report_type = $("#report_type").val();
        var executive_id = $("#executive_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData = {report_type:report_type,executive_id:executive_id,from_date:from_date,to_date:to_date,pdf_type:pdf_type};

		if(valid){
            if(pdf_type == 0){
                $.ajax({
                    url: base_url + controller + '/getPartyBudgetDetails',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").DataTable().clear().destroy();
                        $("#theadData").html(data.thead);
                        $("#tbodyData").html(data.tbody);
                        $("#tfootData").html(data.tfoot);
                        reportTable();
                    }
                });
            }else{
                window.open(base_url + controller + '/getPartyBudgetDetails/'+encodeURIComponent(window.btoa(JSON.stringify(postData))));
            }
        }
    });   
});
</script>