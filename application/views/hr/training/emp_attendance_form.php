<form>
    <div class="col-md-12">
        <div class="row">            
            <input type="hidden" name="id" id="id" value="<?=(!empty($id) ? $id : "")?>" />
            <input type="hidden" name="status" id="status" value="1" />

			<div class="col-md-12 form-group">
				<label for="remark">Notes</label>
				<textarea type="text" name="remark" id="remark" class="form-control" ><?=(!empty($dataRow->remark) ? $dataRow->remark : "")?></textarea>
			</div>

			<h5>Attendee Details:</h5>
			<div class="error table_err"></div>
			<div class="table-responsive ">
				<table class="table table-striped table-borderless">
					<thead class="thead-info">
						<th>Attendees</th>
						<th>Status</th>
					</thead>
					<tbody id="tbodyData">
						<?php 
						if(!empty($empData)){
							foreach ($empData as $row) { 
								echo "<tr>";
								echo '<td>'.$row->emp_name.'</td>';
								echo '<td> 
										<input type="checkbox" name="attendee_id[]" id="attendee_id_'.$row->emp_id.'" class="filled-in chk-col-success" value="'.$row->emp_id.'"><label for="attendee_id_'.$row->emp_id.'"></label>
									</td>';
								echo "</tr>";
							}
						}
						?>
					</tbody>
				</table>
			</div>
        </div>
    </div>
</form>