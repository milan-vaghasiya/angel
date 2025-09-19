<?php 
	$this->load->view('includes/header');	
	$last_day = date("t", strtotime(date("Y-m-d")));
	$start_year = date("Y",strtotime($startYearDate)); $end_year = date("Y",strtotime($endYearDate));
	$monthArr = ['Apr-'.$start_year=>'01-04-'.$start_year,'May-'.$start_year=>'01-05-'.$start_year,'Jun-'.$start_year=>'01-06-'.$start_year,'Jul-'.$start_year=>'01-07-'.$start_year,'Aug-'.$start_year=>'01-08-'.$start_year,'Sep-'.$start_year=>'01-09-'.$start_year,'Oct-'.$start_year=>'01-10-'.$start_year,'Nov-'.$start_year=>'01-11-'.$start_year,'Dec-'.$start_year=>'01-12-'.$start_year,'Jan-'.$end_year=>'01-01-'.$end_year,'Feb-'.$end_year=>'01-02-'.$end_year,'Mar-'.$end_year=>'01-03-'.$end_year];
?>
<div class="page-content-tab">
    <div class="container-fluid">
		<div class="row">
			<div class="col-sm-5">
				<h4 class="card-title pageHeader"><?=$headData->pageTitle;?></h4>
			</div>
			
			<div class="col-sm-2">
				<select name="emp_dept_id" id="emp_dept_id" class="form-control select2 req">
					<option value="0">All Department</option>
					<?php 
						foreach($deptList as $row):
								echo '<option value="'.$row->id.'">'.$row->name.'</option>';
						endforeach;
					?>
				</select>
				<div class="text-danger dept_id"></div>
			</div>
			
			<div class="col-sm-2">
				<div class="input-group-append">
					<select name="month" id="month" class="form-control select2 req">
						<?php
							foreach($monthArr as $key=>$value):
								$selected = (date('m') == date('m',strtotime($value))) ? "selected" : "";
								echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
							endforeach;
						?>
					</select>
					<div class="text-danger month"></div>
				</div>
			</div>
			<div class="col-md-3">  
				<div class="input-group">
					<button type="button" class="btn btn-info loadData" data-type="0" datatip="View Report" flow="down"><i class="fa fa-eye"></i> View</button>

					<button type="button" class="btn btn-primary loadData" data-type="excel" datatip="EXCEL" flow="down" target="_blank"><i class="fa fa-file-excel"></i> Excel</button>

					<button type="button" class="btn btn-danger loadData" data-type="PDF" datatip="PDF" flow="down" target="_blank"><i class="fa fa-file-pdf"></i> PDF</button>
							
				</div>
				<div class="error toDate"></div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr class="text-center">
										<th>#</th>
										<th>Employee Code</th>
										<th>Employee Name</th>
										<th>Department Name</th>
										<th>Dessignation </th>
										<th>Category </th>
										<?php for($d=1;$d<=$last_day;$d++){echo '<th>'.sprintf("%02d", $d).'</th>';} ?>
										
										<th>Week Of</th>
										<th>Present<br/>Days</th>
										<th>Half Day</th>
										<th>Leave </th>
										<th>Absent<br/>Days</th>
										<th>Total<br/>Days</th>
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


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    initModalSelect();
	reportTable();
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var month = $('#month').val();
		var emp_dept_id = $('#emp_dept_id').val();
		var type = $(this).data('type');
		var sendData = {month:month,emp_dept_id:emp_dept_id,file_type:type};

		if($("#month").val() == ""){$(".month").html("Month is required.");valid=0;}else{$(".month").html("");}
		
		if(valid){
			if(type == 'excel' || type == 'PDF'){
				window.open(base_url + controller + '/getMonthlyAttendance/'+encodeURIComponent(window.btoa(JSON.stringify(sendData))),'_blank').focus();
			}else{
				$.ajax({
					url: base_url + controller + '/getMonthlyAttendance',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").dataTable().fnDestroy();
						$("#tbodyData").html(data.tbody);
						$("#theadData").html(data.theadData);
						reportTable();
					}
				});
			}
			
        }
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
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
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