<?php
class EmployeeSkill extends MY_Controller{
    private $indexPage = "hr/emp_skill/index";
    private $form = "hr/emp_skill/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employee Skill";
		$this->data['headData']->controller = "hr/EmployeeSkill";
		$this->data['headData']->pageUrl = "hr/employeeSkill";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('empSkill');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();
        $data['status'] = $status; //0=pending, 1=Approved
        $result = $this->empSkill->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getEmpSkillData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmpSkill(){		
        $this->data['empList'] = $this->employee->getEmployeeList();  
        $this->data['monthList'] = $this->getMonthListFY();
		$this->load->view($this->form,$this->data);
    }
    
    public function getEmpSkillData(){
        $data = $this->input->post(); 
        $skillData = $this->skillMaster->getSkillList(['emp_id'=>$data['emp_id'],'dept_id'=>$data['dept_id'], 'designation_id'=>$data['designation_id'],'oldData'=>1]);
        $tbodyData="";$i=1;
        if(!empty($skillData)):
            foreach($skillData as $row):
                $tbodyData .= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->skill_name.'</td>
                            <td>'.$row->req_skill.'</td> 
                            <td>'.$row->prev_skill.'
                                    <input type="hidden" name="prev_skill[]" value="'.$row->prev_skill.'">
                                </td>
                            <td>
                                <input type="hidden" name="id[]" value="'.$row->trans_id.'">
								<input type="hidden" name="skill_id[]" value="'.$row->id.'">
                                <input type="text" name="current_skill[]" id="current_skill_'.$i.'" value="" floatOnly>
								<div class="error current_skill'.$i.'"></div>
                            </td>
                        </tr>';
                $i++;
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

	public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['month'])){
            $errorMessage['month'] = "Month is required.";
        }  
        if(empty($data['emp_id'])){
            $errorMessage['emp_id'] = "Employee is required.";
        } 
        $i=1;
        foreach($data['current_skill'] as $key=>$value){
            if(isset($data['current_skill'][$key]) && ($data['current_skill'][$key] == '')){
                $errorMessage['current_skill'.$i] = "Current Skill is required.";
            }
            $skillData = $this->skillMaster->getSkillList(['id'=>$data['skill_id'][$key],'single_row'=>1]);
            if($value > 100 || $value < 0 || $value > $skillData->req_skill){
                $errorMessage['current_skill'.$i] = "Invalid percentage.";
            }
            $i++;
        }
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['month'] = (!empty($data['month']) ? $data['month'] : 'm'.intval(date('m'))); 
            $this->printJson($this->empSkill->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->empSkill->getStafffSkillLogData(['month'=>$data['month'],'emp_id'=>$data['emp_id'],'single_row'=>1]);
        $this->data['skillData'] = $this->empSkill->getEmpSkillDetails(['month'=>$dataRow->month,'emp_id'=>$data['emp_id']]);
        $this->data['empList'] = $this->employee->getEmployeeList();  
        $this->data['monthList'] = $this->getMonthListFY();
        $this->load->view($this->form,$this->data);
    }
    
    public function approveEmpSkill(){
		$data = $this->input->post(); 
		if(empty($data)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->empSkill->approveEmpSkill($data));
		endif;
	}
}
?>