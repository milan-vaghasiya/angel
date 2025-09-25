<form id="copyProductProcess" data-res_function="getProductProcessHtml">
    <input type="hidden" name="to_item_id" value="<?= (!empty($to_item_id)) ? $to_item_id : "";?>">
    <div class="col-md-12 form-group">
        <label for="price">Item</label>
        <select name="from_item_id" id="from_item_id" class="form-control select2 me-2 req">
            <option value="">Select Item</option>
            <?php
                if (!empty($productList)){
                    foreach ($productList as $row){
                        echo '<option value="'.$row->id.'">'.$row->item_code.' - '.$row->item_name.'</option>';
                    }
                }
            ?>
        </select>
    </div>
</form>