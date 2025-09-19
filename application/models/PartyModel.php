<?php
class PartyModel extends MasterModel{
    private $partyMaster = "party_master";
    private $groupMaster = "group_master";
    private $countries = "countries";
    private $states = "states";
	private $cities = "cities";
    private $transDetails = "trans_details";
	private $partyActivities = "party_activities";

	public function getPartyCode($data){
        $queryData['tableName'] = $this->partyMaster;
        $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(party_code,'[0-9]+') AS UNSIGNED)) + 1),1) as code";
        $queryData['where']['party_category'] = $data['category'];
        $queryData['where']['party_type'] = $data['party_type'];
        $result = $this->row($queryData)->code;
        return $result;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->partyMaster;
        
		if($data['party_category'] != 4):
            $data['where']['party_master.party_category'] = $data['party_category'];
			
			$data['where']['party_master.party_type'] = $data['party_type'];
        endif;

        $data['searchCol'][] = "";
		$data['searchCol'][] = "";
        if($data['party_category'] == 1):
            $data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "party_master.party_code";
			$data['searchCol'][] = "party_master.currency";
        elseif($data['party_category'] == 2):
            $data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "party_master.party_code";
        elseif($data['party_category'] == 3):
            $data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "party_master.party_address";
			$data['searchCol'][] = "party_master.party_code";
        elseif($data['party_category'] == 4):
            $data['select'] = "party_master.*,(CASE WHEN tl.op_balance > 0 THEN CONCAT(ABS(tl.op_balance), ' Cr.') WHEN tl.op_balance < 0 THEN CONCAT(ABS(tl.op_balance), ' Dr.') ELSE 0 END) as op_balance,(CASE WHEN tl.cl_balance > 0 THEN CONCAT(ABS(tl.cl_balance), ' Cr.') WHEN tl.cl_balance < 0 THEN CONCAT(ABS(tl.cl_balance), ' Dr.') ELSE 0 END) as cl_balance";

            $data['leftJoin']["(SELECT tl.vou_acc_id , (am.opening_balance + SUM( CASE WHEN tl.trans_date < '".$this->startYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, (am.opening_balance  + SUM( CASE WHEN tl.trans_date <= '".$this->endYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id WHERE am.is_delete = 0 AND tl.is_delete = 0 GROUP BY am.id) as tl"] = 'tl.vou_acc_id = party_master.id';

            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "party_master.group_name";
            $data['searchCol'][] = "tl.op_balance";
            $data['searchCol'][] = "tl.cl_balance";
        endif;

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

	public function getPartyList($data=array()){
        $queryData = array();
        $queryData['tableName']  = $this->partyMaster;

        $queryData['select'] = "party_master.*,executive_master.emp_name  as executive";
        $queryData['leftJoin']['employee_master as executive_master'] = "executive_master.id = party_master.sales_executive";

        if(!empty($data['addData'])){
            $queryData['select'] .= ",countries.name as country_name,states.name as state_name";
            $queryData['leftJoin']['countries'] = "party_master.country_id = countries.id";
            $queryData['leftJoin']['states'] = "party_master.state_id = states.id";
        }

        if(!empty($data['party_category'])):
            $queryData['where_in']['party_master.party_category'] = $data['party_category'];
        endif;

        if(!empty($data['group_id'])):
            $queryData['where_in']['party_master.group_id'] = $data['group_id'];
        endif;
		
		if(!empty($data['party_type'])):
            $queryData['where_in']['party_master.party_type'] = $data['party_type'];
        else:
            $queryData['where']['party_master.party_type'] = 1;
        endif;

        if(!empty($data['group_code'])):
            $queryData['where_in']['party_master.group_code'] = $data['group_code'];
        endif;

        if(!empty($data['system_code'])):
            $queryData['where_in']['party_master.system_code'] = $data['system_code'];
            $queryData['order_by_field']['party_master.system_code'] = $data['system_code'];
        else:
            $queryData['order_by']['party_master.party_name'] = "ASC";
        endif;

        if(!empty($data['lead_stage'])):
            $queryData['where_in']['party_master.lead_stage'] = $data['lead_stage'];
        endif;

        if(!empty($data['source']) AND $data['source'] != 'All'):
            $queryData['where']['party_master.source'] = $data['source'];
        endif;

        if(isset($data['executive_id']) && $data['executive_id'] != 'All'):
            $queryData['where']['party_master.sales_executive'] = $data['executive_id'];
        endif;

        if(!empty($data['limit'])):
            $queryData['limit'] = $data['limit']; 
        endif;
        if(isset($data['start'])):
            $queryData['start'] = $data['start'];
        endif;
        if(!empty($data['length'])):
            $queryData['length'] = $data['length'];
        endif;

         if(!empty($data['skey'])):
            $queryData['like']['party_master.party_name'] = str_replace(" ", "%", $data['skey']);
            $queryData['like']['executive_master.emp_name'] = str_replace(" ", "%", $data['skey']);
            $queryData['like']['party_master.contact_person'] = str_replace(" ", "%", $data['skey']);
            $queryData['like']['party_master.source'] = str_replace(" ", "%", $data['skey']);
            $queryData['like']['party_master.party_mobile'] = str_replace(" ", "%", $data['skey']);
        endif;

        if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", executive_master.super_auth_id) > 0 OR executive_master.id = '.$this->loginId.')';
        endif;


        return $this->rows($queryData);
    }

    public function getParty($data){
        $queryData = array();
        $queryData['tableName']  = $this->partyMaster;
        $queryData['select'] = "party_master.*,IF(party_master.gstin > 0,SUBSTRING(party_master.gstin,1,2),96) as state_code,IF(currency.inrrate > 0,currency.inrrate,1) as inrrate, currency.arial_uni_ms as currency_code, (CASE WHEN tl.cl_balance > 0 THEN ABS(TRIM(tl.cl_balance)+0) WHEN tl.cl_balance < 0 THEN ABS(TRIM(tl.cl_balance)+0) ELSE 0 END) as closing_balance,(CASE WHEN tl.cl_balance > 0 THEN 'CR' WHEN tl.cl_balance < 0 THEN 'DR' ELSE '' END) as closing_type,tl.cl_balance";

        $queryData['select'] .= ",b_countries.name as country_name,b_states.name as state_name,IF(b_states.gst_statecode > 0, b_states.gst_statecode, 96) as state_code,d_countries.name as delivery_country_name,d_states.name as delivery_state_name,IF(d_states.gst_statecode > 0, d_states.gst_statecode, 96) as delivery_state_code,lead_stages.stage_type,executive_master.emp_name as executive ";

        $queryData['leftJoin']['currency'] = "currency.currency = party_master.currency";

        $party_id = (!empty($data['id']))?" AND am.id = ".$data['id']:"";
        $queryData['leftJoin']["(
            SELECT am.id as vou_acc_id, (ifnull(am.opening_balance,0) + SUM(CASE WHEN tl.trans_date <= '".$this->endYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0
            WHERE am.is_delete = 0 ".$party_id." GROUP BY am.id
        ) as tl"] = 'tl.vou_acc_id = party_master.id';

        $queryData['leftJoin']['countries as b_countries'] = "party_master.country_id = b_countries.id";
        $queryData['leftJoin']['states as b_states'] = "party_master.state_id = b_states.id";

        $queryData['leftJoin']['countries as d_countries'] = "party_master.delivery_country_id = d_countries.id";
        $queryData['leftJoin']['states as d_states'] = "party_master.delivery_state_id = d_states.id";

        $queryData['leftJoin']['employee_master as executive_master'] = "executive_master.id = party_master.sales_executive";
        $queryData['leftJoin']['lead_stages'] = "lead_stages.lead_stage = party_master.lead_stage";


        if(!empty($data['id'])):
            $queryData['where']['party_master.id'] = $data['id'];
        endif;

        if(!empty($data['party_category'])):
            $queryData['where_in']['party_master.party_category'] = $data['party_category'];
        endif;

        if(!empty($data['system_code'])):
            $queryData['where']['party_master.system_code'] = $data['system_code'];
        endif;

        if(!empty($data['party_name'])):
            $queryData['where']['party_master.party_name'] = $data['party_name'];
        endif;

        // 17-06-25
        if(!empty($data['party_type'])):
            $queryData['where']['party_master.party_type'] = $data['party_type'];
        endif;

        if(!empty($data['party_code'])):
            $queryData['where']['party_master.party_code'] = $data['party_code'];
        endif;

        if(!empty($data['party_mobile'])):
            $queryData['where']['party_master.party_mobile'] = $data['party_mobile'];
        endif;
		
        if(!empty($data['customWhere'])):
            $queryData['customWhere'][] = $data['customWhere'];
        endif;
		
		if(isset($data['is_delete'])):
            $queryData['where']['party_master.is_delete'] = $data['is_delete'];
        endif;

        return $this->row($queryData);
    }

    public function getCurrencyList(){
		$queryData['tableName'] = 'currency';
		return $this->rows($queryData);
	}

    public function getCountries(){
		$queryData['tableName'] = $this->countries;
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
	}

    public function getCountry($data){
		$queryData['tableName'] = $this->countries;
        if(!empty($data['id'])):
		    $queryData['where']['id'] = $data['id'];
        endif;  
        if(!empty($data['name'])):
            $queryData['where']['name'] = $data['name'];
        endif;
		return $this->row($queryData);
	}

    public function getStates($data=array()){
        $queryData['tableName'] = $this->states;
		$queryData['where']['country_id'] = $data['country_id'];
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
    }

    public function getState($data){
        $queryData['tableName'] = $this->states;
        $queryData['select'] = 'states.*,states.name as state_name,states.gst_statecode as state_code,countries.name as country_name';
        $queryData['leftJoin']['countries'] = "countries.id = states.country_id";

		if(!empty($data['id'])):
		    $queryData['where']['states.id'] = $data['id'];
        endif; 
        if(!empty($data['name'])):
            $queryData['where']['states.name'] = $data['name'];
        endif;
		return $this->row($queryData);
    }

    public function getCities($data=array()){
        $queryData['tableName'] = $this->cities;
		$queryData['where']['state_id'] = $data['state_id'];
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
    }

    public function getCity($data){
        $queryData['tableName'] = $this->cities;
        $queryData['select'] = 'cities.*,states.name as state_name,states.gst_statecode as state_code,countries.name as country_name';
        $queryData['leftJoin']['states'] = 'cities.state_id = states.id';
        $queryData['leftJoin']['countries'] = "countries.id = cities.country_id";
		$queryData['where']['cities.id'] = $data['id'];
		return $this->row($queryData);
    }
	
	/* UPDATED BY : AVT DATE:18-12-2024 */
	public function save($data){ 
		try {
			$this->db->trans_begin();

            if(!empty($data['party_category']) && $this->checkDuplicate(['party_category'=>$data['party_category'], 'party_name'=>$data['party_name'], 'party_mobile'=>$data['party_mobile'], 'id'=>$data['id']]) > 0) :
				$errorMessage['party_name'] = "Party name is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;
			
			if(!empty($data['party_category']) && $data['party_type'] == 1 && $this->checkDuplicate(['party_category'=>$data['party_category'], 'party_type'=>$data['party_type'], 'party_code'=>$data['party_code'], 'id'=>$data['id']]) > 0) :
				$errorMessage['party_code'] = "Party code is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;

            if(!empty($data['party_category']) && !in_array($data['party_category'],[4,5]))://Customer,Supplier & Vendor
                $groupData = $this->group->getGroup(['group_code'=>(($data['party_category'] == 1)?"'SD'":"'SC'"), 'is_default' => 1]);

                $data['group_id'] = $groupData->id;
                $data['group_name'] = $groupData->name;
                $data['group_code'] = $groupData->group_code;
            elseif(!empty($data['party_category']) && $data['party_category'] == 4)://Other Ledger
                $groupData = $this->group->getGroup(['id'=>$data['group_id']]);

                $data['group_id'] = $groupData->id;
                $data['group_name'] = $groupData->name;
                $data['group_code'] = $groupData->group_code;
            endif;
			unset($data['form_type']);
            $result = $this->store($this->partyMaster, $data, 'Party');

            if(!empty($data['party_category']) && $data['party_category'] != 4):
                $data['party_id'] = $result['id'];
                $this->saveGstDetail($data);
            endif;			

           
             // Save Record Party Activity 
            if(!empty($result['id'])):
                if(empty($data['id']) && $data['party_type'] == 2):
                    $this->savePartyActivity(['party_id'=>$result['id'],'lead_stage'=>1]);
                endif;

            endif;
			
            if(!empty($result['id']) && !empty($data['contact_person']) ):
				
				//Save Party Contact
                $contactDetail = [
                    'party_id'=>$result['id'],
                    'contact_person'=>$data['contact_person'],
                    'designation'=>(!empty($data['designation']) ? $data['designation'] : ''),
                    'party_mobile'=>$data['party_mobile'],
                    'party_email'=>$data['party_email'],
                    'is_default'=>1
                ];

                if(!empty($contactDetail)):
                    if(empty($data['id'])):
                        $contactDetail['id'] = "";
                    else:
                        $contactDetail['id'] = "-1";
                    endif;
                    $this->savePartyContact($contactDetail);
                endif;
			endif;
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	
	public function checkDuplicate($data){
        $queryData['tableName'] = $this->partyMaster;

        if(!empty($data['party_name'])):
            $queryData['where']['party_name'] = $data['party_name'];        
        endif;

        if(!empty($data['party_code'])):
            $queryData['where']['party_code'] = $data['party_code'];        
        endif;

		if(!empty($data['party_category'])):
            $queryData['where']['party_category'] = $data['party_category']; 
        endif;
		
		if(!empty($data['party_mobile'])):
            $queryData['where']['party_mobile'] = $data['party_mobile']; 
        endif;

        if(!empty($data['party_type'])):
            $queryData['where']['party_type'] = $data['party_type']; 
        endif;
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
		try {
			$this->db->trans_begin();

            $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>0,'party_id'=>$id,'entry_type'=>0]);
            if($checkBillWiseRef == true):
                return ['status'=>2,'message'=>'Bill Wise Reference already adjusted. if you want to delete this account first unset all adjustment.'];
            endif;
            $this->remove("trans_billwise",['trans_main_id'=>0,'party_id'=>$id,'trans_number'=>"OpBal"]);

            $checkData['columnName'] = ['party_id','acc_id','opp_acc_id','vou_acc_id','sp_acc_id'];
			$checkData['ignoreTable'] = ['party_master','party_contact','party_activities'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Party is currently in use. you cannot delete it.'];
            endif;

            $this->trash($this->transDetails, ['main_ref_id' => $id,'table_name' =>  $this->partyMaster,'description' => 'PARTY GST DETAIL']);
			$this->trash('party_contact', ['party_id' => $id]); 
			$result = $this->trash($this->partyMaster, ['id' => $id], 'Party');

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	
    public function getPartyGSTDetail($data){
        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "id, main_ref_id as party_id, t_col_1 as gstin, t_col_2 as party_address, t_col_3 as party_pincode, t_col_4 as delivery_address, t_col_5 as delivery_pincode";
        $queryData['where']['main_ref_id'] = $data['party_id'];
        $queryData['where']['table_name'] = $this->partyMaster;
        $queryData['where']['description'] = "PARTY GST DETAIL";
        return $this->rows($queryData);
    }

    public function saveGstDetail($data){
        try {
			$this->db->trans_begin();

            $partyDetails = $this->getParty(['id'=>$data['party_id']]);

            $queryData['tableName'] = $this->transDetails;
            $queryData['where']['main_ref_id'] = $data['party_id'];
            $queryData['where']['table_name'] = $this->partyMaster;
            $queryData['where']['description'] = "PARTY GST DETAIL";
            $gstData = $this->row($queryData);

            $postData = [
                'id' => (!empty($gstData))?$gstData->id:"",
                'main_ref_id' =>  $data['party_id'],
                'table_name' => $this->partyMaster,
                'description' => "PARTY GST DETAIL",
                't_col_1' => $data['gstin'],
                't_col_2' => $data['party_address'],
			    't_col_3' => $data['party_pincode'],
                't_col_4' => $data['delivery_address']
            ];
            $result = $this->store($this->transDetails,$postData);

            if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function deleteGstDetail($id){
		try {
			$this->db->trans_begin();

			$result = $this->trash($this->transDetails, ['id' => $id], 'Party GST Detail');

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function getTDSClassList($data = array()){
        $queryData = array();
        $queryData['tableName'] = "tds_class";
        
        if(!empty($data['class_type']))
            $queryData['where_in']['class_type'] = $data['class_type'];

        $result = $this->rows($queryData);
        return $result;
    }
    
    /* Party Opening Balance Start */

    public function getPartyOpBalance($data){
		$group_id = (!empty($data['group_id']))? 'AND am.group_id = '.$data['group_id']:'';
		$group_code = (!empty($data['group_code']))? 'AND am.group_code = '.$data['group_code']:'';

        $ledgerSummary = $this->db->query("SELECT 
            am.id,
            am.party_name as account_name, 
            am.group_name, 

            (CASE WHEN am.opening_balance > 0 THEN CONCAT(abs(am.opening_balance),' CR.') WHEN am.opening_balance < 0 THEN CONCAT(abs(am.opening_balance),' DR.') ELSE am.opening_balance END) as op_balance,
            (CASE WHEN am.opening_balance > 0 THEN 1 WHEN am.opening_balance < 0 THEN -1 ELSE 1 END) as op_balance_type,

            (CASE WHEN am.other_op_balance > 0 THEN CONCAT(abs(am.other_op_balance),' CR.') WHEN am.other_op_balance < 0 THEN CONCAT(abs(am.other_op_balance),' DR.') ELSE am.other_op_balance END) as other_op_balance,
            (CASE WHEN am.other_op_balance > 0 THEN 1 WHEN am.other_op_balance < 0 THEN -1 ELSE 1 END) as other_op_balance_type

            FROM party_master as am
            WHERE am.is_delete = 0 ".$group_id." ".$group_code."
            ORDER BY am.party_name
        ")->result();

        return $ledgerSummary;
    }

    public function saveOpeningBalance($data){
        try{
            $this->db->trans_begin();
            
            if(!empty($data['id'])):

				$data['opening_balance'] = floatval(($data['opening_balance'] * $data['balance_type']));
                unset($data['balance_type']);

                $this->store("party_master",$data);
            else:
                return ['status'=>2,'message'=>'Somthing is wrong...Ledger not found.'];
            endif;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger Opening Balance updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /* Party Opening Balance End */ 
	
	/* CREATED BY : AVT DATE:13-12-2024 */
	/********** Start Party Contact Detail **********/
    public function getPartyContact($data){ 
		$queryData['tableName'] = "party_contact";
		$queryData['select'] = "party_contact.*";	
		$queryData['where']['party_contact.party_id'] = $data['party_id'];
        $result = $this->rows($queryData);
        return $result;
	}
	
    public function savePartyContact($data){  
        try {
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['id'] = "";
                $result = $this->store('party_contact', $data,'Party Contact');
            else:
				unset($data['id']);
                $result = $this->edit('party_contact', ['party_id'=>$data['party_id'],'is_default'=>1], $data,'Party Contact');
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

	public function deletePartyContact($id){
		try{
			$this->db->trans_begin();

			$result = $this->trash('party_contact',['id'=>$id],"Record");
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
    /********** End Party Contact Detail **********/
	
	/********** Start Party Activity Detail **********/
    public function savePartyActivity($param){ 
        try{
            $activityNotes =Array();
            $activityNotes[1] = 'New Lead generated';
            $activityNotes[2] = 'New appointment scheduled';
            $activityNotes[3] = (!empty($param['notes']))?$param['notes']:"";
            $activityNotes[4] = 'New Enquiry Received';
            $activityNotes[5] = 'Quotation request';
            $activityNotes[6] = 'Quotation Generated';
            $activityNotes[7] = 'Order Received';
            $activityNotes[8] = 'De-activated Customer';
            $activityNotes[9] = 'Executive assigned';
            $activityNotes[10] = 'Order Confirmed';
            $activityNotes[11] = 'Ohh..No ! We Lost..😞';
            $activityNotes[12] = 'Re-opened Customer';
			$activityNotes[13] = 'Client Visit';

            $this->db->trans_begin();

            $data = Array();
			if(!empty($param['lead_stage'])){
				if($param['lead_stage'] >= 21 AND $param['lead_stage'] <= 30) 
				{
					$leadStageData = $this->leadStages->getLeadStagesList(["lead_stage"=>$param['lead_stage'],"single_row"=>1]);
					$param['notes'] = 'Status updated to ';
					if(!empty($leadStageData->stage_type)){$param['notes'] .= '<b>'.$leadStageData->stage_type.'<b>';}
				}
				else{ $param['notes'] = $activityNotes[$param['lead_stage']]; }
			}
			
            if(empty($param['ref_date'])){ $param['ref_date'] = date('Y-m-d H:i:s'); }
            $param['id'] = (isset($param['id']))? $param['id']:"";
            if(!empty($param['lead_stage']) && $param['lead_stage'] == 12){ $param['lead_stage'] = 1 ;}
            $result = $this->store($this->partyActivities, $param, 'Party Activity');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPartyActivity($data){
        $queryData = [];
        $queryData['tableName'] = $this->partyActivities;
        $queryData['select'] = "party_activities.id, party_activities.lead_stage, party_activities.party_id, party_activities.ref_id, party_activities.ref_date, IFNULL(party_activities.ref_no,'') as ref_no, party_activities.mode, party_activities.notes, IFNULL(party_activities.response,'') as response, party_activities.remark, (CASE WHEN party_activities.lead_stage = 13 THEN (SELECT voice_notes FROM visits WHERE id = party_activities.ref_id AND is_delete = 0) ELSE '' END) AS voice_notes, IFNULL(employee_master.emp_name,'') as created_by_name,party_activities.created_at, IFNULL(party_master.party_name,'') as party_name,emp.emp_name as executive,party_activities.created_by,party_master.party_type,party_master.source";
		
        $queryData['leftJoin']['employee_master'] = "employee_master.id = party_activities.created_by";
        $queryData['leftJoin']['party_master'] = "party_master.id = party_activities.party_id";
        $queryData['leftJoin']['employee_master emp'] = "emp.id = party_master.sales_executive";

		
        if(!empty($data['party_id'])){ $queryData['where']['party_activities.party_id'] = $data['party_id']; }
        if(!empty($data['created_by'])){ $queryData['where']['party_activities.created_by'] = $data['created_by']; }
        if(!empty($data['lead_stage'])){ $queryData['where']['party_activities.lead_stage'] = $data['lead_stage']; }
        if(!empty($data['ref_date'])){ $queryData['where']['DATE(party_activities.ref_date)'] = $data['ref_date']; }
        if(!empty($data['customWhere'])){ $queryData['customWhere'][] = $data['customWhere']; }
		
        if(!empty($data['limit'])) { $queryData['limit'] = $data['limit']; }
        if(isset($data['start'])) { $queryData['start'] = $data['start']; }
        if(!empty($data['length'])) { $queryData['length'] = $data['length']; }


		if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id) > 0 OR party_activities.created_by = '.$this->loginId.')';
        endif;
		
		$queryData['group_by'][] = 'party_activities.id';
        $queryData['order_by']['party_activities.ref_date'] = 'ASC';
        $queryData['order_by']['party_activities.id'] = 'ASC';
		
        if(!empty($data['single_row'])):
            $result = $this->row($queryData);
        else:
            $result = $this->rows($queryData);
        endif;
        return $result;
    }

     public function changeLeadStages($param){
        try{
            $this->db->trans_begin();
            
			$notes = (!empty($param['notes']) ? $param['notes']: "");
            $remark = (!empty($param['remark']) ? $param['remark']: "");
            
            unset($param['notes'],$param['remark']);


			if($param['lead_stage'] != 11){
				$pa = $this->savePartyActivity(['party_id'=>$param['id'],'lead_stage'=>$param['lead_stage']]);
			}else{
				$logData = [
					'id'=>"",
					'party_id'=>$param['id'],
					'lead_stage'=>$param['lead_stage'],
					'ref_date' => date('Y-m-d H:i:s'),
					'notes'=>'Ohh..No ! We Lost..😞',
					'remark'=>$notes,
					'response'=>$remark
				];
				$pa = $this->store($this->partyActivities, $logData, 'Party Activity');
			}

            if($param['lead_stage'] == 12 ){ $param['lead_stage'] = 1 ; }

            $result = $this->store($this->partyMaster, $param, 'Lead Stage');

			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    /********** End Party Activity Detail **********/

    // Bulk Executive
    public function saveExecutive($data){
        try {
            $this->db->trans_begin();
			
			$this->savePartyActivity(['party_id'=>$data['id'],'lead_stage'=>9]);
			
            $result = $this->store($this->partyMaster,['id'=>$data['id'],'sales_executive'=>$data['executive_id']]);
           
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
?>