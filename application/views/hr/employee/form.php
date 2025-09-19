<form autocomplete="off" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="allowed_visitors" value="0" />
            <input type="hidden" name="status" value="<?=(!empty($dataRow->status))?$dataRow->status:$status; ?>" />

            <div class="col-md-4 form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="father_name">Father Name</label>
                <input type="text" name="empDetails[father_name]" class="form-control" value="<?=(!empty($dataRow->father_name))?$dataRow->father_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="emp_email">Email ID</label>
                <input type="text" name="empDetails[emp_email]" class="form-control" value="<?=(!empty($dataRow->emp_email))?$dataRow->emp_email:""?>" />
            </div>
            <?php if($status == 1){ ?>
                <div class="col-md-4 form-group">
                    <label for="emp_code">Emp. Code</label>
                    <input type="text" name="emp_code" class="form-control numericOnly req" value="<?=(!empty($dataRow->emp_code))?$dataRow->emp_code:""?>" />
                </div>
            <?php } ?>
            <div class="col-md-4 form-group">
                <label for="emp_contact">Phone No.(Login ID)</label>
                <input type="text" name="emp_contact" class="form-control numericOnly req" value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="emp_alt_contact">Emergency Contact</label>
                <input type="text" name="empDetails[emp_alt_contact]" class="form-control numericOnly" value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
            </div>
			
            <div class="col-md-4 form-group">
                <label for="emp_joining_date">Emp Joining Date</label>
                <input type="date" name="emp_joining_date" class="form-control" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date('Y-m-d')?>" />
            </div>
			<div class="col-md-4 form-group">
                <label for="emp_birthdate">Date Of Birth</label>
                <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control" value="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="emp_category">Emp Category</label>
                <select name="emp_category" id="emp_category" class="form-control select2">
                    <option value="">Select Category</option>
                    <?php
                        foreach($empCategoryList as $row):
                            $selected = (!empty($dataRow->emp_category) && $row->id == $dataRow->emp_category)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'> '.$row->category.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="emp_gender">Gender</label>
                <select name="empDetails[emp_gender]" id="emp_gender" class="form-control select2">
                    <option value="">Select Gender</option>
                    <?php
                        foreach($genderList as $value):
                            $selected = (!empty($dataRow->emp_gender) && $value == $dataRow->emp_gender)?"selected":"";
                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
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
            <div class="col-md-4 from-group">
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
                <input type="hidden" id="designationTitle" name="designationTitle" value="" />
            </div>
            <div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="emp_role">Role</label>
                <select name="emp_role" id="emp_role" class="form-control select2 req">
                    <option value="">Select Role</option>
                    <?php
                        foreach($roleList as $key => $value):
                            $selected = (!empty($dataRow->emp_role) && $key == $dataRow->emp_role)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<?php if($status == 1){ ?>
            <div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="blood_group">Blood Group</label>
                <select name="empDetails[blood_group]" id="blood_group" class="form-control select2">
                    <option value="">Select Blood Group</option>
                    <?php
                        foreach($this->bloodGroups as $key=>$value):
                            $selected = (!empty($dataRow->blood_group) && $key == $dataRow->blood_group)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <?php } ?>
            <div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="marital_status">Marital Status</label>
                <select name="empDetails[marital_status]" id="marital_status" class="form-control select2">
                    <option value="">Select status</option>
                    <option <?= (!empty($dataRow->marital_status) && $dataRow->marital_status == "YES")? "selected":"";?> value="YES">Married</option>
                    <option <?= (!empty($dataRow->marital_status) && $dataRow->marital_status == "NO")? "selected":"";?> value="NO">Unmarried</option>
                </select>
            </div>
			<?php if($status == 1){ ?>
            <div class="col-md-3 form-group">
                <label for="process_id">Process</label>
                <select name="process_id[]" id="process_id" class="form-control select2 req" multiple>
                    <?php
                    if(!empty($processList)){
                        foreach($processList as $row){
                            $selected = (!empty($dataRow->process_id) && in_array($row->id,explode(',',$dataRow->process_id))) ? "selected" : "";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->process_name.'</option>';
                        }
                    }
                    ?>
                </select>
                <div class="error process_id"></div>
            </div>
            <?php } ?>
            <div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="rec_source">Source</label>
				<select name="empDetails[rec_source]" class="form-control select2">
					<option value="">Select Source</option>
					<?php
						foreach($this->recSource as $row):
							$selected = (!empty($dataRow->rec_source) && $dataRow->rec_source == $row) ? "selected" : "";
							echo '<option '.$selected.' value="'.$row.'">'.$row.'</option>';
						endforeach;
					?>
				</select>
            </div>
			<div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="ref_by">Reference</label>
                <input type="text" name="empDetails[ref_by]" class="form-control" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:""?>" />
            </div>
            <div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="sign_image1">Signature</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="form-control custom-file-input" name="sign_image" id="sign_image" accept=".jpg, .jpeg, .png" />
                    </div>
                </div>
                <div class="error sign_image"></div>
            </div>
			
            <div class="<?=($status != 1)?"col-md-4":"col-md-3"?> form-group">
                <label for="auth_id">Higher Authority</label>
                <select name="auth_id" id="auth_id" class="form-control select2">
					<option value="">Select Employee</option>
                    <?php
                        foreach($empList as $row):
                            $selected = (!empty($dataRow->auth_id) && $dataRow->auth_id ==  $row->id)?"selected":"";
                            if($dataRow->id != $row->id):
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                            endif;    
                        endforeach;
                    ?>
                </select>
            </div>
			
            <div class="col-md-12 form-group">
                <label for="emp_address">Address</label>
                <textarea name="empDetails[emp_address]" class="form-control" style="resize:none;" rows="1"><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="permanent_address">Permanent Address</label>
                <textarea name="empDetails[permanent_address]" class="form-control" style="resize:none;" rows="1"><?=(!empty($dataRow->permanent_address))?$dataRow->permanent_address:""?></textarea>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('change keyup','#designation_id',function(){
        if($(this).val()){
            $('#designationTitle').val($('#designation_id :selected').text());
        }else{
            $('#designationTitle').val("");
        }        
    });
});
</script>