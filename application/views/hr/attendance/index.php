<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-12">
				<form id="attendanceForm">
					<div class="card">
						<div class="card-header">
							<div class="row">
                                <div class="col-md-6">
                                    <h4 class="page-title">Attendance</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
										<div class="input-group-append" >           
											<?php
												
												$excelParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'uploadAttendance', 'form_id' : 'attendForm', 'title' : 'Upload Attendance', 'res_function':'resAttendanceUpload','button':'close', 'js_store_fn' : 'confirmStore', 'fnsave':'importAttendanceExcel'}";

											?>
											<button type="button" class="btn waves-effect waves-light btn-outline-info float-right permission-write press-add-btn " onclick="modalAction(<?=$excelParam?>);"><i class="fas fa-upload"></i> Upload Attendance</button>
										</div>
										<div class="input-group-append" style="width: 30%;"> 
                                        	<input type="date" id="attendance_date" name="attendance_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
                                        </div>
                                        <div class="input-group-append" style="width: 10%;">
                                            <button class="btn btn-info loadData" type="button">Load</button>
                                        </div>
                                    </div>
                                    <div class="error reportDate"></div>
                                </div>  
                            </div>                                         
						</div>
						<div class="card-body reportDiv" style="min-height:75vh">
							<div class="table-responsive">
								<table id='reportTable' class="table table-bordered jpDataTable">
									<thead class="thead-info">
										<tr>
											<th>#</th>
                                            <th>Code</th>
                                            <th>Emp Name</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Category</th>
                                            <th>Attend. Status</th>
										</tr>
									</thead>
									<tbody id="attenData"></tbody>
								</table>
							</div>
							<div class="col-md-12"> 
								<?php $postData = "{'formId':'attendanceForm','fnsave':'saveAttendance','table_id':'reportTable'}"; ?>
								<button type="button" class=" btn waves-effect waves-light btn-success float-right save-form permission-write" style="letter-spacing:1px;" onclick="customStore(<?=$postData?>);">Save Attendance</button>
							</div>
						</div>
					</div>
				</form>
            </div>
        </div>            
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	$(document).on("click",".loadData",function(){
        var attendance_date = $("#attendanceForm #attendance_date").val();

        if($("#attendance_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}

		$.ajax({
			url:base_url + controller + '/getAttendanceData',
			type:'post',
			data:{attendance_date:attendance_date},
			dataType:'json',
			success:function(data)
			{
				$("#reportTable").DataTable().clear().destroy();
				$("#attenData").html(data.tbodyData);
				reportTable();
			}
		});
	});
});

function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		"paging": false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		// lengthMenu: [
        //     [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        // ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loadData").trigger('click');}}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}

</script>