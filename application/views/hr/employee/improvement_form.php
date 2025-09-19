<form  id="skillImprovement">
	<div class="col-md-12">
		<div class="col-md-4 form-group">   
			<select name="cmonth" id="cmonth" class="form-control select2">
				<option value="">Month</option>
				<?php   
					foreach($monthList as $row): 
						echo '<option value="'. ('m'.(int)date('m', strtotime($row['val']))) .'">'.$row['label'].'</option>';
					endforeach; 
				?>
			</select>
			<div class="error cmonth"></div>
					                
		</div> 
        <div class="error general"></div>
    </div>
    <div class="col-md-12 mt-3">
        <div class="row form-group">
            <div class="table-responsive">
				<input type="hidden" name="emp_id" value="<?= !empty($emp_id) ? $emp_id : 0;?>">
                <table id="finaltbl" class="table table-bordered generalTable">
					<thead class="thead-info">
						<tr>
							<th width="60%" class="font-weight-bold">Skill Name</th>
							<th width="15%">Req. Skill(%)</th>
							<th width="15%">Previous Skill(%)</th>
							<th width="25%">Current Skill(%)</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(!empty($skillList)):$i=0;
							foreach($skillList as $row):
								echo '<tbody>
										<tr>
											<td>'.$row->skill_name.'</td>
											<td>'.$row->req_skill.'</td>
											<td>'.$row->prev_skill.'
												 <input type="hidden" name="prev_skill[]" value="'.$row->prev_skill.'">
											</td>
											<td>
												<input type="hidden" name="id[]" value="'.$row->trans_id.'">
												<input type="hidden" name="skill_id[]" value="'.$row->id.'">
												<input type="text" name="current_skill[]" class="form-control floatOnly" value=""/>
												<div class="error current_skill'.$i.'"></div>
											</td>
										</tr>
									</tbody>'; $i++;
							endforeach;
						endif;
						?>
					</tbody>
                </table>
            </div>
        </div>
    </div>
</form>