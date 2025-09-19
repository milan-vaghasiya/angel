<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="machine_id" id="machine_id" value="<?=(!empty($machine_id) ? $machine_id : '')?>" />
            
            <div class="error activity_error"></div>
            <div class="table-responsive" style="min-height:75vh">
                <table id="machineActivity" class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th style="width:5%;">#</th>
                            <th style="width:15%">Frequency</th>
                            <th style="width:80%">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($freqList)):
                            $i=1;
                            $freqArray = [];
                            if(!empty($dataRow)){
                                $freqArray = array_reduce($dataRow, function($freqArray, $freq) { $freqArray[$freq->checking_frequancy]= $freq; return $freqArray; }, []);
                            }
                            
                            foreach ($freqList as $row) :
                                ?>
                                    <tr class="text-center">
                                        <td style="width:5%;"><?=$i?></td>
                                        <td>
                                            <?=$row?>
                                            <input type="hidden" name="checking_frequancy[]" value="<?=$row?>">
                                        </td>
                                        <td>
                                            <input type="hidden" name="id[]" value="<?=(!empty($freqArray[$row]->id) ? $freqArray[$row]->id : "")?>">
                                            <select data-input_id="activity_id<?=$i?>" class="form-control jp_multiselect" multiple="multiple">
                                                <?php
                                                    foreach($activityData as $act):
                                                        $frequency = (!empty($act->frequency)) ? explode(',',$act->frequency) : [];
                                                        if(in_array($row,$frequency)):
                                                            $selected = (!empty($freqArray[$row]->activity_id) && in_array($act->id,explode(',',$freqArray[$row]->activity_id))) ? "selected" : "";
                                                            echo '<option value="'.$act->id.'" '.$selected.'>'.$act->activities.'</option>';
                                                        endif;
                                                    endforeach;
                                                ?>
                                            </select>
                                            <input type="hidden" name="activity_id[]" id="activity_id<?=$i?>" value="<?=(!empty($freqArray[$row]->activity_id) ? $freqArray[$row]->activity_id : "")?>">
                                        </td>
                                    </tr>
                                <?php 
                                $i++;
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</form>