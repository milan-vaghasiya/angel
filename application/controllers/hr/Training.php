<?php
class Training extends MY_Controller{
    private $index = "hr/training/index";
    private $form = "hr/training/form";
    private $attendanceForm = "hr/training/emp_attendance_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Training";
        $this->data['headData']->pageUrl = "hr/training";
		$this->data['headData']->controller = "hr/training";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('training');
        $this->load->view($this->index,$this->data);        
    }
	
    public function getDTRows($status = 0){
        $data = $this->input->post(); 
        $data['status'] = $status; /* 0=Pending 1=Completed 2=Cancelled */
        $result = $this->training->getDTRows($data);
        $sendData = array(); $i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getTrainingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTraining(){
        $this->data['skillData'] = $this->skillMaster->getSkillList();
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post(); 
		$errorMessage = array();

        if (empty($data['type'])) {
			$errorMessage['type'] = "Type is required.";
        }	
        if (empty($data['title'])) {
			$errorMessage['title'] = "Purpose is required.";
        }	
        if (empty($data['trainer_name'])) {
			$errorMessage['trainer_name'] = "Trainer is required.";
        }
        if ($data['start_date'] > $data['end_date']) {
			$errorMessage['end_date'] = "Invalid Date";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:      
            $data['skill_id'] = implode(',',$data['skill_id']);  
            $data['emp_id'] = implode(',',$data['emp_id']);  
            $this->printJson($this->training->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->training->getTraining($data);
        $this->data['skillData'] = $skillData = $this->skillMaster->getSkillList();
        $skillIds = explode(',',$dataRow->skill_id);
        $this->data['empData'] = $this->getAttendeeList(['emp_id'=>$dataRow->emp_id,'skill_id'=>$skillIds]);
        $this->load->view($this->form, $this->data);        
    }

    public function getAttendeeList($param = []){ 
		$data = (!empty($param)) ? $param : $this->input->post();
        $skillIds = implode(',',$data['skill_id']); 
      
        $empData = $this->training->getTrainingData(['skill_id'=>$skillIds]);
        $options ="";
            if(!empty($empData)):
                foreach($empData as $row):
                    $selected = ((!empty($data['emp_id']) && in_array($row->emp_id, explode(',',$data['emp_id']))) ? 'selected' : '');
                    $options .= '<option value="'.$row->emp_id.'" '.$selected.'>'.$row->emp_name.'</option>';
                endforeach;
            endif;
        if(!empty($param)):
            return $options;
        else:
            $this->printJson(['status'=>1,'empOptions'=>$options]);
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->training->delete($id));
        endif;
    }

    public function changeTrainingStatus(){
		$data = $this->input->post();		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->training->changeTrainingStatus($data));
		endif;
	}

    public function addEmpAttendance(){
        $data = $this->input->post(); 
        $this->data['id'] = $data['id'];
        $this->data['empData'] = $this->training->getTrainingData(['id'=>$data['id']]);
        $this->load->view($this->attendanceForm, $this->data);
    }

    public function completeTraining(){
        $data = $this->input->post(); 
		$errorMessage = array();

        if(empty($data['attendee_id'])) {
			$errorMessage['table_err'] = "Attendee Detail is required.";
        }	
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $data['attendee_id'] = implode(',',$data['attendee_id']);
            $this->printJson($this->training->save($data));
        endif;
    }

    public function printTraining($id){
        $this->data['dataRow'] = $this->training->getTraining(['id'=>$id]);
        $this->data['trainingData'] = $this->training->getTrainingData(['id'=>$id]);

		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $pdfData = $this->load->view('hr/training/print', $this->data, true);

        $printedBy = $this->employee->getEmployee(['id'=>$this->loginId]);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
        $htmlFooter = '
			<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
				<tr>
					<td style="width:50%;">
					    Printed By & Date : '.$printedBy->emp_name.' ('.formatDate(date('Y-m-d H:i:s'), 'd-m-Y H:i:s').')
					</td>
					<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';

        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName = 'training-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->useSubstitutions = false;
		$mpdf->simpleTables = true;

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 38, 20, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

}
?>