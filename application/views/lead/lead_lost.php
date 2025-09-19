<form>

    
        <input type="hidden" name="id" value="<?=$data['id']?>">
        <input type="hidden" name="executive_id" value="<?=$data['executive_id']?>">
        <input type="hidden" name="party_type" value="<?=$data['party_type']?>">
        <input type="hidden" name="log_type" value="<?=$data['log_type']?>">
        <input type="hidden" name="is_active" value="<?=isset($data['is_active'])?$data['is_active']:1?>">
        <?php
        if($data['party_type'] == 3){
            ?>
             <div class="col-md-12 form-group">
                <label class="form-label">Reason</label>
                <select id="notes" name="notes" class="form-control select2 req">
                    <option value="">Select Reason</option>
                    <?php
                    if(!empty($reasonList)){
                        foreach($reasonList as $row){
                            ?> <option value="<?=$row->label?>"><?=$row->label?></option> <?php
                        }
                    }
                    ?>
                    <option value="OTHER">OTHER</option>
                </select>
                <div class="error notes"></div>
            </div>
            <div class="col-md-12 form-group">
                <label class="form-label">Remark</label>
                <textarea  id="remark" name="remark" class="form-control"></textarea>
            </div>
            <?php
        }else{
            ?>
            <div class="col-md-12 form-group">
                <label class="form-label">Note</label>
                <textarea  id="notes" name="notes" class="form-control req"></textarea>
            </div>
            <?php
        }
        ?>
    
</form>