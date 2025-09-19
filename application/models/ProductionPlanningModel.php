<?php
class ProductionPlanningModel extends MasterModel{

    public function getNextPlanNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'production_planning';
        $queryData['select'] = "MAX(plan_no) as plan_no ";
		
		$queryData['where']['production_planning.plan_date >='] = $this->startYearDate;
		$queryData['where']['production_planning.plan_date <='] = $this->endYearDate;

		$plan_no = $this->specificRow($queryData)->plan_no;
		$plan_no = (empty($this->last_plan_no))?($plan_no + 1):$plan_no;
		return $plan_no;
    }

    public function getDTRows($data){
        $data['tableName'] = 'so_trans';
        $data['select'] = "so_trans.id as so_trans_id,item_master.item_name, item_master.item_name,so_trans.qty,so_trans.dispatch_qty,(so_trans.qty - so_trans.dispatch_qty) as pending_qty, IFNULL(so_trans.cod_date,'') as cod_date,so_master.trans_number, DATE_FORMAT(so_master.trans_date,'%d-%m-%Y') as trans_date,so_master.doc_no, DATE_FORMAT(so_master.doc_date,'%d-%m-%Y') as doc_date,party_master.party_name"; 

        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $data['join']['item_master'] = "item_master.id = so_trans.item_id AND item_master.is_delete = 0";
		$data['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
       

		

		$data['where']['so_trans.trans_status'] = 3;
		$data['having'][] = 'pending_qty > 0';
     

        $data['order_by']['so_master.trans_date'] = "DESC";
        $data['order_by']['so_master.id'] = "DESC";

        $data['group_by'][] = "so_trans.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "so_master.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(so_trans.cod_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "so_trans.qty";
        $data['searchCol'][] = "so_trans.dispatch_qty";
        $data['searchCol'][] = "(so_trans.qty - so_trans.dispatch_qty)";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getPlanDTRows($data){
        $data['tableName'] = 'production_planning';
        $data['select'] = "production_planning.*,item_master.item_name, DATE_FORMAT(so_master.trans_date,'%d-%m-%Y') as trans_date,so_master.trans_number,party_master.party_name,prc_master.prc_number,prc_master.status AS prc_status"; 

        $data['leftJoin']['so_trans'] = "so_trans.id = production_planning.so_trans_id";
        $data['leftJoin']['so_master'] = "so_master.id = production_planning.so_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_planning.item_id";
		$data['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = production_planning.prc_id";
       
     
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "production_planning.plan_number";
        $data['searchCol'][] = "DATE_FORMAT(production_planning.plan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "production_planning.qty";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function savePlan($data){
        try {
			$this->db->trans_begin();
            $plan_no = $this->productionPlanning->getNextPlanNo();
            $plan_number = 'PL/'.$this->shortYear.'/'.$plan_no;
            $planData = array_reduce($data['planData'], function($planData, $plan) { $planData[$plan['item_id']][] = $plan; return $planData; }, []);
            foreach($planData As $item_id => $plan){
                $masterData = [
                    'id'=>'',
                    'prc_date'=>$data['plan_date'],
                    'item_id'=>$item_id,
                    'prc_qty'=>array_sum(array_column($plan,'plan_qty')),
                ];
                $result = $this->sop->savePRC(['masterData'=>$masterData]);
                $prc_id = $result['id'];

                foreach($plan AS $row){
                    $insertData = [
                        'id'=>'',
                        'plan_no'=>$plan_no,
                        'plan_number'=>$plan_number,
                        'prc_id'=>$prc_id,
                        'plan_date'=>$data['plan_date'],
                        'so_trans_id'=>$row['so_trans_id'],
                        'so_id'=>$row['so_id'],
                        'item_id'=>$row['item_id'],
                        'qty '=>$row['plan_qty'],
                    ];
                    $this->store("production_planning",$insertData);
                }
            }
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getProcessList($data){
        $queryData = array();          
		$queryData['tableName'] = "product_process";
		$queryData['select'] = "product_process.item_id,product_process.process_id,prc_master.id as prc_id,prc_master.prc_qty,(prc_master.prc_qty - (IFNULL(prcLog.ok_qty,0) + IFNULL(prcLog.rej_qty,0) + IFNULL(prcLog.rw_qty,0) + IFNULL(prc_challan_request.ch_qty,0) + IFNULL(rejection_log.review_qty,0))) AS pending_qty,process_master.process_name,prc_master.prc_number,item_master.item_name";

        $queryData['leftJoin']['prc_master'] = 'prc_master.item_id = product_process.item_id AND prc_master.is_delete = 0 AND prc_master.prc_type = 1';
        $queryData['leftJoin']['process_master'] = 'process_master.id = product_process.process_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = product_process.item_id';
        $queryData['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty
										, SUM((prc_log.rej_qty)) as rej_qty, 
										SUM((prc_log.rw_qty)) as rw_qty, 
										SUM(prc_log.rej_found) as rej_found,
										process_id,prc_id 
										FROM prc_log 
										WHERE is_delete = 0 AND process_by != 3
										GROUP BY prc_id,process_id
									) prcLog'] =  "prcLog.process_id = product_process.process_id AND prcLog.prc_id = prc_master.id";
        $queryData['leftJoin']['(SELECT SUM(prc_challan_request.qty- prc_challan_request.without_process_qty) as ch_qty,
                                    process_id,prc_id 
                                    FROM prc_challan_request 
                                    WHERE is_delete = 0 
                                    GROUP BY prc_id,process_id
                                ) prc_challan_request'] =  "prc_challan_request.process_id = product_process.process_id AND prc_challan_request.prc_id = prc_master.id";

        $queryData['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,
                                    rejection_log.log_id,
                                    rejection_log.prc_id,
                                    prc_log.process_id 
                                    FROM rejection_log
                                    LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id 
                                    WHERE rejection_log.is_delete = 0  
                                    GROUP BY rejection_log.prc_id,prc_log.process_id
                                ) rejection_log'] = "rejection_log.prc_id = prc_master.id AND rejection_log.process_id = product_process.process_id";

        $queryData['where_in']['product_process.process_id'] = $data['process_id'];
        $queryData['where']['item_master.machining_grade'] = $data['machining_grade'];
        $queryData['having'][] = 'pending_qty > 0';
        $queryData['where_in']['prc_master.status'] = '1,2';
        $queryData['where_in']['prc_master.prc_type'] = 1;
        return $this->rows($queryData);
    }

    public function getMcPlanDTRows($data){
        $data['tableName'] = "machine_planning";
        $data['select'] = 'machine_planning.*,prc_master.prc_number,item_master.item_name,process_master.process_name,mc.item_code AS machine_code,mc.item_name As machine_name,mc.mc_status';
        $data['leftJoin']['prc_master'] = 'prc_master.id = machine_planning.prc_id';
        $data['leftJoin']['item_master mc']= 'mc.id = machine_planning.machine_id';
        $data['leftJoin']['item_master']= 'item_master.id = machine_planning.item_id';
        $data['leftJoin']['process_master']= 'process_master.id = machine_planning.process_id';

        $data['where']['machine_planning.status'] = $data['status'];
        $data['order_by']['machine_planning.sequence'] = 'ASC';
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(machine_planning.plan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(mc.item_code,' ',mc.item_name)";
        $data['searchCol'][] = "machine_planning.sequence";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "process_master.process_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
    public function getNextSequence($data){
        $queryData = array(); 
		$queryData['tableName'] = 'machine_planning';
        $queryData['select'] = "MAX(sequence) as sequence ";
		
		$queryData['where']['machine_planning.machine_id'] = $data['machine_id'];
		$queryData['where']['machine_planning.status'] = 1;
	

		$sequence = $this->specificRow($queryData)->sequence;
		$sequence = (!empty($sequence))?($sequence + 1):1;
		return $sequence;
    }
    public function saveMachinePlan($data){
        try {
			$this->db->trans_begin();
           
            foreach($data['prc_id'] As $key => $prc_id){
                $sequence = $this->getNextSequence(['machine_id'=>$data['machine_id']]);
                $insertData = [
                    'id'=>'',
                    'plan_date'=>date("Y-m-d"),
                    'prc_id'=>$prc_id,
                    'sequence'=>$sequence,
                    'process_id'=>$data['process_id'][$key],
                    'item_id'=>$data['item_id'][$key],
                    'machine_id'=>$data['machine_id'],
                ];
                $result = $this->store("machine_planning",$insertData);
            }
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getMachinePlanning($data = []){
        $queryData['tableName'] = "machine_planning";
        $queryData['select'] = 'machine_planning.*,prc_master.prc_number,item_master.item_name,process_master.process_name,mc.item_code AS machine_code,mc.item_name AS machine_name,product_process.cycle_time';
        $queryData['leftJoin']['prc_master'] = 'prc_master.id = machine_planning.prc_id';
        $queryData['leftJoin']['item_master']= 'item_master.id = machine_planning.item_id';
        $queryData['leftJoin']['item_master mc']= 'mc.id = machine_planning.machine_id';
        $queryData['leftJoin']['process_master']= 'process_master.id = machine_planning.process_id';
        $queryData['leftJoin']['product_process'] = 'product_process.item_id = machine_planning.item_id AND product_process.process_id = machine_planning.process_id AND product_process.is_delete = 0';


        if(!empty($data['productionData'])){
            $queryData['select'] .= ",IFNULL(prcLog.ok_qty,0) as ok_qty, IFNULL(prcLog.rej_qty,0) as rej_qty, IFNULL(prcLog.rw_qty,0) as rw_qty,IFNULL(prcLog.rej_found,0) as rej_found, IFNULL(prc_challan_request.ch_qty,0) as ch_qty,IFNULL(rejection_log.review_qty,0) as review_qty,IFNULL(prevMovement.inward_qty,0) as inward_qty ";
        
        $queryData['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty
                                    , SUM((prc_log.rej_qty)) as rej_qty, 
                                    SUM((prc_log.rw_qty)) as rw_qty, 
                                    SUM(prc_log.rej_found) as rej_found,
                                    process_id,prc_id 
                                    FROM prc_log 
                                    WHERE is_delete = 0 
                                    GROUP BY prc_id,process_id
                                ) prcLog'] =  "prcLog.process_id = machine_planning.process_id AND prcLog.prc_id = machine_planning.prc_id";

        $queryData['leftJoin']['(SELECT SUM(prc_challan_request.qty- prc_challan_request.without_process_qty) as ch_qty,
                                    process_id,prc_id 
                                    FROM prc_challan_request 
                                    WHERE is_delete = 0 
                                  
                                    GROUP BY prc_id,process_id
                                ) prc_challan_request'] =  "prc_challan_request.process_id = machine_planning.process_id AND prc_challan_request.prc_id = machine_planning.prc_id";

        $queryData['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,
                                    rejection_log.log_id,
                                    rejection_log.prc_id,
                                    prc_log.process_id 
                                    FROM rejection_log
                                    LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id 
                                    WHERE rejection_log.is_delete = 0  AND 
                                    source="MFG" 
                                    GROUP BY  rejection_log.prc_id,prc_log.process_id
                                ) rejection_log'] = "rejection_log.prc_id = machine_planning.prc_id AND rejection_log.process_id = machine_planning.process_id";

                $queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as inward_qty
                                ,prc_id,next_process_id 
                                FROM prc_movement 
                                WHERE prc_movement.is_delete=0 
                                AND send_to = 1 
                                 GROUP BY prc_id,next_process_id
                            ) prevMovement']="prevMovement.next_process_id = machine_planning.process_id AND prevMovement.prc_id = machine_planning.prc_id";
        }
        if(!empty($data['machine_id'])){ $queryData['where']['machine_planning.machine_id'] = $data['machine_id']; }
        if(!empty($data['process_id'])){ $queryData['where']['machine_planning.process_id'] = $data['process_id']; }
        if(!empty($data['prc_id'])){ $queryData['where']['machine_planning.prc_id'] = $data['prc_id']; }
        if(!empty($data['status'])){ $queryData['where_in']['machine_planning.status'] = $data['status']; }
        $queryData['order_by']['machine_planning.sequence'] = 'ASC';

        $result = $this->rows($queryData);
        return $result;
    }

    public function deleteMachinePlan($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash('machine_planning',['id'=>$id],'Plan');
         
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveMachineSequence($data){
		$ids = explode(',', $data['id']);
		$i=1;
		foreach($ids as $pp_id):
			$seqData=Array("sequence"=>$i++);
			$result=$this->edit('machine_planning',['id'=>$pp_id],$seqData);
		endforeach;
    	return $result;		
	}

    public function changeMcPlanStatus($data){
        try{
            $this->db->trans_begin();
            if($data['status'] == 2){
                $result = $this->store('machine_planning',['id'=>$data['id'],'status'=>2,'start_at'=>date("Y-m-d H:i:s")]);
                $this->store("item_master",['id'=>$data['machine_id'],'mc_status'=>2]);
            }
            elseif($data['status'] == 3){
                $result = $this->store('machine_planning',['id'=>$data['id'],'status'=>3,'end_at'=>date("Y-m-d H:i:s")]);
                $this->store("item_master",['id'=>$data['machine_id'],'mc_status'=>1]);
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