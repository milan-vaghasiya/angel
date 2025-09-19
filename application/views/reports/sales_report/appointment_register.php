<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-end" style="width:70%;">
                        <div class="input-group">    
                            <label for="status" style="width:15%;">Status</label>
                            <label for="executive_id" style="width:30%;">Sales Executive</label>
                            <label for="mode" style="width:15%;">Mode</label>
                            <label for="from_date" style="width:14%;">From Date</label>
                            <label for="to_date" style="width:10%;">To Date</label>
                        </div>
					    <div class="input-group">
                            <div class="input-group-append" style="width:15%;">
                                <select id="status" name="status" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                    <option value="3">Delay</option>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:30%;">
                                <select id="executive_id" name="executive_id" class="form-control select2">
                                    <option value="0">All Executive</option>
                                    <?php
                                    if(!empty($salesExecutives)){
                                        foreach($salesExecutives as $row){
                                            ?>
                                            <option value="<?=$row->id?>"><?=$row->emp_name?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:15%;">
                                <select name="mode" id="mode" class="form-control select2">
                                <option value="0">All Mode</option>
                                    <?php
                                        foreach($this->appointmentMode as $mode):
                                            echo '<option value="'.$mode.'">'.$mode.'</option>';
                                        endforeach;
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
                                    <thead class="thead-dark">
                                        <tr>
                                            <th> # </th>
                                            <th>Reminder Date</th>
                                            <th>Executive Name</th>
                                            <th>Party Name</th>
                                            <th>Mode</th>
                                            <th>Notes</th>
                                            <th>Response</th>
                                            <th>Response Date</th>
                                            <th>Due Days</th>
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
    setTimeout(function(){ $(".loadData").trigger('click'); },500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var pdf_type = $(this).data('pdf_type'); 
		var status = $('#status').val();
		var executive_id = $('#executive_id').val();
		var mode = $('#mode').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();

        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData = {status:status, executive_id:executive_id, mode:mode, from_date:from_date, to_date:to_date,pdf_type:pdf_type};

		if(valid){
            if(pdf_type == 0){
                $.ajax({
                    url: base_url + controller + '/getAppointmentRegister',
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
                window.open(base_url + controller + '/getAppointmentRegister/'+encodeURIComponent(window.btoa(JSON.stringify(postData))));
            }
        }
    });   
});
</script>

<?php $this->load->view('includes/footer'); ?>
