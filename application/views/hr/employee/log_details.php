<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="activity">
					<?php
						if(!empty($getEmpLog)):
							foreach($getEmpLog as $row):
					?>
								<div class="activity-info">
									<div class="icon-info-activity">
										<i class="las la-check-circle bg-soft-primary"></i>
									</div>
									<div class="activity-info-text">
										<div class="d-flex justify-content-between align-items-center">
											<h6 class="m-0 w-75"><?= $row->notes;?></h6>
										</div>
										<p class="text-muted mt-3"><?= formatDate($row->ref_date);?></p>
										<p class="text-muted mt-1 mb-1"><b><?= ($row->log_type == 2) ? "Created By" : "Approved By";?>: </b> <?= ($row->log_type != 7) ? $row->employee_name. ' || <b>Reason: </b>'.$row->reason : "-";?></p>
										<p class="text-muted mt-1 mb-1"><b>Rejected By: </b><?= ($row->log_type == 7) ? $row->employee_name. ' || <b>Reason: </b>'.$row->reason : "-";?></p>
										<?php
											if($row->from_stage == 3){
										?>
													<p class="text-muted mt-1 mb-1">
														<b>Aadhar No.:</b> <?= $row->aadhar_no;?>&nbsp;
														<?php if (!empty($row->aadhar_file)):?>
															<a style="font-size:20px;" href="<?= base_url("assets/uploads/emp_documents/".$row->aadhar_file);?>" target="_blank" download><i class="fa fa-download"></i></a>&nbsp;
														<?php endif; ?>&nbsp;
														<b>Pan No.</b> <?= $row->pan_no;?>&nbsp;
														<?php if (!empty($row->pan_file)):?>
															<a style="font-size:20px;" href="<?= base_url("assets/uploads/emp_documents/".$row->pan_file);?>" target="_blank" download><i class="fa fa-download"></i></a>&nbsp;
														<?php endif; ?>
													</p>
										<?php
											}
											if($row->from_stage == 4){
										?>
												<table class="table table-bordered generalTable">
													<thead class="thead-info">
														<tr>
															<th width="60%" class="font-weight-bold">Skill Name</th>
															<th width="15%">Req. Skill(%)</th>
															<th width="25%">Current Skill(%)</th>
														</tr>
													</thead>
													<tbody>
														<?php
															if (!empty($skillList)):
																foreach ($skillList as $row):
																
																	echo'<tr>
																			<td>'.$row->skill_name.'</td>
																			<td>'.$row->req_skill.'</td>
																			<td>'.$row->prev_skill.'</td>
																		</tr>';
																endforeach;
															endif;
														?>
													</tbody>
												</table>
										<?php } ?>
									</div>
								</div>
					<?php
							endforeach;
						endif; 
					?>
				</div>
            </div>
		</div>  
    </div>
</div>