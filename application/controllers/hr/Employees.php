<?php
class Employees extends MY_Controller{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";
    private $profile = "hr/employee/emp_profile";

   
	private $recruitmentIndex = "hr/employee/recruitment_index";
    private $docVerifyForm = "hr/employee/document_form";
    private $skillForm = "hr/employee/skill_form";
    private $appointedForm = "hr/employee/appointed_form";
	private $reasonForm = "hr/employee/reason_form";
	private $logDetails = "hr/employee/log_details";
    private $improvementForm = "hr/employee/improvement_form";


    public function __construct(){
		parent::__construct();
		// $this->data['headData']->pageTitle = "User Master";
		$this->data['headData']->controller = "hr/employees";   
        $this->data['headData']->pageUrl = "hr/employees";
	}

    public function index(){        
		$this->data['headData']->pageTitle = "User Master";
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->employee->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++; 
			$row->emp_role = $this->empRole[$row->emp_role];
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['roleList'] = $this->empRole;
        $this->data['genderList'] = $this->gender;
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList(); 
        // $this->data['emp_no'] = $this->employee->getNextEmpNo();
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        
        if($data['status'] == 1){
            if(empty($data['emp_code']))
                $errorMessage['emp_code'] = "Emp. Code is required.";
        }
       
        if(empty($data['emp_role']))
            $errorMessage['emp_role'] = "Role is required.";
        if(empty($data['emp_contact']))
            $errorMessage['emp_contact'] = "Contact No. is required.";
        if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department is required.";
        if(empty($data['designation_id'])):
            if(empty($data['designationTitle'])):
                $errorMessage['designation_id'] = "Designation is required.";
            else:
                $designation = $this->designation->save(['id'=>'','title'=>$data['designationTitle']]);
                if($designation['status'] != 1):
                    $errorMessage['designation_id'] = "Please Select Valid Designation.";
                else:
                    $data['designation_id'] = $designation['id'];
                endif;                
            endif;
        endif;
        unset($data['designationTitle']);
        if(empty($data['id'])):
            $data['emp_password'] = "123456";
        endif;

        if(!empty($_FILES['sign_image']['name'])):
            $attachment = "";
            $this->load->library('upload');
            
            $_FILES['userfile']['name']     = $_FILES['sign_image']['name'];
            $_FILES['userfile']['type']     = $_FILES['sign_image']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['sign_image']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['sign_image']['error'];
            $_FILES['userfile']['size']     = $_FILES['sign_image']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/signature/');

            $fileName = 'sign_'.$data['emp_code']."_".$this->cm_id;
            $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);

            if(!$this->upload->do_upload()):
                $errorMessage['sign_image'] = $fileName . " => " . $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $data['sign_image'] = $uploadData['file_name'];
            endif;

            if(!empty($errorMessage['sign_image'])):
                if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
            endif;            
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:          
            $data['emp_name'] = ucwords($data['emp_name']);
            if(!empty($data['process_id'])){ $data['process_id'] = implode(',',$data['process_id']); }
            $this->printJson($this->employee->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['status'] = $data['status']; 
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['roleList'] = $this->empRole;
        $this->data['genderList'] = $this->gender;
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList(); 
        $this->data['shiftList'] = $this->shiftModel->getShiftList();
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['dataRow'] = $this->employee->getEmployee($data);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->employeeForm,$this->data);
    }

	public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($data));
        endif;
    }
	
    public function activeInactive(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->activeInactive($postData));
        endif;
    }
    
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['id'] = $this->loginId;
			$result =  $this->employee->changePassword($data);
			$this->printJson($result);
		endif;
    }

    public function resetPassword(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->resetPassword($data['id']));
        endif;
    }

    public function empProfile($emp_id){
        $this->data['empData'] = $this->employee->getEmployee(['id'=>$emp_id]);
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList();
        $this->data['shiftList'] = $this->shiftModel->getShiftList();
        $this->data['empExp'] = $this->employee->getEmpExperience(['emp_id'=>$emp_id]);
		$this->data['empNom'] = $this->employee->getNominationData(['emp_id'=>$emp_id]);
        $this->data['roleList'] = $this->empRole;
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->profile,$this->data);        
    }

    public function editProfile(){
        $data = $this->input->post(); 
        $errorMessage = array();  

        if($data['form_type'] == "updateProfilePic"):
            if($_FILES['emp_profile']['name'] != null || !empty($_FILES['emp_profile']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['emp_profile']['name'];
                $_FILES['userfile']['type']     = $_FILES['emp_profile']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['emp_profile']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['emp_profile']['error'];
                $_FILES['userfile']['size']     = $_FILES['emp_profile']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/emp_profile/');
                $ext = pathinfo($_FILES['emp_profile']['name'], PATHINFO_EXTENSION);
				$file_name = time() . "." . pathinfo($_FILES['emp_profile']['name'], PATHINFO_EXTENSION);
                $config = ['file_name' => $file_name,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];

                if(!empty($data['old_profile']) && file_exists($config['upload_path'].'/'.$data['old_profile'])) unlink($config['upload_path'].'/'.$data['old_profile']);

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['newProfilePhoto'] = $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
					unset($data['old_profile']);
                    $data['emp_profile'] = $uploadData['file_name'];
                endif;
            else:
                $this->printJson(['status'=>0,'message'=>"Image not found."]);exit;
            endif;
        endif;
		
		if($data['form_type'] == 'personalDetails'):
			if(empty($data['emp_name']))
				$errorMessage['emp_name'] = "Employee name is required.";
			if(empty($data['emp_contact']))
				$errorMessage['emp_contact'] = "Contact No. is required.";			
			
			if(empty($data['id'])):
				$data['emp_password'] = "123456";
			endif;

             if(!empty($_FILES['aadhar_file']['name'])):
                $attachment = "";
                $this->load->library('upload');
                
                $_FILES['userfile']['name']     = $_FILES['aadhar_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['aadhar_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['aadhar_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['aadhar_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['aadhar_file']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');

                $fileName = 'aadhar_'.$data['emp_code']."_".$this->cm_id;
                $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);

                if(!$this->upload->do_upload()):
                    $errorMessage['aadhar_file'] = $fileName . " => " . $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $data['empDetails']['aadhar_file'] = $uploadData['file_name'];
                endif;

                if(!empty($errorMessage['aadhar_file'])):
                    if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
                endif;            
            endif;

            if(!empty($_FILES['pan_file']['name'])):
                $attachment = "";
                $this->load->library('upload');
                
                $_FILES['userfile']['name']     = $_FILES['pan_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['pan_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['pan_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['pan_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['pan_file']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');

                $fileName = 'pan_'.$data['emp_code']."_".$this->cm_id;
                $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);

                if(!$this->upload->do_upload()):
                    $errorMessage['pan_file'] = $fileName . " => " . $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $data['empDetails']['pan_file'] = $uploadData['file_name'];
                endif;

                if(!empty($errorMessage['pan_file'])):
                    if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
                endif;            
            endif;
		endif;
		
		if($data['form_type'] == 'workProfile'):
            if(empty($data['designation_id']))
                $errorMessage['designation_id'] = "Designation is required.";
            if(empty($data['dept_id']))
				$errorMessage['dept_id'] = "Department is required.";
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->editProfile($data));
        endif;
    }
	
	public function saveEmpForm(){
		$data = $this->input->post();
		
		if($data['form_type'] == "empExperience"):
			if(empty($data['company_name']))
                $errorMessage['company_name'] = "Company Name is required.";
            if(empty($data['designation']))
                $errorMessage['designation'] = "Designation is required.";
		endif;
		
		if($data['form_type'] == "empNomination"):
			if(empty($data['nom_name']))
                $errorMessage['nom_name'] = "Name is required.";
            if(empty($data['nom_relation']))
                $errorMessage['nom_relation'] = "Relation is required.";
		endif;
		
	
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$msg = $this->employee->editProfile($data);
			if($data['form_type'] == "empExperience"):
				$tbodyData = $this->getEmpExperienceHtml(['emp_id' => $data['emp_id']]);
			elseif($data['form_type'] == "empNomination"):
				$tbodyData = $this->getEmpNominationsHtml(['emp_id' => $data['emp_id']]);
			
			endif;
			
            $this->printJson(['status'=>1,'message'=>$msg['message'],"tbodyData"=>$tbodyData,"form_type"=>$data['form_type']]);
        endif;
	}


    public function getEmpExperienceHtml($data){
        $docData = $this->employee->getEmpExperience(['emp_id'=>$data['emp_id']]);

        $tbodyData="";$i=1; 
        if(!empty($docData)):
            $i=1;
            foreach($docData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id' : ".$row->emp_id.",'form_type' : 'empExperience'}, 'fndelete' : 'deleteEmpForm','message' : 'Employee'}";
                $tbodyData.= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.$row->company_name.'</td>
                    <td class="text-center">'.$row->designation.'</td>
                    <td class="text-center">'.$row->period_service.'</td>
                    
                    <td class="text-center">
                        <button type="button" onclick="trashEmpProfile('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="5" class="text-center">No data available in table</td></tr>';
        endif;

        return $tbodyData;
    }

    public function getEmpNominationsHtml(){
        $data = $this->input->post();
        $empNom = $this->employee->getNominationData(['emp_id'=>$data['emp_id']]);

        $tbodyData="";$i=1; 
        if(!empty($empNom)):
            $i=1;
            foreach($empNom as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id' : ".$row->emp_id.",'form_type' : 'empNomination'}, 'fndelete' : 'deleteEmpForm','message' : 'Employee'}";
                $tbodyData.= '<tr>
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
            $tbodyData.= '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
        endif;
		
        return $tbodyData;
    }
	
	public function deleteEmpForm(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$msg = $this->employee->removeProfileDetails($data);
			if($data['form_type'] == "empExperience"):
				$tbodyData = $this->getEmpExperienceHtml(['emp_id' => $data['emp_id']]);
			elseif($data['form_type'] == "empNomination"):
				$tbodyData = $this->getEmpNominationsHtml(['emp_id' => $data['emp_id']]);
			elseif($data['form_type'] == "empFacility"):
				$tbodyData = $this->getEmpFacilityHtml(['emp_id' => $data['emp_id']]);
			endif;
			
			$this->printJson(['status'=>1,'message'=>$msg['message'],"tbodyData"=>$tbodyData,"form_type"=>$data['form_type']]);
        endif;
    }

    /*created By @Raj 14-10-2024*/
	public function newApplication(){
		$this->data['headData']->pageTitle = "New Application";
        $this->data['tableHeader'] = getHrDtHeader('recruit');
		$this->data['is_status'] = 2;
		$this->data['is_approve'] = 3;
		$this->data['heading'] = "New Application";
        $this->load->view($this->recruitmentIndex,$this->data);
    }
	
	public function docVerify(){
		$this->data['headData']->pageTitle = "Document Verification";
		$this->data['tableHeader'] = getHrDtHeader('recruit');
		$this->data['is_status'] = 3;
		$this->data['is_approve'] = 4;
		$this->data['heading'] = "Document Verification";
        $this->load->view($this->recruitmentIndex,$this->data);
	}
	
	public function skillVerification(){
		$this->data['headData']->pageTitle = "Skill Verification";
        $this->data['tableHeader'] = getHrDtHeader('recruit');
		$this->data['is_status'] = 4;
		$this->data['is_approve'] = 6;
		$this->data['heading'] = "Skill Verification";
        $this->load->view($this->recruitmentIndex,$this->data);
	}
	
	public function appointed(){
		$this->data['headData']->pageTitle = "Appointed";
        $this->data['tableHeader'] = getHrDtHeader('recruit');
		$this->data['is_status'] = 6;
		$this->data['is_approve'] = 1;
		$this->data['heading'] = "Appointed";
        $this->load->view($this->recruitmentIndex,$this->data);
	}
	
	public function rejApplication(){
		$this->data['headData']->pageTitle = "Rejected";
        $this->data['tableHeader'] = getHrDtHeader('recruitRej');
		$this->data['is_status'] = 7;
		$this->data['is_approve'] = 8;
		$this->data['heading'] = "Rejected";
        $this->load->view($this->recruitmentIndex,$this->data);
	}

    public function getRecDTRows($status=2,$is_approve=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->employee->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
		
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->is_approve = $is_approve;
			
			if($status == 7){
				$row->from_stage = !empty($row->from_stage) ? $this->rejType[$row->from_stage] :"";  
			}
			
			$sendData[] = getRecruitmentData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addApplication(){
		$data = $this->input->post();
		$this->data['status'] = $data['status']; 
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['genderList'] = $this->gender;
        $this->data['roleList'] = $this->empRole;
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList();
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->employeeForm,$this->data); 
    }

    public function changeAppStatus(){
        $data = $this->input->post();
		
		if(empty($data['reason']))
            $errorMessage['reason'] = "Reason is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->changeAppStatus($data));
        endif;
    }

	public function uploadDocument(){
		$data = $this->input->post();
        $this->data['dataRow'] = $this->employee->getEmployee(['id'=>$data['id']]);
        $this->load->view($this->docVerifyForm,$this->data);
	}
	
	public function saveDocForm(){
		$data = $this->input->post();
		if($data['form_type'] == "updateDocumnet"):
            if(!empty($_FILES['aadhar_file']['name'])):
                $attachment = "";
                $this->load->library('upload');
                
                $_FILES['userfile']['name']     = $_FILES['aadhar_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['aadhar_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['aadhar_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['aadhar_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['aadhar_file']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');

                $fileName = 'aadhar_'.$data['emp_code']."_".$this->cm_id;
                $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);

                if(!$this->upload->do_upload()):
                    $errorMessage['aadhar_file'] = $fileName . " => " . $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $data['empDetails']['aadhar_file'] = $uploadData['file_name'];
                endif;

                if(!empty($errorMessage['aadhar_file'])):
                    if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
                endif;            
            endif;

            if(!empty($_FILES['pan_file']['name'])):
                $attachment = "";
                $this->load->library('upload');
                
                $_FILES['userfile']['name']     = $_FILES['pan_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['pan_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['pan_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['pan_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['pan_file']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');

                $fileName = 'pan_'.$data['emp_code']."_".$this->cm_id;
                $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);

                if(!$this->upload->do_upload()):
                    $errorMessage['pan_file'] = $fileName . " => " . $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $data['empDetails']['pan_file'] = $uploadData['file_name'];
                endif;

                if(!empty($errorMessage['pan_file'])):
                    if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
                endif;            
            endif;
         
		endif;
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
           $this->printJson($this->employee->editProfile($data));
        endif;
	}

	public function addStaffSkill(){
		$data = $this->input->post();
		$this->data['emp_id'] = $data['id'];
		$this->data['skillList'] = $this->skillMaster->getSkillList(['emp_id'=> $data['id'],'dept_id'=>$data['dept_id'], 'designation_id'=>$data['designation_id'],'oldData'=>1]);
        $this->load->view($this->skillForm,$this->data);
	}
	
	public function saveStaffSkill(){
		$data = $this->input->post();
		
		if(empty($data['skill_id']))
			$errorMessage['general'] = "Skill Set is required.";
		
        $i=0;
        foreach($data['current_skill'] as $key=>$value){
            if(isset($data['current_skill'][$key]) && ($data['current_skill'][$key] == '')){
                $errorMessage['current_skill'.$i] = "Current Skill is required.";
            }
            
            if($value > 100 || $value < 0){
                $errorMessage['current_skill'.$i] = "Invalid Percentage.";
            }
            $i++;
        }
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['cmonth'] = (!empty($data['cmonth']) ? $data['cmonth'] : 'm'.intval(date('m')));  
            $this->printJson($this->employee->saveStaffSkill($data));
        endif;
	}	
	
	public function appointedForm(){
		$data = $this->input->post();
        $this->data['dataRow'] = $this->employee->getEmployee($data);
        $this->load->view($this->appointedForm,$this->data);
	}
	
	public function saveAppointedForm(){
		$data = $this->input->post();
		if(empty($data['emp_joining_date']))
			$errorMessage['emp_joining_date'] = "Joining Date is required.";
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveAppointedForm($data));
        endif;
	}

	public function rejectEmployee(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->employee->getEmployee($data);
		$this->data['status'] = 7;
        $this->load->view($this->reasonForm,$this->data);
	}
	
	public function saveRejectEmployee(){
		$data = $this->input->post();
		
		if(empty($data['reason']))
			$errorMessage['reason'] = "Reason is required.";
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveRejectEmployee($data));
        endif;
	}
	
	public function approveEmployee(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->employee->getEmployee($data);
		$this->data['status'] = $data['status'];
        $this->load->view($this->reasonForm,$this->data);
	}
	
	public function printLogs(){
		$data = $this->input->post();
		$this->data['getEmpLog'] = $this->employee->getEmpLogs(['emp_id'=>$data['id']]);
		$this->data['skillList'] = $this->skillMaster->getStaffSkillData(['emp_id'=>$data['id']]); 
		$this->load->view($this->logDetails,$this->data);
	}

	public function printOfferLetter($id){
		$this->data['empData'] = $empData = $this->employee->getEmployee(['id'=>$id]);
		$this->data['companyData'] = $this->empLoan->getCompanyInfo();
		
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$pdfData = $this->load->view('hr/employee/print_offer_letter',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"></td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>';
		
		$pdfData = $htmlHeader.$pdfData;
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName= ''.$empData->emp_name.'_Employment_Offer.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle($empData->emp_name.'_Employment_Offer');
		//$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    /* Start Vacancy */
    public function vacancy(){
		$this->data['headData']->pageTitle = "Vacancy";
        $this->data['tableHeader'] = getHrDtHeader('vacancy');
        $this->load->view('hr/employee/vacancy_index',$this->data);
    }

    public function getVacancyDTRows(){
        $data = $this->input->post(); 
        $result = $this->employee->getVacancyDTRows($data);	
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $sendData[] = getVacancyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addVacancy(){
        $this->data['skillSetList'] = $this->skillMaster->getSkillSet(['group_by'=>'set_name']);
        $this->load->view('hr/employee/vacancy_form',$this->data);
    }

    public function saveVacancy(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['set_name']))
			$errorMessage['set_name'] = "Set Name is required.";
        if(empty($data['vacancy_no']))
			$errorMessage['vacancy_no'] = "Vacancy No is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveVacancy($data));
        endif;
    }

    public function editVacancy(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->employee->getVacancyData($data);
        $this->data['skillSetList'] = $this->skillMaster->getSkillSet(['group_by'=>'set_name']);
        $this->load->view('hr/employee/vacancy_form',$this->data);
    }

    public function deleteVacancy(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->deleteVacancy($data));
        endif;
    }
    /* End Vacancy */

    /* Start Skill Improvement */
    public function addSkillImprovement(){
		$data = $this->input->post();
		$this->data['emp_id'] = $data['id'];
        $this->data['skillList'] = $this->skillMaster->getSkillList(['emp_id'=>$data['id'],'dept_id'=>$data['dept_id'], 'designation_id'=>$data['designation_id'],'oldData'=>1]);
        $this->data['monthList'] = $this->getMonthListFY();
        $this->load->view($this->improvementForm,$this->data);
    }
    /* End Skill Improvement */
}
?>