<form >
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-6 form-group">
                <label for="vacancy_no">Vacancy No</label>
                <input type="text" name="vacancy_no" id="vacancy_no" class="form-control numericOnly req" value="<?=(!empty($dataRow->vacancy_no))?$dataRow->vacancy_no:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="set_name">Skill Set</label>
                <select name="set_name" id="set_name" class="form-control select2 req">
                    <option value="">Select Set</option>
                    <?php
                        foreach($skillSetList as $row):
                            $selected = (!empty($dataRow->set_name) && $row->set_name == $dataRow->set_name)?"selected":"";
                            echo '<option value="'.$row->set_name.'" '.$selected.'> '.$row->set_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <input type="text" name="notes" id="notes" class="form-control" value="<?=(!empty($dataRow->notes))?$dataRow->notes:""; ?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="publish_to">Publish To</label>
                <input type="text" name="publish_to" id="publish_to" class="form-control" value="<?=(!empty($dataRow->publish_to))?$dataRow->publish_to:""; ?>" />
            </div>
        </div>
    </div>
</form>

