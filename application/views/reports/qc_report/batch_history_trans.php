<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
						<a href="<?= base_url('reports/qualityReport/batchHistory') ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
					
						<div class="input-group">
							<input type="hidden" id="item_id" value="<?=(!empty($item_id))?$item_id:""?>">
							<input type="hidden" id="batch_no" value="<?=(!empty($batch_no))?$batch_no:""?>">
						</div>
					</div>
					<h4 class="card-title pageHeader">Batch History</h4>
					
					
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">				
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-dark">
                                    <tr class="text-center">
										<th colspan="10" class="text-left">Item Name : <?=(!empty($itemData)?$itemData->item_name:'Batch History')?></th>
										<th colspan="5" class="text-right">Batch No.: <?=(!empty($batch_no)?$batch_no:'')?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="6" class="text-center">Batch Details</th>
                                        <th colspan="9" class="text-center">Manufacturing/Issue Details</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>GRN No.</th>
                                        <th>GRN Date</th>
										<th>CH. No.</th>
                                        <th>GRN Qty</th>
										<th>Party Name</th>
                                        <th>Part Name</th>
                                        <th>PRC No.</th>
                                        <th>PRC Qty</th>
                                        <th>Cut Wt.</th>
                                        <th>Issue Qty</th>
                                        <th>Used Qty</th>
                                        <th>Return Qty</th>
                                        <th>PRC Stock</th>
                                        <th>Balance Qty</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
                                <tfoot id="tfootData">
									<tr class="thead-dark">
										<th colspan="4" class="text-right">Total</th>
										<th class="text-center">0</th> 
										<th colspan="3"></th> 
										<th class="text-center">0</th> 
										<th></th>
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
										<th class="text-center">0</th> 
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
	loadData();    
});

function reportTable(tableId = "reportTable",tblOptions = {}){
	var tableOptions = {
        responsive: true,
        "autoWidth" : false,
        order:[],
        "columnDefs": [
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
        buttons: {
            dom: {
                button: {
                    className: "btn btn-outline-dark"
                }
            },
            buttons:[ 
                'pageLength', 
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                },
                {
                    text: 'Refresh',
                    action: function (){ 
                        loadData();
                    } 
                }
            ]
        },
    };
	
	$.extend( tableOptions, tblOptions );
	var reportTable = $('#'+tableId).DataTable(tableOptions);
	reportTable.buttons().container().appendTo( '#'+tableId+'_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	return reportTable;
}

function loadData(){
	$(".error").html("");
	var valid = 1;
	var item_id = $('#item_id').val();
	var batch_no = $('#batch_no').val();
	
	if(item_id == ""){$(".item_id").html("Item is required.");valid=0;}
	
	if(valid){
		$.ajax({
			url: base_url + controller + '/getBatchHistoryData',
			data: {item_id:item_id,batch_no:batch_no},
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
}
</script>