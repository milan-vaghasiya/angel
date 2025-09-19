<?php
class Expense extends MY_Controller{
    private $indexPage = "hr/expense/index";
    private $form = "hr/expense/form";
    private $approve_form = "hr/expense/approve_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Expense";
		$this->data['headData']->controller = "hr/expense";
		$this->data['headData']->pageUrl = "hr/expense";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMasterDtHeader('expense');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->expense->getExpenseDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExpenseData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExpense(){	
        $this->data['exp_prefix'] = "EXP".n2y(date('Y')).n2m(date('m'));  
        $this->data['exp_no'] = $this->expense->getNextExpNo();
        $this->data['empList'] = $this->employee->getEmployeeList();	
		$this->data['expTypeList'] = $this->selectOption->getSelectOptionList(['type'=>3]);
		$this->load->view($this->form, $this->data);
    }

	public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['exp_number'])){
            $errorMessage['exp_number'] = "Expense Number is required.";
        }  
        if(empty($data['exp_date'])){
            $errorMessage['exp_date'] = "Expense date is required.";
        }      
        if(empty($data['exp_by_id'])){
            $errorMessage['exp_by_id'] = "Employee is required.";
        }     
        if(empty($data['exp_type_id'])){
            $errorMessage['exp_type_id'] = "Expense type is required.";
        }      
        if(empty($data['demand_amount'])){
            $errorMessage['demand_amount'] = "Amount is required.";
        }  
        if(!empty($_FILES['proof_file'])):
            if($_FILES['proof_file']['name'] != null || !empty($_FILES['proof_file']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['proof_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['proof_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['proof_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['proof_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['proof_file']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/expense/');
                $fileName = preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($_FILES['proof_file']['name']));
                $config = ['file_name' => $fileName,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['proof_file'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $data['proof_file'] = $uploadData['file_name'];
                endif;
            endif;
        endif;
      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->expense->saveExpense($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post(); 
        $this->data['dataRow'] = $dataRow = $this->expense->getExpense($data);
		$this->data['expTypeList'] = $this->selectOption->getSelectOptionList(['type'=>3]);
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->expense->trash('expense_manager',['id'=>$id]));
        endif;
    }
    
    public function getApprovedData(){
        $data = $this->input->post(); 
        $this->data['id'] = $data['id'];
        $this->data['dataRow'] = $this->expense->getExpense($data);
        $this->load->view($this->approve_form,$this->data);
    }

    public function saveApprovedData(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if($data['status'] == 1){
            if(empty($data['amount'])){
                $errorMessage['amount'] = "Amount is required.";
            } 
        }else{
            if(empty($data['rej_reason'])){
                $errorMessage['rej_reason'] = "Reason is required.";
            } 
        }
      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['status'] == 1){
                $data['approved_by'] = $this->loginId;
                $data['approved_at'] = date('Y-m-d H:i:s');
            }
            $this->printJson($this->expense->saveExpense($data));
        endif;
    }
}
?>