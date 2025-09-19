<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />

           <div class="col-md-4 form-group">   
                <label for="month">Month</label>
                <select name="month" id="month" class="form-control select2">
                    <option value="">Month</option>
                    <?php   
                        foreach($monthList as $row):
                            $selected = ((!empty($dataRow->month) && date('M-Y', strtotime($dataRow->month)) == $row['label']) ? "selected" : ""); 
                            $disabled = ((!empty($dataRow->month) && date('M-Y', strtotime($dataRow->month)) != $row['label']) ? "disabled" : "");
                            echo '<option value="'. $row['label'] .'" '.$selected.' '.$disabled.'>'.$row['label'].'</option>';
                        endforeach; 
                    ?>
                </select>
			    <div class="error month"></div>
		    </div> 
			<div class="col-md-8 form-group">
                <label for="emp_id">Employee</label>
                <div class="input-group">
                    <div class="input-group-append" style="width:70%;">
                        <select name="emp_id" id="emp_id" class="form-control select2 req">
                            <option value="">Select Employee</option>
                            <?php
                                foreach($empList as $row){
                                    $selected = ((!empty($dataRow->emp_id) && $dataRow->emp_id == $row->id) ? "selected" : "");
                                    $disabled = ((!empty($dataRow->emp_id) && $dataRow->emp_id != $row->id) ? "disabled" : "");
                                    echo '<option value="'.$row->id.'" '.$selected.' '.$disabled.' data-dept_id="'.$row->dept_id.'" data-designation_id="'.$row->designation_id.'">'.$row->emp_name.'</option>';
                                }
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
			
            <div class="row">
                <div class="col-md-12">
                    <table id="empTblData" class="table table-bordered">
                        <thead id="theadData" class="thead-dark">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:55%;">Skill Name</th>
                                <th style="width:20%;">Req. Skill(%)</th>
                                <th style="width:20%;">Previous Skill(%)</th>
                                <th style="width:20%;">Result(%)</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData">
                            <?php
                            $i=1;
                            if(!empty($skillData)):
                                foreach ($skillData as $row) {
                                    $m = $row->{'m' .(int)date('m', strtotime($dataRow->month))};
                                    echo '<tr>
                                            <td>'.$i.'</td>
                                            <td>'.$row->skill_name.'</td>
                                            <td>'.$row->req_skill.'</td>
                                            <td>'.$row->prev_skill.'
												<input type="hidden" name="prev_skill[]" value="'.$row->prev_skill.'">
											</td>
                                            <td>
                                                <input type="hidden" name="id[]" value="'.$row->id.'">
                                                <input type="hidden" name="skill_id[]" value="'.$row->skill_id.'">
												<input type="text" name="current_skill[]" class="form-control floatOnly" value="'.$m.'"/>
												<div class="error current_skill'.$i.'"></div>
                                            </td>
                                        </tr>';
                                        $i++;
                                    }
                            endif;
                            ?>
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
        var dept_id = $("#emp_id :selected").data('dept_id');
        var designation_id = $("#emp_id :selected").data('designation_id');
        var emp_id = $("#emp_id").val(); 
         
        if($("#month").val() == ""){$(".month").html("Month is required.");valid=0;}      
        if($("#emp_id").val() == ""){$(".emp_id").html("Employee is required.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getEmpSkillData',
                data: {dept_id:dept_id,designation_id:designation_id,emp_id:emp_id},
				type: "POST",
				dataType:'json',
				success:function(data){ 
                    $("#tbodyData").html('');
					$("#tbodyData").html(data.tbodyData);
                }
            });
        }
    }); 
}); 
</script> 