<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-8">
							
						</div>
						<div class="col-md-3">
							<select name="prc_id" id="prc_id" class="form-control select2">
								<option value="">Select PRC</option>
								<?php   
								if(!empty($prcList)):
									foreach($prcList as $row): 
										echo '<option value="'.$row->id.'">'.$row->prc_number.'</option>';
									endforeach; 
								endif;
								?>
							</select>
						</div>
						<div class="col-md-1">
							<button type="button" class="btn waves-effect waves-light btn-success loadData" title="Load Data">
								<i class="fas fa-sync-alt"></i> Load
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
				    
	
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
                                <thead id="theadData" class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Process Name</th>
                                        <th>OK Qty.</th>
                                        <th>Costing/Pcs</th>
                                        <th>Total Costing</th>                                            
                                    </tr>
                                </thead>
                                <tbody id="tbodyData"></tbody>
                                <tfoot class="thead-dark" id="tfootData">
                                    <tr>
                                        <th colspan="4">Total Costing</th>
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


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	$(document).on('click','.loadData',function(){
		$(".error").html("");
		var valid = 1;
		var prc_id = $('#prc_id').val();
        if($("#prc_id").val() == ""){$(".prc_id").html("PRC is required.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getJobCostingData',
				data: { prc_id:prc_id },
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