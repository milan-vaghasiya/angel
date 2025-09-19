<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:50%;">
                        <div class="input-group">
                            <label for="executive_id" style="width:40%;">Sales Executive</label>
                            <label for="inactive_days" style="width:20%;">Inactive Days</label>
                        </div>
					    <div class="input-group">
                            <div class="input-group-append" style="width:40%;">
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
                            <div class="input-group-append" style="width:20%;">
                                <input type="text" name="inactive_days" id="inactive_days" class="form-control numericOnly" value="10">
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
                                            <th>#</th>
                                            <th>Party Name</th>
                                            <th>Business Type</th>
                                            <th>Contact Person</th>
                                            <th>Contact No.</th>
                                            <th>Sales Executive</th>
                                            <th>Address</th>
                                            <th>Inactive Days</th>
                                            <th>Last Activity Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <!-- <tfoot class="thead-dark" id="tfootData">
                                        <tr>
                                            <th colspan="4">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                        </tr>
                                    </tfoot> -->
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
        var inactive_days = $("#inactive_days").val();
        var executive_id = $("#executive_id").val();
        var postData = {inactive_days:inactive_days,executive_id:executive_id,pdf_type:pdf_type};

		if(valid){
            if(pdf_type == 0){
                $.ajax({
                    url: base_url + controller + '/getInactivePartyList',
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
                window.open(base_url + controller + '/getInactivePartyList/'+encodeURIComponent(window.btoa(JSON.stringify(postData))));
            }
        }
    });   
});
</script>