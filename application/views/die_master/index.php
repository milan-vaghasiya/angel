<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addDieSet', 'form_id' : 'addDieSet', 'title' : 'Add Die Set', 'fnsave' : 'saveDieSet', 'js_store_fn' : 'dieStore'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Die Set</button>
					</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card"> 
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
                                <thead class="thead-dark" id="theadData">
                                    <tr>
                                        <th colspan="4" class="text-center">Other Info</th>
                                        <th colspan="<?=count($catData)?>" class="text-center">List Of Dies</th>
                                        <th colspan="3" class="text-center"> Date & Status </th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Part Name</th>
                                        <th>Part No.</th>
                                        <th>Sr No.</th>
                                        <?php
                                            foreach ($catData as $row) {
                                                echo '<th>'.$row->category_name.'</th>';
                                            }
                                        ?>
                                        <th>Development Date</th>
                                        <th>Die Status</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?=$tbodyData?>
                                </tbody>
                            </table>
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
	reportTable();
});
</script>