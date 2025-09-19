<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
						<?php
							if(!empty($is_status) && $is_status != 7 && $is_status != 6):
						?>
							<ul class="nav nav-pills">
								<li class="nav-item"> 
									<button onclick="recruitTab('employeeTable',<?=$is_status?>,0);" class=" btn waves-effect waves-light btn-outline-primary active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
								</li>
								<li class="nav-item"> 
									<button onclick="recruitTab('employeeTable',<?=$is_approve?>,1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> 
								</li>
							</ul>
						<?php endif; ?>
                    </div>
					<div class="float-end">
                        <?php
							if(!empty($is_status) && $is_status == 2):
								$addParam = "{'postData' : {'status':".$is_status."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addApplication', 'form_id' : 'addApplication', 'title' : 'New Application'}";
                        ?>
							<button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> New Application</button>
						<?php endif; ?>
					</div>
                    <h4 class="card-title text-center"><?= $heading;?></h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='employeeTable' class="table table-bordered ssTable ssTable-cf" data-url="/getRecDTRows/<?=$is_status?>"></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/employee.js?v=<?=time()?>"></script>

<script>
$(document).ready(function(){
	// Check For Employee is under child act or not
    $(document).on('change','#emp_birthdate',function(){
        var dob = new Date($(this).val());
        var today = new Date();
        var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
        $('#age').html(age+' years old');

        if (age < 18) {
            $(".emp_birthdate").html("Under Child Labour Act");
        }
        else{ $(".emp_birthdate").html(""); }
    });
});

function recruitTab(tableId,status,is_approve,hp_fn_name="",page=""){
    $("#"+tableId).attr("data-url",'/getRecDTRows'+'/'+status+'/'+is_approve);

	$("#"+tableId).data("hp_fn_name","");
    $("#"+tableId).data("page","");
    $("#"+tableId).data("hp_fn_name",hp_fn_name);
    $("#"+tableId).data("page",page);

    ssTable.state.clear();
	initTable();
}	
</script>