<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getHrDtHeader($page){
    /* Department Header */
    $data['departments'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['departments'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['departments'][] = ["name"=>"Department Name"];
    $data['departments'][] = ["name"=>"Remark"];

    /* Designation Header */
    $data['designation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['designation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['designation'][] = ["name"=>"Designation Name"];
    $data['designation'][] = ["name"=>"Remark"];

    /* Employee Category Header */
    $data['employeeCategory'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['employeeCategory'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['employeeCategory'][] = ["name"=>"Category Name"];
    $data['employeeCategory'][] = ["name"=>"Over Time"];

    /* Employee Header */
    $data['employees'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['employees'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['employees'][] = ["name"=>"Employee Name"];
    $data['employees'][] = ["name"=>"Emp Code","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Department"];
    $data['employees'][] = ["name"=>"Designation"];
    $data['employees'][] = ["name"=>"Category","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Contact No.","textAlign"=>'center'];

    /* Employee Loan Header */
    $data['empLoan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['empLoan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['empLoan'][] = ["name"=>"Sanction No."];
    $data['empLoan'][] = ["name"=>"Sanction Date"];
    $data['empLoan'][] = ["name"=>"Employee Name"];
    $data['empLoan'][] = ["name"=>"Amount"];
    $data['empLoan'][] = ["name"=>"reason"];
   
    /* Shift Header */
	$data['shift'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['shift'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Time"];
	$data['shift'][] = ["name"=>"Shift Hour"];

    /* Recruitment Header */
    $data['recruit'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['recruit'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['recruit'][] = ["name"=>"Employee Name"];
    $data['recruit'][] = ["name"=>"Contact No.","textAlign"=>'center'];
	$data['recruit'][] = ["name"=>"Department"];
    $data['recruit'][] = ["name"=>"Designation"];
    $data['recruit'][] = ["name"=>"Source"];
    $data['recruit'][] = ["name"=>"Reference"];
	$data['recruit'][] = ["name"=>"Joining Date"];
	
	/* Recruitment Reject Header */
    $data['recruitRej'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['recruitRej'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['recruitRej'][] = ["name"=>"Employee Name"];
    $data['recruitRej'][] = ["name"=>"Contact No.","textAlign"=>'center'];
	$data['recruitRej'][] = ["name"=>"Department"];
    $data['recruitRej'][] = ["name"=>"Designation"];
    $data['recruitRej'][] = ["name"=>"Source"];
    $data['recruitRej'][] = ["name"=>"Reference"];
    $data['recruitRej'][] = ["name"=>"Reject Stage"];
    $data['recruitRej'][] = ["name"=>"Reject By"];

    /* Vacancy Header */
	$data['vacancy'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['vacancy'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['vacancy'][] = ["name"=>"Set Name"];
	$data['vacancy'][] = ["name"=>"Vacancy No"];
	$data['vacancy'][] = ["name"=>"Notes"];
	$data['vacancy'][] = ["name"=>"Publish To"];
    	
    /* Skill Master Header */
	$data['skillMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['skillMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['skillMaster'][] = ["name"=>"Skill Name"];
	$data['skillMaster'][] = ["name"=>"Department"];
	$data['skillMaster'][] = ["name"=>"Designation"];
	$data['skillMaster'][] = ["name"=>"Req. Skill"];

    /* Meeting Header */
	$data['meeting'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['meeting'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['meeting'][] = ["name"=>"Meeting Date"];
    $data['meeting'][] = ["name"=>"Duration"];
    $data['meeting'][] = ["name"=>"Title"];
    $data['meeting'][] = ["name"=>"Location"];
    $data['meeting'][] = ["name"=>"Host By"];
    $data['meeting'][] = ["name"=>"Guest"];
    $data['meeting'][] = ["name"=>"Key Contact"];

    /* Training Docs Header */
	$data['trainingDocs'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['trainingDocs'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['trainingDocs'][] = ["name"=>"Department"];
    $data['trainingDocs'][] = ["name"=>"Designation"];
    $data['trainingDocs'][] = ["name"=>"Description"];

    /* Training Header */
    $data['training'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['training'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['training'][] = ["name"=>"Training Start Date"];
    $data['training'][] = ["name"=>"Training End Date"];
    $data['training'][] = ["name"=>"Purpose"];
    $data['training'][] = ["name"=>"Training Type"];
    $data['training'][] = ["name"=>"Skill"];
    $data['training'][] = ["name"=>"Trainer Name"];
	
    /* Kpi Header */
    $data['kpiChecklist'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['kpiChecklist'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['kpiChecklist'][] = ["name"=>"Department"];
    $data['kpiChecklist'][] = ["name"=>"Designation"];
    $data['kpiChecklist'][] = ["name"=>"Weightage"];
	
    /* Employee Perfomance Header */
    $data['empPerfomance'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['empPerfomance'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['empPerfomance'][] = ["name"=>"Month"];
    $data['empPerfomance'][] = ["name"=>"Employee Code"];
    $data['empPerfomance'][] = ["name"=>"Employee Name"];
    $data['empPerfomance'][] = ["name"=>"Department"];
    $data['empPerfomance'][] = ["name"=>"Designation"]; 
    $data['empPerfomance'][] = ["name"=>"Created By/At"];
    $data['empPerfomance'][] = ["name"=>"Approved By/At"];

    /* Employee Skill Header */
    $data['empSkill'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['empSkill'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['empSkill'][] = ["name"=>"Month"];
    $data['empSkill'][] = ["name"=>"Employee Code"];
    $data['empSkill'][] = ["name"=>"Employee Name"];
    $data['empSkill'][] = ["name"=>"Department"];
    $data['empSkill'][] = ["name"=>"Designation"];
    $data['empSkill'][] = ["name"=>"Created By/At"];
    $data['empSkill'][] = ["name"=>"Approved By/At"];

    /* Kpi Master Header */
    $data['kpiMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['kpiMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['kpiMaster'][] = ["name"=>"KPI Type"];

    return tableHeader($data[$page]);
}

/* Department Table Data */
function getDepartmentData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Department'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editDepartment', 'title' : 'Update Department'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->description];
}

/* Designation Table Data */
function getDesignationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Designation'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editDesignation', 'title' : 'Update Designation','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->description];
}

/* Employee Category Table Data */
function getEmployeeCategoryData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee Category'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editEmployeeCategory', 'title' : 'Update Employee Category','call_function':'edit'}";


    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->category,$data->overtime];
}

/* Employee Table Data */
function getEmployeeData($data){
	$deleteParam = "{'postData':{'id' : ".$data->id.",'aadhar_file':'".$data->aadhar_file."','pan_file':'".$data->pan_file."'},'message' : 'Employee'}";
    $editParam = "{'postData':{'id' : ".$data->id.",'status':'1'},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editEmployee', 'title' : 'Update Employee','call_function':'edit'}";
    
    $activeButton = '';$editButton = '';$deleteButton = '';$improveButton ='';

    if($data->is_active == 1):
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 0},'fnsave':'activeInactive','message':'Are you sure want to De-Active this Employee?'}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-ban"></i></a>';    

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
       
		//$improveParam = "{'postData':{'id' : ".$data->id.",'dept_id' : ".$data->dept_id.",'designation_id' : ".$data->designation_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'skillImprovement', 'title' : 'Add Skill Improvement','call_function':'addSkillImprovement', 'button' : 'both','fnsave':'saveStaffSkill'}";
		//$improveButton = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Skill Improvement" flow="down" onclick="modalAction('.$improveParam.');"><i class="fa fa-plus"></i></a>';
	   
        $empName = $data->emp_name;
    else:
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 1},'fnsave':'activeInactive','message':'Are you sure want to Active this Employee?'}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-check"></i></a>';  
          
        $empName = $data->emp_name;
    endif;
    
    $CI = & get_instance();
    $userRole = $CI->session->userdata('role');

    $resetPsw='';
    if(in_array($userRole,[-1,1])):
        $resetParam = "{'postData':{'id' : ".$data->id."},'fnsave':'resetPassword','message':'Are you sure want to Change ".$data->emp_name." Password?'}";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="confirmStore('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    $empName = '<a href="'.base_url('hr/employees/empProfile/'.$data->id).'" datatip="View Profile" flow="down">'.$data->emp_name.'</a>';

    $action = getActionButton($improveButton.$resetPsw.$activeButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$empName,$data->emp_code,$data->dept_name,$data->emp_designation,$data->emp_category,$data->emp_contact];
}

/* get Shift Data */
function getShiftData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Shift'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editShift', 'title' : 'Update Shift'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->shift_name,$data->shift_start,$data->shift_end,$data->production_hour,$data->total_lunch_time,$data->total_shift_time];
}

/* Recruitment Table Data */
function getRecruitmentData($data){ 
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee'}";
    $editParam = "{'postData':{'id' : ".$data->id.", 'status' : 2},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editEmployee', 'title' : 'Update Employee','call_function':'edit'}";
    
    $editButton = ''; $approveButton = ''; $rejectButton = ''; $approveDocButton = ''; $skillButton = ''; $activeButton = ''; $logButton = ''; $printBtn = '';
		
	if($data->status == 2){
		$approveParam = "{'postData':{'id' : ".$data->id.", 'status' : 3},'modal_id' : 'modal-md', 'form_id' : 'approveEmployee', 'title' : 'Approve Employee','call_function':'approveEmployee', 'fnsave' : 'changeAppStatus'}";
		$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    }
    
	if($data->status == 3){
		$approveParam = "{'postData':{'id' : ".$data->id.", 'status' : 4},'modal_id' : 'modal-md', 'form_id' : 'approveEmployee', 'title' : 'Approve Employee','call_function':'approveEmployee', 'fnsave' : 'changeAppStatus'}";
		$approveDocParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-lg', 'form_id' : 'editEmpDocs', 'title' : 'Add Document Verification','call_function':'uploadDocument', 'button' : 'both','fnsave':'saveDocForm'}";
		$approveDocButton = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Document Upload" flow="down" onclick="modalAction('.$approveDocParam.');"><i class="fa fa-plus"></i></a>';
	}
	
	if($data->status == 4){
		$approveParam = "{'postData':{'id' : ".$data->id.", 'status' : 6},'modal_id' : 'modal-md', 'form_id' : 'approveEmployee', 'title' : 'Approve Employee','call_function':'approveEmployee', 'fnsave' : 'changeAppStatus'}";
		$skillParam = "{'postData':{'id' : ".$data->id.",'dept_id' : ".$data->dept_id.",'designation_id' : ".$data->designation_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addSkill', 'title' : 'Add Staff Skill','call_function':'addStaffSkill','fnsave' : 'saveStaffSkill'}";
		$skillButton = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Staff Skill" flow="down" onclick="modalAction('.$skillParam.');"><i class="fa fa-plus"></i></a>';
	}
	
	if($data->status == 6){
		$activeParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'form_id' : 'activeEmp', 'title' : 'Active Employee','call_function':'appointedForm','fnsave' : 'saveAppointedForm'}";
		$activeButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Staff Skill" flow="down" onclick="modalAction('.$activeParam.');"><i class="fa fa-check"></i></a>';
		
		if(!empty($data->emp_joining_date)){
			$printBtn = '<a class="btn btn-primary btn-edit permission-modify" href="'.base_url('hr/employees/printOfferLetter/'.$data->id).'" target="_blank" datatip="Offer Letter" flow="down"><i class="mdi mdi-file-pdf" ></i></a>';
		}
	}
	
	if($data->status != 7){
		$rejectParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'form_id' : 'rejectEmployee', 'title' : 'Reject Employee','call_function':'rejectEmployee', 'fnsave' : 'saveRejectEmployee'}";
		$rejectButton = '<a class="btn btn-danger btn-edit permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="modalAction('.$rejectParam.');"><i class="fa fa-close"></i></a>';
		
		if(!in_array($data->status,[1,6,7])){
			if($data->status == 3):				
				if(!empty($data->aadhar_no) OR !empty($data->pan_no)):
					$approveButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="fa fa-check"></i></a>';
				endif;
			else:
				$approveButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="fa fa-check"></i></a>';
			endif;
		}
	}
	
	$logParam = "{'postData':{'id' : ".$data->id.", 'status' : 2},'modal_id' : 'modal-lg', 'form_id' : 'empLogs', 'title' : '".$data->emp_name." (".$data->dept_name." - ".$data->emp_designation.")','call_function':'printLogs', 'button' : 'close'}";
	$logButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Log Details" flow="down" onclick="modalAction('.$logParam.');"><i class="fa fa-info"></i></a>';
	
	$action = getActionButton($editButton.$logButton.$activeButton.$skillButton.$approveDocButton.$approveButton.$rejectButton.$printBtn);
	if(!empty($data->is_approve)){ $action = getActionButton(''); }
	
	if($data->status != 7){
		return [$action,$data->sr_no,$data->emp_name,$data->emp_contact,$data->dept_name,$data->emp_designation,$data->rec_source,$data->ref_by,formatDate($data->emp_joining_date)];
	}else{
		return [$action,$data->sr_no,$data->emp_name,$data->emp_contact,$data->dept_name,$data->emp_designation,$data->rec_source,$data->ref_by,$data->from_stage,$data->reject_name.'<br>'.formatDate($data->reject_at,'d-m-Y H:i')];
	}
}

/* get Vacancy Data */
function getVacancyData($data){
    $deleteParam = "{'postData':{'id' : '".$data->id."'},'message' : 'Vacancy','fndelete':'deleteVacancy'}";

    $editParam = "{'postData':{'id' : '".$data->id."'},'modal_id' : 'bs-right-md-modal', 'form_id' : 'addVacancy', 'title' : 'Update Vacancy' , 'fnedit':'editVacancy', 'fnsave':'saveVacancy', 'call_function' : 'editVacancy'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->set_name,$data->vacancy_no,$data->notes,$data->publish_to];
}

/* get Skill Master Data */
function getSkillData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Skill'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editSkill', 'title' : 'Update Skill'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = '<a class="btn btn-success btn-info" href="'.base_url('hr/skillMaster/printSkillSet/'.encodeURL($data->dept_id).'/'.encodeURL($data->designation_id)).'" target="_blank" datatip=" Print" flow="down"><i class="fas fa-print" ></i></a>';

	$action = getActionButton( $print.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->skill_name,$data->name,$data->title,$data->req_skill];
}

/* Meeting Table Data */
function getMeetingData($data){
    $editButton = $deleteButton = $cancelButton = $completeButton = $printBtn = "";

    if ($data->status == 0) {
        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editMeeting', 'title' : 'Update Meeting'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."}, 'message' : 'Meeting'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    
        $cancelParam = "{'postData':{'id' : ".$data->id.", 'status' : 2, 'msg' : 'Cancelled'}, 'message' : 'Are you sure want to Cancel this Meeting?', 'fnsave' : 'changeMeetStatus'}";
        $cancelButton = '<a href="javascript:void(0)" class="btn btn-dark permission-modify" onclick="confirmStore('.$cancelParam.');" datatip="Cancel" flow="down"><i class="fa fa-close"></i></a>';   

        $completeParam = "{'postData':{'id' : ".$data->id.", 'status' : 1}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'addAttendee', 'title' : 'Complete Meeting', 'call_function' : 'addAttendee', 'fnsave' : 'completeMeeting'}"; 
        $completeButton = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Complete" flow="down" onclick="modalAction('.$completeParam.');"><i class="fa fa-check"></i></a>';
    }

    if(($data->status == 1)){
		$printBtn = '<a class="btn btn-primary btn-edit permission-modify" href="'.base_url('hr/meeting/printMeeting/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="mdi mdi-file-pdf" ></i></a>';
	}

	$action = getActionButton($printBtn.$completeButton.$cancelButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,date('d-m-Y H:i:s',strtotime($data->me_date)),$data->duration,$data->title,$data->location,$data->host_by,$data->guest,$data->key_contact];    
}

/* Training Docs Table Data */
function getTrainingDocsData($data){
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editTrainingDocs', 'title' : 'Update Training Docs'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id.",'doc_file':'".$data->doc_file."'}, 'message' : 'Training Docs'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $download = (!empty($data->doc_file) ? '<a class="btn btn-info" href="'.base_url("assets/uploads/training_docs/".$data->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>' : ""); 

	$action = getActionButton($download.$editButton.$deleteButton); 
    return [$action,$data->sr_no,$data->name,$data->title,$data->description];    
}

/* Training Table Data */
function getTrainingData($data){
    $editButton = $deleteButton = $cancelButton = $completeButton = $printBtn = "";
    if($data->status == 0){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Training'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editTraining', 'title' : 'Update Training','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $cancelParam = "{'postData':{'id' : ".$data->id.", 'status' : 2, 'msg' : 'Cancelled'}, 'message' : 'Are you sure want to Cancel this Meeting?', 'fnsave' : 'changeTrainingStatus'}";
        $cancelButton = '<a href="javascript:void(0)" class="btn btn-dark permission-modify" onclick="confirmStore('.$cancelParam.');" datatip="Cancel" flow="down"><i class="fa fa-close"></i></a>';        
        
        $completeParam = "{'postData':{'id' : ".$data->id.", 'status' : 1}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'addEmpAttendance', 'title' : 'Complete Training', 'call_function' : 'addEmpAttendance', 'fnsave' : 'completeTraining'}"; 
        $completeButton = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Complete" flow="down" onclick="modalAction('.$completeParam.');"><i class="fa fa-check"></i></a>';
    }
    if(($data->status == 1)){
		$printBtn = '<a class="btn btn-primary btn-edit permission-modify" href="'.base_url('hr/training/printTraining/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="mdi mdi-file-pdf" ></i></a>';
	}
   
    $action = getActionButton($printBtn.$completeButton.$cancelButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,date('d-m-Y H:i:s',strtotime($data->start_date)),date('d-m-Y H:i:s',strtotime($data->end_date)),$data->title,$data->type,$data->skill_name,$data->trainer_name];   
}

/* Kpi Checklist Table Data */
function getKpiChecklistData($data){
    $editButton = $deleteButton = "";
    
	$deleteParam = "{'postData':{'id' : ".$data->id.",'dept_id' : ".$data->dept_id.",'desi_id' : ".$data->desi_id."},'message' : 'KPI Checklist'}";
	$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$editParam = "{'postData':{'dept_id' : ".$data->dept_id.",'desi_id' : ".$data->desi_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addKpiChecklist', 'title' : 'Update KPI Checklist','call_function':'edit','res_function' : 'resKpiChecklist','button' : 'close', 'js_store_fn':'customStore'}";
	$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
   
    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->dept_name,$data->title,$data->total_per];   
}

/* Employee Perfomance Table Data */
function getEmpPerfomanceData($data){
    $editButton = $approveButton = "";

    if(empty($data->approve_by)){

        $editParam = "{'postData':{'month' : '".$data->month."','emp_id' : ".$data->emp_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addEmpPerfomance', 'title' : 'Update Employee Perfomance','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $approveParam = "{'postData':{'perfomance_id' : ".$data->id.", 'month' : '".$data->month."','emp_id' : ".$data->emp_id.",'msg':'Approved'},'fnsave':'approveEmpPerfomance','message':'Are you sure want to Approve this Employee Perfomance?'}";
        $approveButton = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmStore('.$approveParam.');"><i class="fa fa-check"></i></a>';
    }
    $month = date('M-Y', strtotime($data->month));
   
    $createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
    $approvedBy = $data->approved_name.(!empty($data->approve_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->approve_at)) : '');

    $action = getActionButton($approveButton.$editButton);

    return [$action,$data->sr_no,$month,$data->emp_code,$data->emp_name,$data->dept_name,$data->title,$createdBy,$approvedBy];   
}

/* Employee Skill Table Data */
function getEmpSkillData($data){
    $editButton = $approveButton = "";

    if(empty($data->approve_by)){

        $editParam = "{'postData':{'month' : '".$data->month."','emp_id' : ".$data->emp_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addEmpSkill', 'title' : 'Update Employee Skill','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $approveParam = "{'postData':{'perfomance_id' : ".$data->id.", 'month' : '".$data->month."','emp_id' : ".$data->emp_id.",'msg':'Approved'},'fnsave':'approveEmpSkill','message':'Are you sure want to Approve this Employee Skill?'}";
        $approveButton = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmStore('.$approveParam.');"><i class="fa fa-check"></i></a>';
    }
    
    $month = date('M-Y', strtotime($data->month));
   
    $createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
    $approvedBy = $data->approved_name.(!empty($data->approve_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->approve_at)) : '');

    $action = getActionButton($approveButton.$editButton);

    return [$action,$data->sr_no,$month,$data->emp_code,$data->emp_name,$data->dept_name,$data->title,$createdBy,$approvedBy];   
}

/* KPI Master Table Data */
function getKpiData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'KPI'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editKpi', 'title' : 'Update KPI'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->kpi_name];
}

?>