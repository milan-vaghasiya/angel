<?php
class EmployeeModel extends MasterModel{
    private $empMaster = "employee_master";
    private $emp_experience = "emp_experience";
    private $empNom = "emp_nominee";
    private $interviewLogs = "interview_logs";
	private $empVacancy = "emp_vacancy";
	private $staffSkill = "staff_skill";
	private $empDetail = "emp_detail";

    public function getDTRows($data){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name as dept_name,emp_designation.title as emp_designation,emp_category.category as emp_category,emp_detail.emp_profile,emp_detail.aadhar_no,emp_detail.pan_no,emp_detail.aadhar_file,emp_detail.pan_file";
       
		$data['leftJoin']['emp_detail'] = "employee_master.id = emp_detail.emp_id";
        $data['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";
        $data['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        
        $data['where']['employee_master.emp_role !='] = "-1";
        if($data['status'] == 0):
			$data['where']['employee_master.status']= 1;
            $data['where']['employee_master.is_active']=1;

		elseif($data['status'] == 1):
            $data['where']['employee_master.status']= 1;
            $data['where']['employee_master.is_active']=0;
		else:		
			if(isset($data['status'])):
				$data['where']['employee_master.status']= $data['status'];
			endif;
		endif;
        
		if($data['status'] >= 2 && $data['status'] <= 6):
		
			$data['select'] .= ",emp_detail.rec_source,emp_detail.ref_by";
			$data['leftJoin']['emp_detail'] = "emp_detail.emp_id = employee_master.id";
		
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "employee_master.emp_name";
			$data['searchCol'][] = "employee_master.emp_contact";
			$data['searchCol'][] = "department_master.name";
			$data['searchCol'][] = "emp_designation.title";
			$data['searchCol'][] = "emp_detail.rec_source";
			$data['searchCol'][] = "emp_detail.ref_by";
			
		elseif($data['status'] == 7):
			$data['select'] .= ",emp_detail.rec_source,emp_detail.ref_by,interview_logs.from_stage,reject.emp_name as reject_name,interview_logs.created_at as reject_at";
			$data['leftJoin']['emp_detail'] = "emp_detail.emp_id = employee_master.id";
			$data['leftJoin']['interview_logs'] = "interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 7";
			$data['leftJoin']['employee_master reject'] = "reject.id = interview_logs.created_by";
		
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "employee_master.emp_name";
			$data['searchCol'][] = "employee_master.emp_contact";
			$data['searchCol'][] = "department_master.name";
			$data['searchCol'][] = "emp_designation.title";
			$data['searchCol'][] = "emp_detail.rec_source";
			$data['searchCol'][] = "emp_detail.ref_by";
			$data['searchCol'][] = "emp_detail.from_stage";
			$data['searchCol'][] = "reject.emp_name";
		elseif($data['status'] == 8):
          
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "employee_master.emp_code";
            $data['searchCol'][] = "employee_master.emp_contact";
			$data['searchCol'][] = "department_master.name";

			$data['order_by']['employee_master.emp_code'] = "ASC";
		else:
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "employee_master.emp_name";
			$data['searchCol'][] = "employee_master.emp_code";
			$data['searchCol'][] = "department_master.name";
			$data['searchCol'][] = "emp_designation.title";
			$data['searchCol'][] = "emp_category.category";
			$data['searchCol'][] = "employee_master.emp_contact";
			$data['searchCol'][] = "";
			
			$data['order_by']['employee_master.emp_code'] = "ASC";
		endif;
        
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

	public function getEmployeeList($data=array()){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.*,department_master.name as department_name,emp_designation.title as designation_name,emp_category.category,attendance_log.id as log_id,attendance_log.attendance_date,attendance_log.type";
        $queryData['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";
        $queryData['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $queryData['leftJoin']['attendance_log'] = "employee_master.id = attendance_log.emp_id";

        if(!empty($data['emp_role'])):
            $queryData['where_in'] = $data['emp_role'];
        endif;

        if(!empty($data['emp_sys_desc_id'])):
            $queryData['where']['find_in_set("'.$data['emp_sys_desc_id'].'", emp_sys_desc_id) >'] = 0;
        endif;

        if(!empty($data['designation_id'])):
            $queryData['where']['employee_master.designation_id'] = $data['designation_id'];
        endif;

        if(!empty($data['dept_id'])):
            $queryData['where']['employee_master.dept_id'] = $data['dept_id'];
        endif;


        if(!empty($data['is_active'])):
            $queryData['where_in']['employee_master.is_active'] = $data['is_active'];
        endif;

        if(empty($data['all'])):
            $queryData['where']['employee_master.emp_role !='] = "-1";
        endif;
		
		/*
        if(!empty($data['attendance_date'])){
            $queryData['where']['DATE(employee_master.created_at)'] = $data['attendance_date'];
        }
		*/
		
        $queryData['group_by'][]= 'employee_master.id';

        return $this->rows($queryData);
    }
	
    public function getEmployee($data){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.*,department_master.name as department_name,emp_designation.title as designation_name,emp_detail.father_name, emp_detail.emp_email, emp_detail.emp_alt_contact, emp_detail.marital_status, emp_detail.emp_gender,emp_detail.qualification, emp_detail.emp_address,emp_detail.aadhar_no,emp_detail.pan_no,emp_detail.aadhar_file,emp_detail.pan_file,emp_detail.permanent_address,emp_detail.blood_group,emp_detail.id as emp_detail_id,emp_detail.rec_source,emp_detail.ref_by, emp_detail.emp_profile";

		$queryData['leftJoin']['emp_detail'] = "emp_detail.emp_id = employee_master.id";
        $queryData['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";
        if(!empty($data['id'])){
            $queryData['where']['employee_master.id'] = $data['id'];
        }
        if(!empty($data['emp_name'])){
            $data['where']['emp_name'] = $data['emp_name'];
        }
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['emp_contact'] = "Contact no. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
			
            if($data['status'] == 1 && $this->checkDuplicate(['emp_code'=>$data['emp_code'],'id'=>$data['id']]) > 0) :
				$errorMessage['emp_code'] = "Employee code is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;
			
			//Latest Emp. Code
			// if(empty($data['id'])):
            //     $emp_no = $this->getNextEmpNo();
			// 	$data['biomatric_id'] = $emp_no;
			// 	$data['emp_code'] = sprintf("AE%03d",$emp_no);
            // endif;

            if(empty($data['id'])):
                $data['emp_psc'] = $data['emp_password'];
                $data['emp_password'] = md5($data['emp_password']); 
            endif;

            $empDetails = (!empty($data['empDetails']))?$data['empDetails']:array(); 
			unset($data['empDetails']);
			
			$data['super_auth_id'] = "";
            if(!empty($data['auth_id'])){
                $authData = $this->getEmployee(['id'=>$data['auth_id']]);
                $data['super_auth_id'] = ((!empty($authData->super_auth_id))?$authData->super_auth_id.',':'').$data['auth_id'];
            }
            $result = $this->store($this->empMaster,$data,'Employee');
            
			if(empty($data['id'])){
                $empDetails['id'] = "";
                $empDetails['emp_id'] = $result['id'];
				$this->store($this->empDetail,$empDetails,'Employee');
			}else{
				$this->edit($this->empDetail,['emp_id' => $result['id']],$empDetails,'Employee');
			}

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }

	public function checkDuplicate($data){ 
        $queryData['tableName'] = $this->empMaster;
        if(!empty($data['emp_contact'])){$queryData['where']['emp_contact'] = $data['emp_contact'];}
        if(!empty($data['emp_code'])){$queryData['where']['emp_code'] = $data['emp_code'];}
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }
	
	public function delete($data){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['created_by','updated_by'];
            $checkData['value'] = $data['id'];
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Employee is currently in use. you cannot delete it.'];
            endif;

            $this->trash($this->emp_experience,['emp_id'=>$data['id']],'Employee');
            $this->trash($this->empNom,['emp_id'=>$data['id']],'Employee');
            $this->trash($this->empDetail,['emp_id'=>$data['id']],'Employee');
            $this->trash($this->interviewLogs,['emp_id'=>$data['id']],'Employee');
            $this->trash($this->staffSkill,['emp_id'=>$data['id']],'Employee'); 

            $result = $this->trash($this->empMaster,['id'=>$data['id']],'Employee');
            
            if (!empty($data['aadhar_file'])) {
                $old_file_path = FCPATH."assets/uploads/emp_documents/" . $data['aadhar_file'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
            if (!empty($data['pan_file'])) {
                $old_file_path = FCPATH."assets/uploads/emp_documents/" . $data['pan_file'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function activeInactive($postData){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->empMaster,$postData,'');
            $result['message'] = "Employee ".(($postData['is_active'] == 1)?"Activated":"De-activated")." successfully.";
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changePassword($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                return ['status'=>2,'message'=>'Somthing went wrong...Please try again.'];
            endif;

            $empData = $this->getEmployee(['id'=>$data['id']]);
            if(md5($data['old_password']) != $empData->emp_password):
                $result = ['status'=>0,'message'=>['old_password'=>"Old password not match."]];
            endif;

            $postData = ['id'=>$data['id'],'emp_password'=>md5($data['new_password']),'emp_psc'=>$data['new_password']];
            $result = $this->store($this->empMaster,$postData);
            $result['message'] = "Password changed successfully.";

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function resetPassword($id){
        try{
            $this->db->trans_begin();

            $data['id'] = $id;
            $data['emp_psc'] = '123456';
            $data['emp_password'] = md5($data['emp_psc']); 
            
            $result = $this->store($this->empMaster,$data);
            $result['message'] = 'Password Reset successfully.';

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function editProfile($data){
        try{
            $this->db->trans_begin();
            $form_type = $data['form_type']; unset($data['form_type'], $data['designationTitle']);

            if(in_array($form_type,['workProfile'])):
                $empDetails = (!empty($data['empDetails']))?$data['empDetails']:array(); 
			    unset($data['empDetails']);
                $result = $this->store($this->empMaster,$data,'Employee');
                if(empty($data['id'])){
                    $empDetails['id'] = "";
                    $empDetails['emp_id'] = $result['id'];
                    $this->store($this->empDetail,$empDetails,'Employee');
                }else{
                    $this->edit($this->empDetail,['emp_id' => $result['id']],$empDetails,'Employee');
                }
            endif;
            
            if($form_type == "personalDetails"):
				if($this->checkDuplicate($data) > 0):
					$errorMessage['emp_contact'] = "Contact no. is duplicate.";
					return ['status'=>0,'message'=>$errorMessage];
				endif;

				if(empty($data['id'])):
					$data['emp_psc'] = $data['emp_password'];
					$data['emp_password'] = md5($data['emp_password']); 
				endif;

				$empDetails = (!empty($data['empDetails']))?$data['empDetails']:array(); 
				unset($data['empDetails']);

				$result = $this->store($this->empMaster,$data,'Employee');
				
				if(!empty($data['id'])){
					$this->edit($this->empDetail,['emp_id' => $result['id']],$empDetails,'Employee');
				}
			endif;

           if($form_type == "updateProfilePic"):
                $result = $this->edit($this->empDetail,['emp_id' => $data['id']],['emp_profile'=>$data['emp_profile']],'Profile Photo');
                $result['filePath'] = base_url("assets/uploads/emp_profile/".$data['emp_profile']);
            endif;

            if($form_type == "updateDocumnet"):
                $empDetails = (!empty($data['empDetails']))?$data['empDetails']:array(); 
				unset($data['empDetails']);
               $result = $this->edit($this->empDetail,['emp_id' => $data['id']],$empDetails,'Employee');
            endif;

            if($form_type == "empExperience"):
                $result = $this->store($this->emp_experience,$data,'Employee Experience');
            endif;

            if($form_type == "empNomination"):
                $result = $this->store($this->empNom,$data,'Employee Nomination');
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function removeProfileDetails($data){ 
        try{
            $this->db->trans_begin();

            if($data['form_type'] == "empNomination"):
                $result = $this->trash($this->empNom,['id'=>$data['id']],"Employee Nomination");
            endif;

            if($data['form_type'] == "empExperience"):
                $result = $this->trash($this->emp_experience,['id'=>$data['id']],"Employee Experience");
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getEmpExperience($data){
        $queryData['tableName'] = $this->emp_experience;
        $queryData['where']['emp_id']=$data['emp_id'];		
        return $this->rows($queryData);
    }

    public function getNominationData($data){
		$queryData['where']['emp_id'] = $data['emp_id'];
		$queryData['tableName'] = $this->empNom;
		return $this->rows($queryData);
	}
  
    public function changeAppStatus($postData){
        try{
            $this->db->trans_begin();
			
			if(!empty($postData['id'])):
				$empData = $this->getEmployee(['id' => $postData['id']]);
				$empStatus = (!empty($empData->status) ? $empData->status : 0);				
				$this->interviewLogs(['log_type'=>$postData['status'], 'from_stage'=> $empStatus, 'emp_id'=>$postData['id'], 'reason' => $postData['reason'], 'notes'=>$this->interviewType[$empStatus]]); 
			endif;
			unset($postData['reason']);
            $result = $this->store($this->empMaster,$postData,'');
			
            $result['message'] = "Employee ".(($postData['status'] == 7)?"Rejected":"Approved")." successfully.";
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	public function interviewLogs($data){
		try{
            $this->db->trans_begin();
			
			if(!empty($data)):
				$logData = [
					'id' => "",
					'log_type' => $data['log_type'],
					'from_stage' => $data['from_stage'],
					'emp_id' => $data['emp_id'],
					'ref_date' => date("Y-m-d"),
					'notes' => $data['notes'],
					'reason' => (!empty($data['reason']) ? $data['reason'] : NULL),
					'created_by' => $this->loginId,
				];
				$result =  $this->store($this->interviewLogs,$logData,'Interview Logs');
			endif;
			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

    public function saveStaffSkill($data = array()){
		try{
            $this->db->trans_begin();
			
			if(!empty($data['current_skill'])){
				$i = 0;               
				foreach($data['current_skill'] as $row){
					$staffSkillData = $this->skillMaster->getStaffSkillData(['emp_id'=>$data['emp_id'],'skill_id'=>$data['skill_id'][$i],'single_row'=>1]);
					if(isset($row)){
						$storeData = [
							'id' => $data['id'][$i],
							'skill_id' => $data['skill_id'][$i],
							'emp_id' => $data['emp_id'],
							'prev_skill' => $row,
							'prev_month' => $data['cmonth']
						];
						if(empty($data['id'][$i])){ 
                            $storeData['created_by'] = $this->loginId;
                            $storeData['created_at'] = date('Y-m-d H:i:s'); 
                        }
						else{ 
                            $storeData['updated_by'] = $this->loginId;
                            $storeData['updated_at'] = date('Y-m-d H:i:s'); 
                        }
                        
                        $storeData[$data['cmonth']] = $row; 
						$this->store($this->staffSkill,$storeData,'Staff Skill');

                        /* update prev data */
                        if(!empty($staffSkillData)){
                            $cmonth = intval(substr($data['cmonth'], 1));
                            $prev_month = intval(substr($staffSkillData->prev_month, 1));

                            for ($x = $prev_month; $x < $cmonth; $x++) {
                                $month_key = 'm' . $x;

                                if (empty($staffSkillData->{$month_key})) {
                                    $updateData[$month_key] = $staffSkillData->prev_skill;
                                    $this->edit($this->staffSkill,['id'=>$staffSkillData->id],$updateData);
                                } 
                            }
                        }
                       
					}
					$i++;
				}
			}
			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1, 'message' => 'Staff Skill saved Successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}
	
    public function saveAppointedForm($data = array()){
		try{
            $this->db->trans_begin();
			
			$result = $this->store($this->empMaster,$data,'Appointed Interview');
			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function saveRejectEmployee($data = array()){
		try{
            $this->db->trans_begin();
			
			if(!empty($data['id'])):
				$empData = $this->getEmployee(['id' => $data['id']]);
				$empStatus = (!empty($empData->status) ? $empData->status : 0);
				$result = $this->store($this->empDetail,['id' => $empData->emp_detail_id, 'rejected_reason' => $data['reason']],'Employee Rejected');
				$result = $this->store($this->empMaster,['id' => $data['id'], 'status' => 7],'Employee Rejected');
				$this->interviewLogs(['log_type'=>7, 'from_stage'=>$empStatus, 'emp_id'=>$result['id'], 'reason' => $data['reason'], 'notes'=>$this->interviewType[7]]);
			endif;
			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getEmpLogs($data = array()){
		$queryData['tableName'] = $this->interviewLogs;
        $queryData['select'] = 'interview_logs.*, employee_master.emp_name,emp_type.emp_name as employee_name,emp_detail.aadhar_no,emp_detail.pan_no,emp_detail.aadhar_file,emp_detail.pan_file';
		$queryData['leftJoin']['employee_master'] = "employee_master.id = interview_logs.emp_id";
		$queryData['leftJoin']['emp_detail'] = "emp_detail.emp_id = employee_master.id";
		$queryData['leftJoin']['employee_master emp_type'] = "emp_type.id = interview_logs.created_by";
        if(!empty($data['emp_id'])){ $queryData['where']['interview_logs.emp_id']=$data['emp_id']; }
        return $this->rows($queryData);
	}

    /* Start Vacancy */
    public function getVacancyDTRows($data){ 		
        $data['tableName'] = $this->empVacancy;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "emp_vacancy.set_name";
        $data['searchCol'][] = "emp_vacancy.vacancy_no";
        $data['searchCol'][] = "emp_vacancy.notes";
        $data['searchCol'][] = "emp_vacancy.publish_to";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getVacancyData($data){
        $queryData['tableName'] = $this->empVacancy;
        if(!empty($data['id'])){
            $queryData['where']['emp_vacancy.id'] = $data['id'];
        }
        return $this->row($queryData);
    }

    public function saveVacancy($data){
		try {
            $this->db->trans_begin();
            
            $result = $this->store($this->empVacancy,$data,'Vacancy');
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        }catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }        
    }

    public function deleteVacancy($data){
		try{
            $this->db->trans_begin();

            $result = $this->trash($this->empVacancy,['id'=>$data['id']],'Vacancy');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
    /* End Vacancy */

    // Recruitment Report Data 
    public function getRecruitmentData($data = array()){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id, employee_master.emp_contact, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as dsg_title, employee_master.status,
		(select interview_logs.ref_date from interview_logs where interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 2) as new_ref_date,
		(select interview_logs.ref_date from interview_logs where interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 3) as doc_ref_date,
		(select interview_logs.ref_date from interview_logs where interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 4) as tech_ref_date,
		(select interview_logs.ref_date from interview_logs where interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 5) as hr_ref_date,
		(select interview_logs.ref_date from interview_logs where interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 6) as appointed_ref_date,
		(select interview_logs.ref_date from interview_logs where interview_logs.emp_id = employee_master.id AND interview_logs.log_type = 7) as rej_ref_date";

        $queryData['leftJoin']['department_master'] = "department_master.id = employee_master.dept_id";
        $queryData['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.designation_id";
		
		$queryData['where']['employee_master.emp_role !='] = "-1";
		
		if($data['type'] == ""):
			$queryData['customWhere'][] = "employee_master.status BETWEEN 1 AND 8";
		elseif($data['type'] == 0):
			$queryData['where']['employee_master.status != '] = 1;
		elseif($data['type'] == 1):
			$queryData['where']['employee_master.status'] = 1;
		else:
			$queryData['where']['employee_master.status'] = 7;
		endif;
		
        $queryData['order_by']['employee_master.id'] = "DESC";

        return $this->rows($queryData);
    }

    
    /********** Sales Target ************/
	public function saveTargets($data){
		try{
			$this->db->trans_begin();
			
			foreach($data['id'] as $key=>$id){
				$targetData = [
                        'id'=>$id,
                        'emp_id'=>$data['emp_id'][$key],
                        'target_month'=>$data['month'],
                        'new_lead'=>$data['new_lead'][$key],
                        'new_visit'=>$data['new_visit'][$key],
                        'sales_amount'=>$data['sales_amount'][$key],
                    ];
				$result = $this->store('executive_targets',$targetData,'Target');
			}
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
			
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}
	}
		
	public function getSalesTargetDetails($data=[]){
		$queryData = [];
		$queryData['tableName'] = $this->empMaster;

		$queryData['select'] = "employee_master.*,executive_targets.id as target_id,executive_targets.new_lead,executive_targets.sales_amount,executive_targets.new_visit";

		$queryData['leftJoin']['executive_targets'] = "executive_targets.emp_id = employee_master.id AND executive_targets.target_month = '".$data['month']."' "; 
		$queryData['where']['employee_master.is_active'] = 1;
		
		if(empty($data['all'])):
            $queryData['where']['employee_master.emp_role !='] = "-1";
        endif;

		$queryData['group_by'][]='employee_master.id';
        return $this->rows($queryData);
		return $result;
	}

	/********** End Sales Target *********/
}
?>