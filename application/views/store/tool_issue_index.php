<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                        <li class="nav-item"> 
                                <a href="<?=base_url($headData->controller."/toolIssue/1/2")?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 1) ? "active" : ""?>  mr-1"> Issued </a>
                            </li>
                            <li class="nav-item"> 
                                <a href="<?=base_url($headData->controller."/toolIssue/2/2")?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 2) ? "active" : ""?>  mr-1">  Pending Return  </a>
                            </li>
                        </ul>
                    </div>
                    <div class="float-end">
                        <?php
                            $addParam = "{'postData':{'item_type' : 2},'modal_id' : 'bs-right-lg-modal', 'call_function':'addIssueRequisition', 'form_id' : 'addIssueRequisition', 'title' : 'Tool Issue' , 'fnsave' : 'saveIssueRequisition'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Tool Issue</button>
					</div>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='issueRequisitionTable' class="table table-bordered ssTable ssTable-cf" data-url='/getIssueDTRows/<?=$status?>/<?=$item_type?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>