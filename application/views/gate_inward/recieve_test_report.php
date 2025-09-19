<form enctype="multipart/form-data">
    <div class="col-md-12">
        <table class="table jpExcelTable">
            <tr>
                <th class="bg-light">GI No.</th>
                <td><?=$testData->trans_number?></td>
                <th class="bg-light">Item Name</th>
                <td colspan="3"><?=(!empty($testData->item_code) ? "[".$testData->item_code."] " : "").$testData->item_name?></td>
                <th class="bg-light">Material Grade</th>
                <td><?=$testData->material_grade?></td>
            </tr>
            <tr>
                <th style="width:10%;" class="bg-light">Test Type</th>
                <td style="width:15%;"><?=$testData->test_description?></td>
                <th style="width:10%;" class="bg-light">Agency</th>
                <td style="width:15%;"><?=$testData->name_of_agency?></td>
                <th style="width:10%;" class="bg-light">Batch No.</th>
                <td style="width:15%;"><?=$testData->batch_no?></td>
                <th style="width:10%;" class="bg-light">Ref./Heat No.</th>
                <td style="width:15%;"><?=$testData->heat_no?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($testData->id)) ? $testData->id : ""; ?>"/>            
            
            <div class="col-md-2 form-group">
                <label for="sample_qty">Sample Qty/Total Weight</label>
                <input type="text" name="sample_qty" class="form-control floatOnly req" value="<?=(!empty($testData->sample_qty))?$testData->sample_qty:""; ?>" readOnly/>
            </div>

            <div class="col-md-2 form-group" >
                <label for="test_report_no">Test Report No</label>
                <input type="text" name="test_report_no" id="test_report_no" class="form-control" value="" />
            </div>

            <div class="col-md-3 form-group">
                <label for="inspector_name">Inspector Name</label>
                <input type="text" name="inspector_name" id="inspector_name" class="form-control" value="" />
            </div>

            <div class="col-md-2 form-group" >
                <label for="test_result">Test Result</label>
                <select name="test_result" id="test_result" class="form-control select2 ">
                    <option value="Accept">Accept</option>
                    <option value="Reject">Reject</option>
                    <option value="Accept U.D.">Accept U.D.</option>
                </select>
            </div>

            <div class="col-md-3 form-group" >
                <label for="tc_file">T.C. File</label>
                <input type="file" name="tc_file" id="tc_file" class="form-control req"  />
            </div>

            <div class="col-md-12 form-group" >
                <label for="test_remark">Test Remark</label>
                <input type="text" name="test_remark" id="test_remark" class="form-control" value="" />
            </div>

            <div class="col-md-12 form-group" >
                <label for="spc_instruction">Special Instruction</label>
                <input type="text" name="spc_instruction" id="spc_instruction" class="form-control" value="" />
            </div>

        </div>
    </div>
</form>