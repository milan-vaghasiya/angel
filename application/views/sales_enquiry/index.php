<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesEnquiryTable',0);" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesEnquiryTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('salesEnquiryTable',4);" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Regretted</button> 
                            </li>
                        </ul>
                    </div>
					<div class="float-end">
					    <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="window.location.href='<?=base_url($headData->controller.'/addEnquiry')?>'"><i class="fa fa-plus"></i> Add Enquiry</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
            <div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id='salesEnquiryTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
						</div>
					</div>
				</div>
            </div>
        </div> 
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>