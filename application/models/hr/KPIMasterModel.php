<?php
class KPIMasterModel extends MasterModel
{
    private $kpiMaster = "kpi_master";	

    public function getKpiDTRows($data){
        $data['tableName'] = $this->kpiMaster;       

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "kpi_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
			$this->db->trans_begin();

			$result = $this->store($this->kpiMaster,$data,'KPI');
            
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }              
    }

    public function getKpiData($data=array()){
		$queryData['tableName'] = $this->kpiMaster;
        $queryData['select'] = "kpi_master.*,";
        if(!empty($data['id'])){$queryData['where']['kpi_master.id'] = $data['id'];}

       if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
	}

    public function delete($data){ 
         try{
			$this->db->trans_begin();

            $checkData['columnName'] = ['kpi_id'];
            $checkData['value'] = $data['id'];
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The KPI is currently in use. you cannot delete it.'];
            endif;
            $result = $this->trash($this->kpiMaster,$data,'KPI');
            
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