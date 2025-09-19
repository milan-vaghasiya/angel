<?php
class SkillMaster extends MY_Controller{
    private $indexPage = "hr/skill_master/index";
    private $formPage = "hr/skill_master/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Skill Master";
		$this->data['headData']->controller = "hr/skillMaster";
	}

    public function index(){
		$this->data['tableHeader'] = getHrDtHeader('skillMaster');
		$this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->skillMaster->getDTRows($data);	
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $sendData[] = getSkillData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSkill(){
		$this->data['departmentList'] = $this->department->getDepartmentList();
		$this->data['designationList'] = $this->designation->getDesignations();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['skill_name']))
			$errorMessage['skill_name'] = "Skill Name is required.";
        if(empty($data['dept_id']))
			$errorMessage['dept_id'] = "Department is required.";
		if(empty($data['designation_id']))
			$errorMessage['designation_id'] = "Designation is required.";
        if(empty($data['req_skill'])){
			$errorMessage['req_skill'] = "Skill Per is required.";
        }else{
            if($data['req_skill'] <= 0 || $data['req_skill'] > 100)
			    $errorMessage['req_skill'] = "Req. Skill Per Not valid.";
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->skillMaster->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
		$this->data['departmentList'] = $this->department->getDepartmentList();
		$this->data['designationList'] = $this->designation->getDesignations();
        $this->data['dataRow'] = $this->skillMaster->getSkillList(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->skillMaster->delete($id));
        endif;
    }

    function printSkillSet($dept_id='',$designation_id=''){ 
        $dept_id = decodeURL($dept_id);
        $designation_id = decodeURL($designation_id);
		$this->data['skillSetData'] = $this->skillMaster->getSkillList(['dept_id'=>$dept_id,'designation_id'=>$designation_id]);

		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $pdfData = $this->load->view('hr/skill_master/print_skill_set', $this->data, true);
 
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
        $pdfFileName = 'SS-' . $set_name . '.pdf';
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