<?php
class MachineBreakdownModel extends MasterModel{
    private $machine_breakdown = "machine_breakdown";

    public function getNextMachineNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'machine_breakdown';
        $queryData['select'] = "MAX(trans_no ) as trans_no ";
		$queryData['where']['machine_breakdown.trans_date >='] = $this->startYearDate;
		$queryData['where']['machine_breakdown.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (empty($this->last_trans_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }

    public function getDTRows($data){
        $data['tableName'] = "machine_breakdown";
		$data['select'] = "machine_breakdown.*,prc_master.prc_number,item_master.item_name as machine_name,rejection_comment.code,rejection_comment.remark as idle_reason,item_master.item_code as machine_code";
        $data['leftJoin']['prc_master'] = "prc_master.id = machine_breakdown.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = machine_breakdown.machine_id";
        $data['leftJoin']['rejection_comment'] = "rejection_comment.id = machine_breakdown.idle_reason";

        if($data['status'] == 1){ $data['where']['machine_breakdown.end_date'] = NULL; }
        else { $data['where']['machine_breakdown.end_date != '] = NULL; }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "machine_breakdown.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(machine_breakdown.trans_date,'%d-%m-%Y %H:%i:%s')";
        $data['searchCol'][] = "DATE_FORMAT(machine_breakdown.end_date,'%d-%m-%Y %H:%i:%s')";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "CONCAT('[',rejection_comment.code,'] ',rejection_comment.remark)";
        $data['searchCol'][] = "machine_breakdown.remark";
        $data['searchCol'][] = "machine_breakdown.solution";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMachineBreakdown($data){
        $queryData['tableName'] = $this->machine_breakdown;
		$queryData['select'] = "machine_breakdown.*,item_master.item_name,item_master.item_code,prc_master.prc_number,(CASE WHEN machine_breakdown.idle_reason = '-1' THEN 'Prev. Maintenance' ELSE rejection_comment.remark END) as idle_reason";
		$queryData['leftJoin']['item_master'] = "item_master.id = machine_breakdown.machine_id";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = machine_breakdown.prc_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = machine_breakdown.idle_reason";
       
        if(!empty($data['id'])):
            $queryData['where']['machine_breakdown.id'] = $data['id'];
        endif;

        if(!empty($data['machine_id'])):
            $queryData['where']['machine_breakdown.machine_id'] = $data['machine_id'];
        endif;

        if(!empty($data['pending_solution'])):
            $queryData['customWhere'][] = "machine_breakdown.end_date IS NULL AND machine_breakdown.solution IS NULL";
        endif;

        if(!empty($data['from_date']) && !empty($data['to_date'])):
            $queryData['customWhere'][] = "DATE(machine_breakdown.trans_date) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        endif;

        if(!empty($data['multi_row'])):
            return $this->rows($queryData);
        else:
            return $this->row($queryData);
        endif;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->getNextMachineNo();
                $data['trans_number'] = 'MT/'.getYearPrefix('SHORT_YEAR').'/'.$data['trans_no'];
            endif;
         
            $result = $this->store($this->machine_breakdown,$data,"Machine Breakdown");

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function delete($id){
        try{
            $this->db->trans_begin(); 
            
            $reqData = $this->getSparPartRequestData(['ref_id'=>$id,'multi_row'=>1]);
            if(!empty($reqData)){
                return ['status'=>0,'message'=>'You can not delete this Machine'];
            }

            $result = $this->trash($this->machine_breakdown,['id'=>$id],'Machine Breakdown');

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