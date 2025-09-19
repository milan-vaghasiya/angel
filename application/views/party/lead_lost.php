<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=$data['id']?>">
            <input type="hidden" name="lead_stage" value="<?=$data['lead_stage']?>">
            
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
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label class="form-label">Remark</label>
                <textarea  id="remark" name="remark" class="form-control"></textarea>
            </div>
        </div> 
    </div>
</form>