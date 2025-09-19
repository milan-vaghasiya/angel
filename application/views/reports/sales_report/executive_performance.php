<?php $this->load->view('includes/header'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
					<div class="float-end" style="width:50%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:30%;">
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>"/> 
                            </div>
                            <div class="input-group-append" style="width:30%;">                                   
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>"/>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf_type= "0" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                                <button type="button" class="btn waves-effect waves-light btn-primary loadData" data-pdf_type= "1" title="Load Data">
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

        <!-- End Stacked Column Chart -->
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
                                            <th>Apr</th>
                                            <th>May</th>
                                            <th>Jun</th>
                                            <th>Jul</th>
                                            <th>Aug</th>
                                            <th>Sep</th>
                                            <th>Oct</th>
                                            <th>Nov</th>
                                            <th>Dec</th>
                                            <th>Jan</th>
                                            <th>Feb</th>
                                            <th>Mar</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot id="tfootData" class="thead-dark">
                                        <tr>
                                            <th class="text-right">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
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
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/extra-libs/c3/d3.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/c3/c3.min.js"></script>
<script>
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var pdf_type = $(this).data('pdf_type');
		var sendData = {from_date:from_date, to_date:to_date,pdf_type:pdf_type};

        if(valid){
			if(pdf_type != 1){
				$.ajax({
					url: base_url + controller + '/getExecutivePerformanceData',
                    data: sendData,
					type: "POST",
					dataType:'json',
					success:function(response){
                        loadChart(response.performData);
                        $("#tbodyData").html(response.tbodyData);
                        $("#tfootData").html(response.tfootData);
					}
				});
			}else{
				window.open(base_url + controller + '/getExecutivePerformanceData/'+encodeURIComponent(window.btoa(JSON.stringify(sendData))));
			}
        }
    });   
});

function loadChart(soData){
    arr = [];catArr=[];
    arr.push(soData.map(function(value){
        return [value.emp_name,parseFloat(value.apr_amt),parseFloat(value.may_amt),parseFloat(value.jun_amt),parseFloat(value.jul_amt),parseFloat(value.aug_amt),parseFloat(value.sep_amt),parseFloat(value.oct_amt),parseFloat(value.nov_amt),parseFloat(value.dec_amt),parseFloat(value.jan_amt),parseFloat(value.feb_amt),parseFloat(value.mar_amt)];
    }));
    catArr.push(soData.map(function(value){
        return value.emp_name;
    }));
    // Callback that creates and populates a data table, instantiates the stacked column chart, passes in the data and draws it.
    var stackedColumnChart = c3.generate({
        bindto: '#stacked-column',
        size: { height: 400 },
        color: {
            pattern: ['#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080', '#ffffff', '#000000']
        },

        // Create the data table.
        data: {
            columns: arr[0]
             
           ,
            type: 'bar',
           groups: [
                catArr[0]
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
                categories: ['APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','JAN','FEB','MAR']
            }
        },
        bar: {
            width: {
                ratio: 0.2
            }
        }
    });

    // Instantiate and draw our chart, passing in some options.
    setTimeout(function() {
        stackedColumnChart.groups([
            catArr[0]
        ]);
    }, 1000);



    // Resize chart on sidebar width change
    $(".sidebartoggler").on('click', function() {
        stackedColumnChart.resize();
    });
}
</script>