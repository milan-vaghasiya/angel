<form>
    <div class="col-md-12">
        <div class="error item_name"></div>
        <div class="row">

            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="status" value="<?=(!empty($dataRow->status))?$dataRow->status:$status?>" />
           
			<div class="col-md-6 form-group">
				<label for="category_id">Item Code</label>
				<input type="text" name="item_code" id="item_code" class="form-control req" value="<?= (!empty($dataRow->item_code)) ?$dataRow->item_code   : ""; ?>"/>
			</div>
			<div class="col-md-6 form-group">
				<label for="category_id">Category</label>
				<select name="category_id" id="category_id" class="form-control select2 req">
					<option value="">Select Category</option>
					<?php
						foreach ($categoryList as $row) :
							$selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
							echo '<option value="'. $row->id .'" '.$selected.' data-cat_code="'.$row->category_code.'" data-cat_name="'.$row->category_name.'">'.((!empty($row->category_code))?'['.$row->category_code.'] '.$row->category_name:$row->category_name).'</option>';
						endforeach;
					?>
				</select>
			</div>
            
            <div class="col-md-6 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" class="form-control floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:""?>" />
            </div>
            
            <div class="col-md-6 form-group">
                <label for="location_id">Location</label>
                <select name="location_id" id="location_id" class="form-control select2 req">
					<option value="">Select Location</option>
                    <?php
						foreach ($locationList as $row) :
							$selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : "";
							echo '<option value="' . $row->id . '" ' . $selected . '>[' .$row->store_name. '] '.$row->location.'</option>';
						endforeach;
                    ?>
                </select>
            </div>  

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>