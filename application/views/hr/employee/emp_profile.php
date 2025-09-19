<?php $this->load->view('includes/header'); ?>

<?php
    $profile_pic = 'male_user.png';
    if(!empty($empData->emp_profile)):
        $profile_pic = $empData->emp_profile;
    else:
        if(!empty($empData->emp_gender) and $empData->emp_gender=="Female"):
            $profile_pic = 'female_user.png';
        endif;
    endif;
?>
<link href="<?=base_url();?>assets/css/icard.css?v=<?=time()?>" rel="stylesheet" type="text/css">
<style>
	.profile_detail{position:relative;}
	.editBtn{position: absolute;top: 0%;right: 0%;border-radius: 0px 5px 0px 0px;}
	.pic-holder {flex-direction: column;align-items: center;justify-content: center;flex-wrap:wrap;}
	.pic {max-width: 100%;height: auto;}
</style>

<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="met-profile">
							<div class="row profile_detail">
								<div class="col-lg-4 align-self-center mb-3 mb-lg-0">
									<div class="met-profile-main flex-nowrap">
										<div class="card-body p-2">
											<form id="profileForm" action="POST" enctype="multipart/form-data">
												<div class="profile-pic-wrapper">
													<div class="pic-holder">
														<!-- uploaded pic shown here -->
														<img id="profilePic" class="pic" src="<?= base_url('assets/uploads/emp_profile/'.$profile_pic) ?>">
														<Input class="uploadProfileInput" type="file" name="profile_pic" id="newProfilePhoto" accept="image/*" style="opacity: 0;" />
														<label for="newProfilePhoto" class="upload-file-block">
															<div class="text-center">
																<div class="mb-2"><i class="fa fa-camera fa-2x"></i></div>
																<div class="text-uppercase">Update <br /> Profile Photo</div>
															</div>
														</label>
														<input type="hidden" name="old_profile" id="old_profile" value="<?=(!empty($empData->emp_profile))?$empData->emp_profile:""; ?>" />
														<input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($empData->id))?$empData->id:""; ?>" />
													</div>
												</div>
											</form>
										</div>
										<div class="met-profile_user-detail ml-5">
											<h4><?= (!empty($empData->emp_code) ? $empData->emp_code : "")?></h4>
											<h4><?= (!empty($empData->emp_name) ? $empData->emp_name : "")?></h4>
											<p class="mb-0 met-user-name-post"><?= (!empty($empData->department_name) ? $empData->department_name : "")?> (<?= (!empty($empData->designation_name) ? $empData->designation_name : "")?>)</p>
											<p class="mb-0 met-user-name-post"></p>
										</div>
									</div>                                                
								</div>
								
								<div class="col-lg-3 ms-auto align-self-center">
									<ul class="list-unstyled personal-detail mb-0">
										<li class=""> <i class="las la-phone mr-2 text-secondary font-22 align-middle"></i> <b> Phone </b> : <?= (!empty($empData->emp_contact) ? $empData->emp_contact : "")?></li>
										<li class="mt-2"> <i class="las la-envelope text-secondary font-22 align-middle mr-2"></i> <b> E-mail </b> : <?= (!empty($empData->emp_email) ? $empData->emp_email : "")?></li>
										<li class="mt-2"> <i class="las la-calendar-minus mr-2 text-secondary font-22 align-middle"></i> <b> Date Of Birth </b> : <?= (!empty($empData->emp_birthdate) ? formatDate($empData->emp_birthdate) : "")?></li>
										<li class="mt-2"> <i class="las la-calendar-minus mr-2 text-secondary font-22 align-middle"></i> <b> Joining Date </b> : <?= (!empty($empData->emp_joining_date) ? formatDate($empData->emp_joining_date) : "")?></li>
									</ul>
								</div>
								
								<div class="col-lg-5 ms-auto align-self-center">
									<div class="row flex-nowrap">
										<div class="col-auto text-end border-end text-center">
											<button type="button" class="btn btn-soft-info btn-icon-circle btn-icon-circle-sm mb-2">
												<i class="las la-info-circle mr-2 text-secondary font-22 align-middle"></i>
											</button>
											<h4 class="m-0 fw-bold">
												<p class="mb-0 fw-semibold"><?= (!empty($empData->category) ? $empData->category : "");?></p>
												
												<p class="mb-0 fw-semibold">Experience</p>
												<span class="font-12 fw-normal"> <?= (!empty($empData->emp_experience) ? $empData->emp_experience : "");?> </span>
											</h4>
										</div><!--end col-->
										<div class="col-auto text-left">
											<button type="button" class="btn btn-soft-info btn-icon-circle btn-icon-circle-sm mb-2">
												<i class="las la-map-marked-alt mr-2 text-secondary font-22 align-middle"></i>
											</button>
											<p class="mb-0 fw-semibold">Address</p>
											<h4 class="m-0 fw-bold" style="width:80%;"><span class="font-12 fw-normal"><?= (!empty($empData->emp_address) ? $empData->emp_address : "")?></span></h4>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab" aria-selected="true">Personal</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#work_profile" role="tab" aria-selected="true">Work Profile</a>
							</li>                                    
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#nomination" role="tab" aria-selected="false">Nomination</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#experience" role="tab" aria-selected="false">Past Experience</a>
							</li>
							
						</ul>

						<div class="tab-content">
							<div class="tab-pane p-3 active" id="personal" role="tabpanel">
								<div class="row">
									<div class="col-lg-12">
										<div class="card">
											<form id="personalDetails" data-res_function="resPersonalDetail">
												<div class="card-body">
													<div class="row">
														<input type="hidden" name="id" id="id" value="<?= (!(empty($empData->id)) ? $empData->id : "");?>" />
														<input type="hidden" name="form_type" value="personalDetails" />

														<?php if($empData->status == 1){ ?>
														<div class="col-md-3 form-group">
															<label for="emp_code">Employee Code</label>
															<input type="text" name="emp_code" class="form-control" value="<?=(!empty($empData->emp_code))?$empData->emp_code:""; ?>" />
														</div>
														<?php } ?>

														<div class="col-md-3 form-group">
															<label for="emp_name">Employee Name</label>
															<input type="text" name="emp_name" class="form-control text-capitalize req" value="<?=(!empty($empData->emp_name))?$empData->emp_name:""; ?>" />
														</div>
														<div class="col-md-3 form-group">
															<label for="father_name">Father/Husband Name</label>
															<input type="text" name="empDetails[father_name]" class="form-control" value="<?=(!empty($empData->father_name))?$empData->father_name:""?>" />
														</div>
														<div class="col-md-3 form-group">
															<label for="emp_email">E-Mail</label>
															<input type="text" name="empDetails[emp_email]" class="form-control" value="<?=(!empty($empData->emp_email))?$empData->emp_email:""?>" />
														</div>
														<div class="col-md-3 form-group">
															<label for="emp_contact">Phone</label>
															<input type="text" name="emp_contact" class="form-control numericOnly req" value="<?=(!empty($empData->emp_contact))?$empData->emp_contact:""?>" />
														</div>
													   <div class="col-md-3 form-group">
                                                            <label for="emp_alt_contact">Emergency Contact</label>
                                                            <input type="text" name="empDetails[emp_alt_contact]" class="form-control numericOnly" value="<?=(!empty($empData->emp_alt_contact))?$empData->emp_alt_contact:""?>" />
                                                        </div>
														<div class="col-md-3 form-group">
															<label for="emp_birthdate">Date Of Birth</label>
															<input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control" value="<?=(!empty($empData->emp_birthdate))?$empData->emp_birthdate:""?>" />
														</div>
													
														<div class="col-md-3 form-group">
															<label for="emp_gender">Gender</label>
															<select id="emp_gender" name="empDetails[emp_gender]" class="form-control select2">
																<option value="">Select Gender</option>
																<?php
																	foreach($this->gender as $value):
																		$selected = (!empty($empData->emp_gender) && $value == $empData->emp_gender)?"selected":"";
																		echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
																	endforeach;
																?>
															</select>
														</div>
                                                        <?php if($empData->status == 1){ ?>
														<div class="col-md-3 form-group">
															<label for="blood_group">Blood Group</label>
															<select name="empDetails[blood_group]" id="blood_group" class="form-control select2">
																<option value="">Select Blood Group</option>
																<?php
																	foreach($this->bloodGroups as $key=>$value):
																		$selected = (!empty($empData->blood_group) && $key == $empData->blood_group)?"selected":"";
																		echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
																	endforeach;
																?>
															</select>
														</div>
														<?php } ?>
														<div class="col-md-3 form-group">
															<label for="marital_status">Marital Status</label>
															<select name="empDetails[marital_status]" id="marital_status" class="form-control select2">
																<option value="">Select status</option>
																<option <?= (!empty($empData->marital_status) && $empData->marital_status == "YES")? "selected":"";?> value="YES">Married</option>
																<option <?= (!empty($empData->marital_status) && $empData->marital_status == "NO")? "selected":"";?> value="NO">Unmarried</option>
															</select>
														</div>
                                                          <div class="col-md-6 form-group">
															<label for="qualification">Qualification</label>
															<input type="text" name="empDetails[qualification]" id="qualification" class="form-control req" value="<?=(!empty($empData->qualification))?$empData->qualification:""?>" />
														</div>
														<div class="col-md-12 form-group">
															<label for="emp_address">Address</label>
															<textarea name="empDetails[emp_address]" class="form-control" style="resize:none;" rows="1"><?=(!empty($empData->emp_address))?$empData->emp_address:""?></textarea>
														</div>

                                                        <div class="col-md-12 form-group">
                                                            <label for="permanent_address">Permanent Address</label>
                                                            <textarea name="empDetails[permanent_address]" class="form-control" style="resize:none;" rows="1"><?=(!empty($empData->permanent_address))?$empData->permanent_address:""?></textarea>
                                                        </div>

                                                        <hr>
                                                      
                                                        <div class="col-md-3 form-group">
															<label for="aadhar_no">Aadhar No.</label>
															<input type="text" name="empDetails[aadhar_no]" id="aadhar_no" class="form-control " value="<?=(!empty($empData->aadhar_no))?$empData->aadhar_no:""?>" />
														</div>

                                                         <div class="col-md-3 form-group">
                                                            <label for="aadhar_file">Aadhar File</label>
                                                            <div class="input-group">
                                                                <input type="file" name="aadhar_file" class="form-control" value="" />
                                                                <?php if(!empty($empData->aadhar_file)): ?>
                                                                    <div class="input-group-append">
                                                                        <a href="<?=base_url('assets/uploads/emp_documents/'.$empData->aadhar_file)?>" class="btn btn-outline-primary" target="_blank"><i class="fas fa-download"></i></a>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-3 form-group">
															<label for="pan_no">Pan No.</label>
															<input type="text" name="empDetails[pan_no]" id="pan_no" class="form-control " value="<?=(!empty($empData->pan_no))?$empData->pan_no:""?>" />
                                                          
														</div>
													    <div class="col-md-3 form-group">
                                                            <label for="pan_file">Pan File</label>
                                                            <div class="input-group">
                                                                <input type="file" name="pan_file" class="form-control" value="" />
                                                                <?php if(!empty($empData->pan_file)): ?>
                                                                    <div class="input-group-append">
                                                                        <a href="<?=base_url('assets/uploads/emp_documents/'.$empData->pan_file)?>" class="btn btn-outline-primary" target="_blank"><i class="fas fa-download"></i></a>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    
													
													</div>
												</div>
												<div class="card-footer">   
													<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right" onclick="customStore({'formId':'personalDetails','fnsave':'editProfile'});"><i class="fa fa-check"></i> Save </button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane p-3" id="work_profile" role="tabpanel">
								<div class="row">
									<div class="col-lg-12">
										<div class="card">
											<form id="workProfile" data-res_function="resPersonalDetail">
												<div class="card-body">
													<div class="row">
														<input type="hidden" name="id" id="id" value="<?= (!(empty($empData->id)) ? $empData->id : "");?>" />
														<input type="hidden" name="form_type" value="workProfile" />

														<div class="col-md-3 form-group">
															<label for="emp_category">Emp Category</label>
															<select name="emp_category" class="form-control select2">
																<option value="">Select Category</option>
																<?php
																	foreach($empCategoryList as $row):
																		$selected = (!empty($empData->emp_category) && $row->id == $empData->emp_category)?"selected":"";
																		echo '<option value="'.$row->id.'" '.$selected.'> '.$row->category.'</option>';
																	endforeach;
																?>
															</select>
														</div>
														
														<div class="col-md-3 form-group">
															<label for="dept_id">Department</label>
															<select name="dept_id" id="dept_id" class="form-control select2 req">
																<option value="">Select Department</option>
																<?php
																	foreach($departmentList as $row):
																		$selected = (!empty($empData->dept_id) && $row->id == $empData->dept_id)?"selected":"";
																		echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
																	endforeach;
																?>
															</select>
														</div>
														<div class="col-md-3 from-group">
															<label for="designation_id">Designation</label>
															<select name="designation_id" id="designation_id" class="form-control select2 req">
																<option value="">Select Designation</option>
																<?php
																	foreach($designationList as $row):
																		$selected = (!empty($empData->designation_id) && $row->id == $empData->designation_id)?"selected":"";
																		echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
																	endforeach;
																?>
															</select>
														</div>
                                                        <div class="col-md-3 form-group">
                                                            <label for="emp_role">Role</label>
                                                            <select name="emp_role" id="emp_role" class="form-control select2 req">
                                                                <option value="">Select Role</option>
                                                                <?php
                                                                    foreach($roleList as $key => $value):
                                                                        $selected = (!empty($empData->emp_role) && $key == $empData->emp_role)?"selected":"";
                                                                        echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                                                    endforeach;
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 form-group">
															<label for="emp_joining_date">Joining Date</label>
															<input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control" value="<?=(!empty($empData->emp_joining_date))?$empData->emp_joining_date:""?>" />
														</div>
														
														
														<div class="col-md-3 form-group">
															<label for="rec_source">Source</label>
															<select name="empDetails[rec_source]" class="form-control select2">
																<option value="">Select Source</option>
																<?php
																	foreach($this->recSource as $row):
																		$selected = (!empty($empData->rec_source) && $empData->rec_source == $row) ? "selected" : "";
																		echo '<option '.$selected.' value="'.$row.'">'.$row.'</option>';
																	endforeach;
																?>
															</select>
														</div>
														<div class="col-md-3 form-group">
															<label for="ref_by">Reference</label>
															<input type="text" name="empDetails[ref_by]" class="form-control" value="<?=(!empty($empData->ref_by))?$empData->ref_by:""?>" />
														</div>
														
													
														<div class="col-md-3 form-group">
															<label for="auth_id">Higher Authority</label>
															<select name="auth_id" id="auth_id" class="form-control select2">
																<option value="">Select Employee</option>
																<?php
																	foreach($empList as $row):
																		$selected = (!empty($empData->auth_id) && $empData->auth_id ==  $row->id)?"selected":"";
																		if($empData->id != $row->id):
																			echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
																		endif;    
																	endforeach;
																?>
															</select>
														</div>
													</div>
												</div>
												<div class="card-footer">   
													<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right" onclick="customStore({'formId':'workProfile','fnsave':'editProfile'});"><i class="fa fa-check"></i> Save </button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane p-3" id="experience" role="tabpanel">
								<div class="row">                    
									<div class="col-md-12">
										<div class="card">
											<form id="getEmpExperience" enctype="multipart/form-data">
												<div class="card-body">
													<div class="row">
														<input type="hidden" name="id" id="id" value="" />
														<input type="hidden" name="emp_id" value="<?= (!(empty($empData->id)) ? $empData->id : "");?>" />
														<input type="hidden" name="form_type" value="empExperience" />

														<div class="col-md-4 form-group">
															<label for="company_name">Company Name</label>
															<input type="text" name="company_name" id="company_name" class="form-control req" value="" />
														</div>
                                                      
														<div class="col-md-4 form-group">
															<label for="designation">Designation</label>
															<input type="text" name="designation" id="designation" class="form-control req" value="" />
														</div>
													
                                                        <div class="col-md-4 form-group">
                                                            <label for="period_service">Period Of Sevice(Month)</label>
                                                            <div class="input-group">
                                                                <input type="text" id="period_service" name="period_service"  class="form-control floatOnly" />
                                                                <button type="button" class="btn btn-outline-success btn-save float-right ml-2" onclick="saveEmpFormData('getEmpExperience','saveEmpForm');"><i class="fa fa-check"></i> Save</button>
                                                            </div>
                                                        </div>
													</div>
												</div>
											</form>
											<hr class="mt-0 mb-0">
											<div class="card-body">
												<div class="table-responsive">
													<table id="inspection" class="table table-bordered align-items-center">
														<thead class="thead-info">
															<tr>
																<th style="width:5%;">#</th>
																<th class="text-center">Company Name</th>
																<th class="text-center">Designation</th>
																<th class="text-center">Period Of Sevice(Month)</th>
																<th class="text-center" style="width:10%;">Action</th>
															</tr>
														</thead>
														<tbody id="expBody">
															<tr>
																<?php
                                                                    if(!empty($empExp)):
                                                                        $i=1;
                                                                        foreach($empExp as $row):
																			$deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id' : ".$row->emp_id.",'form_type' : 'empExperience'}, 'fndelete' : 'deleteEmpForm','message' : 'Employee'}";
                                                                            echo '<tr>
																				<td class="text-center">'.$i++.'</td>
																				<td class="text-center">'.$row->company_name.'</td>
																				<td class="text-center">'.$row->designation.'</td>
																				<td class="text-center">'.$row->period_service.'</td>
																				<td class="text-center">
																					<button type="button" onclick="trashEmpProfile('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
																				</td>
																			</tr>';
                                                                        endforeach;
                                                                    else:
                                                                        echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>                                                
							<div class="tab-pane p-3" id="nomination" role="tabpanel">
								<div class="row">
									<div class="col-lg-12 col-xl-12">
										<form id="getEmpNom">
											<div class="card-body">
												<div class="row">
													<input type="hidden" name="id" id="id" value="" />
													<input type="hidden" name="emp_id" value="<?= (!(empty($empData->id)) ? $empData->id : "");?>" />
													<input type="hidden" name="form_type" value="empNomination" />
													<div class="col-md-4 form-group">
														<label for="nom_name">Name</label>
														<input type="text" id="nom_name" name="nom_name" class="form-control req" value="" />
													</div>
													<div class="col-md-2 form-group">
														<label for="nom_gender">Gender</label>
														<select id="nom_gender" name="nom_gender" class="form-control select2">
															<option value="Male">Male</option>
															<option value="Female">Female</option>
															<option value="Other">Other</option>
														</select>
													</div>
													<div class="col-md-3 form-group">
														<label for="nom_relation">Relation</label>
														<input type="text" id="nom_relation" name="nom_relation" class="form-control req" value="" />
													</div>
													
                                                    <div class="col-md-3 form-group">
                                                        <label for="grade">Mobile No.</label>
                                                        <div class="input-group">
                                                            <input type="text" id="nom_contact_no" name="nom_contact_no"  class="form-control" value="" />
                                                            <button type="button" class="btn btn-outline-success btn-save float-right ml-2" onclick="saveEmpFormData('getEmpNom','saveEmpForm');"><i class="fa fa-check"></i> Save</button>
                                                        </div>
                                                    </div>
												</div>
											</div>
										</form>
										<div class="card-body">
											<div class="table-responsive">
												<table id="empNomtbl" class="table table-bordered align-items-center">
													<thead class="thead-info">
														<tr>
															<th style="width:5%;">#</th>
															<th>Name</th>
															<th>Gender</th>
															<th>Relation</th>
															<th>Mobile No.</th>
															<th class="text-center" style="width:10%;">Action</th>
														</tr>
													</thead>
													<tbody id="empNomBody">
														<tr>
															<?php
																if (!empty($empNom)) : $i = 1;
																	foreach ($empNom as $row) :
																		$deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id' : ".$row->emp_id.",'form_type' : 'empNomination'}, 'fndelete' : 'deleteEmpForm','message' : 'Employee'}";
																		echo '<tr>
																			<td>' . $i++ . '</td>
																			<td>' . $row->nom_name . '</td>
																			<td>' . $row->nom_gender . '</td>
																			<td>' . $row->nom_relation . '</td>
																			<td>' . $row->nom_contact_no . ' </td>
																			<td class="text-center">
																				<button type="button" onclick="trashEmpProfile('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
																			</td>
																		</tr>';
																	endforeach;
																else:
																	echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
																endif;
															?>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/emp_profile.js?V=<?=time()?>"></script>