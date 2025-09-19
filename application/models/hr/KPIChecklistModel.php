<?php
class KPIChecklistModel extends MasterModel
{
    private $kpi_checklist = "kpi_checklist";	

    public function getKpiDTRows($data){
        $data['tableName'] = $this->kpi_checklist;       
        $data['select'] = "kpi_checklist.*,department_master.name as dept_name,emp_designation.title,SUM(kpi_checklist.req_per) as total_per";
        $data['leftJoin']['department_master'] = "kpi_checklist.dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "kpi_checklist.desi_id = emp_designation.id";
        $data['group_by'][] = "kpi_checklist.dept_id,kpi_checklist.desi_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
			$this->db->trans_begin();

			$result = $this->store($this->kpi_checklist,$data,'KPI CheckList');
            
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }              
    }

    public function getKpiData($data){
		$queryData['tableName'] = 'kpi_checklist';
        $queryData['select'] = "kpi_checklist.*,kpi_master.kpi_name";
        $queryData['leftJoin']['kpi_master'] = "kpi_master.id = kpi_checklist.kpi_id";
        $queryData['where']['kpi_checklist.dept_id'] = $data['dept_id'];
        $queryData['where']['kpi_checklist.desi_id'] = $data['desi_id']; 
        if(!empty($data['id'])){$queryData['where']['kpi_checklist.id !='] = $data['id'];}

		$result = $this->rows($queryData);
		return $result;
	}

    public function getKpi($data){
		$queryData['tableName'] = 'kpi_checklist';
        $queryData['select'] = "kpi_checklist.*,kpi_master.kpi_name,department_master.name as department_name,emp_designation.title";
        $queryData['leftJoin']['kpi_master'] = "kpi_master.id = kpi_checklist.kpi_id";
        $queryData['leftJoin']['department_master'] = "kpi_checklist.dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "kpi_checklist.desi_id = emp_designation.id";

        if(!empty($data['id'])){$queryData['where']['kpi_checklist.id'] = $data['id'];}
        if(!empty($data['dept_id'])){$queryData['where']['kpi_checklist.dept_id'] = $data['dept_id'];}
        if(!empty($data['desi_id'])){$queryData['where']['kpi_checklist.desi_id'] = $data['desi_id'];}

        $result = $this->row($queryData);
		return $result;
	}

    public function delete($data){ 
         try{
			$this->db->trans_begin();
            $kpiOldData = $this->getKpiData(['dept_id'=>$data['dept_id'],'desi_id'=>$data['desi_id']]);
            foreach($kpiOldData as $row):
                $KpiData = [
                    'id'=>$row->id,
                    'dept_id' => $row->dept_id,
                    'desi_id' => $row->desi_id,
                ]; 
                $result = $this->trash($this->kpi_checklist,$KpiData,'KPI CheckList');
            endforeach;
            
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }  
    }

}
?>