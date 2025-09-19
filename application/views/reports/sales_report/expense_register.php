<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:50%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:30%;">
                                <select id="emp_id" name="emp_id" class="form-control select2">
                                    <option value="0">All Employee</option>
                                    <?php
                                    if(!empty($empList)){
                                        foreach($empList as $row){
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
                                <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
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
                                            <th> # </th>
                                            <th>Exp.Date</th>
                                            <th>Exp. No.</th>
                                            <th>Employee Name</th>
                                            <th>Exp Type</th>
                                            <th>Demand Amount</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot class="thead-dark" id="tfootData">
                                        <tr>
                                            <th colspan="5">Total</th>
                                            <th>0</th>
                                            <th></th>
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
        var emp_id = $("#emp_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getExpenseRegister',
                data: {emp_id:emp_id,from_date:from_date,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});
</script>