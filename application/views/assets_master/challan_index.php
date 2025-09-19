<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
					</div>
                    <ul class="nav nav-pills">
                        <li class="nav-item"> <button onclick="statusTab('challanTable',0);" class="btn waves-effect waves-light btn-outline-danger active mr-1" data-bs-toggle="tab" aria-expanded="false">Pending</button> </li>
                        <li class="nav-item"> <button onclick="statusTab('challanTable',1);" class="btn waves-effect waves-light btn-outline-success" data-bs-toggle="tab" aria-expanded="false">Completed</button> </li>
                    </ul>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='challanTable' class="table table-bordered ssTable ssTable-cf" data-url='/getChDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
