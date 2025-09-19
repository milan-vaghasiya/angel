<?php
$tcHeads = array_reduce($tcHeadList, function($tcHeads, $head) { $tcHeads[$head->test_name] = $head; return $tcHeads; }, []);
foreach($tcHeadList As $row){
    $masterParam = (array)json_decode($row->parameter);
    $countHead = 0;$thead = '';$tbodyMinTr = "";$tbodyMaxTr = "";$tbodyResultTr = "";$otherTr = "";
    ?>
    <table class="table item-list-bb" style="margin-top:10px">
            <?php
			$flag = false;
            foreach($masterParam AS $key=>$param){
                if(!empty($param->min) || !empty($param->max) || !empty($param->other)){
					$flag = !empty($param->other) ? true : false;
                    $thead .= '<th class="bg-light text-center">'.$param->param.'</th>';
                    $tbodyMinTr .= '<td class=" text-center">'.(!empty($param->min)?$param->min:'-').'</td>';
                    $tbodyMaxTr .= '<td class=" text-center">'.(!empty($param->max)?$param->max:'-').'</td>';
                    $otherTr .= '<td class=" text-center">'.(!empty($param->other)?$param->other:'-').'</td>';
                    $countHead ++;
                }else{
                    unset($masterParam[$key]);
                }
               
            }
            ?>
        <thead>
            <tr>
                <th colspan="<?=$countHead+1?>"><?=$row->head_name?></th>
            </tr>
            <tr>
                <th class="bg-light text-left" style="width:15%">Test Specification</th>
                <?= $thead?> 
            </tr>
            <tr>
                <th class="bg-light text-left" style="width:15%">Minimum</th>
                <?=$tbodyMinTr?>
            </tr>
            <tr>
                <th class="bg-light text-left" style="width:15%">Maximum</th>
                <?=$tbodyMaxTr?>
            </tr>
			<?php if($flag){ ?>
			<tr>
                <th class="bg-light text-left" style="width:15%">Other</th>
                <?= $otherTr;?>
            </tr>
			<?php } ?>
        </thead>
        <tbody>
            <?php
            if(!empty($reportList)){
                foreach($reportList As $report){
					if($report->test_type == $row->test_type){
						$paramArray = (array)json_decode($report->parameter);
						$tbodyResultTr = "";
						foreach($masterParam AS $param){
							$tbodyResultTr .= '<td class=" text-center">
								'.(!empty($paramArray[$param->param]->result)?$paramArray[$param->param]->result:'-').
							'</td>';
						}
            ?>
						<tr>
							<td>Out Test (<?=$report->batch_no?>)</td>
							<?=$tbodyResultTr?>
						</tr>
            <?php
					}
                }
            }
            ?>
        </tbody>
    </table>
    <?php  
}
?>