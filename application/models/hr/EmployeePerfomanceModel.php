<?php
class EmployeePerfomanceModel extends MasterModel
{
    private $empPerformance = "employee_performance";	
    private $empPerfomanceLog = "employee_performance_log";	

    public function getDTRows($data){
        $data['tableName'] = $this->empPerformance;       
        $data['select'] = "employee_performance.*,department_master.name as dept_name,emp_designation.title,employee_master.emp_name,employee_master.emp_code,employee_performance_log.month,employee_performance_log.approve_by,employee_performance_log.approve_at,approvedBy.emp_name as approved_name,createdBy.emp_name as created_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = employee_performance.emp_id";
        $data['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";
        $data['leftJoin']['employee_performance_log'] = "employee_performance.id = employee_performance_log.perfomance_id";
        $data['leftJoin']['employee_master createdBy'] = "createdBy.id = employee_performance.created_by";
        $data['leftJoin']['employee_master approvedBy'] = "approvedBy.id = employee_performance_log.approve_by";

        $data['group_by'][] = "employee_performance.emp_id,employee_master.dept_id,employee_master.designation_id,employee_performance_log.month";

        if(empty($data['status'])):
			$data['where']['employee_performance_log.approve_by'] = 0;
		else:
			$data['where']['employee_performance_log.approve_by !='] = 0;
		endif; 

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "CONCAT(createdBy.emp_name,DATE_FORMAT(employee_performance.created_at,'%d-%m-%Y %H:%i:%s'))";
        $data['searchCol'][] = "CONCAT(approvedBy.emp_name,DATE_FORMAT(employee_performance_log.approve_at,'%d-%m-%Y %H:%i:%s'))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function save($data){ 
        try{ 
			$this->db->trans_begin();

            $cMonth = 'm' .(int)date('m', strtotime($data['month']));
            $cdate = date('Y-m-01', strtotime($data['month']));   
            if (!empty($data['current_per'])) {
                $i = 0;               
                foreach ($data['current_per'] as $row) { 
                    $perfomanceData = $this->empPerfomance->getEmpPerfomanceDetails(['emp_id'=>$data['emp_id'],'kpi_id'=>$data['kpi_id'][$i],'single_row'=>1]);
                    $storeData = [
                        'id' =>  (!empty($perfomanceData->id) ? $perfomanceData->id : ''),
                        'kpi_id' => $data['kpi_id'][$i],
                        'emp_id' => $data['emp_id'],
                    ];
                     
                    $storeData[$cMonth] = $row; 
                    $result = $this->store($this->empPerformance, $storeData, 'Emp Performance');

                    if(!empty($result)) {
                        $perfomanceLogData = $this->getEmpPerfomanceLogData(['emp_id'=>$data['emp_id'],'perfomance_id'=>$result['id'],'month'=>$cdate,'single_row'=>1]);
                            $logData = [
                                'id' => (!empty($perfomanceLogData->id) ? $perfomanceLogData->id : ''),
                                'perfomance_id' => $result['id'], 
                                'emp_id' => $data['emp_id'],
                                'month' => $cdate
                            ]; 
                            $this->store($this->empPerfomanceLog, $logData, 'Emp Performance');
                    }
                    $i++;
                }
            }
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }              
    }

    public function getEmpPerfomanceLogData($data){
        $queryData['tableName'] = $this->empPerfomanceLog; 

        if(!empty($data['month'])){
            $queryData['where']['employee_performance_log.month'] = $data['month'];
        }
        if(!empty($data['emp_id'])){
            $queryData['where']['employee_performance_log.emp_id'] = $data['emp_id'];
        } 
        if(!empty($data['perfomance_id'])){
            $queryData['where']['employee_performance_log.perfomance_id'] = $data['perfomance_id'];
        } 
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
	
    public function getEmpPerfomanceDetails($data=array()){
        $queryData['tableName'] = $this->empPerformance; 
        $queryData['select'] = "employee_performance.*,employee_master.emp_name,employee_master.emp_code,kpi_checklist.kpi_desc,kpi_checklist.req_per,kpi_master.kpi_name,kpi_checklist.id as trans_id,department_master.name as dept_name,emp_designation.title";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = employee_performance.emp_id";
        $queryData['leftJoin']['kpi_checklist'] = "kpi_checklist.id = employee_performance.kpi_id";
        $queryData['leftJoin']['kpi_master'] = "kpi_master.id = kpi_checklist.kpi_id";
        $queryData['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";

        if(!empty($data['emp_id'])){$queryData['where']['employee_performance.emp_id'] = $data['emp_id'];} 
          if(!empty($data['kpi_id'])){$queryData['where']['employee_performance.kpi_id'] = $data['kpi_id'];} 

        $queryData['where']['employee_performance.created_at >='] = $this->startYearDate;
        $queryData['where']['employee_performance.created_at <='] = $this->endYearDate;
        
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
	}
	
    public function approveEmpPerfomance($data){ 
        try{
            $this->db->trans_begin();

            $oldData = $this->getEmpPerfomanceLogData(['month'=>$data['month'],'emp_id'=>$data['emp_id']]);
            foreach($oldData as $row){
                $result = $this->edit($this->empPerfomanceLog, ['month'=> $row->month,'emp_id'=> $row->emp_id], ['approve_by' => $this->loginId, 'approve_at'=>date('Y-m-d H:i:s')]);
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

}
?>