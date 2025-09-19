<form>
	<div class="col-md-12">
        <div class="row">
            <span><b><?='['.$dataRow->item_code.'] '.$dataRow->item_name; ?></b></span>
        </div>
    </div>
    
	<hr>
	
    <div class="col-md-12 row">
        <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>" />
        <input type="hidden" name="challan_id" value="<?=(!empty($dataRow->challan_id))?$dataRow->challan_id:''?>" />
        <input type="hidden" name="assets_id" value="<?=(!empty($dataRow->assets_id))?$dataRow->assets_id:''?>" />

        <div class="col-md-12 form-group">
            <label for="receive_at">Receive Date</label>
            <input type="date" name="receive_at" id="receive_at" class="form-control req" value="<?=date("Y-m-d")?>">
        </div> 
        <div class="col-md-12 form-group">
            <label for="in_ch_no">In Challan No</label>
            <input type="text" name="in_ch_no" id="in_ch_no" class="form-control" value="">
        </div>
    </div>
</form>