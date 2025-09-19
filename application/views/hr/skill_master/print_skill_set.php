<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}</style>
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;" style="margin-top:5px;">
			<tr>
			    <td style="width:20%;"></td>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;">Skill Set</td>
				<td class="text-uppercase text-right" style="font-size:1.3rem;font-weight:bold;width:20%;"></td>
			</tr>
		</table>
		<hr>
		<table class="table">
			<tr>
				<th style="font-size:15px;">Department : <?=$skillSetData[0]->name?></th>
				<th style="font-size:15px;">Designation : <?=$skillSetData[0]->title?></th>
			</tr>
		</table>
		<table class="table itemList pad5 tbl-fs-11" style="margin-top:10px;">
			<tr class="text-center thead-gray">
				<th style="width:5%;">#</th>
				<th>Skil Name</th>
				<th>Percentage Of Skill</th>
			</tr>
			<?php
			if (!empty($skillSetData)) :
				$i = 1;
				foreach ($skillSetData as $row) :
					echo '<tr>';
					echo '<td class="text-center">' . $i++ . '</td>';
					echo '<td class="text-center">' . $row->skill_name . '</td>';
					echo '<td class="text-center">' . $row->req_skill . '</td>';
					echo '</tr>';
				endforeach;
			else :
				echo '<tr><th class="text-center" colspan="3">Record Not Found !</th></tr>';
			endif;
			?>

		</table>
	</div>
</div>