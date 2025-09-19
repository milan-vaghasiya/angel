<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-5 form-group">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>   
                            <div class="col-md-2 form-group">
                                <select name="shift_id" id="shift_id" class="form-control select2">
                                    <option value="" data-plan_prod_time="24">ALL</option>
                                    <?php
                                        if(!empty($shiftData)){
                                            foreach ($shiftData as $row) :
                                                $time = explode(":",$row->production_hour);
                                                $plan_prod_time = $time[0] + (!empty($time[1])?$time[1]:0);
                                                echo '<option value="' . $row->id . '" data-plan_prod_time="'.$plan_prod_time.'">' . $row->shift_name . '</option>';
                                            endforeach;
                                        }
                                    ?>
                                </select>
                                <div class="error shift_id"></div>
                            </div>
                            <div class="col-md-2 form-group">  
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?= date("Y-m-01");?>" />
								<div class="error fromDate"></div>
							</div>   
                            <div class="col-md-3 form-group">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" data-pdf="0" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    
                                    </div>
                                </div>
                                <div class="error to_date"></div>
                            </div>               
                        </div>                                        
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th>Operator Name</th>
                                        <th>M/C NO.</th>
                                        <th>Part Name</th>
                                        <th>Job No</th>
                                        <th>Set up</th>
                                        <th>Cycle time<br>(Sec.)</th>
                                        <th>Plan Production <br> Time(hrs.)</th>
                                        <th>Plan Qty</th>
                                        <th>Actual Production <br> Time(hrs.)</th>
                                        <th>Actual Plan Qty</th>
                                        <th>Actual Cycle Time<br>(Sec.)</th>
                                        <th>Production Qty</th>
                                        <th>Rejection qty.</th>
                                        <th>Rework Time.</th>
                                        <th>Availablility (%)</th>
                                        <th>Effciency (%)</th>
                                        <th>Quality Rate (%)</th>
                                        <th>OEE (%)</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>	
                                <tfoot id="tfootData" class="thead-info">
								    <th colspan="11" class="text-right">Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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

    $(document).on('click','.loadData',function(e){
		e.stopImmediatePropagation();e.preventDefault();
		$(".error").html("");
		var valid = 1;
		var to_date = $('#to_date').val();
		var from_date = $('#from_date').val();
		if(to_date == ""){$(".to_date").html("Date is required.");valid=0;}
		
        if(valid){
            var shift_id = $('#shift_id').val();
            var plan_prod_time = $("#shift_id :selected").data('plan_prod_time');
            var postData = {from_date : from_date, to_date : to_date,shift_id:shift_id,plan_prod_time:plan_prod_time};
            $.ajax({
                url: base_url + controller + '/getOeeRegister',
                data: postData,
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
                    $("#tbodyData").html(data.tbody);
                    $("#tfootData").html(data.tfoot);
                    reportTable();
                }
            });
        }
		
    });   
});

function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,2] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {  }}]
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