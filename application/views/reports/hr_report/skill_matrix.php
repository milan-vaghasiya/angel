<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>
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
                <div class="table-responsive">
                    <table class="table table-bordered" id="tblData">
                    </table>
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
                    url: base_url + controller + '/getSkillMatrixData',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#tblData").html(data.tblData);
                    }
                });
            }else{
                var url = base_url + controller + '/getSkillMatrixData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url);
            } 
        }
    });   
});
</script>