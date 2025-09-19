<?php
class AssetsMasterModel extends MasterModel{
    private $assetsMaster = "assets_master";
    private $assetsChallan = "assets_challan";
    private $assetsChallanTrans = "assets_challan_trans";

	public function getDTRows($data){
        $data['tableName'] = $this->assetsMaster;
        $data['select'] = "assets_master.*, CONCAT('[',item_category.category_name,'] ',item_category.category_name) as category_name,location_master.location,party_master.party_name";
        $data['leftJoin']['item_category'] = "item_category.id = assets_master.category_id";
        $data['leftJoin']['location_master'] = "location_master.id = assets_master.location_id";
        $data['leftJoin']['grn_trans'] = "grn_trans.id = assets_master.ref_id";
        $data['leftJoin']['grn_master'] = "grn_trans.grn_id = grn_master.id";
        $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        
        $data['where']['assets_master.status'] = $data['status'];
		
		$columns = array();
        if($data['status'] == 1){
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "assets_master.item_code";
			$data['searchCol'][] = "assets_master.item_name";
			$data['searchCol'][] = "assets_master.price";
			$data['searchCol'][] = "location_master.location";
			$data['searchCol'][] = "assets_master.hsn_code";
			$data['searchCol'][] = "DATE_FORMAT(assets_master.inward_date,'%d-%m-%Y')";
			$data['searchCol'][] = "party_master.party_name";

			$columns =array('','','','assets_master.item_code','assets_master.item_name','assets_masterassets_master.price','location_master.location','assets_master.hsn_code','assets_master.inward_date','party_master.party_name');
		}else{			
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "assets_master.item_code";
			$data['searchCol'][] = "assets_master.item_name";
			$data['searchCol'][] = "assets_master.price";
			$data['searchCol'][] = "location_master.location";
			$data['searchCol'][] = "assets_master.hsn_code";
			$data['searchCol'][] = "DATE_FORMAT(assets_master.inward_date,'%d-%m-%Y')";
			$data['searchCol'][] = "party_master.party_name";

			$columns =array('','','assets_master.item_code','assets_master.item_name','assets_masterassets_master.price','location_master.location','assets_master.hsn_code','assets_master.inward_date','party_master.party_name');
		}
        
		if(isset($data['order'])){
		    $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}else{
            $data['order_by']['assets_master.id'] = 'DESC';
		}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getItem($data=[]){
        $queryData['tableName'] = $this->assetsMaster;
        $queryData['select'] = 'assets_master.*,item_category.category_name,item_category.category_code as cat_code,item_master.item_name,employee_master.emp_name';
        $queryData['leftJoin']['item_category'] = 'item_category.id = assets_master.category_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = assets_master.item_id';
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = assets_master.created_by';

        if (!empty($data['id'])) { $queryData['where']['assets_master.id'] = $data['id']; }

        if (!empty($data['ids'])) { $queryData['where_in']['assets_master.id'] = str_replace("~", ",", $data['ids']); }

        if (!empty($data['status'])) { $queryData['where']['assets_master.status'] = $data['status']; }
        
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function save($data){
        try{
            $this->db->trans_begin();
			
			if($this->checkDuplicate($data) > 0):
                $errorMessage['item_code'] = "Item Code is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
			
            $assetsItem = $this->getItem(['id'=>$data['id'], 'single_row'=>1]);
            
            $data['item_name'] = $data['item_code'].' '.$assetsItem->item_name;
    
            $result = $this->store($this->assetsMaster,$data,"Assets");

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }   
	}
	
	public function checkDuplicate($data){
        $queryData['tableName'] = $this->assetsMaster;

        if(!empty($data['item_code']))
            $queryData['where']['item_code'] = $data['item_code'];
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }
	
	public function saveRejectAsset($data){
        try{
            $this->db->trans_begin();

            $result = $this->edit($this->assetsMaster,['id'=>$data['id']],['status'=>3,'reject_reason'=>$data['reject_reason'],'rejected_by'=>$this->loginId]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }  
    }
	
	/* Assets Challan Functions Start */
	public function getChDTRows($data = array()){
		$data['tableName'] = $this->assetsChallanTrans;
        $data['select'] = 'assets_challan_trans.*,assets_challan.trans_number,assets_challan.challan_type,assets_challan.trans_date,assets_challan.party_id,assets_master.item_name,assets_master.item_code,(CASE WHEN assets_challan.challan_type = 1 THEN location_master.location WHEN assets_challan.challan_type = 2 THEN party_master.party_name ELSE employee_master.emp_name END) as issue_to';
		
        $data['leftJoin']['assets_challan'] = "assets_challan.id = assets_challan_trans.challan_id";
        $data['leftJoin']['assets_master'] = "assets_master.id = assets_challan_trans.assets_id";
        $data['leftJoin']['party_master'] = "party_master.id = assets_challan.party_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = assets_challan.emp_id";
		$data['leftJoin']['location_master'] = "location_master.id = assets_challan.location_id";
        
        if(empty($data['status'])){
			$data['where']['assets_challan_trans.trans_status'] = 0; 
		}else{
			$data['where']['assets_challan_trans.trans_status >'] = 0; 
		}
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('/',assets_challan.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(assets_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "assets_challan.challan_type";
        $data['searchCol'][] = "employee_master.emp_name";   
        $data['searchCol'][] = "assets_master.item_code";
        $data['searchCol'][] = "assets_master.item_name";
        $data['searchCol'][] = "assets_challan_trans.item_remark";

		$columns =array('','','assets_challan.trans_no','assets_challan.trans_date','assets_challan.challan_type','employee_master.emp_name','assets_master.item_code','assets_master.item_name','assets_challan_trans.item_remark');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
	}
	
	public function getAssetsChallan($id){
        $queryData['tableName'] = $this->assetsChallan;
        $queryData['select'] = 'assets_challan.*,employee_master.emp_name';
        $queryData['leftJoin']['employee_master'] = "employee_master.id = assets_challan.emp_id";
        $queryData['where']['assets_challan.id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getAssetsChallanTrans(['challan_id'=>$id]); 
        return $challanData;
    }
	
	public function getAssetsChallanTrans($data=[]){
        $queryData = array();
        $queryData['tableName'] = $this->assetsChallanTrans;
        $queryData['select'] = 'assets_challan_trans.*,assets_master.item_name,assets_master.item_code,assets_challan.party_id,party_master.party_name,assets_challan.challan_type,assets_challan.trans_number,assets_challan.trans_date,location_master.location,employee_master.emp_name';

        $queryData['leftJoin']['assets_master'] = "assets_challan_trans.assets_id = assets_master.id";
        $queryData['leftJoin']['assets_challan'] = "assets_challan.id = assets_challan_trans.challan_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = assets_challan.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = assets_challan.created_by";
        $queryData['leftJoin']['location_master'] = "location_master.id = assets_challan.location_id";

        if (!empty($data['challan_id'])) { $queryData['where']['assets_challan_trans.challan_id'] = $data['challan_id']; }

        if (!empty($data['id'])) { $queryData['where']['assets_challan_trans.id'] = $data['id']; }

        if (!empty($data['assets_id'])) { $queryData['where']['assets_challan_trans.assets_id'] = $data['assets_id']; }

        if (!empty($data['challan_type'])) { $queryData['where_in']['assets_challan.challan_type'] = $data['challan_type']; }

        if(!empty($data['single_row']) && $data['single_row'] == 1){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
	
	public function nextTransNo(){
        $data['tableName'] = $this->assetsChallan;
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }
	
	public function saveChallan($masterData,$itemData){
        try{
            $this->db->trans_begin();
			
			$issue_to = $masterData['issue_to'];
            unset($masterData['issue_to']);
			
            if(empty($masterData['id'])):
                $inChallan = $this->store($this->assetsChallan,$masterData);
                $mainId = $inChallan['insert_id'];
                $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("assetsMaster/assetsChallan")];
            else:
                $this->store($this->assetsChallan,$masterData);
                $mainId = $masterData['id'];
                $challanItems = $this->getAssetsChallanTrans(['challan_id'=>$mainId]); 
				
                if(!empty($challanItems)){					
					foreach($challanItems as $row):
						if(!in_array($row->id,$itemData['id'])):
							$this->trash($this->assetsChallanTrans,['id'=>$row->id]);
							$this->edit('assets_master', ['id'=>$row->assets_id], ['status' => 1, 'issue_details' => NULL]);
						endif;
					endforeach;
				}
    
                $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("assetsMaster/assetsChallan")];
            endif;
    
            foreach($itemData['item_id'] as $key=>$value):
                $item = $this->getItem(['id'=>$value, 'single_row'=>1]); 
                $transData = [
                    'id' => $itemData['id'][$key],
                    'challan_id' => $mainId,
                    'assets_id' => $value,
                    'item_remark' => $itemData['item_remark'][$key],
                    'created_by' => $itemData['created_by']
                ];
                $saveTrans = $this->store($this->assetsChallanTrans,$transData);
				
				/* Update Assets Master Table */
				$challan_type = (($masterData['challan_type'] == 1) ? "In-House Issue" : (($masterData['challan_type'] == 2) ? "Vendor Issue" : "Employee Issue"));
				$issue_details = $challan_type."~@".$issue_to."~@".$masterData['trans_date'];
				
                $this->edit('assets_master', ['id'=>$value], ['status' => 2, 'issue_details' => $issue_details]);
            endforeach;
    
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }
	
	public function deleteChallan($id){
        try{
            $this->db->trans_begin();

            $transData = $this->getAssetsChallanTrans(['challan_id'=>$id]);

            foreach($transData as $row):    
                $this->trash($this->assetsChallanTrans,['id'=>$row->id]);
                $this->edit('assets_master', ['id'=>$row->assets_id], ['status' => 1, 'issue_details'=> NULL]);
            endforeach;

            $result = $this->trash($this->assetsChallan,['id'=>$id],'Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
	public function getPendingAssetsChallan($id){
        $data['tableName'] = $this->assetsChallanTrans;
        $data['select'] = "COUNT(trans_status) as trans_status";
        $data['where']['challan_id'] = $id;
        $data['where']['trans_status'] = 0;
        return $this->specificRow($data)->trans_status;
    }
	
	public function saveReceiveChallan($data = array()){
		try{
            $this->db->trans_begin();
			
			$result = $this->edit($this->assetsChallanTrans, ['id'=>$data['id']], ['trans_status' => 1, 'in_ch_no' => $data['in_ch_no'], 'receive_by' => $this->loginId, 'receive_at' => $data['receive_at']]);
			$this->edit($this->assetsMaster,['id'=>$data['assets_id']], ['status' => 1, 'issue_details' => NULL]);
			
			if(!empty($data['challan_id'])):
				$pendingAssets = $this->getPendingAssetsChallan($data['challan_id']);
				if(empty($pendingAssets)):
					$this->edit($this->assetsChallan, ['id'=>$data['challan_id']], ['trans_status' => 1]);
				endif;
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
	/* Assets Challan Functions End */
}
?>