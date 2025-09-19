<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control select2 req">
                    <option value="">Select Department</option>
                    <?php
					if(!empty($departmentList)):
                        foreach($departmentList as $row):
                            $selected = (!empty($dataRow->dept_id) && $row->id == $dataRow->dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
					endif;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="designation_id">Designation</label>
                <select name="designation_id" id="designation_id" class="form-control select2 req">
                    <option value="">Select Designation</option>
                    <?php
					if(!empty($designationList)):
                        foreach($designationList as $row):
                            $selected = (!empty($dataRow->designation_id) && $row->id == $dataRow->designation_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
					endif;
                    ?>
                </select>
            </div>
			
			<div class="col-md-12 form-group">
                <label for="doc_file">Document File</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="form-control custom-file-input" name="doc_file" id="doc_file" accept=".jpg, .jpeg, .png" />
                    </div>
                </div>
                <div class="error doc_file"></div>
            </div>

			<div class="col-md-12 form-group">
                <label for='description' class="control-label">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>

		</div>
	</div>	
</form>
            
