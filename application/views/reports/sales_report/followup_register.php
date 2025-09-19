
<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                     <div class="float-end" style="width:60%;">
                        <div class="input-group">    
                            <label for="party_id" style="width:30%;">Customer</label>
                            <label for="business_type" style="width:20%;">Business Type</label>
                            <label for="from_date" style="width:17%;">From Date</label>
                            <label for="to_date" style="width:10%;">To Date</label>
                        </div>
					    <div class="input-group">
                            <div class="input-group-append" style="width:30%;">
                               <select id="party_id" name="party_id" class="form-control select2">
                                    <option value="0">All Customer</option>
                                    <?php
                                        if(!empty($partyList)){
                                            foreach($partyList as $row){
                                                ?>
                                                <option value="<?=$row->id?>"><?=$row->party_name?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:20%;">
                                <select name="business_type" id="business_type" class="form-control select2">
                                    <option value ="">Select Business Type</option>
                                    <?php
                                        if(!empty($businessTypeList)){
                                            foreach($businessTypeList as $row){
                                                ?>
                                                <option value="<?=$row->label?>"><?=$row->label?></option>
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
                                            <th>Date</th>
                                            <th>Executive Name</th>
                                            <th>Party Name</th>
                                            <th>Business Type</th>
                                            <th>FollowUp Massage</th>
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
		var party_id = $('#party_id').val();
		var business_type = $('#business_type').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();

        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var postData =  {party_id:party_id, business_type:business_type,from_date:from_date, to_date:to_date,pdf_type:pdf_type};

        if(valid){
            if(pdf_type == 0){
                $.ajax({
                    url: base_url + controller + '/getFollowUpRegister',
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
                window.open(base_url + controller + '/getFollowUpRegister/'+encodeURIComponent(window.btoa(JSON.stringify(postData))));
            }
        }
    });   
});
</script>

<?php $this->load->view('includes/footer'); ?>