<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}
.page-break{
        page-break-before: always;
        display: block;
    }
</style>
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;" style="margin-top:5px;">
			<tr>
			    <td style="width:20%;"></td>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;"><?=$dataRow->title?></td>
				<td class="text-uppercase text-right" style="font-size:1.3rem;font-weight:bold;width:20%;"></td>
			</tr>
		</table>

		<table class="table item-list-bb fs-22 text-left" style="margin-top:5px;">
            <tr>
                <th style="width:17%;">Training Start Date</th>
                <td><?=date('Y-m-d H:i:s', strtotime($dataRow->start_date))?></td>
                <th style="width:17%;">Training Type</th>
                <td><?=$dataRow->type?></td>
            </tr>
            <tr>
                <th>Training End Date</th>
                <td><?=date('Y-m-d H:i:s', strtotime($dataRow->end_date))?></td>
                <th>Trainer Name</th>
                <td><?=$dataRow->trainer_name?></td>
            </tr>
            <tr>
                <th>Skill</th>
                <td><?=$dataRow->skill_name?></td>
                <th>Attendee</th>
                <?php $presentEmployees = !empty($dataRow->attendee_id) ? explode(',', $dataRow->attendee_id) : [];?>
                <td>Present : <b>(<?=COUNT($presentEmployees)?>)</b> / Invited : <b>(<?=$dataRow->empCount?>)</b></td>
            </tr>
        </table>

        <table style="margin-top:5px;">
            <tr><td><b>Notes :- </b><br><?=nl2br(htmlspecialchars($dataRow->remark))?></td></tr>;
        </table>

        <div class="page-break"></div>          
        <h2 style="font-size: 1.2rem;">Attendee Details:</h2>
        <table class="table table-bordered">
            <tr class="bg-light">
                <th>Sr.No</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
            </tr>
            <tbody>
                <?php $i=1;
                if (!empty($trainingData)) { 
                    $attendeeIds = explode(',', $dataRow->attendee_id);
                    foreach ($trainingData as $row) { 
                        if(in_array($row->emp_id, $attendeeIds)) {
                            echo '<tr class="text-center">
                                    <td>'.$i++.'</td>
                                    <td>'.$row->emp_code.'</td>
                                    <td>'.$row->emp_name.'</td>
                                    <td>'.$row->name.'</td>
                                    <td>'.$row->title.'</td>
                                </tr>';
                        }
                    }
                }
                ?>
            </tbody>
        </table>

        <h2 style="font-size: 1.2rem;">Absent Details:</h2>
        <table class="table table-bordered">
            <tr class="bg-light">
                <th>Sr.No</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
            </tr>
            <tbody>
                <?php $i=1;
                if(!empty($trainingData)){
                    foreach($trainingData as $row){ 
                        if (!in_array($row->emp_id, $attendeeIds)) {
                        echo '<tr class="text-center">
                                <td>'.$i++.'</td>
                                <td>'.$row->emp_code.'</td>
                                <td>'.$row->emp_name.'</td>
                                <td>'.$row->name.'</td>
                                <td>'.$row->title.'</td>
                            </tr>';
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>