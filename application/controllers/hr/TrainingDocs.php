<?php
class TrainingDocs extends MY_Controller{
    private $indexPage = "hr/training_docs/index";
    private $formPage = "hr/training_docs/form";
	    
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Training Docs";
		$this->data['headData']->controller = "hr/trainingDocs";
		$this->data['headData']->pageUrl = "hr/trainingDocs";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('trainingDocs');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $data = $this->input->post();
        $result = $this->trainingDocs->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $sendData[] = getTrainingDocsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function addTrainingDocs(){
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->load->view($this->formPage,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['dept_id'])) {
            $errorMessage['dept_id'] = "Department is required.";
        }
        if (empty($data['designation_id'])) {
            $errorMessage['designation_id'] = "Designation is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if(!empty($_FILES['doc_file']['name'])):
				$attachment = "";
				$this->load->library('upload');
				
				$_FILES['userfile']['name']     = $_FILES['doc_file']['name'];
				$_FILES['userfile']['type']     = $_FILES['doc_file']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['doc_file']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['doc_file']['error'];
				$_FILES['userfile']['size']     = $_FILES['doc_file']['size'];

				$imagePath = realpath(APPPATH . '../assets/uploads/training_docs/');
				$fileName = 'doc_file_'.time();

				$config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

				$this->upload->initialize($config);

				if(!$this->upload->do_upload()):
					$errorMessage['doc_file'] = $fileName . " => " . $this->upload->display_errors();
				else:
					$uploadData = $this->upload->data();
					$data['doc_file'] = $uploadData['file_name'];
				endif;

				if(!empty($errorMessage['doc_file'])):
					if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
				endif;            
			endif;
		
            $this->printJson($this->trainingDocs->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->trainingDocs->getTrainingDocs($data);
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->load->view($this->formPage,$this->data);
    }

	public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->trainingDocs->delete($data));
        endif;
    }
}
?>