
<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id)) ? $dataRow->id : ""?>" />
            <input type="hidden" name="exp_source" id="exp_source" value="<?=(!empty($dataRow->exp_source)) ? $dataRow->exp_source : 1?>" />

            <div class="col-md-6 form-group">
                <label for="exp_number">Expense No.</label>
                <input type="text" name="exp_number" id="exp_number" class="form-control req" value="<?=(!empty($dataRow->exp_number) ? $dataRow->exp_number : $exp_prefix.sprintf("%03d",$exp_no))?>" readOnly />
                <input type="hidden" name="exp_prefix" id="exp_prefix" value="<?=(!empty($dataRow->exp_prefix)) ? $dataRow->exp_prefix : (!empty($exp_prefix) ? $exp_prefix :"")?>" />
                <input type="hidden" name="exp_no" id="exp_no" value="<?=(!empty($dataRow->exp_no)) ? $dataRow->exp_no : (!empty($exp_prefix) ? $exp_prefix :"")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="exp_date">Expense Date</label>
                <input type="date" name="exp_date" id="exp_date" class="form-control req" value="<?=(!empty($dataRow->exp_date) ? date("Y-m-d",strtotime($dataRow->exp_date)): date('Y-m-d'))?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="exp_by_id">Employee</label>
                <select name="exp_by_id" id="exp_by_id" class="form-control select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empList as $row){
                            $selected = ((!empty($dataRow->exp_by_id) && $dataRow->exp_by_id == $row->id) ? "selected" : "");
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                        }
                    ?>
                </select>
            </div>
			
			<div class="col-md-6 form-group">
                <label for="exp_type_id">Expense Type</label>
                <select name="exp_type_id" id="exp_type_id" class="form-control select2 req">
                    <option value="">Select Type</option>
                    <?php
                        if(!empty($expTypeList)){
                            foreach($expTypeList as $row){
                                $selected = ((!empty($dataRow->exp_type_id) && $dataRow->exp_type_id == $row->id) ? "selected" : "");
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->label.'</option>';
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="demand_amount">Amount</label>
                <input type="text" name="demand_amount" id="demand_amount" class="form-control floatOnly req" value="<?=(!empty($dataRow->demand_amount) ? $dataRow->demand_amount : "")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="proof_file">File Upload</label>
                <div class="input-group">
                    <input type="file" name="proof_file" class="form-control" style="width:<?=(!empty($dataRow->proof_file)) ? "75%" : "" ?>"  />
                    <?php
                    if(!empty($dataRow->proof_file)){
                    ?>
                        <div class="input-group-append">
                          <a href="<?=base_url('assets/uploads/expense/'.$dataRow->proof_file)?>" class="btn btn-outline-primary" download ><i class="fa fa-download"></i></a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
				<div class="error proof_file"></div>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="2"><?=(!empty($dataRow->notes) ? $dataRow->notes : "")?></textarea>
            </div>
        </div>
    </div>
</form>