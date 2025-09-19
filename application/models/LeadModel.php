<?php
class LeadModel extends MasterModel{
    private $partyMaster = "party_master";

    public function getLeadList($param=[]){
        $queryData['tableName'] = $this->partyMaster;
        $queryData['select'] = "party_master.*,emp.emp_name as executive";
        $queryData['leftJoin']['employee_master emp'] = "emp.id = party_master.sales_executive";

        if(!empty($param['party_category'])):
            $queryData['where_in']['party_master.party_category'] = $param['party_category'];
        endif;

        if(!empty($param['reminder'])){
            $queryData['select'] .= ",IFNULL(sl.reminder_date,'') as reminder_date";
            $queryData['leftJoin']['(SELECT MIN(ref_date) as reminder_date,party_id FROM sales_logs WHERE is_delete = 0 AND log_type = 3 and remark IS NULL GROUP BY party_id) sl'] = "sl.party_id = party_master.id";
            $queryData['order_by']['sl.reminder_date'] = "ASC";
            $queryData['where']['party_type !='] = 1;
        }else{
            if(!empty($param['party_type'])):
                if($param['party_type'] != 'ALL'):
                    $queryData['where_in']['party_master.party_type'] = $param['party_type'];
                endif;
            else:
                $queryData['where']['party_master.party_type !='] = 1;
            endif;
        }

        if(!empty($param['lead_source']) AND $param['lead_source'] != 'All'):
            $queryData['where']['party_master.source'] = $param['lead_source'];
        endif;

        if(!empty($param['skey'])):
            $queryData['like']['party_master.party_name'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['emp.emp_name'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['party_master.contact_person'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['party_master.source'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['party_master.contact_phone'] = str_replace(" ", "%", $param['skey']);
        endif;

        if(!empty($param['limit'])):
            $queryData['limit'] = $param['limit']; 
        endif;
        if(isset($param['start'])):
            $queryData['start'] = $param['start'];
        endif;
        if(!empty($param['length'])):
            $queryData['length'] = $param['length'];
        endif;

        return $this->rows($queryData);
    }

    public function getReminders($param=array()){
        $queryData = array();
        $queryData['tableName'] = 'sales_logs';

        if(!empty($param['crmDesk'])):
            $queryData['select'] = "party_master.*,emp.emp_name as executive,sales_logs.ref_date";
        else:
            $queryData['select'] = "sales_logs.*,emp.emp_name as executive,party_master.party_name,party_master.source,party_master.party_name";
        endif;

        $queryData['leftJoin']['party_master'] = "party_master.id = sales_logs.party_id";
        $queryData['leftJoin']['employee_master emp'] = "emp.id = party_master.sales_executive";
        $queryData['where']['sales_logs.log_type'] = 3;
        $queryData['where']['party_master.party_type'] = 2;

        if(!empty($param['status'])) {
            if($param['status'] == 1){$queryData['customWhere'][] = 'sales_logs.remark IS NULL';} // Pending Response
            if($param['status'] == 2){$queryData['customWhere'][] = 'sales_logs.remark IS NOT NULL';} // Response Given
        }
        
        if(!empty($param['ref_date'])){
            $queryData['where']['sales_logs.ref_date'] = $param['ref_date'];
        }
        
        // Search
        if(!empty($param['skey'])):
            $queryData['like']['party_master.party_name'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['emp.emp_name'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['party_master.contact_person'] = str_replace(" ", "%", $param['skey']);
            $queryData['like']['party_master.source'] = str_replace(" ", "%", $param['skey']);
        endif;
        
        if(!empty($param['executive_id'])) { $queryData['where']['sales_logs.created_by'] = $param['executive_id']; }
        if(!empty($param['group_by'])) { $queryData['group_by'][] = $param['group_by']; }		
        if(!empty($param['limit'])) { $queryData['limit'] = $param['limit']; }
        if(isset($param['start'])) { $queryData['start'] = $param['start']; }
        if(!empty($param['length'])) { $queryData['length'] = $param['length']; }
        
        $queryData['order_by']['sales_logs.ref_date'] = "ASC";
        $queryData['order_by']['party_master.party_name'] = "ASC";
        
        if(isset($param['count'])):
            $result = $this->numRows($queryData);
        else:
            $result = $this->rows($queryData);
        endif;
        return  $result;
    }

    public function saveLead($data){
        try {
			$this->db->trans_begin();
        
            if($this->checkDuplicate($data['party_name'],$data['party_category'],$data['party_type'],$data['id']) > 0):
                $errorMessage['party_name'] = "Party Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->partyMaster, $data, 'Party');
            endif;

            if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function deleteLead($id){
        try {
            $this->db->trans_begin();

            $checkData['columnName'] = ['party_id'];
            $checkData['value'] = $id;
            $checkData['ignoreTable'] = ["sales_logs"];
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Lead is currently in use. you cannot delete it.'];
            endif;
            
            $result = $this->trash($this->partyMaster, ['id' => $id], 'Lead');
            $this->trash('sales_logs', ['party_id'=>$id]);
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function changeLeadStatus($data){
        try {
            $this->db->trans_begin();

            $result = $this->store($this->partyMaster,['id'=>$data['id'],'party_type'=>$data['party_type'],'is_active'=>$data['is_active']]); 

            $logData = [
                'id' => '',
                'log_type' => $data['log_type'],
                'party_id' => $data['id'],
                'ref_id' => $result['id'],
                'ref_date' => date("Y-m-d"),
                'notes' => (!empty($data['notes'])?$data['notes']:''),
                'executive_id ' => $data['executive_id'],
                'created_by' => $this->loginId,
                'remark' => !empty($data['remark'])?$data['remark']:'',
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->saveSalesLogs($logData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveSalesLogs($data){
        try{
            $this->db->trans_begin();

            $result = $this->store('sales_logs', $data);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getSalesLog($param=[]){
        $data['tableName'] = "sales_logs";
        $data['select'] = "sales_logs.*,employee_master.emp_name as creator,party_master.party_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = sales_logs.created_by";
        $data['leftJoin']['employee_master se'] = "se.id = sales_logs.executive_id";
        $data['leftJoin']['party_master'] = "party_master.id = sales_logs.party_id";
        if(!empty($param['created_by'])){ $data['where']['sales_logs.created_by'] = $param['created_by']; }
        if(!empty($param['party_id'])){ $data['where']['sales_logs.party_id'] = $param['party_id']; }
        if(!empty($param['log_type'])){ 
            $data['where']['sales_logs.log_type'] = $param['log_type'];
            if($param['log_type'] == 3){
                $data['where'] ['sales_logs.ref_date'] = date("Y-m-d");
            }
        }
        
        if(!empty($param['not_log_type'])){ 
            $data['where_not_in']['sales_logs.log_type'] = $param['not_log_type'];
        }
        if(!empty($param['executive_id'])){
            $data['select'] .= ",ex.emp_name as executive";
            $data['leftJoin']['employee_master ex'] = "ex.id = sales_logs.executive_id";
            $data['where']['sales_logs.executive_id'] = $param['executive_id'];
        }
        if(!in_array($this->userRole,[1,-1])):
            if($this->leadRights == 2): // Zone Wise Leads Rights
                $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", se.super_auth_id ) >0 OR se.id = '.$this->loginId.')';
            elseif($this->leadRights == 1):
                $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", se.super_auth_id ) >0 OR se.id = '.$this->loginId.')';
            endif;
        endif;
        
        $data['order_by']['sales_logs.id'] = "ASC";
        if(!empty($param['limit'])) { $data['limit'] = $param['limit']; $data['order_by']['sales_logs.id'] = "DESC"; }
        if(!empty($param['single_row'])):
            return $this->row($data);
        else:
            return $this->rows($data);
        endif;
    }		
}
?>