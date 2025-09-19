<form>
    <input type="hidden" name="id" value="<?=$requestData->id?>">
    <div class="col-md-12 form-group">
        <label for="next_process_ids">Next Process</label>
        <select name="next_process_ids[]" id="next_process_ids" class="form-control select2 req" multiple>
            <?php
            $masterProcess = array_reduce($processList, function($masterProcess, $process) { 
                                            $masterProcess[$process->id] = $process;  return $masterProcess; 
                                        }, []);

            $nextProcessIds = explode(",",$requestData->next_process_ids);
            $process = explode(",",$requestData->process_ids);
            $processKey = array_search($nextProcessIds[0],$process);
            $processOptions = '';
            foreach($process as $key => $pid):
                if($key >= $processKey):
                    $selected = ((in_array($pid,$nextProcessIds))?'selected':'');
                    echo '<option value="'.$pid.'" '.$selected.'>'.$masterProcess[$pid]->process_name.'</option>';
                endif;
            endforeach;
            ?>
        </select>
        <div class="error next_process_ids"></div>
    </div>
    <div class="col-md-12 form-group">
        <label for="next_process_by">Vendor</label>
        <select name="next_process_by" id="next_process_by" class="form-control select2 req">
            <option value="0">Inhouse</option>
            <?php
            if(!empty($vendorList)){
                foreach($vendorList as $row){
                    $selected = (!empty($requestData->next_process_by) && $requestData->next_process_by== $row->id)?'selected':'';
                    ?>
                    <option value="<?=$row->id?>" <?=$selected?>><?=$row->party_name?></option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
</form>