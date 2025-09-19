<?php
class OutsourceModel extends MasterModel{

    public function getNextChallanNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'outsource';
        $queryData['select'] = "MAX(ch_no) as ch_no ";	
		$queryData['where']['outsource.ch_date >='] = $this->startYearDate;
		$queryData['where']['outsource.ch_date <='] = $this->endYearDate;

		$ch_no = $this->specificRow($queryData)->ch_no;
		$ch_no = $ch_no + 1;
		return $ch_no;
    }

    public function getDTRows($data){
        $style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "prc_challan_request";
		$data['select'] = "prc_challan_request.*,outsource.id as out_id,outsource.ch_number,outsource.ch_date,outsource.party_id,prc_master.prc_date,prc_master.prc_number,prc_master.batch_no,process_master.process_name,item_master.item_name,IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty,party_master.party_name,product_process.output_qty,outsource.ewb_status,outsource.eway_bill_no,nextParty.party_name AS next_party_name";
        $data['select'] .=",(SELECT GROUP_CONCAT(pm.process_name SEPARATOR '<hr ".$style.">')
                            FROM process_master pm
                            WHERE FIND_IN_SET(pm.id, prc_challan_request.challan_process)
                            ) AS process_names,
                            (SELECT GROUP_CONCAT(pm.process_name SEPARATOR '<hr ".$style.">')
                            FROM process_master pm
                            WHERE FIND_IN_SET(pm.id, prc_challan_request.next_process_ids)
                            ) AS next_process_names";
		$data['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
		$data['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";
        $data['leftJoin']['party_master nextParty'] = "nextParty.id = prc_challan_request.next_process_by";

		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
        $data['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,process_id,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY process_id,ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id AND prc_challan_request.process_id = receiveLog.process_id";
		$data['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_challan_request.process_id AND product_process.is_delete = 0 ";
        
		$data['where']['prc_challan_request.challan_id >'] = 0;
		$data['where']['prc_challan_request.request_ref_id'] = 0;
        
		if ($data['status'] == 0) :
            $data['having'][] = "((prc_challan_request.qty - without_process_qty) * product_process.output_qty) > (ok_qty+rej_qty)";
        endif;
        if ($data['status'] == 1) :
            $data['having'][] = "((prc_challan_request.qty - without_process_qty) * product_process.output_qty) - (ok_qty+rej_qty) <= 0";
        endif;
        $data['group_by'][] = 'prc_challan_request.id';
		$data['order_by']['outsource.ch_date'] = 'DESC';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(outsource.ch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "outsource.ch_number";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "prc_master.batch_no";
        $data['searchCol'][] = "prc_challan_request.qty";
        $data['searchCol'][] = "ok_qty";
        $data['searchCol'][] = "rej_qty";
        $data['searchCol'][] = "prc_challan_request.without_process_qty";
        $data['searchCol'][] = "(prc_challan_request.qty - (ok_qty+rej_qty+without_process_qty))";
        $data['searchCol'][] = "prc_challan_request.price";
        $data['searchCol'][] = "(prc_challan_request.price * prc_challan_request.qty)";
        /*
		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "nextProcess.process_name";
        $data['searchCol'][] = "nextParty.party_name";
		*/
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        
        return $result;
    }

    public function save($data){
		try {
			$this->db->trans_begin();
            $ch_prefix = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
            $ch_no = $this->outsource->getNextChallanNo();
            $challanData = [
                'id'=>'',
                'party_id'=>$data['party_id'],
                'ch_date'=>$data['ch_date'],
                'delivery_date'=>$data['delivery_date'],
                'ch_no'=>$ch_no,
                'ch_number'=>$ch_prefix.$ch_no,
                'vehicle_no'=>$data['vehicle_no'],
                'transport_id'=>$data['transport_id'],
                'remark'=>$data['remark']
            ];
            $result = $this->store('outsource',$challanData);
            foreach($data['id'] as $key=>$id){
                $chData = [
                    'id'=>$id,
                    'qty'=>$data['ch_qty'][$key],
                    'price'=>$data['price'][$key],
					'material_value'=>$data['material_value'][$key],
					'material_wt'=>$data['material_wt'][$key],
					'material_price'=>$data['material_price'][$key],
					'pre_process_cost'=>$data['pre_process_cost'][$key],
					'challan_process'=>implode(",",$data['process_ids'][$id]),
					'next_process_ids'=>$data['next_process_ids'][$key],
                    'challan_id'=>$result['id'],
                ];
                $this->store('prc_challan_request',$chData, 'Challan Request');
            }
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function delete($id){
        try {
			$this->db->trans_begin();
            $chData = $this->sop->getChallanRequestData(['challan_id'=>$id,'challan_receive'=>1]);
            foreach($chData as $row){
                if(($row->ok_qty+$row->rej_qty) > 0){
                    return ['status'=>0,'message'=>'You can not delete this Challan'];
                }
                $this->store("prc_challan_request",['id'=>$row->id,'challan_id'=>0,'qty'=>$row->old_qty]);
            }
			$result = $this->trash('outsource', ['id'=>$id], 'Challan');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getOutSourceData($data){
		$data['tableName'] = 'outsource';
		$data['select'] = 'outsource.*,employee_master.emp_name,party_master.party_name,party_master.party_address,party_master.gstin, transport_master.transport_name,party_master.party_mobile';
		$data['leftJoin']['employee_master'] = 'employee_master.id = outsource.created_by';
		$data['leftJoin']['party_master'] = 'party_master.id = outsource.party_id';
		$data['leftJoin']['transport_master'] = 'transport_master.id = outsource.transport_id';
		$data['where']['outsource.id'] = $data['id'];
		return $this->row($data);
	}

    public function saveLog($data){
		try {
			$this->db->trans_begin();
            $chData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'single_row'=>1]);
            $log_id = "";
            foreach($data['process_id'] As $key=>$process_id){
                if($key == 0){
                    //First Process receive
                    $logData=[
                                'id'=>'',
                                'trans_type' => $data['trans_type'],
                                'process_from' => $data['process_from'],
                                'prc_id' => $data['prc_id'],
                                'process_id' => $process_id,
                                'ref_id' => !empty($data['ref_id'])?$data['ref_id']:'',
                                'ref_trans_id' => !empty($data['ref_trans_id'])?$data['ref_trans_id']:'',
                                'trans_date' => $data['trans_date'],
                                'qty' => !empty($data['ok_qty'][$key])?$data['ok_qty'][$key]:0,
                                'rej_found' =>  !empty($data['rej_found'][$key])?$data['rej_found'][$key]:0,
                                'without_process_qty' =>  !empty($data['without_process_qty'][$key])?$data['without_process_qty'][$key]:0, // Used in outsource Receive Form
                                'in_challan_no' => !empty($data['in_challan_no'])?$data['in_challan_no']:0,
                                'process_by' => $data['process_by'],
                                'processor_id' =>!empty($data['processor_id'])?$data['processor_id']:0
                            ];
                    $result = $this->sop->savePRCLog($logData);
                    $log_id = $result['id'];
                }else{
                    //Prev Process Movement
                    if($data['ok_qty'][($key-1)] > 0){
                        $movementData=[
                                    'id'=>'',
                                    'auto_log_id'=>$log_id,
                                    'move_type' => $data['trans_type'],
                                    'move_from' => $data['trans_type'],
                                    'process_from' => (!empty($data['process_id'][($key-2)]))?$data['process_id'][($key-2)]:$data['process_from'],
                                    'prc_id' => $data['prc_id'],
                                    'process_id' => $data['process_id'][($key-1)],
                                    'next_process_id' => $process_id,
                                    'trans_date' => $data['trans_date'],
                                    'qty' => !empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0,
                                ];
                        $this->sop->savePRCMovement($movementData);

                        //Auto Accept
                        $acceptData = [
                                        'id' => '',
                                        'auto_log_id'=>$log_id,
                                        'accepted_process_id' => $process_id,
                                        'prc_id' => $data['prc_id'],
                                        'trans_type' => $data['trans_type'],
                                        'process_from' =>$data['process_id'][($key-1)],
                                        'accepted_qty' => (!empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0),
                                        'trans_date' => $data['trans_date'],
                                        'created_by' => $this->loginId,
                                        'created_at' => date("Y-m-d H:i:S")
                                    ];
                        $this->sop->saveAcceptedQty($acceptData);

                        //Auto Challan
                        
                        $challanData=[
                                        'id' => '',
                                        'auto_log_id'=>$log_id,
                                        'request_ref_id'=>$data['ref_trans_id'],
                                        'prc_id' => $data['prc_id'],
                                        'process_id' => $process_id,
                                        'trans_type' => $data['trans_type'],
                                        'process_from' =>$data['process_id'][($key-1)],
                                        'trans_date' =>  $data['trans_date'],
                                        'qty' => (!empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0),
                                        'old_qty' => (!empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0),
                                        'challan_process'=>$chData->challan_process,
                                        'next_process_ids'=>$chData->next_process_ids,
                                        'challan_id'=>$chData->challan_id,
                                    ];
                        $chResult = $this->store('prc_challan_request',$challanData);
                        $mtValue = $this->getMaterialValue(['id'=>$chResult['id']]);
                        $this->store('prc_challan_request',['id'=>$chResult['id'],'material_value'=>($mtValue['cost_per_pcs'] * $data['ok_qty'][($key-1)]),'price'=>$mtValue['process_cost']]);
                        //Auto Receive
                        $logData=[
                                    'id'=>'',
                                    'trans_type' => $data['trans_type'],
                                    'process_from' => $data['process_id'][($key-1)],
                                    'prc_id' => $data['prc_id'],
                                    'process_id' => $process_id,
                                    'auto_log_id'=>$log_id,
                                    'ref_id' => !empty($data['ref_id'])?$data['ref_id']:'',
                                    'ref_trans_id' => $chResult['id'],
                                    'trans_date' => $data['trans_date'],
                                    'qty' => !empty($data['ok_qty'][$key])?$data['ok_qty'][$key]:0,
                                    'rej_found' =>  !empty($data['rej_found'][$key])?$data['rej_found'][$key]:0,
                                    'without_process_qty' =>  !empty($data['without_process_qty'][$key])?$data['without_process_qty'][$key]:0, // Used in outsource Receive Form
                                    'in_challan_no' => !empty($data['in_challan_no'])?$data['in_challan_no']:0,
                                    'process_by' => $data['process_by'],
                                    'processor_id' =>!empty($data['processor_id'])?$data['processor_id']:0
                                ];
                        $result = $this->sop->savePRCLog($logData);
                    }
				}
				//IF Next Process is set for outsource
				if($key == (count($data['process_id']) - 1) && $data['ok_qty'][$key] > 0){
					$nextProcessArray = explode(",",$chData->next_process_ids);
					$next_process_id = $nextProcessArray[0];
					if($chData->next_process_by > 0 && $next_process_id > 0){
						
						//Auto Movement
						$movementData=[
										'id'=>'',
										'auto_log_id'=>$log_id,
										'move_type' => $data['trans_type'],
										'move_from' => $data['trans_type'],
										'process_from' => (!empty($data['process_id'][($key-1)]))?$data['process_id'][($key-1)]:$data['process_from'],
										'prc_id' => $data['prc_id'],
										'process_id' => $process_id,
										'next_process_id' => $next_process_id,
										'trans_date' => $data['trans_date'],
										'qty' => $data['ok_qty'][$key],
									];
						$this->sop->savePRCMovement($movementData);

						//Auto Accept
						$acceptData = [
										'id' => '',
										'auto_log_id'=>$log_id,
										'accepted_process_id' => $next_process_id,
										'prc_id' => $data['prc_id'],
										'trans_type' => $data['trans_type'],
										'process_from' =>$process_id,
										'accepted_qty' => $data['ok_qty'][$key],
										'trans_date' => $data['trans_date'],
										'created_by' => $this->loginId,
										'created_at' => date("Y-m-d H:i:S")
									];
						$this->sop->saveAcceptedQty($acceptData);
						//Auto Challan Master
						$ch_prefix = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
						$ch_no = $this->outsource->getNextChallanNo();
						$outsourceData = [
							'id'=>'',
							'party_id'=>$chData->next_process_by,
							'ref_id'=>$log_id,
							'ch_date'=>$data['trans_date'],
							/* 'delivery_date'=>$data['delivery_date'], */
							'ch_no'=>$ch_no,
							'ch_number'=>$ch_prefix.$ch_no,
						];
						$outsourceResult = $this->store('outsource',$outsourceData);

						//Auto Challan Trans
						$process = explode(",",$chData->process_ids);
						$lastProcessKey = array_search($nextProcessArray[(count($nextProcessArray)-1)],$process);
			   
						$nextProcess= (!empty($process[$lastProcessKey +1])?$process[$lastProcessKey +1]:0);

						$challanData=[
									'id' => '',
									'auto_log_id'=>$log_id,
									'prc_id' => $data['prc_id'],
									'process_id' => $next_process_id,
									'trans_type' => $data['trans_type'],
									'process_from' =>$process_id,
									'trans_date' =>  $data['trans_date'],
									'qty' => $data['ok_qty'][$key],
									'old_qty' => $data['ok_qty'][$key],
									'challan_process'=>$chData->next_process_ids,
									'next_process_ids'=>$nextProcess,
									'challan_id'=>$outsourceResult['id'],
								];
						$chResult = $this->store('prc_challan_request',$challanData);
						$mtValue = $this->getMaterialValue(['id'=>$chResult['id']]);
						$this->store('prc_challan_request',['id'=>$chResult['id'],'material_value'=>($mtValue['cost_per_pcs'] * $data['ok_qty'][$key]),'price'=>$mtValue['process_cost']]);
					}
				}
			
                
            }
            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}

    }

    public function deleteLog($param){
        try {
			$this->db->trans_begin();
            
            $logData = $this->sop->getProcessLogList(['customWhere'=>' (prc_log.id = '.$param['id'].' OR prc_log.auto_log_id = '.$param['id'].')','rejection_review_data'=>1,'outsource_without_process'=>1]);

			$prcData = $this->sop->getPRC(['id'=>$logData[0]->prc_id]);
			if(!in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
            
			if(!empty($logData)){
                foreach($logData AS $row){
                    if ($row->review_qty > 0){
                        return ['status'=>0,'message'=>'You can not delete this Log. You have to delete rejection review first'];
                    }
                    if($row->without_process_qty > 0){
                        $prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$row->process_id,'prc_id'=>$row->prc_id,'log_data'=>1,'movement_data'=>1,'log_process_by'=>1,'pending_accepted'=>1,'single_row'=>1,'process_from'=>$row->process_from,'move_type'=>$row->trans_type]); 
                        $in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
                        $ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
                        $rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
                        $rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
                        $rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
                        $pendingReview = $rej_found - $prcProcessData->review_qty;
                        $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$prcProcessData->ch_qty);

                        if($row->without_process_qty > $pending_production){
                            return ['status'=>0,'message'=>'You can not delete this Log. Production of returned qty without process has been done. '];
                        }
                        $setData = array();
                        $setData['tableName'] = 'prc_challan_request';
                        $setData['where']['id'] = $row->ref_trans_id;
                        $setData['set']['without_process_qty'] = 'without_process_qty, - ' . $row->without_process_qty;
                        $this->setValue($setData);
                        $this->trash('without_process_log',['log_id'=>$row->id]);
                    }

                    if($row->id == $param['last_log_id']){
                        $movementData =  $this->sop->getPRCProcessList(['prc_id'=>$row->prc_id,'process_id'=>$row->process_id,'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
				        $pending_movement = $movementData->ok_qty - $movementData->movement_qty;

                        if(($row->qty > $pending_movement) ){
                            return ['status'=>0,'message'=>'You can not delete this Log. Qty is sent to next process'];
                        }
                    }

                    $this->trash('prc_log_detail',['log_id'=>$row->id]);
                }
                $this->trash("prc_movement",['auto_log_id'=>$param['id']]);
                $this->trash("prc_accept_log",['auto_log_id'=>$param['id']]);
                $this->trash("prc_challan_request",['auto_log_id'=>$param['id']]);
                $this->trash("prc_log",['auto_log_id'=>$param['id']]);
				$this->trash('prc_log_detail',['log_id'=>$param['id']]);
				$result = $this->trash('prc_log',['id'=>$param['id']]);
				
		
			}else{
				$result = ['status'=>0,'message'=>'Log already deleted'];
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

    public function saveNextProcessSchedule($data){
        try {
			$this->db->trans_begin();
            $data['next_process_ids'] = implode(",",$data['next_process_ids']);
            $result = $this->store("prc_challan_request",$data);
           
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getMaterialValueOld($data){
        $challanData = $this->sop->getChallanRequestData(['id'=>$data['id'],'single_row'=>1]);
        $prcBom = $this->sop->getPrcBomData(['prc_id'=>$challanData->prc_id,'stock_data'=>1,'single_row'=>1]);
        if($challanData->cutting_flow == 1){
            $item_id = $prcBom->item_id;
            $rmPriceData = $this->item->getItem(['id'=>$item_id]);
            if($rmPriceData->uom == 'KGS'){
                $material_wt = $prcBom->ppc_qty;
            }else{
                $material_wt = $rmPriceData->wt_pcs;
            }
           
        }else{
            $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'single_row'=>1]);
            $item_id = $cuttingBatch->item_id;
             $rmPriceData = $this->item->getItem(['id'=>$item_id]);
            $material_wt = $cuttingBatch->ppc_qty;
        }
        $mtPrice = $this->gateInward->getLastPurchasePrice(['item_id'=>$item_id]);
        $processArray = explode(",",$challanData->process_ids);
        $currentProcessKey = array_search($challanData->process_id,$processArray);
        $prevProcess = [];
        foreach($processArray as $key=>$process){
            if($key <= $currentProcessKey){
                $prevProcess[] = $process;
            }
        }
        $processData = $this->item->getProductProcessList(['item_id'=>$challanData->item_id,'process_id'=>$prevProcess,'process_cost_sum'=>1,'single_row'=>1]);
        /* $rmPriceData = $this->item->getItem(['id'=>$item_id]); */
		$rmPrice = (!empty($rmPriceData->price)) ? $rmPriceData->price : 0;
        $material_price = ((!empty($mtPrice->price))?($mtPrice->price * $material_wt):0);
        $process_cost = ((!empty($processData->total_process_cost))?($processData->total_process_cost):0)+$rmPrice;
        $cost_per_pcs = $material_price + $process_cost;
        return ['status'=>1,'cost_per_pcs'=>round($cost_per_pcs,3),'mtPrice'=>$mtPrice,'process_cost'=>$challanData->process_cost];
    }

    public function getMaterialValue(){
        $data = $this->input->post();
        $challanData = $this->sop->getChallanRequestData(['id'=>$data['id'],'single_row'=>1]);
        $prcBom = $this->sop->getPrcBomData(['prc_id'=>$challanData->prc_id,'stock_data'=>1,'single_row'=>1]);
		
        if($challanData->cutting_flow == 1){
            $item_id = $prcBom->item_id;
            $rmPriceData = $this->item->getItem(['id'=>$item_id]);
            if($rmPriceData->uom == 'KGS'){
                $material_wt = $prcBom->ppc_qty;
            }else{
                $material_wt = $rmPriceData->wt_pcs;
            }
            $mtPrice = $this->gateInward->getLastPurchasePrice(['item_id'=>$item_id]);
			$material_price = ((!empty($mtPrice->price))?($mtPrice->price * $prcBom->ppc_qty):0);
        }else{
            $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'single_row'=>1]);
            $item_id = $cuttingBatch->item_id;
            $rmPriceData = $this->item->getItem(['id'=>$item_id]);
            $material_wt = $cuttingBatch->ppc_qty;
            $mtPrice = $this->gateInward->getLastPurchasePrice(['item_id'=>$item_id]);
			$material_price = ((!empty($mtPrice->price))?($mtPrice->price * $material_wt):0);
        }
       
        $processArray = explode(",",$challanData->process_ids);
        $currentProcessKey = array_search($challanData->process_id,$processArray);
        $prevProcess = [];$pre_process_cost = 0;
        $processCostError = '';
        foreach($processArray as $key=>$process){
            if($key < $currentProcessKey){
                $prevProcess[] = $process;
                $processData = $this->item->getProductProcessList(['item_id'=>$challanData->item_id,'process_id'=>$process,'process_cost_sum'=>1,'single_row'=>1]);
                
                if(!empty($processData->total_process_cost) && $processData->total_process_cost <= 0){
                    $processCostError = 'Enter cost in the previous process.';
                }else{
                    $pre_process_cost += $processData->total_process_cost;
                }
                
            }
        }
        // $processData = $this->item->getProductProcessList(['item_id'=>$challanData->item_id,'process_id'=>$prevProcess,'process_cost_sum'=>1,'single_row'=>1]);
       
		$rmPrice = (!empty($rmPriceData->price)) ? $rmPriceData->price : 0;
        
        $process_cost = ((!empty($pre_process_cost))?($pre_process_cost):0)+$rmPrice;
        $cost_per_pcs = $material_price + $process_cost;
        return['status'=>1,'cost_per_pcs'=>round($cost_per_pcs,3),'material_wt'=>$material_wt,'material_price'=>((!empty($mtPrice->price))?$mtPrice->price:0),'pre_process_cost'=>$process_cost,'processCostError'=>$processCostError];
    }

    /** For Vendor Desk */
    public function getVendorDTRows($data){
        $style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "prc_challan_request";
		$data['select'] = "prc_challan_request.*,outsource.id as out_id,outsource.ch_number,outsource.ch_date,outsource.party_id,prc_master.prc_date,prc_master.prc_number,prc_master.batch_no,item_master.item_name,IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty,product_process.output_qty,outsource.ewb_status,outsource.eway_bill_no,nextParty.party_name AS next_party_name";
        $data['select'] .=",(SELECT GROUP_CONCAT(pm.process_name SEPARATOR '<hr ".$style.">')
                            FROM process_master pm
                            WHERE FIND_IN_SET(pm.id, prc_challan_request.challan_process)
                            ) AS process_names";
		$data['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
        $data['leftJoin']['party_master nextParty'] = "nextParty.id = prc_challan_request.next_process_by";

		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
        $data['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,process_id,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id AND prc_challan_request.process_id = receiveLog.process_id";
		$data['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_challan_request.process_id AND product_process.is_delete = 0 ";
        
		$data['where']['prc_challan_request.challan_id >'] = 0;
		$data['where']['prc_challan_request.request_ref_id'] = 0;
		$data['where']['outsource.party_id'] = $this->partyId;
        
		if ($data['status'] == 0) :
            $data['having'][] = "((prc_challan_request.qty - without_process_qty) * product_process.output_qty) > (ok_qty+rej_qty)";
        endif;
        if ($data['status'] == 1) :
            $data['having'][] = "((prc_challan_request.qty - without_process_qty) * product_process.output_qty) - (ok_qty+rej_qty) <= 0";
        endif;
        $data['group_by'][] = 'prc_challan_request.id';
		$data['order_by']['outsource.ch_date'] = 'DESC';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(outsource.ch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "outsource.ch_number";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "prc_master.batch_no";
        $data['searchCol'][] = "prc_challan_request.qty";
        $data['searchCol'][] = "(ok_qty+rej_qty+without_process_qty)";
        $data['searchCol'][] = "(prc_challan_request.qty - (ok_qty+rej_qty+without_process_qty))";
        $data['searchCol'][] = "nextParty.party_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        
        return $result;
    }

    public function acceptChallan($data){
        try {
			$this->db->trans_begin();
            if($data['status'] == 1){
                $accepted_at = date("Y-m-d H:i:s");
            }else{
                $accepted_at = NULL;
            }
            $result = $this->store("prc_challan_request",['id'=>$data['id'],'accepted_at'=>$accepted_at]);
           
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
}
?>