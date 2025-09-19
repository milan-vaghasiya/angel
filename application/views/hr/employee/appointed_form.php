<form autocomplete="off" id="saveBulkAdvance">
    <div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<input type="hidden" name="status" id="status" value="<?= !empty($dataRow->emp_joining_date) ? 1 : 6 ;?>" />
			<div class="col-md-12 from-group">
                <label for="emp_joining_date">Joining Date</label>
				<input type="date" name="emp_joining_date" class="form-control req" value="<?= !empty($dataRow->emp_joining_date) ? $dataRow->emp_joining_date : date("Y-m-d");?>">
            </div>
        </div>
    </div>
</form>