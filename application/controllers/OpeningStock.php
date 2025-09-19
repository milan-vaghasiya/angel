<?php
class OpeningStock extends MY_Controller{
    private $indexPage = "opening_stock/index";
    private $form = "opening_stock/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Opening Stock";
		$this->data['headData']->controller = "openingStock";        
        $this->data['headData']->pageUrl = "openingStock";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'openingStock']);        
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader("openingStock");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->itemStock->getItemInwardDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockInwardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addStock(){
        $data = $this->input->post();
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>['1,2,3,4,7,8,9']]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'location_not_in'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id,$this->PACKING_STORE->id,$this->FPCK_STORE->id]]);
		$this->data['brandList'] = $this->selectOption->getSelectOptionList(['type'=>8]);
        $this->load->view($this->form, $this->data);
    }
	
	public function save(){
        $data = $this->input->post();
		$errorMessage = array();		

		$itemData = [];
        if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Item Name is required.";
        }else{
            $itemData = $this->item->getItem(['id'=>$data['item_id']]);
            //Packing Material Then add stock in packing Area
            if($itemData->item_type == 9){
                $data['location_id'] = $this->PACKING_STORE->id;
            }
			if($itemData->item_type == 1){
				if(empty($data['brand_id'])){
					$errorMessage['brand_id'] = "Brand is required.";
				}
			}
        }
        if(empty(floatVal($data['qty'])))
			$errorMessage['qty'] = "Qty is required.";
        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";
		/*if(empty($data['batch_no']))
			$errorMessage['batch_no'] = "Batch No. is required.";*/
		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Date is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if($itemData->item_type == 1){ 
				$data['batch_no'] = n2y(date('Y',strtotime($data['trans_date']))).n2m(date('m'),strtotime($data['trans_date'])).date('d',strtotime($data['trans_date'])).'/'.sprintf('%03d',$data['brand_id']); 
			}
			unset($data['brand_id']);
            $this->printJson($this->itemStock->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemStock->deleteOpeningStock($id));
        endif;
    }
}
?>