<?php
class CustomerComplaints extends MY_Controller
{
    private $indexPage = "customer_complaints/index";
    private $formPage = "customer_complaints/form";
    private $solution_form = "customer_complaints/solution_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Customer Complaints";
		$this->data['headData']->controller = "customerComplaints";
		$this->data['headData']->pageUrl = "customerComplaints";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->customerComplaints->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCustomerComplaintsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCustomerComplaints(){
        $this->data['trans_prefix'] = "C" . n2y(date('Y')).n2m(date('m')); 
        $this->data['nextTransNo'] = $this->customerComplaints->getNextTransNo();
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList']=$this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

		if (empty($data['complaint']))
            $errorMessage['complaint'] = "Details of Complaint is required.";

        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";

        if (empty($data['product_returned']))
            $errorMessage['product_returned'] = "product returned is required.";

        if (empty($data['inv_id']))
            $errorMessage['inv_id'] = "Ref. of Complaint is required.";
		
        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = "Date is required.";
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
		
		if(!isset($data['qty'])){
            $resultData = $this->itemStock->getStockTrans(['item_id'=>$data['item_id'],'child_ref_id'=>$data['inv_trans_id'],'batch_no'=>$data['batch_no'],'trans_type'=>'INV']);
            if($data['qty'] > $resultData->qty) {
                $errorMessage['qty'] = "Invalid qty.";
            }
        }

        if(!empty($_FILES['defect_image'])):
            if($_FILES['defect_image']['name'] != null || !empty($_FILES['defect_image']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['defect_image']['name'];
                $_FILES['userfile']['type']     = $_FILES['defect_image']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['defect_image']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['defect_image']['error'];
                $_FILES['userfile']['size']     = $_FILES['defect_image']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/defect_image/');
                $config = ['file_name' => 'defect_image-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['defect_image'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $data['defect_image'] = $uploadData['file_name'];
                endif;
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
        	    'id' => $data['id'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_no' => $data['trans_no'], 
				'trans_number' => $data['trans_prefix'].sprintf("%04d",$data['trans_no']),
				'trans_date' => date('Y-m-d',strtotime($data['trans_date'])),
				'party_id' => $data['party_id'],
				'inv_trans_id' => $data['inv_trans_id'],
				'inv_id' => $data['inv_id'],
				'item_id' => $data['item_id'],
				'complaint' => $data['complaint'],
				'product_returned' => $data['product_returned'],
                'defect_image' => (!empty($data['defect_image'])?$data['defect_image']:''),
				'inv_date' => date('Y-m-d',strtotime($data['inv_date'])),
				'batch_no' => $data['batch_no'],
				'qty' => $data['qty']
            ];
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->customerComplaints->save($masterData));
        endif;
    }

	public function edit(){
        $data = $this->input->post(); 
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['dataRow'] = $dataRow = $this->customerComplaints->getCustomerComplaints(['id'=>$data['id']]);
        $soList = $this->getPObyParty(['inv_id'=>$dataRow->inv_id,'party_id'=>$dataRow->party_id]);
        $this->data['soOptions'] = $soList['partyOptions'];
        $itemList = $this->getItemList(['inv_id'=>$dataRow->inv_id,'item_id'=>$dataRow->item_id]);
		
        $this->data['itmOptions'] = $itemList['itemOptions'];
        $this->data['itemList']=$this->item->getItemList(['item_type'=>1]);
        $batchNoList = $this->getbatchNoList(['inv_trans_id'=>$dataRow->inv_trans_id,'item_id'=>$dataRow->item_id,'batch_no'=>$dataRow->batch_no]);
        
		$this->data['batchNo'] = $batchNoList['batchOption'];
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->customerComplaints->delete($id));
        endif;
    }

    public function getPObyParty($param =[]) {
		$data = (!empty($param)) ? $param : $this->input->post();
		$resultData = $this->customerComplaints->getSalesInvoiceByParty($data);
		$html = "<option value=''>Select Sales Invoice</option>";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $selected = (!empty($data['inv_id']) && $data['inv_id'] == $row->id) ? 'selected' : '';
                //$html .= '<option value="'.$row->id.'"  '.$selected.'>'.$row->trans_number.'</option>';
				$html .= '<option value="'.$row->id.'"  data-trans_date="'.$row->trans_date.'"  '.$selected.'>'.$row->trans_number.'</option>';
                $i++;
            endforeach;
        else:
            $html = '<option value="">Select Sales Invoice</option>';
        endif;

        if(!empty($param)):
            return['partyOptions'=>$html];
        else:
            $this->printJson(['partyOptions'=>$html]);
        endif;
	}

    public function getItemList($param =[]){
        $data = (!empty($param)) ? $param : $this->input->post();

        $options = '<option value="">Select Item Name</option>';
        if(empty($data['inv_id'])):
            $itemList= $this->item->getItemList(['item_type'=>1]);
            foreach($itemList as $row):
                $selected = (!empty($data['item_id']) && $data['item_id'] == $row->id) ? 'selected' : '';
                $options .= '<option value="'.$row->id.'" data-inv_trans_id="0" '.$selected.'>'.((!empty($row->item_code)?'[ '.$row->item_code.' ] ': '').$row->item_name).'</option>';
            endforeach;
        else:
            $itemList = $this->salesInvoice->getPendingInvoiceItems($data);
            foreach($itemList as $row):
                $selected = (!empty($data['item_id']) && $data['item_id'] == $row->item_id) ? 'selected' : '';
                $options .= '<option value="'.$row->item_id.'" data-inv_trans_id="'.$row->id.'" '.$selected.'>'.((!empty($row->item_code)?'[ '.$row->item_code.' ] ': '').$row->item_name).'</option>';
            endforeach;
        endif;

        if(!empty($param)):
            return['itemOptions'=>$options];
        else:
            $this->printJson(['itemOptions'=>$options]);
        endif;
    }
	
    public function getbatchNoList($param =[]) {
		$data = (!empty($param)) ? $param : $this->input->post();
		$resultData = $this->itemStock->getStockTrans(['item_id'=>$data['item_id'],'child_ref_id'=>$data['inv_trans_id'],'trans_type'=>'INV','multi_rows'=>1]);
		$html = "<option value=''>Select Batch No.</option>";
        if(!empty($resultData)):
            foreach($resultData as $row):
                $selected = (!empty($data['batch_no']) && $data['batch_no'] == $row->batch_no) ? 'selected' : '';
                $html .= '<option value="'.$row->batch_no.'" data-qty="'.$row->qty.'"  '.$selected.'>'.$row->batch_no.'</option>';
            endforeach;
        endif;

        if(!empty($param)):
            return['batchOption'=>$html];
        else:
            $this->printJson(['batchOption'=>$html]);
        endif;
	}
 
    public function complaintSolution(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->customerComplaints->getCustomerComplaints(['id'=>$data['id']]);
        $this->load->view($this->solution_form,$this->data);
    }
    
    public function saveSolution(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['action_taken']))
            $errorMessage['action_taken'] = " Action Taken is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			if(isset($_FILES['report_8d']['name'])):
                if($_FILES['report_8d']['name'] != null || !empty($_FILES['report_8d']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['report_8d']['name'];
                    $_FILES['userfile']['type']     = $_FILES['report_8d']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['report_8d']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['report_8d']['error'];
                    $_FILES['userfile']['size']     = $_FILES['report_8d']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/8d_report/');
                    $config = ['file_name' => 'report-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['report_8d'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['report_8d'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
            $data['status'] =1;
            $this->printJson($this->customerComplaints->save($data));
        endif;
    }
}
?>