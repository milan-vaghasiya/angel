<?php $this->load->view('includes/header'); ?>
<style>
/* .layout-spacing {
  padding-bottom: 25px;
} */


</style>
	
<div class="page-content-tab">
    <div class="container-fluid" style="padding:0px 10px;">
        <?php  if($this->userRole == 8): ?>
        <?php else : ?>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card overflow-hidden">
                        <!-- <div class="card-body"> -->
                            <div class="widget widget-card-four gradient-light-orange">
                                <div class="widget-content">
                                    <a href="javascript:void(0)">
                                        <div class="w-header">
                                            <div class="w-info">
                                                <h5 class="value fs-20">MACHINE ANALYSIS</h5>
                                            </div>
                                        </div>
                                        <div class="w-content">
                                            <div class="w-info">
                                                <p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=((!empty($mcData->free_mc))?$mcData->free_mc:0)?></small><br><span class="fs-18">Free</span></p>
                                            </div>
                                            <div class="w-info">
                                                <p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=((!empty($mcData->inprocess_mc))?$mcData->inprocess_mc:0)?></small><br><span class="fs-18">Running</span></p>
                                            </div>
                                            <div class="w-info">
                                                <p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=((!empty($mcData->maintance_mc))?$mcData->maintance_mc:0)?></small><br><span class="fs-18">Under Maintenance</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div> 
                        <!-- </div> -->
                    </div>                 
                </div>
                <div class="col-lg-8">
                    <div class="row justify-content-center"> 
                        <div class="col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body gradient-light-green">
                                    <div class="row d-flex">
                                        <div class="col-3">
                                            <i class="ti ti-basket font-36 align-self-center text-dark"></i>
                                        </div><!--end col-->
                                        <div class="col-12 ms-auto align-self-center">
                                            <b><div id="dash_spark_1" class="mb-3 fw-bold"></div></b>
                                        </div><!--end col-->
                                        <div class="col-12 ms-auto align-self-center">
                                            <h3 class="text-dark my-0 font-22 fw-bold"><?=(!empty($soData->no_of_items)?$soData->no_of_items:0)?></h3>
                                            <p class="text-muted mb-0 fw-bold">Total Orders</p>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div><!--end card-body--> 
                            </div><!--end card-->                                     
                        </div> <!--end col--> 
                        <div class="col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body gradient-light-blue">
                                    <div class="row d-flex">
                                        <div class="col-3">
                                            <i class="ti ti-license font-36 align-self-center text-dark"></i>
                                        </div><!--end col-->
                                    
                                        <div class="col-12 ms-auto align-self-center">
                                            <div id="dash_spark_2" class="mb-3"></div>
                                        </div><!--end col-->
                                        <div class="col-12 ms-auto align-self-center">
                                            <h3 class="text-dark my-0 font-22 fw-bold"><?=(!empty($soData->avg_order))?moneyFormatIndia(round($soData->avg_order,2)):0?></h3>
                                            <p class="text-muted mb-0 fw-bold">Order Avg. Value</p>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div><!--end card-body--> 
                            </div><!--end card-->                                     
                        </div> <!--end col--> 
                        <div class="col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body gradient-light-yellow">
                                    <div class="row d-flex">
                                        <div class="col-3">
                                            <i class="ti ti-activity font-36 align-self-center text-dark"></i>
                                        </div><!--end col-->
                                        <div class="col-12 ms-auto align-self-center">
                                            <div id="dash_spark_3" class="mb-3"></div>
                                        </div><!--end col-->
                                        <div class="col-12 ms-auto align-self-center">
                                            <h3 class="text-dark my-0 font-22 fw-bold">
                                                <?php 
                                                    $conversionRate = 0;
                                                    if(!empty($soData->total_order_qty) && $soData->total_order_qty > 0){
                                                        $conversionRate = round((($dispatchData->total_dispatch_qty * 100)/$soData->total_order_qty),2);
                                                    }
                                                    echo $conversionRate.' %';
                                                ?>
                                            </h3>
                                            <p class="text-muted mb-0 fw-bold">Conversion Rate </p>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div><!--end card-body--> 
                            </div><!--end card-->                                     
                        </div> <!--end col--> 
                        
                        <div class="col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body gradient-light-cyan">
                                    <div class="row d-flex">
                                        <div class="col-3">
                                            <i class="ti ti-clock font-36 align-self-center text-dark"></i>
                                        </div><!--end col-->
                                    
                                        <div class="col-12 ms-auto align-self-center">
                                            <div id="dash_spark_4" class="mb-3"></div>
                                        </div><!--end col-->
                                        <div class="col-12 ms-auto align-self-center">
                                            <h3 class="text-dark my-0 font-22 fw-bold"><?=((!empty($soDelayData->delayed_item))?$soDelayData->delayed_item:0)?></h3>
                                            <p class="text-muted mb-0 fw-bold">Delay Dispatch</p>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div><!--end card-body--> 
                            </div><!--end card-->                                     
                        </div> <!--end col-->                                                                   
                    </div><!--end row-->
                </div><!--end col-->                        
            </div><!--end row-->
			
            <div class="row">
                <div class="col-lg-8 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">SALES ANALYSIS
                            <?php
                            $currentMonth = date("Ym");
                            $preMonth = date("Ym", strtotime(date("Y-m-01") . " -1 month"));

                            if(!empty($soAnalysis[$preMonth]) &&  $soAnalysis[$preMonth] > 0){
                                $lastMonthCom = (($soAnalysis[$currentMonth] - $soAnalysis[$preMonth])/$soAnalysis[$preMonth])*100;
                            }else{
                                $lastMonthCom = '100%';
                            }
                            if($lastMonthCom >= 0){
                                ?>
                                    <p class="mb-0 px-3 py-1 bg-soft-success rounded d-inline-block float-right"><b><?=round($lastMonthCom,2)?>% <i class="mdi mdi-arrow-up"></i></b> <small>of Comparision with Last Month</small></p>
                                <?php
                            }else{
                                ?>
                                <p class="mb-0 px-3 py-1 bg-soft-danger rounded d-inline-block  float-right"><b><?=round($lastMonthCom,2)?>% <i class="mdi mdi-arrow-down"></i></b> <small>of Comparision with Last Month</small></p>
                                <?php
                            }
                        
                            ?>
                            </h4>
                            
                        </div><!--end card-header-->
                        <div class="card-body">            
                            <canvas id="salesAnalysisLine" width="300" height="410"></canvas>            
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!-- end col -->

                <div class="col-lg-4 col-xl-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light-thunder">
                                    <h4 class="card-title mb-0 px-2 py-0">PROCESS ANALYSIS</h4>
                                </div><!--end card-header-->
                                <div class="card-body">  
                                    <div data-simplebar="" style="height: 420px;">
                                        <ul class="list-unsyled m-0 ps-0 mt-1">
                                            <li class="align-items-center d-flex justify-content-between py-1 border-bottom">
                                                <a href="javascript:void(0)" class="my-1 fw-bold" style="width:55%">Process</a>
                                                <span class="text-muted fw-bold" style="width:25%">Work Load Time</span>
                                                <span class="text-muted fw-bold text-right" style="width:20%">Work Load Qty</span>
                                            </li>
                                            <?php
                                                if(!empty($wipData)){
                                                    foreach($wipData AS $row){
                                                        $in_qty = (!empty($row->in_qty)?$row->in_qty:0);
                                                        $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                                                        $movement_qty = !empty($row->movement_qty)?$row->movement_qty:0;
                                                        $rej_found_qty = !empty($row->rej_found)?$row->rej_found:0;
                                                        $rej_qty = !empty($row->rej_qty)?$row->rej_qty:0;
                                                        $rw_qty = !empty($row->rw_qty)?$row->rw_qty:0;
                                                        $pendingReview = $rej_found_qty - $row->review_qty;
                                                        $pending_production =(($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview));
                                                        ?>
                                                        <li class="align-items-center d-flex justify-content-between py-1 border-bottom">
                                                            <a href="javascript:void(0)" class="my-1" style="width:60%"><?=$row->current_process?></a>
                                                            <span class="text-muted" style="width:20%"><?=(!empty($row->work_load_time))?secondsToTime($row->work_load_time,'H:i'):''?></span>
                                                            <span class="text-muted text-right" style="width:20%"><?=$pending_production?></span>
                                                        </li>
                                                        <?php
                                                    }
                                                }    
                                            ?>
                                        </ul> 
                                    </div> 
                                </div>
                            </div><!--end card--> 
                        </div>
                    </div>
                                                   
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
						<div class="col-lg-6">
                            <div class="card overflow-hidden">
                                <div class="widget widget-card-four bg-light-cream" style="padding: 10px 23px;">
                                    <div class="widget-content">
                                        <a href="javascript:void(0)">
                                            <div class="w-header">
                                                <div class="w-info">
													<h5 class="value fs-22">OEE</h5>
                                                </div>
                                            </div>
                                            <div class="w-content" style="margin-top:25px">
                                                <div class="w-info">
                                                    <p class="value text-dark" style="line-height:23px;">
														<small class="fs-18"><?=(!empty($oee_per)?round($oee_per,2):0)?>%</small><br>
														<span class="fs-18">Total</span>
													</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
						<div class="col-lg-6">
                            <div class="card overflow-hidden">
								<div class="widget widget-card-four gradient-light-blue" style="padding: 10px 23px;">
									<div class="widget-content">
										<a href="javascript:void(0)">
											<div class="w-header">
												<div class="w-info">
													<h5 class="value fs-20">DEFECT RATE</h5>
												</div>
											</div>
											<div class="w-content" style="margin-top:5px">
												<div class="w-info">
													<?php
														$defectPer = "0";
														if(!empty($rejData->total_production_qty)){
															$defectPer = ($rejData->total_rej_qty * 100)/$rejData->total_production_qty;
														}
													?>
													<p class="value text-dark" style="line-height:23px;">
														<small class="fs-18"><?=sprintf("%.2f",$defectPer)?>%</small><br>
														<span class="fs-18">Rejection </span>
													</p>
													<small> % against production </small>
												</div>
											</div>
										</a>
									</div>
								</div> 
                            </div>                 
                        </div>
						
						<div class="col-lg-12">
                            <div class="card overflow-hidden">
                                <div class="widget widget-card-four bg-light-teal" style="padding: 10px 23px;">
                                    <div class="widget-content">
										<div class="w-header">
											<div class="w-info">
												<h6 class="value fs-20">PRODUCTION LOSS</h6>
											</div>
										</div>
                                        <div class="w-content" style="margin-top:25px">
											<div class="w-info">
												<p class="value text-dark" style="line-height:25px;"><small class="fs-18"><?=sprintf("%.2f",$machineLoss->total_machine_loss)?></small><br><span class="fs-14">Machine</span></p>
											</div>
											<div class="w-info">
												<p class="value text-dark" style="line-height:25px;"><small class="fs-18"><?=sprintf("%.2f",$rejLoss->total_rej_loss)?></small><br><span class="fs-14">QC-Rejection </span></p>
											</div>
										</div>
                                    </div>
                                </div> 
                            </div>
                        </div>
					</div>
                </div>
				
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rework Time Rate</h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-demo m-0">
                                <div id="rework_rate_chart" class="apex-charts"></div>
                            </div>                                        
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">First Pass Yield (FPY) </h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-demo m-0">
                                <div id="fpy_chart" class="apex-charts"></div>
                            </div>                                        
                        </div>
                    </div>
                </div>

            </div>
			<div class="row">
				<div class="col-lg-6">
					<div class="card overflow-hidden">
						<!-- <div class="card-body"> -->
							<div class="widget widget-card-four gradient-light-orange" style="padding: 10px 23px;">
								<div class="widget-content">
									<a href="javascript:void(0)">
										<div class="w-header">
											<div class="w-info">
												<h5 class="value fs-20">CUSTOMER COMPLAINT</h5>
											</div>
										</div>
										<div class="w-content" style="margin-top:5px">
											<div class="w-info">
											
												<p class="value text-dark" style="line-height:23px;"><small class="fs-16"><?=$complainData->comp_counstomer?></small><br><span class="fs-16">No. of Customer</span></p>
											</div>
											<div class="w-info">
											
												<p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=$complainData->comp_items?></small><br><span class="fs-16">No. of Parts </span></p>
											</div>
											<div class="w-info">
											
												<p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=$complainData->total_complaint?></small><br><span class="fs-16">No. of Complaints </span></p>
												<small>- </small>
											</div>
										</div>
									</a>
								</div>
							</div> 
						<!-- </div> -->
					</div>                 
				</div>
                    
				<div class="col-lg-3">
					<div class="card overflow-hidden">
						<!-- <div class="card-body"> -->
							<div class="widget widget-card-four gradient-light-green" style="padding: 10px 23px;">
								<div class="widget-content">
									<a href="javascript:void(0)">
										<div class="w-header">
											<div class="w-info">
												<h5 class="value fs-20">INVENTORY</h5>
											</div>
										</div>
										<div class="w-content" style="margin-top:2px">
											<div class="w-info">
												<p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=((!empty($overInvent->over_stock_amount))?moneyFormatIndia(round($overInvent->over_stock_amount,2)):0)?></small><br><span class="fs-18">Over Inventory</span></p>
												<small>Amount of Total Stock</small>
											</div>
											<div class="w-info">
												<p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=((!empty($lowInvent))?$lowInvent:0)?></small><br><span class="fs-18">Low Inventory</span></p>
												<small> No. of Items</small>
											</div>
										</div>
									</a>
								</div>
							</div> 
						<!-- </div> -->
					</div>                 
				</div>
                      
				<div class="col-lg-3">
					<div class="card overflow-hidden">
						<!-- <div class="card-body"> -->
							<div class="widget widget-card-four gradient-light-cyan" style="padding: 10px 23px;">
								<div class="widget-content">
									<a href="javascript:void(0)">
										<div class="w-header">
											<div class="w-info">
												<h5 class="value fs-18">DEMAND Vs. INVENTORY</h5>
											</div>
										</div>
										<div class="w-content" style="margin-top:2px">
											<div class="w-info">
												<?php
												$defectPer = "0";
												if(!empty($rejData->total_production_qty)){
													$defectPer = ($rejData->total_rej_qty * 100)/$rejData->total_production_qty;
												}

												?>
												<p class="value text-dark" style="line-height:23px;"><small class="fs-18"><?=$inventory_per?>%</small><br><span class="fs-18">Inventory  </span></p>
												<small> % of Stock Against Demand</small>
											</div>
										</div>
									</a>
								</div>
							</div> 
						<!-- </div> -->
					</div>                 
				</div>
			</div>
        <?php endif;?>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<!-- Javascript  -->   
<script src="<?=base_url()?>assets/plugins/chartjs/chart.js"></script>
<script src="<?=base_url()?>assets/plugins/lightpicker/litepicker.js"></script>
<script src="<?=base_url()?>assets/plugins/apexcharts/apexcharts.min.js"></script>
<script src="<?=base_url()?>assets/pages/analytics-index.init.js"></script>

<script>
$(document).ready(function(){
    var ctx1 = document.getElementById('salesAnalysisLine').getContext('2d');
    var myChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec','Jan','Feb', 'Mar'],
            datasets: [{
                label: 'Monthly Order',
                data: [
                        <?=(!empty($soAnalysis[$this->startYear.'04'])?$soAnalysis[$this->startYear.'04']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'05'])?$soAnalysis[$this->startYear.'05']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'06'])?$soAnalysis[$this->startYear.'06']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'07'])?$soAnalysis[$this->startYear.'07']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'08'])?$soAnalysis[$this->startYear.'08']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'09'])?$soAnalysis[$this->startYear.'09']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'10'])?$soAnalysis[$this->startYear.'10']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'11'])?$soAnalysis[$this->startYear.'11']:0)?>,
                        <?=(!empty($soAnalysis[$this->startYear.'12'])?$soAnalysis[$this->startYear.'12']:0)?>,
                        <?=(!empty($soAnalysis[$this->endYear.'01'])?$soAnalysis[$this->endYear.'01']:0)?>,
                        <?=(!empty($soAnalysis[$this->endYear.'02'])?$soAnalysis[$this->endYear.'02']:0)?>,
                        <?=(!empty($soAnalysis[$this->endYear.'03'])?$soAnalysis[$this->endYear.'03']:0)?>,
                        
                      ],
                backgroundColor: [
                    'rgba(11, 81, 183, 0.1)',
                ],
                borderColor: [
                    'rgba(11, 81, 183, 1)',
                ],
                borderWidth: 2,
                borderDash	:[1],
                borderJoinStyle: "round",
                borderCapStyle: "round",
                pointBorderColor: 'rgba(11, 81, 183, 1)',
                pointRadius: 3,
                pointBorderWidth: 1,
                tension: 0.5,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {                   
                        color: '#7c8ea7',
                    }
                }
            }  ,
            scales: {            
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Include a dollar sign in the ticks
                        callback: function(value, index, values) {
                            return '' + value;
                        },
                        color: '#7c8ea7',
                    },               
                    grid: {
                        drawBorder: 'border',
                        color: 'rgba(132, 145, 183, 0.15)',
                        borderDash: [3],
                        borderColor: 'rgba(132, 145, 183, 0.15)',
                    } ,
                    beginAtZero: true,
                },
                x: {   
                ticks: {
                    color: '#7c8ea7',
                },            
                    grid: {
                        display: false,
                        color: 'rgba(132, 145, 183, 0.09)',
                        borderDash: [3],
                        borderColor: 'rgba(132, 145, 183, 0.09)',
                    }    
                }            
            },
            
        }
    });

    //Rework Chart
    var options = {
        chart: {
            height: 270,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '50%',
                },
                track: {
                background: '#b9c1d4',
                opacity: 0.5,
                },
                dataLabels: {
                name: {
                    fontSize: '18px',
                },
                value: {
                    fontSize: '16px',
                    color: '#8997bd',
                },          
                }
            },
        },
        colors: ["#4a8af6"],
        series: [<?=$rw_rate?>],
        labels: ['Rework %'],
    
    }
    
    var chart = new ApexCharts(
        document.querySelector("#rework_rate_chart"),
        options
    );
    
    chart.render();

    //FPY CHART

    var options = {
        chart: {
            height: 270,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '50%',
                },
                track: {
                background: '#b9c1d4',
                opacity: 0.5,
                },
                dataLabels: {
                name: {
                    fontSize: '18px',
                },
                value: {
                    fontSize: '16px',
                    color: '#8997bd',
                },          
                }
            },
        },
        colors: ["#13124aff"],
        series: [<?=$fpy_per?>],
        labels: ['FPY'],
    
    }
    
    var chart = new ApexCharts(
        document.querySelector("#fpy_chart"),
        options
    );
    
    chart.render();
});
</script>