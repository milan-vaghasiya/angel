<?php 
class ProductionReportModel extends MasterModel{
	private $prcMaster = "prc_master";
	private $prcLog = "prc_log";
	
	/* PRC Register */
	public function getPrcRegisterData($data){
        $queryData = array();          
		$queryData['tableName'] = "prc_master";
		
		$queryData['select'] = "prc_master.id, prc_master.prc_number,prc_master.item_id, prc_master.mfg_route, DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y') as prc_date, DATE_FORMAT(prc_master.target_date,'%d-%m-%Y') as target_date, prc_master.status, prc_master.prc_qty,prc_master.rev_no,employee_master.emp_name, IFNULL(item_master.item_code,'') as item_code,IFNULL(item_master.item_name,'') as item_name, IFNULL(party_master.party_name,'') as party_name, IFNULL(prc_detail.remark,'') as job_instruction,IFNULL(prc_movement.stored_qty,0) as ok_qty,IFNULL(rejection_log.rej_qty,0) as rej_qty,so_master.trans_number as so_no"; 
        
        $queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = prc_master.party_id";
        $queryData['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_master.id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = prc_master.created_by";
        $queryData['leftJoin']['so_trans'] = "so_trans.id = prc_master.so_trans_id"; 
        $queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id"; 
		$queryData['leftJoin']['(SELECT SUM(qty) as rej_qty,prc_id FROM rejection_log WHERE decision_type = 1 AND source="MFG" AND is_delete = 0 GROUP BY prc_id) rejection_log'] = "prc_master.id = rejection_log.prc_id";
		$queryData['leftJoin']['(SELECT SUM(qty) as stored_qty,prc_id FROM prc_movement WHERE next_process_id = 0 AND is_delete = 0 GROUP BY prc_id) prc_movement'] = "prc_master.id = prc_movement.prc_id";

		if(!empty($data['party_id'])){ $queryData['where']['prc_master.party_id'] = $data['party_id']; }
		if(!empty($data['item_id'])){ $queryData['where']['prc_master.item_id'] = $data['item_id']; }
        $queryData['customWhere'][] = "prc_master.prc_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['prc_master.prc_type'] = 1;
		$queryData['group_by'][] = "prc_master.id";
        $result = $this->rows($queryData);
        return $result;  
    }

	/* Outsource Outward Register */
	public function getOutSourceRegister($postData=[]){
		$data['tableName'] = "prc_challan_request";
		$data['select'] = "prc_challan_request.*,outsource.id as out_id,outsource.ch_number,outsource.ch_date,outsource.party_id,prc_master.prc_date,prc_master.prc_number,process_master.process_name,item_master.item_name,party_master.party_name,product_process.output_qty,,IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty";//,IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.trans_date,'') as trans_date,IFNULL(receiveLog.in_challan_no,'') as in_challan_no";

		$data['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
		$data['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";
		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
		
		$data['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_challan_request.process_id AND product_process.is_delete = 0 ";

		$data['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,process_id,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY process_id,ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id AND prc_challan_request.process_id = receiveLog.process_id";
		
        $data['where']['prc_challan_request.challan_id >'] = 0;
		$data['customWhere'][] = "outsource.ch_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'";
		if(!empty($postData['vendor_id'])){ $data['where']['outsource.party_id'] = $postData['vendor_id']; }
		if(!empty($postData['item_id'])){ $data['where']['prc_master.item_id'] = $postData['item_id']; }
		if(!empty($postData['process_id'])){ $data['where']['prc_challan_request.process_id'] = $postData['process_id']; }
		$result = $this->rows($data);
      
		return $result;
	}

	/* Outsource Inward Register */
	public function getJobInwardData($postData=[]){
		$data['tableName'] = $this->prcLog;
		$data['where']['prc_log.ref_id'] = $postData['ref_id'];
		$data['where']['prc_log.prc_id'] = $postData['prc_id'];
		$data['where_in']['prc_log.process_by'] = 3;
		$data['order_by']['prc_log.trans_date'] = 'ASC';
		$data['order_by']['prc_log.id'] = 'ASC';
		$result = $this->rows($data);
		return $result;
	}
	
	/* Production Log Sheet */
	public function getProductionLogSheet($postData=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_log";
		
		$queryData['select'] = "prc_log.*,employee_master.emp_name,shift_master.shift_name,prc_master.item_id,prc_master.prc_number, item_master.item_name, item_master.item_code,process_master.process_name,material_master.material_grade,prc_master.batch_no,prc_master.prc_qty,product_process.finish_wt,item_master.cutting_flow"; 

		$queryData['select'] .= ',SUM(prc_log.qty) as ok_qty,SUM(prc_log.rej_qty) as total_rej_qty,SUM(prc_log.rej_found) as rej_found_qty,SUM(prc_log.rw_qty) as total_rw_qty,SUM(rejection_log.review_qty) as review_qty';

		$queryData['select'] .=', IF(prc_log.process_by = 1, machine.item_code, IF(prc_log.process_by = 2,department_master.name, IF(prc_log.process_by = 3,party_master.party_name,""))) as processor_name,prev_process.finish_wt AS prev_fg_wt';
		
		$queryData['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
		$queryData['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
		
		$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = prc_log.shift_id";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
        $queryData['leftJoin']['product_process'] = "product_process.process_id = prc_log.process_id AND product_process.item_id = prc_master.item_id AND product_process.is_delete = 0";

		$queryData['leftJoin']['product_process prev_process'] = "prev_process.process_id = prc_log.process_from  AND prev_process.item_id = prc_master.item_id AND product_process.is_delete = 0";

        $queryData['leftJoin']['(SELECT SUM(qty) AS review_qty,log_id FROM rejection_log WHERE rejection_log.is_delete = 0 GROUP BY log_id )rejection_log'] = "rejection_log.log_id = prc_log.id";
		
		if(!empty($postData['operator_id'])){ $queryData['where_in']['prc_log.operator_id'] = $postData['operator_id']; }
		if(!empty($postData['item_id'])){ $queryData['where_in']['prc_master.item_id'] = $postData['item_id']; }
		if(!empty($postData['process_id'])){ $queryData['where_in']['prc_log.process_id'] = $postData['process_id']; }
		if(!empty($postData['from_date']) && !empty($postData['to_date'])){
			$queryData['customWhere'][] =' prc_log.trans_date BETWEEN "'.$postData['from_date'].'" AND "'.$postData['to_date'].'"';
		}
		elseif(!empty($postData['to_date'])){ $queryData['where']['prc_log.trans_date'] = $postData['to_date']; }
		if(!empty($postData['process_by']) && $postData['process_by'] != "All"){ $queryData['where']['prc_log.process_by'] = $postData['process_by']; }
		
        $queryData['where']['prc_log.process_id >'] = 0;
		$queryData['where']['prc_master.prc_type'] = 1;
		
		$queryData['group_by'][] = "prc_master.id,prc_log.process_id,prc_master.item_id,prc_log.operator_id,prc_log.processor_id"; 
		$queryData['order_by']['product_process.sequence'] = 'ASC'; 

		$result = $this->rows($queryData);
		
        return $result;  
    }
	
	/* WIP RM Repor*/
	public function getWIPRawMaterialData($param){ 
        $data['tableName'] = "prc_bom";
        $data['select'] = "prc_bom.*,prc_master.prc_number,prc_master.prc_qty,item_master.item_name,item_master.item_type,item_master.uom,IFNULL(product_process.output_qty,1) AS output_qty,prc_master.status,prc_detail.process_ids,prc_master.id,item_master.cutting_flow,prc_master.prc_type";
        $data['leftJoin']['prc_master'] = "prc_master.id = prc_bom.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_bom.item_id";
        $data['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_master.id";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";

		$data['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_bom.process_id AND product_process.is_delete = 0";

		if(!empty($param['production_data'])){
			$data['select'] .= ",prcLog.production_qty,prcLog.cutting_cons";
			$data['leftJoin']['(SELECT SUM(qty+rej_found) as production_qty,SUM((qty+rej_found) * wt_nos) as cutting_cons,prc_id,process_id FROM prc_log WHERE  is_delete = 0 AND trans_type = 1  GROUP BY prc_id,process_id) prcLog'] = "prc_bom.prc_id = prcLog.prc_id AND prc_bom.process_id = prcLog.process_id";
		}

		if(!empty($param['stock_data'])){
			$customWhere = (!empty($param['item_id']))?' AND stock_trans.item_id ="'.$param['item_id'].'"':'';
			$data['select'] .= ",IFNULL(stock_trans.issue_qty,0) as issue_qty,IFNULL(stock_trans.return_qty,0) as return_qty,IFNULL(stock_trans.supplier_name,'') as supplier_name,IFNULL(stock_trans.batch_no,'') as batch_no,IFNULL(stock_trans.location_id,'') as location_id,(IFNULL(stock_trans.return_qty,0) + IFNULL(scrap_stock.scrap_return,0) + IFNULL(end_pcs_stock.end_pcs,0)) as cutting_return_qty";

			$data['leftJoin']['(SELECT SUM(CASE WHEN trans_type = "SSI" THEN abs(stock_trans.qty) ELSE 0 END) as issue_qty,
								SUM(CASE WHEN trans_type = "PMR" THEN stock_trans.qty ELSE 0 END) as return_qty,
								GROUP_CONCAT(DISTINCT party_master.party_name) as supplier_name, GROUP_CONCAT(DISTINCT stock_trans.batch_no) as batch_no,
								child_ref_id,stock_trans.item_id, GROUP_CONCAT(DISTINCT stock_trans.location_id) as location_id
								FROM stock_trans
								LEFT JOIN batch_history ON batch_history.batch_no = stock_trans.batch_no 
								LEFT JOIN party_master ON batch_history.party_id = party_master.id 
								WHERE stock_trans.is_delete=0 AND stock_trans.trans_type IN("SSI","PMR") '.$customWhere.' GROUP BY stock_trans.child_ref_id,stock_trans.item_id) stock_trans']="stock_trans.child_ref_id = prc_bom.prc_id AND prc_bom.item_id = stock_trans.item_id";

			//Join For Returned Scrap
			$data['leftJoin']['(SELECT SUM(stock_trans.qty) as scrap_return,
						child_ref_id,stock_trans.item_id
						FROM stock_trans
						WHERE stock_trans.is_delete=0
							AND stock_trans.trans_type = "PMR"
						GROUP BY stock_trans.child_ref_id,stock_trans.item_id
					) scrap_stock'] = 'scrap_stock.child_ref_id = prc_bom.prc_id AND material_master.scrap_group = scrap_stock.item_id';
			
			// Join For Retured End Pcs
			$data['leftJoin']['(SELECT SUM(end_piece_return.qty) as end_pcs,
						prc_id,end_piece_return.item_id
						FROM end_piece_return
						WHERE end_piece_return.is_delete=0
						GROUP BY end_piece_return.prc_id,end_piece_return.item_id
					) end_pcs_stock'] = 'end_pcs_stock.prc_id = prc_bom.prc_id AND end_pcs_stock.item_id = prc_bom.item_id';
		}
		if(!empty($param['item_id'])){ $data['where']['prc_bom.item_id'] = $param['item_id']; }

		return $this->rows($data);
    }

	public function getCuttingPrcLogData($param = []){
        $data['tableName'] = 'prc_log';
        $data['select'] = 'prc_log.*,prc_master.prc_number,prc_master.prc_date,prc_master.batch_no,ope.emp_name as operator_name,machine.item_name as machine_name, machine.item_code as machine_code,material_master.material_grade,IFNULL(im.item_name,"") as item_name,IFNULL(im.item_code,"") as item_code, im.cut_rate, prc_detail.cutting_dia, prc_detail.cut_weight, prc_detail.cutting_length';
        
        $data['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
        $data['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
        $data['leftJoin']['employee_master ope'] = "ope.id = prc_log.operator_id";
		$data['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
		$data['leftJoin']['material_master'] = "material_master.id = im.grade_id";
		$data['leftJoin']['prc_detail'] = 'prc_detail.prc_id = prc_master.id';

        if(!empty($param['id'])){ $data['where']['prc_master.id'] = $param['id']; }

		if(!empty($param['prc_type'])){ $data['where']['prc_master.prc_type'] = $param['prc_type']; }
        
		if(!empty($param['item_id'])){ $data['where']['prc_master.item_id'] = $param['item_id']; }
		
		if(!empty($param['operator_id'])){ $data['where']['prc_log.operator_id'] = $param['operator_id']; }

        if(!empty($param['from_date']) && !empty($param['to_date'])){
            $data['customWhere'][] = "prc_log.trans_date BETWEEN '".$param['from_date']."' AND '".$param['to_date']."'";
        }
		
		$data['order_by']['prc_master.prc_number'] = "ASC";
        if(!empty($param['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
    }

	public function getParetoAnalysisData($data = []){
		$queryData['tableName'] = "prc_log";
		$queryData['select'] = 'prc_log.trans_date,SUM(qty) AS total_ok_qty,SUM(rej_qty) AS total_rej_qty,SUM(rw_qty) AS total_rw_qty,item_master.item_code,item_master.item_name,process_master.process_name';
		$queryData['leftJoin']['prc_master'] = 'prc_master.id = prc_log.prc_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = prc_log.process_id';

		if(!empty($data['item_id'])){ $queryData['where_in']['prc_master.item_id'] = $data['item_id']; }
		if(!empty($data['process_id'])){ $queryData['where_in']['prc_log.process_id'] = $data['process_id']; }
		if($data['process_by'] == 1){ $queryData['where']['prc_log.process_by !='] = 3; }
		else{$queryData['where']['prc_log.process_by'] = 3;}
		$queryData['where']['prc_master.prc_type'] = 1;
		$queryData['customWhere'][] =' prc_log.trans_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'"';
		$queryData['group_by'][] = 'prc_master.item_id,prc_log.process_id';
		$queryData['order_by']['item_master.item_code'] = 'ASC';
		$queryData['order_by']['process_master.process_name'] = 'ASC';
		return $this->rows($queryData);
	}

	public function getParetoAnalysisRejData($data = []){
		$queryData['tableName'] = "rejection_log";
		$queryData['select'] = 'SUM(rejection_log.qty) AS rej_qty,rejection_comment.remark AS rej_reason';
		$queryData['leftJoin']['prc_log'] = 'prc_log.id = rejection_log.log_id';
		$queryData['leftJoin']['prc_master'] = 'prc_master.id = prc_log.prc_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = prc_log.process_id';
		$queryData['leftJoin']['rejection_comment'] = 'rejection_comment.id = rejection_log.rr_reason';

		if(!empty($data['item_id'])){ $queryData['where_in']['prc_master.item_id'] = $data['item_id']; }
		if(!empty($data['process_id'])){ $queryData['where_in']['prc_log.process_id'] = $data['process_id']; }
		if($data['process_by'] == 1){ $queryData['where']['prc_log.process_by !='] = 3; }
		else{$queryData['where']['prc_log.process_by'] = 3;}
		$queryData['where']['prc_master.prc_type'] = 1;
		$queryData['where']['rejection_log.decision_type'] = 1;
		$queryData['customWhere'][] =' prc_log.trans_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'"';
		$queryData['group_by'][] = 'rejection_log.rr_reason';

		return $this->rows($queryData);
	}

	public function getPoorQualitydata($data){
		$queryData['tableName'] = "prc_log";
		$queryData['select'] = 'prc_log.prc_id,prc_master.item_id,
								SUM(CASE WHEN process_master.process_type = 2 AND prc_log.process_by != 3 THEN prc_log.rej_qty ELSE 0 END) AS ih_forge_qty,
								SUM(CASE WHEN process_master.process_type = 2 AND prc_log.process_by = 3 THEN prc_log.rej_qty ELSE 0 END) AS v_forge_qty,
								SUM(CASE WHEN process_master.process_type = 1 AND prc_log.process_by != 3 THEN prc_log.rej_qty ELSE 0 END) AS ih_mc_qty,
								SUM(CASE WHEN process_master.process_type = 1 AND prc_log.process_by = 3 THEN prc_log.rej_qty ELSE 0 END) AS v_mc_qty,

								SUM( CASE WHEN process_master.process_type = 2 AND prc_log.process_by != 3 
									THEN ( prc_log.rej_qty * ( SELECT SUM(CASE WHEN processCost.uom = "NOS"  THEN processCost.process_cost ELSE (processCost.process_cost * processCost.finish_wt) END ) FROM product_process processCost WHERE item_id = prc_master.item_id AND processCost.is_delete = 0 AND processCost.sequence <=  product_process.sequence ) ) 
									ELSE 0 END ) AS ih_forge_cost,

								SUM( CASE WHEN process_master.process_type = 2 AND prc_log.process_by = 3 
										THEN prc_log.rej_qty * (( SELECT SUM(CASE WHEN processCost.uom = "NOS"  THEN processCost.process_cost ELSE (processCost.process_cost * processCost.finish_wt) END ) FROM product_process processCost WHERE item_id = prc_master.item_id AND processCost.is_delete = 0 AND processCost.sequence <= product_process.sequence ))
									 ELSE 0 END ) AS v_forge_cost,

								SUM( CASE WHEN process_master.process_type = 1 AND prc_log.process_by != 3 
										THEN (( prc_log.rej_qty * ( SELECT SUM(CASE WHEN processCost.uom = "NOS"  THEN processCost.process_cost ELSE
													(processCost.process_cost * processCost.finish_wt) END ) FROM product_process processCost WHERE item_id = prc_master.item_id  AND processCost.is_delete = 0 AND processCost.sequence <= product_process.sequence ))
									) ELSE 0 END ) AS ih_mc_cost,

								SUM( CASE WHEN process_master.process_type = 1 AND prc_log.process_by = 3 
										THEN( ( prc_log.rej_qty *( SELECT SUM(CASE WHEN processCost.uom = "NOS"  THEN processCost.process_cost ELSE (processCost.process_cost * processCost.finish_wt) END ) FROM product_process processCost WHERE item_id = prc_master.item_id AND processCost.is_delete = 0 AND  processCost.sequence <= product_process.sequence ) ))
										 ELSE 0 END ) AS v_mc_cost										 
								,item_master.item_code,item_master.item_name,item_master.cutting_flow,item_master.cut_rate'; 


		$queryData['leftJoin']['prc_master'] = 'prc_master.id = prc_log.prc_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = prc_log.process_id';

		

		$queryData['leftJoin']['prc_bom'] = 'prc_bom.prc_id = prc_master.id AND prc_bom.is_delete=0';

		$queryData['leftJoin']['product_process'] = 'product_process.item_id = prc_master.item_id AND product_process.process_id = prc_log.process_id AND product_process.is_delete = 0';

		if(!empty($data['item_id'])){ $queryData['where_in']['prc_master.item_id'] = $data['item_id']; }
		if(!empty($data['process_id'])){ $queryData['where_in']['prc_log.process_id'] = $data['process_id']; }

		$queryData['where']['prc_log.rej_qty >'] = 0;
		$queryData['where']['prc_master.prc_type'] = 1;
		$queryData['customWhere'][] =' prc_log.trans_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'"';
		$queryData['group_by'][] = 'prc_log.prc_id';
		return $this->rows($queryData);
	}

	public function getPrcCutWeight($data){
		$queryData['tableName'] = 'prc_master';
		$queryData['select'] = 'prc_master.id As prc_id,(SELECT price FROM grn_trans WHERE grn_trans.is_delete = 0 AND grn_trans.batch_no = prc_master.batch_no ORDER BY grn_trans.id DESC LIMIT 1) AS mt_price,
								(SELECT prc_detail.cut_weight FROM stock_trans LEFT JOIN prc_master cutting_prc ON cutting_prc.prc_number = stock_trans.batch_no  LEFT JOIN prc_detail ON prc_detail.prc_id = cutting_prc.id WHERE stock_trans.trans_type = "SSI" AND stock_trans.is_delete = 0 AND stock_trans.child_ref_id = prc_master.id LIMIT 1) AS cut_weight,
								prc_bom.ppc_qty';

		$queryData['leftJoin']['prc_bom'] = 'prc_bom.prc_id = prc_master.id AND prc_bom.is_delete=0';
		$queryData['where_in']['prc_master.id'] = $data['prc_id'];
		return $this->rows($queryData);
	}

	public function getOeeRegister($postData){
        $queryData = array();
        $queryData['tableName'] = "prc_log";
        $queryData['select'] = "prc_log.id,employee_master.emp_name,machine_master.item_code AS machine_code,machine_master.item_name as machine_name,item_master.item_name as product_name,prc_master.prc_number,process_master.process_name,
		SUM(CASE WHEN prc_log.trans_type = 1 THEN prc_log.production_time ELSE 0 END) as total_production_time,
		product_process.cycle_time,
		SUM(CASE WHEN prc_log.trans_type = 1 AND prc_master.prc_type = 1 THEN prc_log.qty ELSE 0 END) as total_ok_qty,
		SUM(CASE WHEN prc_log.trans_type = 2  OR prc_master.prc_type = 3 THEN prc_log.qty ELSE 0 END) as rw_ok_qty,
		SUM(CASE WHEN prc_log.trans_type = 1 THEN prc_log.rej_qty ELSE 0 END) as total_rej_qty,
		SUM(CASE WHEN prc_log.trans_type = 1 THEN prc_log.rw_qty ELSE 0 END) as total_rw_qty,
		SUM(CASE WHEN prc_log.trans_type = 2  OR prc_master.prc_type = 3 THEN prc_log.production_time ELSE 0 END) as rw_production_time,
		prc_log.operator_id,prc_log.processor_id,prc_log.process_id,prc_master.item_id,prc_log.prc_id";

        $queryData['leftJoin']['employee_master'] = "prc_log.operator_id = employee_master.id";
        $queryData['leftJoin']['process_master'] = "prc_log.process_id = process_master.id";
        $queryData['leftJoin']['prc_master'] = "prc_log.prc_id = prc_master.id";
        $queryData['leftJoin']['item_master'] = "prc_master.item_id = item_master.id";
        $queryData['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_log.process_id AND product_process.is_delete = 0";
        $queryData['leftJoin']['item_master as machine_master'] = "prc_log.processor_id = machine_master.id";

		$queryData['customWhere'][] = "prc_log.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'";
        $queryData['where']['prc_log.process_id > '] = 0;
        $queryData['where']['prc_log.process_by'] = 1;
		if(!empty($postData['shift_id'])){
        	$queryData['where']['prc_log.shift_id'] = $postData['shift_id'];
		}

        $queryData['group_by'][] = "prc_log.operator_id";
        $queryData['group_by'][] = "prc_log.processor_id";
        $queryData['group_by'][] = "prc_log.process_id";
        $queryData['group_by'][] = "prc_log.prc_id";

        $result = $this->rows($queryData);
        return $result;
    }
	
}
?>