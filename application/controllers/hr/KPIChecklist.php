<?php
class KPIChecklist extends MY_Controller{
    private $indexPage = "hr/kpi_checklist/index";
    private $form = "hr/kpi_checklist/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "KPI Checklist";
		$this->data['headData']->controller = "hr/KPIChecklist";
		$this->data['headData']->pageUrl = "hr/KPIChecklist";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('kpiChecklist');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->kpiChecklist->getKpiDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getKpiChecklistData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addKpiChecklist(){	
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['departmentList'] = $this->department->getDepartmentList();	
		$this->data['kpiList'] = $this->kpiMaster->getKpiData();
		$this->load->view($this->form,$this->data);
    }

	public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['dept_id'])){
            $errorMessage['dept_id'] = "Department is required.";
        }  
        if(empty($data['desi_id'])){
            $errorMessage['desi_id'] = "Designation is required.";
        }      
        if(empty($data['kpi_id'])){
            $errorMessage['kpi_id'] = "KPI Type is required.";
        }     
        if(empty($data['kpi_desc'])){
            $errorMessage['kpi_desc'] = "KPI is required.";
        }      
        if(empty($data['req_per'])){
            $errorMessage['req_per'] = "Weightage is required.";
        }else{
            $kpiOldData = $this->kpiChecklist->getKpiData(['dept_id'=>$data['dept_id'],'desi_id'=>$data['desi_id'],'id'=>$data['id']]);
            $totalPer = array_sum(array_column($kpiOldData, 'req_per'));
            $reqPer = $data['req_per'] + $totalPer; 
           
            if($reqPer > 100){
                $errorMessage['req_per'] = "Total Weightage cannot exceed 100.";
            }
        }
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->kpiChecklist->save($data));
        endif;
    }

    public function getKpiTransHtml(){
        $data = $this->input->post();
        $kpiData = $this->kpiChecklist->getKpiData(['dept_id'=>$data['dept_id'],'desi_id'=>$data['desi_id']]);
        $tbodyData="";$i=1; 
        if(!empty($kpiData)):
            $i=1;
            foreach($kpiData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.",'dept_id' : ".$row->dept_id.",'desi_id' : ".$row->desi_id."},'res_function':'resKpiChecklist','fndelete' : 'deleteKpi'}";
                $editBtn = "<button type='button' onclick='editKpi(".json_encode($row).",this);' class='btn btn-sm btn-outline-info waves-effect waves-light btn-sm permission-modify' datatip='Edit'><i class='far fa-edit'></i></button>";
                $tbodyData.= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->kpi_name.'</td>
                            <td>'.$row->kpi_desc.'</td>
                            <td>'.$row->req_per.'</td>
                            <td class="text-center">
                            '.$editBtn.'
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
                    </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->kpiChecklist->getKpi(['dept_id'=>$data['dept_id'],'desi_id'=>$data['desi_id']]);
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['departmentList'] = $this->department->getDepartmentList();	
		$this->data['kpiList'] = $this->kpiMaster->getKpiData();
        $this->load->view($this->form,$this->data);
    }

    public function deleteKpi(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->kpiChecklist->trash('kpi_checklist',['id'=>$data['id']]));
        endif;
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->kpiChecklist->delete($data));
        endif;
    }
}
?>