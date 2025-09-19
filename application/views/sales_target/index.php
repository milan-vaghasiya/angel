<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-12">
				<form id="targetDataForm">
					<div class="card">
						<div class="card-header">
							<div class="row">
								<div class="col-md-8">
								</div>
								<div class="col-md-4 float-right">   
                            		<div class="input-group">
                                		<div class="input-group-append" style="width:40%;">
											<select name="month" id="month" class="form-control select2">
												<option value="">Month</option>
												<?php   
													foreach($monthList as $row): 
														echo '<option value="'.$row['val'].'">'.$row['label'].'</option>';
													endforeach; 
												?>
											</select>
											<div class="error month"></div>
										</div>    
										<div class="input-group-append">
											<button type="button" class="btn waves-effect waves-light btn-success loaddata" title="Load Data">
												<i class="fas fa-sync-alt"></i> Load
											</button>
										</div>
									</div>                      
								</div>                                         
							</div>
						</div>
						<div class="card-body reportDiv" style="min-height:75vh">
							<div class="table-responsive">
								<table id='targetTable' class="table table-bordered jpDataTable">
									<thead class="thead-dark" id="theadData">
										<tr>
											<th style="width:5%;">#</th>
											<th>Employee Code</th>
											<th>Employee Name</th>
											<th>Lead</th>
											<th>Visit</th>
											<th>Amount</th>
										</tr>
									</thead>
									<tbody class="salesTargetData"></tbody>
								</table>
							</div>
						</div>
					</div>
				</form>
            </div>
        </div>  
    </div>
</div>

<div class="bottomBtn bottom-25 right-25 permission-write">
<?php $postData = "{'formId':'targetDataForm','fnsave':'saveTargets','table_id':'targetTable'}"; ?>
    <button type="button" class=" btn btn-primary btn-round  font-bold permission-write save-form" style="letter-spacing:1px;" onclick="customStore(<?=$postData?>);">SAVE</button>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	$(document).on("click",".loaddata",function(){
		var valid = 1;
        var month = $("#month").val();
		if($("#month").val() == ""){$(".month").html("Month is required.");valid=0;}
		if(valid){
			$.ajax({
				url:base_url + controller + '/getSalesTargetDetails',
				type:'post',
				data:{month:month},
				dataType:'json',
				success:function(data)
				{
					$("#targetTable").DataTable().clear().destroy();
					$(".salesTargetData").html(data.targetData);
					reportTable();
				}
			});
		}
	});
});

function reportTable()
{
	var reportTable = $('#targetTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger('click');}}]
	});
	reportTable.buttons().container().appendTo( '#targetTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}

</script>