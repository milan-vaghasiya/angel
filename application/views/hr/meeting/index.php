<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('meetingTable',0);" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('meetingTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('meetingTable',2);" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Cancelled</button> 
                            </li>
                        </ul>
                    </div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addMeeting', 'form_id' : 'addMeeting', 'title' : 'Add Meeting'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Meeting</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
            <div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id='meetingTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
						</div>
					</div>
				</div>
            </div>
        </div> 
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>