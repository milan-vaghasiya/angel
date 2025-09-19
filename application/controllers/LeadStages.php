<?php
class LeadStages extends MY_Controller{
    private $index = "lead_stages/index";
    private $form = "lead_stages/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Lead Stages";
		$this->data['headData']->controller = "leadStages";
        $this->data['headData']->pageUrl = "leadStages";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->leadStages->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getLeadStagesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLeadStages(){
		$postData = $this->input->post();
		$seqData = $this->leadStages->getMaxStageSequence();
		$this->data['next_seq_no'] = (!empty($seqData->next_seq_no) ? ($seqData->next_seq_no + 1) : 1);
		$this->load->view($this->form, $this->data);
	}

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['stage_type'])){
			$errorMessage['stage_type'] = "Stage Type is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->printJson($this->leadStages->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->leadStages->getLeadStagesList(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->leadStages->delete($id));
        endif;
    }
}
?>