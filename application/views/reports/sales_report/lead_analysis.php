<?php $this->load->view('includes/header'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
					<div class="float-end" style="width:50%;"> 
                        <div class="input-group">
                            <label for="group_by" style="width:20%;">Type</label>
                            <label for="executive_id" style="width:30%;">Sales Executive</label>
                            <label for="from_date" style="width:16%;">From Date</label>
                            <label for="to_date" style="width:10%;">To Date</label>
                        </div>
					    <div class="input-group">
                            <div class="input-group-append" style="width:20%;">
                                <select name="group_by" id="group_by" class="form-control select2">
                                    <option value="source" selected>Source</option>
                                    <option value="business_type">Business Type</option>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:30%;">
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
                        </div>
                        <div class="error fromDate"></div>
                        <div class="error toDate"></div>
                        </div> 
					</div>
				</div>
            </div>
		</div>
        <!-- End Stacked Column Chart -->
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive table-scroll lazy-wrapper">
                                <table id="reportTable" class="table dataTable dt-table-hover table-striped table-fixed laDetail" style="width:100%">
                                </table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
      
        <!-- Start Stacked Column Chart -->
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <h4 class="card-title"></h4>
                            <div id="stacked-column"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/extra-libs/c3/d3.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/c3/c3.min.js"></script>
<script>
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		 var group_by = $("#group_by").val();
        var executive_id = $("#executive_id").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var valid = 1;
        var pdf_type = $(this).data('pdf_type');
		var sendData = {group_by:group_by, executive_id:executive_id, from_date:from_date, to_date:to_date,pdf_type:pdf_type};
        if(valid){
			if(pdf_type != 1){
				$.ajax({
					url: base_url + controller + '/getLeadAnalysis',
                    data: sendData,
					type: "POST",
					dataType:'json',
					success:function(response){
                        $(".laDetail").html(response.laDetail);
                        loadChart(response.result,response.xAxise);
					}
				});
			}else{
				window.open(base_url + controller + '/getLeadAnalysis/'+encodeURIComponent(window.btoa(JSON.stringify(sendData))));
			}
        }
    });  
});

function loadChart(lData,xAxise){
    arr = [];stgArr=[];
    stgArr.push(lData.map(function(value){
        return value[0];
    }));

    // Callback that creates and populates a data table, instantiates the stacked column chart, passes in the data and draws it.
    var stackedColumnChart = c3.generate({
        bindto: '#stacked-column',
        size: { height: 400 },
        color: {
            pattern: ['#2962FF', '#ced4da', '#4fc3f7', '#f62d51','#2E8B57','#e5acb6']
        },

        // Create the data table.
        data: {
            columns: lData
             
           ,
            type: 'bar',
           groups: [
                stgArr[0]
            ]
        },
        grid: {
            y: {
                show: true
            }
        },
        axis: {
            x: {
                type: 'categorized',
                categories: xAxise
            }
        },
        bar: {
            width: {
                ratio: 0.3
            }
        }
    });

    // Instantiate and draw our chart, passing in some options.
    setTimeout(function() {
        stackedColumnChart.groups([
            stgArr[0]
        ]);
    }, 1000);



    // Resize chart on sidebar width change
    $(".sidebartoggler").on('click', function() {
        stackedColumnChart.resize();
    });
}
</script>