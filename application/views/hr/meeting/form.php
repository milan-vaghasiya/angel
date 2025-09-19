<form>
    <div class="col-md-12">
        <div class="row">            
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />

            <div class="col-md-6 form-group">
				<label for="me_date">Meeting Date</label>
				<input type="datetime-local" name="me_date" id="me_date" class="form-control req" min="<?= date('Y-m-d')?>" value="<?=(!empty($dataRow->me_date))?$dataRow->me_date:date('Y-m-d H:i')?>">
			</div>

            <div class="col-md-6 form-group">
                <label for="duration">Duration (In Hours)</label>
                <input type="text" name="duration" id="duration" class="form-control" value="<?=(!empty($dataRow->duration) ? $dataRow->duration : "")?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <textarea type="text" name="title" id="title" class="form-control req" rows="1"><?=(!empty($dataRow->title) ? $dataRow->title : "")?></textarea>
            </div>

            <div class="col-md-12 form-group">
				<label for="emp_id">Member</label> <strong class="text-danger">*</strong>
                <select name="emp_id[]" id="emp_id" class="form-control select2 req" multiple>
                    <?php
                    if(!empty($empData)):
                        foreach($empData as $row):
                            $selected = ((!empty($dataRow->emp_id) && in_array($row->id, explode(',',$dataRow->emp_id))) ? 'selected' : '');
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
                <div class="error emp_id"></div>
			</div>

            <div class="col-md-12 form-group">
                <label for="description">Agenda</label>
                <textarea type="text" name="description" id="description" class="form-control" rows="3" ><?=(!empty($dataRow->description) ? $dataRow->description : "")?></textarea>
            </div>
         
            <div class="col-md-6 form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="<?=(!empty($dataRow->location) ? $dataRow->location : "")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="host_by">Host By</label>
                <input type="text" name="host_by" class="form-control" value="<?=(!empty($dataRow->host_by))?$dataRow->host_by:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="guest">Guest</label>
                <input type="text" name="guest" class="form-control" value="<?=(!empty($dataRow->guest))?$dataRow->guest:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="key_contact">Key Contact</label>
                <input type="text" name="key_contact" class="form-control" value="<?=(!empty($dataRow->key_contact))?$dataRow->key_contact:""; ?>" />
            </div>
            
        </div>
    </div>
</form>