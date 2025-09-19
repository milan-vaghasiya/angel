<form>
    <div class="col-md-12">
        <div class="row">            
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />

            <div class="col-md-6 form-group">
				<label for="start_date">Training Start Date</label>
				<input type="datetime-local" name="start_date" id="start_date" class="form-control req" value="<?=(!empty($dataRow->start_date))?$dataRow->start_date:date('Y-m-d H:i')?>">
			</div>

            <div class="col-md-6 form-group">
				<label for="end_date">Training End Date</label>
				<input type="datetime-local" name="end_date" id="end_date" class="form-control req" value="<?=(!empty($dataRow->end_date))?$dataRow->end_date:date('Y-m-d H:i')?>">
			</div>

            <div class="col-md-12 form-group">
                <label for="title">Purpose</label>
                <textarea type="text" name="title" id="title" class="form-control req" rows="1"><?=(!empty($dataRow->title) ? $dataRow->title : "")?></textarea>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea type="text" name="description" id="description" class="form-control" ><?=(!empty($dataRow->description) ? $dataRow->description : "")?></textarea>
            </div>

             <div class="col-md-6 form-group">
                <label for="type">Training Type</label>
                <input type="text" name="type" class="form-control req" value="<?= (!empty($dataRow->type)) ? $dataRow->type : "" ?>" />
			</div>
            
            <div class="col-md-6 form-group">
                <label for="trainer_name">Trainer Name</label>
                <input type="text" name="trainer_name" class="form-control req" value="<?=(!empty($dataRow->trainer_name))?$dataRow->trainer_name:""; ?>" />
            </div>
            
            <div class="col-md-12 form-group">
                <label for="skill_id">Included Skill</label>
                <select name="skill_id[]" id="skill_id" class="form-control select2" multiple>
                    <?php
                    if(!empty($skillData)):
                        foreach($skillData as $row):
                            $selected = ((!empty($dataRow->skill_id) && in_array($row->id, explode(',',$dataRow->skill_id))) ? 'selected' : '');
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->skill_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
			</div>

             <div class="col-md-12 form-group">
                <label for="emp_id">Attendees</label>
                <select name="emp_id[]" id="emp_id" class="form-control select2" multiple>
                   <?=(!empty($empData) ? $empData : '');?>
                </select>
			</div>
            
        </div>
    </div>
</form><script>
$(document).ready(function(){
    $(document).on('change','#skill_id',function(){
        var skill_id = $('#skill_id').val();  
        
        if(skill_id){
            $.ajax({
                url : base_url + controller + '/getAttendeeList',
                type : 'post',
                data : { skill_id:skill_id },
                dataType : 'json'
            }).done(function(response){
                $("#emp_id").html(response.empOptions);
            });
        }else{
            $("#emp_id").html('<option value="">Select Employee</option>'); 
        }
    });
});
</script>
