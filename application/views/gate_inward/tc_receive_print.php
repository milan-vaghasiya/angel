<table class="table item-list">
    <tr>
        <td>
            <img src="<?=$letter_head?>" class="img">
        </td>
    </tr>
    <tr>
        <th style="font-size:1.2rem;border-top:1px solid;padding:5px">Test Challan</th>
    </tr>
</table>

<table class="table item-list-bb">
	<tr>
		<td style="width:60%; vertical-align:top;" rowspan="2">
			<b>M/S. <?=$dataRow->name_of_agency?></b><br>
			<?=($dataRow->party_address ." - ".$dataRow->party_pincode)?><br>
			<b>Kind. Attn. : <?=$dataRow->contact_person?></b> <br>
			Contact No. : <?=$dataRow->party_mobile?><br>
			Email : <?=$dataRow->party_email?><br>
		</td>
		<td>
			<b>Challan No. :</b> <?=(!empty($dataRow->trans_number)?$dataRow->trans_number:'')?>
		</td>
	</tr>
	<tr>
		<td>
			<b>Challan Date : </b><?=(!empty($dataRow->created_at)?formatDate($dataRow->created_at):'')?>
		</td>
	</tr>
</table>
<table class="item-list-bb" style="margin-bottom:15px;">
    <tr class="bg-light">
        <th>Test Type</th>
        <th>Sample Qty</th>
        <th>Material Grade</th>
    </tr>
    <tr>
        <td class="text-center"><?=(!empty($dataRow->test_description)?$dataRow->test_description:'')?></td>
        <td class="text-center"><?=(!empty($dataRow->sample_qty)?floatval($dataRow->sample_qty):'')?></td>
        <td class="text-center"><?=(!empty($dataRow->material_grade)?$dataRow->material_grade:'')?></td>
    </tr>
   
</table>
<span style="font-size:16px;"><b>Identification of Sample : <b></span>
<p>
    <b>Item Name : </b><?= !empty($dataRow->item_name) ? '<u>'.$dataRow->item_name.'</u>':"________"?> <b> / Batch No: </b><?=!empty($dataRow->batch_no) ? '<u>'.$dataRow->batch_no.'</u>':"________"?> <b> / Heat No: </b><?=!empty($dataRow->heat_no) ? '<u>'.$dataRow->heat_no.'</u>':"________"?> <b> / GRN No: </b><?=!empty($dataRow->trans_number) ? '<u>'.$dataRow->trans_number.'</u>':"________"?>
</p>
<br>
<b>Special Instruction : </b><?=(!empty($dataRow->spc_instruction)?$dataRow->spc_instruction:'')?><br><br>
<b>Requested By : </b><?=(!empty($dataRow->created_name)?$dataRow->created_name:'')?>