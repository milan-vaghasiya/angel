<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button onclick="statusTab('outsourceTable','0');" class="nav-tab btn waves-effect waves-light btn-outline-danger active" id="pending_receive" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>
                            </li>
                            <li class="nav-item">
                                <button onclick="statusTab('outsourceTable','1');" class="nav-tab btn waves-effect waves-light btn-outline-success" id="completed_receive" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button>
                            </li>
                        </ul>
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
                                <table id='outsourceTable' class="table table-bordered ssTable ssTable-cf" data-url='/getVendorDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/js/custom/sop_desk.js?v=<?=time()?>"></script>
