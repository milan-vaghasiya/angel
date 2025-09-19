<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control select2">
                    <option value="">Select Machine</option>
                    <?php
                    if(!empty($machineList)){
                        foreach($machineList AS $row){
                            ?><option value="<?=$row->id?>"><?=((!empty($row->item_code))?'['.$row->item_code.'] ':'').$row->item_name?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                
                <button type="button" class="btn waves-effect waves-light btn-success float-left loadData  mt-20" title="Load Data">
                    <i class="fas fa-sync-alt"></i> Load
                </button>
            </div>
        </div>
        <div class="error general_error"></div>
        <div class="row">
            <div class="col-md-6 form-group" style="border-right:1px solid #daeafa;">
                <h5>Plan Machine : </h5>
                <div class="table-responsive" data-simplebar style="height:78vh;">
                    <table class="table jpExcelTable">
                        <thead class="thead-info">
                            <tr>
                                <th></th>
                                <th>Item</th>
                                <th>PRC</th>
                                <th>Process</th>
                            </tr>
                        </thead>
                        <tbody id="pendingPlanTbody">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6 form-group">
                <h5 id="totalWorkLoad" class="text-danger"></h5>
                <h5>Planned Process : </h5>
                <div class="table-responsive" data-simplebar style="height:78vh;">
                    <table class="table jpExcelTable">
                        <thead class="thead-info">
                            <tr>
                                <th>Sequence</th>
                                <th>Item</th>
                                <th>PRC</th>
                                <th>Process</th>
                                <th>Pend. Production</th>
                                <th>Total Time</th>
                            </tr>
                        </thead>
                        <tbody id="plannedTbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('click','.loadData',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		$(".error").html("");
		var valid = 1;
		var machine_id = $('#machine_id').val();
        if($("#machine_id").val() == ""){$(".machine_id").html("Machine is required.");valid=0;}
	    

		if(valid){
            $.ajax({
                url: base_url + controller + '/getProcessDetail',
                data: {machine_id:machine_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#pendingPlanTbody").html(data.pendingPlanTbody);
                    $("#plannedTbody").html(data.plannedTbody);
                    $("#totalWorkLoad").html(data.totalWorkLoad);
                }
            });
        }
    }); 
    
    $(document).on("click", ".planProcess", function() {
        var rowid = $(this).data('rowid');
        $(".error").html("");
        if (this.checked) {
            $(".checkRow" + rowid).removeAttr('disabled');
        }else{
            $(".checkRow" + rowid).attr('disabled','disabled');
        }
    });
});
</script>