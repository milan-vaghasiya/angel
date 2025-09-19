<?php
class EmployeePerfomance extends MY_Controller{
    private $indexPage = "hr/emp_perfomance/index";
    private $form = "hr/emp_perfomance/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employee Perfomance";
		$this->data['headData']->controller = "hr/EmployeePerfomance";
		$this->data['headData']->pageUrl = "hr/employeePerfomance";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('empPerfomance');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();
        $data['status'] = $status; //0=pending, 1=Approved
        $result = $this->empPerfomance->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getEmpPerfomanceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmpPerfomance(){		
        $this->data['empList'] = $this->employee->getEmployeeList();  
        $this->data['monthList'] = $this->getMonthListFY();
		$this->load->view($this->form,$this->data);
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
        
        foreach($data['current_per'] as $key=>$value){
            if(isset($data['current_per'][$key]) && ($data['current_per'][$key] == '')){
                $errorMessage['current_per'.$i] = "Current Per is required.";
            }
            $kpiData = $this->kpiChecklist->getKpi(['id'=>$data['kpi_id'][$key]]);
            if($value > 100 || $value < 0 || $value > $kpiData->req_per){
                $errorMessage['current_per'.$i] = "Invalid Percentage.";
            }
            $i++;
        }
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['month'] = (!empty($data['month']) ? $data['month'] : 'm'.intval(date('m'))); 
             
            $this->printJson($this->empPerfomance->save($data));
        endif;
    }

    public function getEmpPerfomanceData(){
        $data = $this->input->post();
        $kpiData = $this->kpiChecklist->getKpiData(['dept_id'=>$data['dept_id'],'desi_id'=>$data['desi_id']]);
        $tbodyData="";$i=1;
        if(!empty($kpiData)):
            $kpiList = array_reduce($kpiData, function($kpiList, $kpi) { $kpiList[$kpi->kpi_name][] = $kpi; return $kpiList; }, []);
            foreach ($kpiList as $kpi_name=>$rows):
                
                $tbodyData .= '<tr><th colspan="4">'.$kpi_name.'</th></tr>';
                foreach ($rows as $row) {
                   
                    $tbodyData .= '<tr>
                                <td>'.$i.'</td>
                                <td>'.$row->kpi_desc.'</td>
                                <td>'.$row->req_per.'</td>
                                <td>
                                    <input type="hidden" name="id[]" value="">
                                    <input type="hidden" name="kpi_id[]" value="'.$row->id.'">
                                    <input type="text" name="current_per[]" id="current_per_'.$i.'" value="" floatOnly>
                                    <div class="error current_per'.$i.'"></div>
                                </td>
                            </tr>';
                            $i++;
                }
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->empPerfomance->getEmpPerfomanceLogData(['month'=>$data['month'],'emp_id'=>$data['emp_id'],'single_row'=>1]);
        $this->data['perfomanceData'] = $this->empPerfomance->getEmpPerfomanceDetails(['month'=>$dataRow->month,'emp_id'=>$data['emp_id']]);
        $this->data['empList'] = $this->employee->getEmployeeList();  
        $this->data['monthList'] = $this->getMonthListFY();
        $this->load->view($this->form,$this->data);
    }

    public function approveEmpPerfomance(){
		$data = $this->input->post(); 
		if(empty($data)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->empPerfomance->approveEmpPerfomance($data));
		endif;
	}
}
?>