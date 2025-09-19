<?php
class KpiMaster extends MY_Controller{
    private $indexPage = "hr/kpi_master/index";
    private $form = "hr/kpi_master/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "KPI Master";
		$this->data['headData']->controller = "hr/kpiMaster";
		$this->data['headData']->pageUrl = "hr/kpiMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('kpiMaster');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->kpiMaster->getKpiDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getKpiData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addKpi(){	
		$this->load->view($this->form,$this->data);
    }

	public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['kpi_name'])){
            $errorMessage['kpi_name'] = "KPI is required.";
        } 
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->kpiMaster->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->kpiMaster->getKpiData(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->kpiMaster->delete($data));
        endif;
    }
}
?>