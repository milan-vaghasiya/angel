<?php
class AssetsMaster extends MY_Controller
{
    private $indexPage = "assets_master/index";
    private $formPage = "assets_master/form";
    private $challanForm = "assets_master/challan_form";
    private $challanIndex = "assets_master/challan_index";
    private $returnForm = "assets_master/return_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Assets Stock";
		$this->data['headData']->controller = "assetsMaster";
	}
	
	/* Created By @Raj 08-06-2025 */
	public function index($status=0){
        $this->data['status']=$status;
		
		if(in_array($status,[1])):
			$controller = 'assetsMasterChk'; 
		else: 
			$controller = 'assets_master';
		endif;
		
        $this->data['tableHeader'] = getStoreDtHeader($controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=1){ 
		$data=$this->input->post();
		$data['status']=$status;
		$result = $this->assetsMaster->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getAssetsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function getChallanDTRows($status=2){ 
		$data=$this->input->post();
		$data['status']=$status;
		$result = $this->qcChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = 'qcChallan';
            $sendData[] = getQcChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function inwardAsset(){
        $data = $this->input->post();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>4]);
        $this->data['dataRow'] = $this->assetsMaster->getItem(['id'=>$data['id'], 'single_row'=>1]);
        $this->data['status'] = $data['status'];
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";
		if (empty($data['item_code']))
            $errorMessage['item_code'] = "Item Code is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->assetsMaster->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>4]);
        $this->data['dataRow'] = $this->assetsMaster->getItem(['id'=>$id, 'single_row'=>1]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }
	
	public function saveRejectAsset(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['reject_reason'])):
            $errorMessage['reject_reason'] = "Reject Reason is required.";
        endif;
        
        $data['id'] = $data['assets_id'];
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->assetsMaster->saveRejectAsset($data));
        endif;
    }
	
	/* Assets Challan Start */
	public function assetsChallan(){
		$this->data['headData']->pageTitle = "Assets Challan";
		$this->data['tableHeader'] = getStoreDtHeader("assetsChallan");
        $this->load->view($this->challanIndex,$this->data);
	}
	
	public function getChDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->assetsMaster->getChDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function createChallan($id = ""){
		$this->data['trans_no'] = $this->assetsMaster->nextTransNo();
        $this->data['trans_prefix'] = 'ACH/'.getYearPrefix('SHORT_YEAR').'/';
		$this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>'3']);
        $this->data['itemData']  = $this->assetsMaster->getItem(['status'=>1]);
        $this->data['challanItem'] = $this->assetsMaster->getItem(['ids'=>$id]);
        $this->data['empData']  = $this->employee->getEmployeeList();
		$this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'location_not_in'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id,$this->CUT_STORE->id,$this->PACKING_STORE->id]]);
        $this->load->view($this->challanForm,$this->data);
	}
	
	public function editChallan($id){
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>'3']);
        $this->data['itemData']  = $this->assetsMaster->getItem(['status'=>1]);
        $this->data['empData']  = $this->employee->getEmployeeList();
		$this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'location_not_in'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id,$this->CUT_STORE->id,$this->PACKING_STORE->id]]);
        $this->data['dataRow'] = $this->assetsMaster->getAssetsChallan($id);
        $this->load->view($this->challanForm,$this->data);
    }
	
	public function saveChallan(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Challan No is required."; 
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Challan Date is required.";
		
		if($data['challan_type'] == 1){
			if(empty($data['location_id']))
				$errorMessage['location_id'] = "Location is required";
		}else if($data['challan_type'] == 2){
			if(empty($data['party_id']))
				$errorMessage['party_id'] = "Vendor is required";
		}else{
			if(empty($data['emp_id']))
				$errorMessage['emp_id'] = "Employee is required";
		}
        
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Items is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['id'],
                'trans_prefix' => $data['trans_prefix'],  
                'trans_no' => $data['trans_no'],
                'trans_number' => $data['trans_number'],
                'trans_date' => $data['trans_date'],
                'challan_type' => $data['challan_type'],
                'location_id' => $data['location_id'],
                'party_id' => $data['party_id'],
                'issue_to' => $data['issue_to'],
                'emp_id' => $data['emp_id'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->assetsMaster->saveChallan($masterData,$itemData));
        endif;
	}
	
	public function deleteChallan(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->assetsMaster->deleteChallan($id));
		endif;
	}
	
	public function returnChallan(){
		$id = $this->input->post('id');
        $this->data['dataRow'] = $this->assetsMaster->getAssetsChallanTrans(['id'=>$id,'single_row'=>1]);
        $this->load->view($this->returnForm,$this->data);
    }
	
	public function saveReceiveChallan(){
		$data = $this->input->post(); 
		$errorMessage = array();
		if(empty($data['receive_at']))
			$errorMessage['receive_at'] = "Date is required.";
        
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->assetsMaster->saveReceiveChallan($data));
        endif;
	}
	/* Ended By @Raj 09-06-2025
	Assets Challan End */
}
?>