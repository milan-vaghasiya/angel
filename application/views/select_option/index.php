<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-start">
						<ul class="nav nav-pills">
							<li><button onclick="statusTab('selectOptionTable',1);" data-type="1" class="btn btn-outline-info statusTabChange active" data-bs-toggle="tab">Source</button></li>
							<li><button onclick="statusTab('selectOptionTable',2);" data-type="2" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Lost Reason</button></li>
							<li><button onclick="statusTab('selectOptionTable',3);" data-type="3" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Expense Type</button></li>
							<li><button onclick="statusTab('selectOptionTable',4);" data-type="4" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Sales Zone</button></li>
							<li><button onclick="statusTab('selectOptionTable',5);" data-type="5" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Visit Purpose</button></li>
							<li><button onclick="statusTab('selectOptionTable',6);" data-type="6" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Complaint Reason</button></li>
							<li><button onclick="statusTab('selectOptionTable',7);" data-type="7" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Business Type</button></li>
							<li><button onclick="statusTab('selectOptionTable',8);" data-type="8" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Brand Master</button></li>
							<li><button onclick="statusTab('selectOptionTable',9);" data-type="9" class="btn btn-outline-info statusTabChange" data-bs-toggle="tab">Material Test Type</button></li>
						</ul>
					</div>
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'type':'1'},'modal_id' : 'bs-right-md-modal', 'call_function':'addSelectOption', 'form_id' : 'addSelectOption', 'title' : 'Add Option'}";
                        ?>
                        <button type="button" id="addbtn" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Option</button>
                    </div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='selectOptionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
	$(document).on('click',".statusTabChange",function(){
		var type = $(this).data('type');
		var paramData = "{'postData':{'type':'"+type+"'},'modal_id' : 'bs-right-md-modal', 'call_function':'addSelectOption', 'form_id' : 'addSelectOption', 'title' : 'Add "+$(this).text()+"'}";

		$('#addbtn').attr('onclick', 'modalAction('+paramData+')');
	});
});
</script>
