<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('employeeTable',0);" class=" btn waves-effect waves-light btn-outline-success active" style="outline:0px" data-toggle="tab" aria-expanded="false">Active</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('employeeTable',1);" class=" btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-toggle="tab" aria-expanded="false">Inactive</button> 
                            </li>
                        </ul>
                    </div>
					<div class="float-end">
                        <?php
                            $addParam = "{'postData' : {'status' : 1},'modal_id' : 'bs-right-lg-modal', 'call_function':'addApplication', 'form_id' : 'addApplication', 'title' : 'Add Employee'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Employee</button>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='employeeTable' class="table table-bordered ssTable ssTable-cf" data-url="/getDTRows"></table>
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