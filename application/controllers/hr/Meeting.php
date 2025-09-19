<?php
class Meeting extends MY_Controller{
    private $index = "hr/meeting/index";
    private $form = "hr/meeting/form";
    private $complete_form = "hr/meeting/complete_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Meeting";
        $this->data['headData']->pageUrl = "hr/meeting";
		$this->data['headData']->controller = "hr/meeting";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('meeting');
        $this->load->view($this->index,$this->data);        
    }
	
    public function getDTRows($status = 0){
        $data = $this->input->post(); 
        $data['status'] = $status;/* 0=Pending 1=Completed 2=Cancelled */
        $result = $this->meeting->getDTRows($data);
        $sendData = array(); $i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getMeetingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMeeting(){
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if (empty($data['title'])) {
			$errorMessage['title'] = "Title is required.";
        }			
        if (empty($data['emp_id'])) {
			$errorMessage['emp_id'] = "Meeting is required.";
        }
        if ($data['me_date'] < (date('Y-m-d'))) {
			$errorMessage['me_date'] = "Invalid Date";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:      
            $data['emp_id'] = implode(',',$data['emp_id']);     
            $this->printJson($this->meeting->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->meeting->getMeeting($data);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->form, $this->data);        
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->meeting->delete($id));
        endif;
    }

    public function changeMeetStatus(){
		$data = $this->input->post();		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->meeting->changeMeetStatus($data));
		endif;
	}

    public function addAttendee(){
        $data = $this->input->post();
        $this->data['dataRow'] = $data;
        $this->data['empData'] = $this->meeting->getMeetingData(['id'=>$data['id']]);
        $this->load->view($this->complete_form, $this->data);
    }

    public function completeMeeting(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['attendee_id'])) {
			$errorMessage['table_err'] = "Attendee Detail is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:      
            $data['attendee_id'] = implode(',',$data['attendee_id']);     
            $this->printJson($this->meeting->save($data));
        endif;
    }

    public function printMeeting($id){
        $this->data['dataRow'] = $this->meeting->getMeeting(['id'=>$id]);
        $this->data['meetingData'] = $this->meeting->getMeetingData(['id'=>$id]);

		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $pdfData = $this->load->view('hr/meeting/print', $this->data, true);

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
        $pdfFileName = 'meeting-' . $id . '.pdf';
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