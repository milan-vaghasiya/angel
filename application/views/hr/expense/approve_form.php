<form>
    <div class="col-md-12">
        <table class="table jpExcelTable" style="marging: 0px;">
            <tbody>
                <tr>
                    <td class="fw-semibold" line-height="10">Expense No</td>
                    <td><?=$dataRow->exp_number?></td>
                    <td class="fw-semibold">Date</td>
                    <td><?=formatDate($dataRow->exp_date)?></td>
                </tr>
                <tr>
                    <td class="fw-semibold">Expense By</td>
                    <td colspan="3"><?=$dataRow->emp_name?> </td>
                </tr>
                <tr>
                    <td class="fw-semibold">Expense Type</td>
                    <td colspan="3"><?=$dataRow->exp_name?></td>
                </tr>
                <tr>
                    <td class="fw-semibold  border-bottom-0">Demand Amount</td>
                    <td class="text-dark  border-bottom-0" colspan="3"><strong><?=$dataRow->demand_amount?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 form-group">
        <div class="row">  
            <input type="hidden" name="id" id="id" value="<?=$id?>" />

            <div class="col-md-12 form-group">
                <label for="status">Approve/Reject</label>
                <select name="status" id="status" class="form-control modal-select2">
                    <option value="1">Approve</option>
                    <option value="2">Reject</option>
                </select>
            </div>

            <div class="col-md-12 form-group amt">
                <label for="amount"> Amount</label>
                <input type="text" name="amount" id="amount" class="form-control floatOnly req" value="<?=$dataRow->demand_amount?>" />
            </div>

            <div class="col-md-12 form-group rejReason">
                <label for="rej_reason">Reason</label>
                <textarea name="rej_reason" id="rej_reason" class="form-control req" rows="2"></textarea>
            </div>

        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(".rejReason").hide();
    setTimeout(function(){ setPlaceHolder(); $('#status').trigger('change'); }, 5);

    $(document).on('change', '#status', function () {
		var status = $('#status').val(); 
		if(status){
            if(status == 1){
                $(".amt").show();
                $(".rejReason").hide();
            }else{  
                $(".rejReason").show();
                $(".amt").hide();
            }
		}
	});
});
</script>