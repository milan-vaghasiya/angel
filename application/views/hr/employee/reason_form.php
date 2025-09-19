<form autocomplete="off" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<input type="hidden" name="status" value="<?= !(empty($status)) ? $status : 0; ?>" />
			
			<div class="col-md-12 form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" class="form-control req" rows="2"></textarea>
            </div>
			
        </div>
    </div>
</form>