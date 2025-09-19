<form data-res_function="resKpiChecklist" id="addKpiChecklist">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />

            <div class="col-md-6 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control select2 req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($departmentList as $row):
                            $selected = (!empty($dataRow->dept_id) && $row->id == $dataRow->dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<div class="col-md-6 from-group">
                <label for="desi_id">Designation</label>
                <div class="input-group">
                    <div class="input-group-append" style="width:70%;">
                        <select name="desi_id" id="desi_id" class="form-control select2 req">
                            <option value="">Select Designation</option>
                            <?php
                                foreach($designationList as $row):
                                    $selected = (!empty($dataRow->desi_id) && $row->id == $dataRow->desi_id)?"selected":"";
                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="input-group-append" style="width:30%;">
                        <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                            <i class="fas fa-sync-alt"></i> Load
                        </button>
                    </div>
                </div>
            </div>

            <hr>        
			
            <div class="col-md-12 form-group">
                <label for="kpi_id">KPI Type</label>
                <select name="kpi_id" id="kpi_id" class="form-control select2 req">
                    <option value="">Select KPI</option> 
                    <?php
                        if(!empty($kpiList)){
                            foreach($kpiList as $row){
                                echo '<option value="'.$row->id.'">'.$row->kpi_name.'</option>';
                            }
                        }
                    ?>
                </select>
            </div>

			<div class="col-md-12 form-group">
                <label for="kpi_desc">KPI</label>
                <input name="kpi_desc" id="kpi_desc" class="form-control req">
            </div>
			
            <div class="col-md-6 form-group">
                <label for="req_per">Weightage</label>
                <div class="input-group">
                    <input type="text" name="req_per" id="req_per" class="form-control floatOnly req" value="">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-success btn-custom-save">
                            <i class="fa fa-plus"></i>Add
                        </button>
                    </div>
                </div>
            </div>
			
            <hr>
			
            <div class="row">
                <div class="col-md-12">
                    <table id="kpiTblData" class="table table-bordered">
                        <thead id="theadData" class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>KPI Type</th>
                                <th>KPI</th>
                                <th>Weightage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="kpiTbodyData">
                            <tr>
                                <td colspan="5" class="text-center">No data available in table</td>
                            </tr>
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
        var dept_id = $("#dept_id").val();
        var desi_id = $("#desi_id").val();

        if($("#dept_id").val() == ""){$(".dept_id").html("Department is required.");valid=0;}      
        if($("#desi_id").val() == ""){$(".desi_id").html("Designation is required.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getKpiTransHtml',
                data: {desi_id:desi_id,dept_id:dept_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#kpiTbodyData").html('');
					$("#kpiTbodyData").html(data.tbodyData);
					$("#tfootData").html(data.tfoot);
                }
            });
        }
    });  

    var postData = {'postData':{'dept_id':$("#dept_id").val(),'desi_id':$("#desi_id").val()},'table_id':"kpiTblData",'tbody_id':'kpiTbodyData','tfoot_id':'','fnget':'getKpiTransHtml'};
    getTransHtml(postData);
}); 


function resKpiChecklist(data){
    if(data.status==1){
		
		$('#req_per').val('');
		$('#kpi_desc').val('');
		$('#kpi_id').val('');
		$('#id').val('');
        initSelect2();

        Swal.fire({ icon: 'success', title: data.message});

        var postData = {'postData':{'dept_id':$("#dept_id").val(),'desi_id':$("#desi_id").val()},'table_id':"kpiTblData",'tbody_id':'kpiTbodyData','tfoot_id':'','fnget':'getKpiTransHtml'};
        getTransHtml(postData);
		initTable();
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function editKpi(data) { 
    $.each(data, function (key, value) {
        $("#" + key).val(value);
	});
    initSelect2();
}

</script> 