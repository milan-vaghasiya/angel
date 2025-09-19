<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="nav nav-pills">
                                <li class="nav-item"> 
                                    <button onclick="statusTab('planTable',1);" id="pending" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                                </li>
                                <li class="nav-item"> 
                                    <button onclick="statusTab('planTable',2);" id="inprogress" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Inprogress</button> 
                                </li>
                                <li class="nav-item"> 
                                    <button onclick="statusTab('planTable',3);" id="end" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Ended</button> 
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control select2" id="mcId"  >
                                <option value="">Select Machine</option>
                                <?php
                                if(!empty($machineList))
                                {
                                    foreach($machineList as $row)
                                    {
                                        ?>
                                        <option value="<?=$row->id?>"><?=((!empty($row->item_code))?'['.$row->item_code.'] ':'').$row->item_name?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn waves-effect waves-light btn-outline-primary setPriority" >Set Priority</button>
                        </div>
                        <div class="col-md-2">
                            <?php
                                $addParam = "{'modal_id' : 'bs-right-xl-modal', 'call_function':'addMachinePlan', 'form_id' : 'addMachinePlan', 'title' : 'New Plan', 'fnsave' : 'saveMachinePlan'}";
                            ?>
                            <button type="button" class="btn btn-info permission-write press-add-btn float-end" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> New Plan</button>
                        </div>
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
                                <table id='planTable' class="table table-bordered ssTable ssTable-cf" data-url='/getMcPlanDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', ".setPriority", function() {
            var machine_id=$("#mcId").val();
            $(".machine_id").html("");
           if(machine_id=='')
           {
                $(".machine_id").html("Please Select Machine");
           }
           else
           {
                var machine_name = $("#mcId :selected").text();
                var postdata = {machine_id:machine_id}
                var call_function ='changeMachineSequence';
                var fnsave = 'saveMachineSequence';
                var modal_id = 'bs-right-lg-modal';
                var form_id = 'changeMachineSequence';
                var button = 'close';
                var js_store_fn = 'customStore';
                var title = machine_name;
                var controllerName = controller;
                var data = {call_function:call_function,fnsave:fnsave,modal_id:modal_id,form_id:form_id,title:title};
                var ajaxParam = {
                    url: base_url + controllerName + '/' + call_function,   
                    type: "POST",   
                    data: postdata
                }; 
            
                $.ajax(ajaxParam).done(function(response){
                    initModal(data,response);
                })
            }
        });
    });
</script>

