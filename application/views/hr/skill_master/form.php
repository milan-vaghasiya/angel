<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-12 form-group">
                <label for="skill_name">Skill Name</label>
                <input type="text" name="skill_name" id="skill_name" class="form-control req" value="<?=(!empty($dataRow->skill_name))?$dataRow->skill_name:""; ?>" />
            </div>

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
                <label for="designation_id">Designation</label>
                <select name="designation_id" id="designation_id" class="form-control select2 req">
                    <option value="">Select Designation</option>
                    <?php
                        foreach($designationList as $row):
                            $selected = (!empty($dataRow->designation_id) && $row->id == $dataRow->designation_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			
            <div class="col-md-6 form-group">
                <label for="req_skill">Req. Skill(%)</label>
                    <input type="text" name="req_skill" id="req_skill" class="form-control req floatOnly" value="<?=(!empty($dataRow->req_skill))?$dataRow->req_skill:""; ?>" />
                </div>                
            </div>
           
        </div>
    </div>
</form>