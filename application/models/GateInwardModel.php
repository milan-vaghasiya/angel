<?php
class GateInwardModel extends masterModel{
    private $grn_master = "grn_master";
    private $grn_trans = "grn_trans";
    private $po_master = "po_master";
    private $po_trans = "po_trans";
    private $stockTrans = "stock_trans";
    private $icInspection = "ic_inspection";
    private $inspectParam = "inspection_param";
    private $testReport = "grn_test_report";
    private $batch_history = "batch_history";
    private $qc_instruments = "qc_instruments";
	private $assetsMaster = "assets_master";

    public function getNextGrnNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'grn_master';
        $queryData['select'] = "MAX(trans_no ) as trans_no ";
		$queryData['where']['grn_master.trans_date >='] = $this->startYearDate;
		$queryData['where']['grn_master.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (empty($this->last_trans_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }

    public function getDTRows($data){
 
        $data['tableName'] = $this->grn_trans;

        $data['select'] = "grn_master.id,grn_master.trans_number,DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y') as trans_date,party_master.party_name,item_master.item_name,grn_master.doc_no,ifnull(DATE_FORMAT(grn_master.doc_date,'%d-%m-%Y'),'') as doc_date,po_master.trans_number as po_number,grn_trans.trans_status,grn_trans.qty,grn_trans.id as mir_trans_id,item_master.item_type,grn_trans.heat_no,item_category.category_name,material_master.material_grade,location_master.location,grn_trans.batch_no,grn_trans.ok_qty,grn_trans.reject_qty,grn_trans.short_qty,tcReport.report_count,item_master.item_code,item_master.uom,grn_trans.fg_item_id,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name,grn_trans.price,grn_trans.item_remark,(CASE WHEN grn_master.type = 1 THEN 'Purchase' WHEN grn_master.type = 2 THEN 'Jobwork' ELSE 'Customer Return' END) AS type,grn_trans.qc_by,grn_trans.qc_date";

        $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $data['leftJoin']['item_master fgItem'] = "fgItem.id = grn_trans.fg_item_id";
        $data['leftJoin']['po_master'] = "po_master.id = grn_trans.po_id";        
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";        
        $data['leftJoin']['location_master'] = "location_master.id = grn_trans.location_id";
        $data['leftJoin']['(SELECT count(*) as report_count,grn_trans_id FROM grn_test_report WHERE is_delete = 0 GROUP BY grn_trans_id) as tcReport'] = "tcReport.grn_trans_id = grn_trans.id";

        $data['where']['grn_trans.trans_status'] = $data['trans_status'];
		
		if($data['trans_status'] != 1):
			$data['where']['grn_master.trans_date >='] = $this->startYearDate;
			$data['where']['grn_master.trans_date <='] = $this->endYearDate;
		endif;
		
		if(!empty($data['grn_type'])){
            $data['where']['grn_master.grn_type'] = $data['grn_type'];
        }
        $data['order_by']['grn_master.trans_date'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "grn_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "(CASE WHEN grn_master.type = 1 THEN 'Purchase' WHEN grn_master.type = 2 THEN 'Jobwork' END)";
		$data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "CONCAT('[',fgItem.item_code,'] ',fgItem.item_name)";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "grn_trans.batch_no";
        $data['searchCol'][] = "grn_trans.heat_no";
        $data['searchCol'][] = "grn_trans.price"; 
        $data['searchCol'][] = "grn_trans.qty";
        $data['searchCol'][] = "item_master.uom";
        $data['searchCol'][] = "grn_trans.ok_qty";
        $data['searchCol'][] = "grn_trans.reject_qty";
        $data['searchCol'][] = "grn_trans.short_qty";
        $data['searchCol'][] = "po_master.trans_number";
        $data['searchCol'][] = "grn_trans.item_remark";
    
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}

		return $this->pagingRows($data);
    }

    public function save($data){ 
        try{
            $this->db->trans_begin();

            if(!empty($data['id'])):
                $gateInwardData = $this->getGateInward($data['id']);

                if(!empty($gateInwardData->ref_id)):
                    $this->store($this->grn_master,['id'=>$gateInwardData->ref_id,'trans_status'=>0]);
                endif;

                foreach($gateInwardData->itemData as $row):
                    if(!empty($row->po_trans_id)):
                        $setData = array();
                        $setData['tableName'] = $this->po_trans;
                        $setData['where']['id'] = $row->po_trans_id;
                        $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                        $setData['update']['trans_status'] = 3;
                        $this->setValue($setData);

                        $setData = array();
                        $setData['tableName'] = $this->po_master;
                        $setData['where']['id'] = $row->po_id;
                        $setData['update']['trans_status'] = 3;
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->grn_trans,['id'=>$row->id]);
                endforeach;
            endif;

            $itemData = $data['batchData'];unset($data['batchData']);
 
            $result = $this->store($this->grn_master,$data,'Gate Inward');

            foreach($itemData as $row):        
                $rmItem = $this->item->getItem(['id'=>$row['item_id']]);

                $row['grn_id'] = $result['id'];
                $row['is_delete'] = 0;
                if(!empty($rmItem->item_type) && ($rmItem->item_type != 3)){
                    $row['qc_by'] = $this->loginId;
                    $row['qc_date'] = date('Y-m-d');
                }

                $this->store($this->grn_trans,$row);

                if(!empty($row['po_trans_id'])):
                    $setData = array();
                    $setData['tableName'] = $this->po_trans;
                    $setData['where']['id'] = $row['po_trans_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['qty'];
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->po_master;
                    $setData['where']['id'] = $row['po_id'];
					$setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status != 3, 1, 0)) ,1 , 3 ) as trans_status FROM po_trans WHERE po_id = ".$row['po_id']." AND is_delete = 0)";
                    $this->setValue($setData);
                endif;
                
            endforeach;

            //Update GI Status
            if(!empty($data['ref_id'])):
                $this->store($this->grn_master,['id'=>$data['ref_id'],'trans_status'=>1]);
            endif;        

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getGateInward($id){
        $queryData['tableName'] = $this->grn_master;
        $queryData['select'] = "grn_master.*,party_master.party_name,party_master.party_mobile,party_master.gstin,party_master.contact_person,party_master.party_address,party_master.party_email,party_master.party_pincode,party_master.delivery_address";
        $queryData['leftJoin']['party_master'] = "grn_master.party_id = party_master.id";
        $queryData['where']['grn_master.id'] = $id;
        $result = $this->row($queryData);
        $result->itemData = $this->getInwardItem(['grn_id'=>$id, 'multi_row'=>1]);
        return $result;
    }
    
    public function getInwardItem($data){
        $queryData['tableName'] = $this->grn_trans;
		$queryData['select'] = "grn_trans.*,item_master.item_code,item_master.item_name,location_master.location as location_name,grn_master.trans_number,grn_master.trans_date,party_master.party_name,grn_master.doc_no,grn_master.doc_date,grn_master.trans_prefix,grn_master.trans_no,item_master.item_type,item_master.category_id,item_master.hsn_code,item_master.gst_per,item_master.uom,item_master.make_brand,item_master.part_no,item_master.size,item_master.description,material_master.material_grade,item_master.grade_id,po_master.trans_number as po_number,fgItem.item_name as fg_item_name,fgItem.item_code as fg_item_code,po_trans.mill_name,grn_master.party_id,ecn_master.rev_no, ecn_master.cust_rev_no, ecn_master.drw_no,material_master.standard";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $queryData['leftJoin']['item_master fgItem'] = "fgItem.id = grn_trans.fg_item_id";
        $queryData['leftJoin']['location_master'] = "location_master.id = grn_trans.location_id";
        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $queryData['leftJoin']['po_master'] = "po_master.id = grn_trans.po_id";
        $queryData['leftJoin']['po_trans'] = "po_trans.id = grn_trans.po_trans_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id"; 
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = grn_trans.fg_item_id";
        
		if (!empty($data['grn_id'])) { $queryData['where']['grn_trans.grn_id'] = $data['grn_id']; }

		if (!empty($data['id'])) { $queryData['where']['grn_trans.id'] = $data['id']; }

        if(!empty($data['multi_row'])):
            return $this->rows($queryData);
        else:
            return $this->row($queryData);
        endif;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $gateInwardData = $this->getGateInward($id);

            if(!empty($gateInwardData->ref_id)):
                $this->store($this->grn_master,['id'=>$gateInwardData->ref_id,'trans_status'=>0]);
            endif;

            foreach($gateInwardData->itemData as $row):
                if(!empty($row->po_trans_id)):
                    $setData = array();
                    $setData['tableName'] = $this->po_trans;
                    $setData['where']['id'] = $row->po_trans_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                    $setData['update']['trans_status'] = 3;
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->po_master;
                    $setData['where']['id'] = $row->po_id;
                    $setData['update']['trans_status'] = 3;
                    $this->setValue($setData);
                endif;
				
				// product return flag update
                if(!empty($row->ref_id)):
                    $this->store('customer_complaints',['id'=>$row->ref_id,'product_returned'=>1]);
                endif;

                $this->trash($this->grn_trans,['id'=>$row->id]);
            endforeach;

            $result = $this->trash($this->grn_master,['id'=>$id],'Gate Inward');        

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function getBatchWiseItemList($data){
        $queryData['tableName'] = $this->batch_history;
		$queryData['select'] = "batch_history.*";
        if(!empty($data['partyData'])){
            $queryData['select'] .= ',party_master.party_name';
            $queryData['leftJoin']['party_master'] = 'party_master.id = batch_history.party_id';
        }
		if(!empty($data['grnData'])){
            $queryData['select'] .= ',grn_trans.price';
            $queryData['leftJoin']['grn_trans'] = 'grn_trans.batch_no = batch_history.batch_no';
        }
        if(!empty($data['party_id'])) { $queryData['where']['batch_history.party_id'] = $data['party_id']; }
        if(!empty($data['po_trans_id'])) { $queryData['where']['batch_history.po_trans_id'] = $data['po_trans_id']; }
        if(!empty($data['heat_no'])) { $queryData['where']['batch_history.heat_no'] = $data['heat_no']; }
        if(!empty($data['batch_no'])) { $queryData['where']['batch_history.batch_no'] = $data['batch_no']; }
        if(!empty($data['mill_name'])) { $queryData['where']['batch_history.mill_name'] = $data['mill_name']; }
        if(!empty($data['fg_item_id'])) { $queryData['where']['batch_history.fg_item_id'] = $data['fg_item_id']; }
        return $this->row($queryData);
    }

    public function getNextBatchNo(){
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = "IFNULL(MAX(CAST(SUBSTRING_INDEX(batch_no, '/', -1) AS UNSIGNED)) + 1,1) AS batch_no";
        $queryData['leftJoin']['item_master'] = "stock_trans.item_id = item_master.id";
        $queryData['where']['item_master.item_type !='] = 1;
		$queryData['where']['stock_trans.trans_date'] = date('Y-m-d');
        $result = $this->row($queryData);
        return (!empty($result->batch_no)) ? sprintf("%03d",$result->batch_no) : 0;
    }
	
    public function saveInspectedMaterial($data){
        try{
            $this->db->trans_begin();
			
            $mirData = $this->getGateInward($data['grn_id']);
            $mirItem = $this->getInwardItem(['id'=>$data['id']]);

            $data['ok_qty'] = (!empty($data['ok_qty']) ? $data['ok_qty']: 0);
            $data['reject_qty'] = (!empty($data['reject_qty']) ? $data['reject_qty']: 0);
            $data['short_qty'] = (!empty($data['short_qty']) ? $data['short_qty']: 0);

            $totalQty = 0;
            $totalQty = ($data['ok_qty'] + $data['reject_qty'] + $data['short_qty']);
            
            if($mirItem->qty != $totalQty): 
                $this->db->trans_rollback();  
                return ['status'=>0,'message'=>['ok_qty' => "Invalid Qty."]];
            endif;
            if($mirItem->item_type == 6){
                $data['trans_status'] = ($totalQty >= $mirItem->qty) ? 2 : 1;   
				$this->store($this->grn_trans,$data);
				
                for($j =1;$j<=($data['ok_qty'] + $data['reject_qty']);$j++){
					$item_name = $mirItem->item_name.' '.$mirItem->size;
					
                    $instrumentData = [
                        'id' => "",
                        'ref_id' => $mirItem->id,
                        'item_id' => $mirItem->item_id,
                        'item_code' => '',
                        'item_name' => $item_name,
                        'price' => $mirItem->price,
                        'serial_no' => 0,
                        'description' => $mirItem->description,
                        'category_id' => $mirItem->category_id,
                        'hsn_code' => $mirItem->hsn_code,
                        'gst_per' => $mirItem->gst_per,
                        'make_brand' => $mirItem->make_brand,
                        'size' => $mirItem->size,
                        'mfg_sr' => $mirItem->part_no,
                        'in_challan_no' => (!empty($mirData->doc_no) ? $mirData->doc_no : ''),
                        'location_id' => (!empty($data['location_id']) ? $data['location_id'] : '')
                    ];
                    if($j > $data['ok_qty']){
                        $instrumentData['status '] = 4;
                    }
                    $this->store($this->qc_instruments,$instrumentData);
                }
            }else if($mirItem->item_type == 4){
				$data['trans_status'] = ($totalQty >= $mirItem->qty) ? 2 : 1;
				$this->store($this->grn_trans,$data);
				
				for($j =1;$j<=($data['ok_qty'] + $data['reject_qty']);$j++){
					$item_name = $mirItem->item_name.' '.$mirItem->size;
					
                    $assetsData = [
                        'id' => "",
                        'ref_id' => $mirItem->id,
                        'item_id' => $mirItem->item_id,
                        'item_name' => $item_name,
                        'category_id' => $mirItem->category_id,
						'location_id' => (!empty($data['location_id']) ? $data['location_id'] : 0),
                        'price' => $mirItem->price,
                        'hsn_code' => $mirItem->hsn_code,
                        'gst_per' => $mirItem->gst_per,
                        'remark' => (!empty($mirItem->item_remark) ? $mirItem->item_remark : NULL),
						'inward_date' => $mirItem->trans_date
                    ];
                    if($j > $data['ok_qty']){
                        $assetsData['status '] = 3;
                    }
                    $this->store($this->assetsMaster,$assetsData);
                }
			}else{
                $batch_no = $this->getNextBatchNo();
                $batchNo = n2y(date('Y')).n2m(date('m')).date('d').'/'.$batch_no; 
                $this->remove($this->stockTrans,['trans_type'=>'GRN','main_ref_id' => $mirData->id,'child_ref_id' => $mirItem->id]);
                
                $data['trans_status'] = ($totalQty >= $mirItem->qty) ? 2 : 1;            

                $data['batch_no'] = "";
                if($mirItem->item_type != 9){
                    $data['batch_no'] = $batchNo;
                }

                $this->store($this->grn_trans,$data);

                if(!empty($data['reject_qty']) && $data['reject_qty'] > 0):
                    $rejData = [
                        'id' => '',
                        'trans_type' => 2,
                        'trans_date' => date('Y-m-d'),
                        'ref_id' => $mirItem->id,
                        'party_id'=>$mirData->party_id,
                        'item_id' => $mirItem->item_id,
                        'location_id' => $data['location_id'],
                        'qty' => $data['reject_qty'],
                        'batch_no' => $data['batch_no'],
                        'created_by' => $this->loginId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->store('rej_found',$rejData);
                endif;

                if(!empty($data['ok_qty'])):
                    $stockData = [
                        'id' => "",
                        'trans_type' => 'GRN',
                        'trans_date' => date("Y-m-d"),
                        'item_id' => $mirItem->item_id,
                        'location_id' => $data['location_id'],
                        'batch_no' => $data['batch_no'],
                        'p_or_m' => 1,
                        'qty' => $data['ok_qty'],
                        'ref_no' => $mirData->trans_number,
                        'main_ref_id' => $mirData->id,
                        'child_ref_id' => $mirItem->id
                    ];
                    $this->store($this->stockTrans,$stockData);
                endif;
            }
            
            $result = ['status'=>1,'message'=>"Material Inspected successfully."];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getPendingInwardItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->grn_trans;
        $queryData['select'] = "grn_trans.*,(grn_trans.qty - grn_trans.inv_qty) as pending_qty,grn_master.entry_type as main_entry_type,grn_master.trans_number,grn_master.trans_date,grn_master.inv_no,grn_master.inv_date,grn_master.doc_no,grn_master.doc_date,item_master.item_code,item_master.item_name,item_master.item_type,item_master.hsn_code,item_master.gst_per,unit_master.id as unit_id,unit_master.unit_name,'0' as stock_eff";
        $queryData['leftJoin']['grn_master'] = "grn_trans.grn_id = grn_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['where']['grn_trans.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['(grn_trans.qty - grn_trans.inv_qty) >'] = 0;
        $queryData['where']['grn_trans.trans_status'] = 0;
        return $this->rows($queryData);
    }

    public function getInInspectData($data) {
        $queryData = array();
        $queryData['tableName'] = $this->icInspection;
        $queryData['select'] = "ic_inspection.*";
        if(!empty($data['mir_trans_id'])){ $queryData['where']['ic_inspection.mir_trans_id'] = $data['mir_trans_id']; }
        return $this->row($queryData);
    }

    public function getNextIIRNo($type = 2){
        $queryData['tableName'] = $this->icInspection;
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
        $queryData['where']['YEAR(trans_date)'] = date("Y");
        return $this->row($queryData)->next_no;
    }

	public function saveInwardQc($data){
		try{
            $this->db->trans_begin();

            // $this->edit($this->grn_trans,['id'=>$data['mir_trans_id']],['iir_status'=>1]);
    		$result = $this->store($this->icInspection,$data,'Inward QC');

    		if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function getTestReport($postData){
        $data['tableName'] = $this->testReport;
        $data['select'] = "grn_test_report.*,select_master.label as test_description,agencyMaster.party_address,agencyMaster.party_pincode,agencyMaster.contact_person,agencyMaster.party_mobile,agencyMaster.party_email,agencyMaster.party_name as agency_name,qc_instruments.item_name as instrument_name,qc_instruments.last_cal_date,qc_instruments.next_cal_date,employee_master.emp_name,created.emp_name as created_name";
        $data['leftJoin']['select_master'] = 'select_master.id = grn_test_report.test_type';
        $data['leftJoin']['party_master agencyMaster'] = 'agencyMaster.id = grn_test_report.agency_id';
        $data['leftJoin']['qc_instruments'] = 'qc_instruments.id = grn_test_report.inst_id'; 
        $data['leftJoin']['employee_master'] = 'employee_master.id = grn_test_report.approve_by'; 
		$data['leftJoin']['employee_master created'] = 'created.id = grn_test_report.created_by';

        if(!empty($postData['grnData'])){
            $data['select'] .= ',grn_master.trans_number,grn_master.trans_date,item_master.item_name,material_master.material_grade,party_master.party_name,grn_trans.heat_no,grn_trans.batch_no,item_master.item_code,item_master.grade_id';

            $data['leftJoin']['grn_master'] = 'grn_master.id = grn_test_report.grn_id';
            $data['leftJoin']['party_master'] = 'grn_master.party_id = party_master.id';
            $data['leftJoin']['grn_trans'] = 'grn_trans.id = grn_test_report.grn_trans_id';
            $data['leftJoin']['item_master'] = 'item_master.id = grn_trans.item_id';
            $data['leftJoin']['material_master'] = 'item_master.grade_id = material_master.id';
		}

        if(!empty($postData['lastOldReport'])){
			$data['where']['grn_trans.batch_no'] = $postData['batch_no'];
            $data['where']['grn_test_report.id !='] = $postData['gtr_id'];
            $data['order_by']['grn_test_report.id'] = 'DESC';
            $data['limit'] = 1;
        }

        if (!empty($postData['id'])) { $data['where']['grn_test_report.id'] = $postData['id']; }
        
        if (!empty($postData['grn_id'])) { $data['where']['grn_test_report.grn_id'] = $postData['grn_id']; }

        if (!empty($postData['grn_trans_id'])) { $data['where']['grn_test_report.grn_trans_id'] = $postData['grn_trans_id']; }

        if (!empty($postData['batch_no'])) { $data['where']['grn_test_report.batch_no'] = $postData['batch_no']; }

        if(!empty($postData['group_by'])){
            $data['group_by'][] = $postData['group_by'];
        }
        
        if(!empty($postData['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
    }

    public function saveTestReport($data){
        try{
            $this->db->trans_begin();
			
			$result = $this->store($this->testReport, $data, 'Test Report');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function deleteTestReport($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->testReport, ['id'=>$id], 'Test Report');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getTestParameterData($data){
        $queryData['tableName'] = 'grn_tc_trans';
        $queryData['select'] = 'grn_tc_trans.*,grn_test_report.test_type,test_type.head_name,test_type.test_name';
        $queryData['leftJoin']['grn_test_report'] = 'grn_test_report.id = grn_tc_trans.main_id AND grn_test_report.is_delete = 0';
        $queryData['leftJoin']['test_type'] = 'test_type.id = grn_tc_trans.head_id';

        if (!empty($data['main_id'])) { $queryData['where']['grn_tc_trans.main_id'] = $data['main_id']; }

        if (!empty($data['test_type'])) {
            $queryData['customWhere'][] = "FIND_IN_SET(".$data['test_type'].",test_type.test_type) > 0";
        }
        
        return $this->rows($queryData);
    }

    public function approveTestReport($data) {
        try{
            $this->db->trans_begin();

            $this->store($this->testReport, ['id' => $data['id'], 'approve_by' => $this->loginId]);
            $result = ['status' => 1, 'message' => 'Test Report Approved Successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPendingQcDTRows($data){
        $data['tableName'] = $this->testReport;
		$data['select'] = "grn_test_report.*,select_master.label as test_name,grn_master.trans_number,item_master.item_name,item_master.item_code,material_master.material_grade,grn_trans.item_remark";
        $data['leftJoin']['grn_trans'] = 'grn_trans.id = grn_test_report.grn_trans_id';
        $data['leftJoin']['grn_master'] = 'grn_master.id = grn_test_report.grn_id';
        $data['leftJoin']['item_master'] = 'item_master.id = grn_trans.item_id';
		$data['leftJoin']['select_master'] = "select_master.id = grn_test_report.test_type";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $data['where']['grn_test_report.tc_file'] = null;	
		$data['group_by'][] = "grn_test_report.id";
       
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		$data['searchCol'][] = "grn_master.trans_number";
        $data['searchCol'][] = "grn_test_report.name_of_agency";
        $data['searchCol'][] = "select_master.label";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)"; 
		$data['searchCol'][] = "grn_test_report.sample_qty";
        $data['searchCol'][] = "grn_test_report.batch_no";
        $data['searchCol'][] = "grn_test_report.heat_no";
        $data['searchCol'][] = "grn_trans.item_remark";      

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}
		return $this->pagingRows($data);
    } 

    public function checkTestReportStatus($param = []){
        $queryData['tableName'] = "material_master";
        $queryData['select'] = "GROUP_CONCAT(DISTINCT tc_master.test_type) as required_test,GROUP_CONCAT(DISTINCT grn_test_report.test_type) as tested_report";
        $queryData['leftJoin']['tc_master'] = "FIND_IN_SET(tc_master.test_type,material_master.tc_head) > 0 AND tc_master.is_delete = 0 AND tc_master.ins_type = 'GRN' AND tc_master.item_id = '".$param['item_id']."'";
        $queryData['leftJoin']['grn_test_report'] = "tc_master.test_type = grn_test_report.test_type AND grn_test_report.grn_trans_id='".$param['grn_trans_id']."' AND grn_test_report.is_delete = 0 AND grn_test_report.test_type > 0";
        $queryData['leftJoin']['grn_trans'] = "grn_trans.id = grn_test_report.grn_trans_id";
        $queryData['where']['tc_master.ins_type'] = 'GRN';
        $queryData['where']['grn_trans.fg_item_id'] = $param['item_id'];
        return $this->row($queryData);
    }
     
	public function getLastPurchasePrice($postData){
        $data['tableName'] = 'grn_trans';
        $data['select'] = 'grn_trans.price';
        $data['where']['grn_trans.item_id'] = $postData['item_id'];
        $data['order_by']['grn_trans.id'] = 'DESC';
        return $this->row($data);
    }

    public function approveInwardQc($data) {
        try{
            $this->db->trans_begin();

            $inspectionData = $this->getInInspectData(['mir_trans_id'=>$data['id']]);
            
            if(!empty($inspectionData)){
                $date = ($data['is_approve'] == 1) ? date('Y-m-d') : "NULL";
                $isApprove =  ($data['is_approve'] == 1) ? $this->loginId : 0;
                $this->edit($this->grn_trans,['id'=>$data['id']],['qc_by' => $isApprove, 'qc_date'=>$date]);
                $result = ['status' => 1, 'message' => 'Inward Qc' . $data['msg'] . ' successfully.'];
            }else{
                $result = ['status' => 0, 'message' => 'Incoming Inspection Report is Pending.'];
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