<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-8">
				<h4 class="card-title pageHeader"><?=$headData->pageTitle;?></h4>
			</div>
			<div class="col-sm-2">
				<div class="input-group-append">
					<select id="type" name="type" class="form-control select2">
						<option value="">All</option>
						<option value="0">In Progress</option>
						<option value="1">Recruited</option>
						<option value="2">Rejected</option>
					</select>
				</div>
			</div>
			
			<div class="col-sm-2">
				<div class="input-group">
					<div class="input-group-append">
						<button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData " title="Load Data">
							<i class="fas fa-sync-alt"></i> Load
						</button>
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
									<thead id="theadData" class="thead-info">
										<tr>
											<th>#</th>
											<th>Emp. Name</th>
											<th>Contact</th>
											<th>New Application</th>
											<th>Document Verification</th>
											<th>Technical Interview</th>
											<th>HR Interview</th>
											<th>Appointed Interview</th>
											<th>Rejected Interview</th>
											<th>Duration(Days)</th>
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
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
	    var type = $('#type').val();
       
		if(valid){
            $.ajax({
                url: base_url + controller + '/getRecruitmentData',
                data: {type:type},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   
});
</script>