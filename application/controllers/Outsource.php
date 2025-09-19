<?php
class Outsource extends MY_Controller
{
    private $indexPage = "outsource/index";
    private $formPage = "outsource/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Outsource";
		$this->data['headData']->controller = "outsource";
		$this->data['headData']->pageUrl = "outsource";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader('outsource');
        $this->data['testTypeList'] = $this->testType->getTypeList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->outsource->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			$row->unit_id = $this->unit_id;
            $row->trans_status = $status;
            $sendData[] = getOutsourceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['ch_prefix'] = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['ch_no'] = $this->outsource->getNextChallanNo();
        $this->data['requestData']=$this->sop->getChallanrequestData(['pending_challan'=>1]);
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
		$this->data['transportList'] = $this->transport->getTransportList();
		$this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id'])){ $errorMessage['party_id'] = "Vendor is required.";}
        if(empty($data['id'])){ $errorMessage['general_error'] = "Select Item ";}else{
            foreach($data['id'] as $key=>$id){
                $reqData = $this->sop->getChallanRequestData(['id'=>$id,'single_row'=>1]);
                if($data['ch_qty'][$key] > $reqData->qty || empty($data['ch_qty'][$key])){
                    $errorMessage['chQty' . $id] = "Qty. is invalid.";
                }

                if(empty($data['process_ids'][$id])){
                    $errorMessage['process_ids' . $id] = "Process Required";
                }
                elseif($data['process_ids'][$id][0] != $reqData->process_id){
                    $errorMessage['process_ids' . $id] = "Invalid Process.";
                }
                else{
                    $process = explode(",",$reqData->process_ids);
					$outProcessList = $data['process_ids'][$id];
					$a = 0;$jwoProcessIds = array();
					foreach ($process as $k => $value) :
						if (isset($outProcessList[$a])) :
							$processKey = array_search($outProcessList[$a], $process);
							$jwoProcessIds[$processKey] = $outProcessList[$a];
							$a++;
						endif;
					endforeach;
					ksort($jwoProcessIds);
					
					$processList = array();
					foreach ($jwoProcessIds as $k => $value) :
						$processList[] = $value;
					endforeach;
					
					$nextProcessKey = array_search($reqData->process_id,$process);
					$i = 0;$error = false;
					foreach($process as $ky => $pid):
						if ($ky >= $nextProcessKey) :
							if (isset($processList[$i])) :
								if ($processList[$i] != $pid) :
									$error = true;
									break;
								endif;
								$i++;
							endif;
						endif;
					endforeach;

					if ($error == true) :
                        $errorMessage['process_ids' . $id] = "Invalid Process Sequence.";
					endif;

                    $lastProcessKey = array_search($data['process_ids'][$id][(count($data['process_ids'][$id])-1)],$data['process_ids'][$id]);
                    $data['next_process_ids'][$key] = (!empty($process[$lastProcessKey +1])?$process[$lastProcessKey +1]:0);
                }
            }
        }
		
        if(empty($data['ch_date'])){
            $errorMessage['ch_date'] = "Date is required."; 
        }else{
			if (($data['ch_date'] < $this->startYearDate) OR ($data['ch_date'] > $this->endYearDate)){
				$errorMessage['ch_date'] = "Invalid Date (Out of Financial Year).";
			}
		}

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->outsource->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->outsource->delete($id));
        endif;
    }  
    
    public function outSourcePrint($id){
        $this->data['outSourceData'] = $this->outsource->getOutSourceData(['id'=>$id]);
        $this->data['reqData'] = $this->sop->getChallanRequestData(['challan_id'=>$id]);
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	

        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
    
        $pdfData = $this->load->view('outsource/print', $this->data, true);        
		$mpdf = new \Mpdf\Mpdf();
        $pdfFileName='VC-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
        $mpdf->showWatermarkImage = true;
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
		
        $mpdf->WriteHTML($pdfData);
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    public function jobworkOutChallan($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;
        
        $printTypes = array();
        if(!empty($postData['original'])):
            $printTypes[] = "ORIGINAL";
        endif;

        if(!empty($postData['duplicate'])):
            $printTypes[] = "DUPLICATE";
        endif;

        if(!empty($postData['triplicate'])):
            $printTypes[] = "TRIPLICATE";
        endif;

        if(!empty($postData['extra_copy'])):
            for($i=1;$i<=$postData['extra_copy'];$i++):
                $printTypes[] = "EXTRA COPY";
            endfor;
        endif;

        $postData['header_footer'] = (!empty($postData['header_footer']))?1:0;
        $this->data['header_footer'] = $postData['header_footer'];

        $id = (!empty($postData['id']) ? $postData['id'] : '');
        $req_id = (!empty($postData['req_id']) ? $postData['req_id'] : '');
        $test_type = (!empty($postData['test_type']) ? $postData['test_type'] : '');

        $this->data['outSourceData'] = $this->outsource->getOutSourceData(['id'=>$id]);
        $this->data['reqData'] = $this->sop->getChallanRequestData(['challan_id'=>$id,'id'=>$req_id,'single_row'=>1,'challan_receive'=>1]);
        $prcBom = $this->sop->getPrcBomData(['prc_id'=>$this->data['reqData']->prc_id,'stock_data'=>1,'single_row'=>1]);
		
        $this->data['tcData'] = (!empty($test_type)) ? $this->materialGrade->getTcMasterData(['item_id'=>$this->data['reqData']->item_id,'test_type'=>$test_type]) : [];
		
		if($this->data['reqData']->cutting_flow == 1){
            $this->data['description_good'] = $prcBom->category_name;
            $this->data['material_wt'] = $prcBom->ppc_qty;
        }else{
            $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'heat_no'=>$prcBom->heat_no,'single_row'=>1]);
            $this->data['description_good'] = $cuttingBatch->category_name;
            $this->data['material_wt'] = $cuttingBatch->ppc_qty;
        }
		
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	
        
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
		
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
		    $pdfData .= $this->load->view('outsource/print', $this->data, true);
            if($i != $countPT): $pdfData .= "<pagebreak>"; endif;
        endforeach;
            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = 'Challan_' . $id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetProtection(array('print'));
		if(!empty($logo))
		{
		    $mpdf->SetWatermarkImage($logo,0.03,array(100,100));
		    $mpdf->showWatermarkImage = true;
		}
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
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
       
		$rmPrice = (!empty($rmPriceData->price)) ? $rmPriceData->price : 0;
        $material_price = ((!empty($mtPrice->price))?($mtPrice->price * $material_wt):0);
        $process_cost = ((!empty($processData->total_process_cost))?($processData->total_process_cost):0)+$rmPrice;
        $cost_per_pcs = $material_price + $process_cost;
        $this->printJson(['status'=>1,'cost_per_pcs'=>round($cost_per_pcs,3),'material_wt'=>$material_wt,'material_price'=>((!empty($mtPrice->price))?$mtPrice->price:0),'pre_process_cost'=>$process_cost]);
    }

    public function addLog(){
		$data = $this->input->post();
		$this->data['process_from'] = $data['process_from'];
		$this->data['dataRow'] = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'single_row'=>1]);
		$this->data['challan_id'] = $data['challan_id'];
        $this->data['ref_trans_id'] = $data['ref_trans_id'];
        $this->data['process_by'] = $data['process_by'];
        $this->data['processor_id'] = $data['processor_id'];
		$this->data['trans_type'] = (!empty($data['trans_type']))?$data['trans_type']:1;
		$this->data['processList'] = $this->process->getProcessList();
		
		$this->load->view('outsource/log_form',$this->data);
	}
	
	public function saveLog(){
		$data = $this->input->post(); 
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
        if (empty($data['in_challan_no'])){ $errorMessage['in_challan_no'] = "Challan is required.";}
		if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ $errorMessage['trans_date'] = "Date is required."; }
		
        foreach($data['process_id'] as $jobKey=>$process_id){

            $okQty = !empty($data['ok_qty'][$jobKey])?$data['ok_qty'][$jobKey]:0;
            $without_prs_qty = !empty($data['without_process_qty'][$jobKey])?$data['without_process_qty'][$jobKey]:0;
            $rej_qty =  (!empty($data['rej_found'][$jobKey])) ? $data['rej_found'][$jobKey] : 0;

            $totalReceivedQty = $okQty+$without_prs_qty+$rej_qty;

            if($jobKey == 0){
                $challanData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'challan_receive'=>1,'single_row'=>1]); 
                $pending_production =($challanData->qty * $challanData->output_qty) - ($challanData->ok_qty + $challanData->rej_qty + ($challanData->without_process_qty * $challanData->output_qty));
                if($totalReceivedQty == 0){
                    $errorMessage['ok_qty'.$process_id] = "Qty is required.";
                }elseif($totalReceivedQty > $pending_production){
                    $errorMessage['ok_qty'.$process_id] = "Qty is invalid.";
                }
            }elseif($jobKey > 0){
                
                if($totalReceivedQty > $data['ok_qty'][$jobKey-1]){
                    $errorMessage['ok_qty'.$process_id] = "Qty is invalid.";
                }
                elseif(!empty($data['ok_qty'][$jobKey-1]) && $data['ok_qty'][$jobKey-1] > 0 && $totalReceivedQty <$data['ok_qty'][$jobKey-1]){
                    $errorMessage['ok_qty'.$process_id] = "Qty is invalid.";
                }
                elseif(!empty($data['ok_qty'][$jobKey-1]) && $data['ok_qty'][$jobKey-1] > 0 && $totalReceivedQty <= 0){
                    $errorMessage['ok_qty'.$process_id] = "Qty is required.";
                }
            }
        }
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

			$this->printJson($this->outsource->saveLog($data));
		endif;	
	}

    public function getReceiveLogHtml(){
        $data = $this->input->post();
        $data['outsource_without_process'] = 1;
        $logData = $this->sop->getProcessLogList($data);
        $html="";
        if (!empty($logData)) :
            $i = 1;
            $processData = array_reduce($logData, function($processData, $process) { 
					$processData[$process->in_challan_no][] = $process; 
					return $processData; 
				}, []);
			foreach($processData AS $key=>$process){
				$firstRow=true;
				foreach($process AS $row){
					$html .= '<tr>';
					if($firstRow == true){
						$html .= '<td rowspan="'.count($process).'" class="text-center">'.$row->in_challan_no.'</td>';
						$html .= '<td rowspan="'.count($process).'" class="text-center">'.formatdate($row->trans_date).'</td>';
					}
					$html.='<td>'.$row->process_name.'</td>';
					$html.='<td class="text-center">'.floatval($row->qty).'</td>';
					$html.='<td class="text-center">'.floatval($row->rej_found).'</td>';
					$html.='<td class="text-center">'.floatval($row->without_process_qty).'</td>';
                   

					if($firstRow == true){
                        $createdBy = $row->created_name.(!empty($row->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($row->created_at)) : '');
						$deleteParam = "{'postData':{'id' : ".$row->id.",'last_log_id':'".$process[(count($process) - 1)]->id."'},'message' : 'Record','fndelete' : 'deleteLog','res_function':'getPrcLogResponse','controller':'outsource'}";
				        $deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
						$html.='<td rowspan="'.count($process).'">'.$createdBy.'</td>';
						$html.='<td rowspan="'.count($process).'" class="text-center">'.$deleteBtn.'</td>';
						$firstRow = false;
					}
					

					$html.='</tr>';
				}
			}
        else :
            $html = '<td colspan="12" class="text-center">No Data Found.</td>';
        endif;
        $this->printJson(['status'=>1,'tbodyData'=>$html]);
    }

    public function deleteLog(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->outsource->deleteLog($data));
        endif;
    }
}
?>