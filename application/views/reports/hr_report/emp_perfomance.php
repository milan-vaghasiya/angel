<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row"> 
                         <div class="col-md-6">
						</div>
                         <div class="col-md-3">
                            <select id="emp_id" class="form-control select2">
                                <option value="">Select ALL Employee</option>
                                <?php
                                    foreach($empList as $row):
                                        echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                                    endforeach;
                                ?>
                            </select>	
						</div>
						<div class="col-md-3">  
							<div class="input-group">
                                <div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf="0" title="Load Data">
										<i class="fas fa-sync-alt"></i> Load
									</button>
                                    <button type="button" class="btn waves-effect waves-light btn-warning float-right loadData" data-pdf="1" title="PDF">
                                        <i class="fas fa-print"></i> PDF
                                    </button>
								</div>
							</div>
							<div class="error toDate"></div>
						</div>     
					</div> 
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead id="theadData" class="thead-dark">
                                    <tr>
                                        <th rowspan="2" style="width:4%">#</th>
                                        <th rowspan="2" style="width:6%">Emp. Code</th>
                                        <th rowspan="2" style="width:10%">Emp. Name</th>
                                        <th rowspan="2" style="width:10%">Department</th>
                                        <th rowspan="2" style="width:10%">Designation</th>
                                        <th rowspan="2" style="width:20%">KPI</th>
                                        <th rowspan="2" style="width:5%">Weightage</th>
                                        <th colspan="12" style="width:40%" class="text-center">Achieved</th>
                                    </tr>
                                    
                                    <tr>
                                        <?php foreach($monthList as $row){ ?>
                                            <th><?=$row['label']?></th>
                                        <?php } ?>
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

    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var emp_id = $('#emp_id').val();
        var is_pdf = $(this).data('pdf');
       
        var postData = {emp_id:emp_id,is_pdf:is_pdf};
		if(valid){
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/getEmpPerfomanceData',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
						$("#tbodyData").html(data.tbodyData);
                    }
                });
            }else{
                var url = base_url + controller + '/getEmpPerfomanceData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url);
            } 
        }
    });   
});
</script>