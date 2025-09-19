<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('giTable','1/1','getStoreDtHeader','inwardQC');" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending Inward</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('giTable','1/2','getStoreDtHeader','inwardQC');" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Inward Done</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('giTable','2/3','getStoreDtHeader','pendingQc');" class="nav-tab btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending T.P. QC</button> 
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
                                <table id='giTable' class="table table-bordered ssTable ssTable-cf" data-url='/getInwardQcDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/gate-inward.js?V=<?=time()?>"></script>