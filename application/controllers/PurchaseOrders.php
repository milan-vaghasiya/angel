<?php
class PurchaseOrders extends MY_Controller{
    private $indexPage = "purchase_order/index";
    private $form = "purchase_order/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Purchase Order";
		$this->data['headData']->controller = "purchaseOrders";        
        $this->data['headData']->pageUrl = "purchaseOrders";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'purchaseOrders','tableName'=>'po_master']);
	}

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader("purchaseOrders");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->purchaseOrder->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPurchaseOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function getEnquiryList(){
        $data = $this->input->post();
		$this->data['enqItems'] = $this->purchase->getPurchaseEnqList(['party_id'=>$data['party_id'], 'orderData'=>1, 'status'=>'2,5']);
        $this->load->view('purchase_order/create_order',$this->data);
    }

    public function createOrder($id){ 
        $this->data['enqItemList'] = $enqItemList = $this->purchase->getPurchaseEnqList(['ids'=>$id, 'orderData'=>1]);
        $this->data['quoteNo'] = implode(", ",array_column($enqItemList,'quote_no'));
		$this->data['quoteDate'] = $enqItemList[0]->quote_date;
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);  
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view($this->form,$this->data);
    }
	
    public function addOrder(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitList'] = '';$this->item->itemUnits();
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
		if (formatDate($data['trans_date'], 'Y-m-d') < $this->startYearDate OR formatDate($data['trans_date'], 'Y-m-d') > $this->endYearDate)
			$errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->data['entryData']->trans_no;
                $data['trans_number'] = $this->data['entryData']->trans_prefix.$data['trans_no'];
            endif;
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->purchaseOrder->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->purchaseOrder->getPurchaseOrder(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->purchaseOrder->delete($id));
        endif;
    }

    public function printPO($id){
		$this->data['dataRow'] = $poData = $this->purchaseOrder->getPurchaseOrder(['id'=>$id,'itemList'=>1]);
		$this->data['partyData'] = $this->party->getParty(['id'=>$poData->party_id]);
        $taxClass = $this->taxClass->getTaxClass($poData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$this->data['termsData'] = (!empty($poData->termsConditions) ? $poData->termsConditions: "");
		
		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
				
		
		
		$prepare = $this->employee->getEmployee(['id'=>$poData->created_by]);
		$this->data['dataRow']->prepareBy = $prepareBy = $prepare->emp_name.' <br>('.formatDate($poData->created_at).')'; 
		$this->data['dataRow']->approveBy = $approveBy = '';
		if(!empty($poData->is_approve)){
			$approve = $this->employee->getEmployee(['id'=>$poData->is_approve]);
			$this->data['dataRow']->approveBy = $approveBy .= $approve->emp_name.' <br>('.formatDate($poData->approve_date).')'; 
		}

        $pdfData = $this->load->view('purchase_order/print',$this->data,true);
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;">PO No. & Date : '.$poData->trans_number.' ['.formatDate($poData->trans_date).']</td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='PO-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,5,10,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function getPartyOrderItems(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->purchaseOrder->getPendingInvoiceItems($data);
        $this->load->view('purchase_invoice/create_po_invoice',$this->data);
    }

	public function changeOrderStatus(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->purchaseOrder->changeOrderStatus($postData));
        endif;
    }
   
	public function addPOFromRequest($id){ 
        $this->data['req_id'] = $id;
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1); 
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->data['reqItemList'] = $this->purchaseIndent->getPurchaseRequestForOrder($id);
        $this->load->view($this->form,$this->data);
	}

	public function approvePurchaseOrder(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseOrder->approvePurchaseOrder($data));
		endif;
	}

    public function getPoWiseItemList(){
        $data = $this->input->post();
        $itemList = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $options = '<option value="">Select Product Name</option>';
        foreach($itemList as $row){
            $itemName = (!empty($row->item_code)) ? "[ ".$row->item_code." ] ".$row->item_name : $row->item_name;
            $options .= '<option value="'.$row->id.'">'.$itemName.(!empty($row->material_grade) ? ' '.$row->material_grade : '').'</option>';
        }
        $this->printJson(['options'=>$options]);
    }

	public function addPOFromForecast($postData){ 
		$data = (!empty($postData) ? json_decode(urldecode($postData)) : []);
		$so_ids = implode(',',array_column($data,'id'));

        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1); 
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['transportList'] = $this->transport->getTransportList();		
        $this->data['rmList'] = $this->purchaseIndent->getForecastDtRows(['so_ids'=>$so_ids,'rowData'=>'1']);
		$this->load->view($this->form,$this->data);
	}
}
?>