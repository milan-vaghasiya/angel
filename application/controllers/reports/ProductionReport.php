<?php
class ProductionReport extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Report";
		$this->data['headData']->controller = "reports/productionReport";
    }

    public function productionAnalysis(){
		$this->data['headData']->pageTitle = "PRODUCTION ANALYSIS";
        $this->data['headData']->pageUrl = "reports/productionReport/productionAnalysis";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/production_report/production_analysis",$this->data);
    }

    public function getProductionAnalysisData(){
        $data = $this->input->post();
        $customWhere = "prc_log.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$postData = [
			'item_id'=>$data['item_id'],
			//'prc_type'=>1,
			'breakdown'=>1,
			'grouped_data'=>1,
			'rejection_review_data'=>1,
			'customWhere'=>$customWhere,
			'group_by'=>'prc_log.trans_date,prc_master.item_id,prc_log.processor_id,prc_log.process_id'
		];
		if(!empty($data['process_id'])){
			$postData['process_id'] = $data['process_id'];
		}
        $result = $this->sop->getProcessLogList($postData);

        $tbody = '';$tfoot = '';
        $i = 1;  $totalIdeal = 0;$totalActual = 0;$totalLost = 0;
        foreach($result as $row):

            $totalProd = floor($row->ok_qty + $row->rej_qty + $row->rw_qty + $row->pending_qty);
    
            $workingHour = round($row->production_time / 60 ,2);

            $breakdownHour = round($row->breakdown_time / 3600 ,2);

            $ideal_ph=0;
            if(!empty($row->cycle_time) && $row->cycle_time > 0) { $ideal_ph  = floor(3600 / $row->cycle_time); }
            $ideal_total  = floor($ideal_ph * $workingHour);

            $actual_ph=0;
            if(!empty($totalProd) && !empty($workingHour)) { $actual_ph  = floor($totalProd / $workingHour); }

            $lost_ph = ($ideal_ph - $actual_ph);
            $lost_total = ($ideal_total - $totalProd);

            $tbody .= '<tr>
                <td>'.$i.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td class="text-left">'.$row->item_name.'</td>
                <td>'.$row->process_name.'</td>
                <td>'.$row->processor_name.'</td>
                <td>'.$row->cycle_time.'</td>
                <td>'.$workingHour.'</td>
                <td>'.$breakdownHour.'</td>
                <td>'.(($ideal_ph > 0) ? $ideal_ph : 0).'</td>
                <td>'.(($ideal_total > 0) ? $ideal_total : 0).'</td>
                <td>'.(($actual_ph > 0) ? $actual_ph : 0).'</td>
                <td>'.(($totalProd > 0) ? $totalProd : 0).'</td>
                <td>'.(($lost_ph > 0) ? $lost_ph : 0).'</td>
                <td>'.(($lost_total > 0) ? $lost_total : 0).'</td>
                <td>
					<a href="'.base_url("reports/productionReport/productionDetail/".$row->trans_date.'/'.$row->item_id.'/'.$row->process_id.'/'.$row->processor_id).'" target="_blank" datatip="Production Detail" flow="left">'.(($lost_total > 0) ? $lost_total + ($breakdownHour * $ideal_ph) : ($breakdownHour * $ideal_ph)).'</a>
				</td>
            </tr>';
            $i++;
            $totalIdeal += (($ideal_total > 0) ? $ideal_total : 0);
            $totalActual += (($totalProd > 0) ? $totalProd : 0);
            $totalLost += (($lost_total > 0) ? $lost_total : 0);
        endforeach;

            $tfoot .= '<tr>
                <th colspan="8" class="text-right">Total</th>
                <th></th>
                <th class="text-center">'.$totalIdeal.'</th>
                <th></th>
                <th class="text-center">'.$totalActual.'</th>
                <th></th>
                <th class="text-center">'.$totalLost.'</th>
                <th></th>

            </tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    public function productionDetail($trans_date = "",$item_id="",$process_id="",$processor_id=""){
		$this->data['headData']->pageTitle = "Production Detail";
        
        $result = $this->sop->getProcessLogList(['item_id'=>$item_id,'process_id'=>$process_id,'processor_id'=>$processor_id,'trans_date'=>$trans_date,'rejection_review_data'=>1]);
        $tbodyData = '';$i=1;
        foreach($result as $row):
            $idealQty = (!empty($row->cycle_time) ? round($row->production_time / $row->cycle_time ,2) : 0);
            $actualQty = ($row->qty + $row->rej_qty + $row->rw_qty + $row->pending_qty);
            $tbodyData .= '<tr>
                <td class="text-center">'.$i++.'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->start_time)).'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->end_time)).'</td>
                <td>'.$row->prc_number.'</td>
                <td>'.$row->process_name.'</td>
                <td>'.$row->machine_name.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->shift_name.'</td>
                <td>'.$row->production_time.'</td>
                <td>'.$idealQty.'</td>
                <td>'.$actualQty.'</td>
                <td>'.$row->qty.'</td>
                <td>'.$row->rej_qty.'</td>
                <td></td>
            </tr>';
        endforeach;
        $this->data['tbodyData'] = $tbodyData;
        $this->load->view('reports/production_report/production_detail',$this->data);
    }

	public function stageWiseProduction(){
        $this->data['headData']->pageTitle =  'Stage Wise Production';
        $this->data['headData']->pageUrl = "reports/productionReport/stageWiseProduction";
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,5"]);
        $this->load->view("reports/production_report/stage_wise_production",$this->data);
    }

    public function getStageWiseProductionData(){
        $data = $this->input->post();
        $productProcessData = $this->item->getProductProcessList(['item_id'=>$data['item_id']]);
        $thead = '<tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">PRC Date</th>
                    <th rowspan="2">PRC No</th>
                    <th rowspan="2">PRC Qty</th>';
        $thead2 = '<tr>';
        $processArray = [];
		
        if(!empty($productProcessData)){
            foreach($productProcessData as $row){
                $thead .= '<th colspan="4">'.$row->process_name.'</th>';
                $thead2 .= '<th>In</th>
                            <th>Ok</th>
                            <th>Rej</th>
                            <th>Pending</th>';
                $processArray[] = $row->process_id;
            }
        } 
        $thead2 .= '</tr>';
        $thead .= '</tr>';
        $prcData = $this->sop->getPRCProcessList(['item_id'=>$data['item_id'],'prc_type'=>1,'order_by_date'=>1,'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'group_by'=>'prc_master.id,product_process.process_id','move_type'=>1]);
        $tbody = '';$i=1;$tfoot = "";
		
        if(!empty($prcData)){
            $prcArray = [];
            foreach($prcData as $row){
                if(!empty($row->prc_id)){
                    $prcArray[$row->prc_id]['prc_number'] = $row->prc_number;
                    $prcArray[$row->prc_id]['prc_date'] = $row->prc_date;
                    $prcArray[$row->prc_id]['prc_qty'] = $row->prc_qty;
                    $prcArray[$row->prc_id][$row->process_id] = $row;
                }
                
            }
            $totalIn = [];$totalOk=[];$totalRej=[];$totalPending = [];
            foreach($prcArray as $prc){
                $tbody .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.formatDate($prc['prc_date']).'</td>
                            <td>'.$prc['prc_number'].'</td>
                            <td>'.floatVal($prc['prc_qty']).'</td>';
							
					foreach($processArray as $key=>$process_id){
						$in_qty = (!empty($prc[$process_id]->in_qty)?$prc[$process_id]->in_qty:'0');
						$ok_qty = !empty($prc[$process_id]->ok_qty)?$prc[$process_id]->ok_qty:0;
						$rej_found_qty = !empty($prc[$process_id]->rej_found)?$prc[$process_id]->rej_found:0;
						$rej_qty = !empty($prc[$process_id]->rej_qty)?$prc[$process_id]->rej_qty:0;
						$rw_qty = !empty($prc[$process_id]->rw_qty)?$prc[$process_id]->rw_qty:0;
						$pendingReview = $rej_found_qty - (!empty($prc[$process_id]->review_qty)?$prc[$process_id]->review_qty:0);
						$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
						
						$tbody .= '<td>'.(($in_qty > 0)?floatval($in_qty):'').'</td>
								   <td>'.(($ok_qty > 0)?floatval($ok_qty):'').'</td>
								   <td>'.(($rej_qty > 0)?floatval($rej_qty):'').'</td>
								   <td>'.(($pending_production > 0)?floatval($pending_production):'').'</td>';
					   $totalIn[$process_id][] = $in_qty; $totalOk[$process_id][] = $ok_qty; $totalRej[$process_id][]=$rej_qty; $totalPending[$process_id][]= $pending_production;
					}
					
				$tbody .= '</tr>';
            }
            $tfoot = '<tr>
                        <th colspan="4" class="text-right">Total</th>';
            foreach($processArray as $key=>$process_id){
                $tfoot .= '<th>'.array_sum($totalIn[$process_id]).'</th>
						<th>'.array_sum($totalOk[$process_id]).'</th>
						<th>'.array_sum($totalRej[$process_id]).'</th>
						<th>'.array_sum($totalPending[$process_id]).'</th>';
            }
                       
            $tfoot .= '</tr>';
            
        }
        
        $this->printJson(['status'=>1,'tbody'=>$tbody,'thead'=>$thead.$thead2,'tfoot'=>$tfoot]);
    }
	
    /* PRC REGISTER */
	public function prcRegister(){
        $this->data['headData']->pageTitle = 'PRC Register';
        $this->data['headData']->pageUrl = "reports/productionReport/prcRegister";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/production_report/prc_register",$this->data);
    }

    public function getPrcRegisterData(){
        $data = $this->input->post();

        $jobCardData = $this->productionReport->getPrcRegisterData($data);
        $tbody = ''; $tfoot = '';
        $i = 1;  $totalQty = 0; $totalOkQty = 0;$totalRejQty = 0;
        foreach ($jobCardData as $row) :
            $job_no = '<a href="'.base_url("sopDesk/printDetailRouteCard/".$row->id).'" target="_blank">'.$row->prc_number.'</a>';
            $party_name = (!empty($row->party_name) ? $row->party_name : "Self Stock");
            $sales_order = (!empty($row->so_no) ? $row->so_no : "-");
            $item_name = (!empty($row->item_code)? "[".$row->item_code."] " : "").$row->item_name;

            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $job_no . '</td>
                <td>' . formatDate($row->prc_date) . '</td>
                <td>' . $party_name . '</td>
				<td>' . $sales_order . '</td>
                <td>' . $item_name . '</td>
                <td>' . floatVal($row->prc_qty) . '</td>
                <td>' . floatVal($row->ok_qty) . '</td>
                <td>' . floatVal($row->rej_qty) . '</td>
                <td>' . $row->emp_name . '</td>
                <td>' . $row->job_instruction . '</td>
            </tr>';
            
            $totalQty += floatval($row->prc_qty);
            $totalOkQty += floatval($row->ok_qty);
            $totalRejQty += floatval($row->rej_qty);

        endforeach;
        $tfoot .= '<tr>
            <th colspan="6" class="text-right">Total</th>
            <th class="text-center">'.$totalQty.'</th>
            <th class="text-center">'.$totalOkQty.'</th>
            <th class="text-center">'.$totalRejQty.'</th>
            <th></th>
            <th></th>
        </tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    /* Jobwork Register */
    public function outSourceRegister(){
		$this->data['headData']->pageTitle = "Outsource Register";
        $this->data['headData']->pageUrl = "reports/productionReport/outSourceRegister";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/production_report/outsource_register",$this->data);
    }

    public function getOutSourceRegister(){
        $data = $this->input->post();
        $jobOutData = $this->productionReport->getOutSourceRegister($data);
        $blankInTd = '<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $i = 1;
        $tblData = "";$tfoot=""; $totalQty=0; $totalInQty=0;$totalRejQty=0;$totalWtQty=0;$totalPendQty=0;
        foreach ($jobOutData as $row) :
            $pendingQty = (($row->qty * $row->output_qty)- ($row->ok_qty + $row->rej_qty + $row->without_process_qty));
            $outData = $this->productionReport->getJobInwardData(['ref_id'=>$row->challan_id,'prc_id'=>$row->prc_id]);
            $outCount = count($outData); 
			
			$ch_number = $row->ch_number;//'<a href="'.base_url("outsource/jobworkOutChallan/".$row->challan_id).'" target="_blank">'.$row->ch_number.'</a>';
            $tblData .= '<tr>
				<td>' . $i++ . '</td>
				<td>' . $ch_number. '</td>
				<td>' . formatDate($row->ch_date) . '</td>
				<td>' . $row->prc_number. '</td>
				<td>' . $row->party_name. '</td>
				<td>' . (!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name . '</td>
				<td>' . $row->process_name . '</td>
				<td>' . floatVal($row->qty) . '</td>
                <td>' . floatVal($pendingQty) . '</td>
                <td>' . floatVal($row->price) . '</td>
                <td>' . round(($row->price * $row->qty),2) . '</td>';
                            
			$totalQty+= $row->qty;			
            $totalPendQty+= floatVal($pendingQty);

							
            if ($outCount > 0) :
                $usedQty = 0; $j=1;
                foreach ($outData as $outRow) :

					$outQty = $row->qty;
					$wpQty = (($j ==1) ? floatVal($row->without_process_qty) :  0);

					$tblData .= '<td>' . formatDate($outRow->trans_date) . '</td>
								<td>' . $row->party_name. '</td>
								<td>' . $outRow->in_challan_no . '</td>
								<td>' . floatVal($outRow->qty) . '</td>
								<td>' . floatVal($outRow->rej_qty) . '</td>
								<td>' . $wpQty. '</td>
								';   
								
                    if ($j != $outCount) {
                        $tblData .= '</tr><tr><td>' . $i++ . '</td>' . $blankInTd;
                    }
					
                    $j++;

                    $totalInQty+=floatVal($outRow->qty);
					$totalRejQty += floatVal($outRow->rej_qty);
					$totalWtQty += floatVal($wpQty);
                endforeach;
            else :
                $tblData .= '<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>';
            endif;
            $tblData .= '</tr>';
        endforeach;
		
        $tfoot = '<tr>
                <th colspan="7" class="text-right">Total</th>
                <th>'.floatVal($totalQty).'</th>                
                <th>'.floatVal($totalPendQty).'</th>
                <th></th>
                <th></th>               
                <th></th>
                <th></th>
                <th></th>
                <th>'.floatVal($totalInQty).'</th>
                <th>'.(floatVal($totalRejQty)).'</th>
                <th>'.(floatVal($totalWtQty)).'</th>

            </tr>';
        $this->printJson(['status' => 1, "tblData" => $tblData,"tfoot"=>$tfoot]);
    }
	
    public function productionLogSheet(){
        $this->data['headData']->pageTitle = 'PRODUCTION LOG SHEET';
        $this->data['headData']->pageUrl = "reports/productionReport/productionLogSheet";
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/production_report/production_log_sheet", $this->data);
    }

	public function getProductionLogSheet(){
        $data = $this->input->post();
        $productionData = $this->productionReport->getProductionLogSheet($data);

		$tbody = '';$i=1; $tfoot=''; 
        $totalOkQty = $totalRejQty = $totalProdQty = $totalScrap = $totalProdWt = $totalRejPer = $totalPrsScrapWt = $totalPrcQty = 0;

        if(!empty($productionData)):
            $prcIds = array_unique(array_column($productionData,'prc_id'));
            $cuttingData = $this->productionReport->getPrcCutWeight(['prc_id'=>$prcIds]);
             $cuttingPRC = array_reduce($cuttingData, function($prcData, $prc) { 
                if(!empty($prc->cut_weight)){
                    $weight = explode("-",$prc->cut_weight);
                    $prc->cut_weight = ((!empty($weight[1]) && $weight[1] > 0)?$weight[1]:((!empty($weight[0]))?$weight[0]:0));
                }
                $prcData[$prc->prc_id] = $prc; 
                return $prcData; 
            }, []);
            // print_r($cuttingPRC);
            foreach($productionData as $row):
                $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                $total_rej_qty = !empty($row->total_rej_qty)?$row->total_rej_qty:0;
                $productionQty = $ok_qty + $total_rej_qty;
                if($productionQty > 0):
                
                
                    $forge_wt = !empty($row->finish_wt) ? $row->finish_wt : 0;
                    
                    /* Scrap WT/Pc Calculation */
                    if($row->process_from > 0){
                        $prevWt = $row->prev_fg_wt ;
                    }else{
                        $prevWt = ($row->cutting_flow == 2)?(!empty($cuttingPRC[$row->prc_id]->cut_weight)?$cuttingPRC[$row->prc_id]->cut_weight:0):$cuttingPRC[$row->prc_id]->ppc_qty;
                    }
                
                    $prsScrapWt = ($forge_wt > 0) ? $productionQty * ($prevWt -  $forge_wt) : 0;
                    /* Scrap WT/Pc Calculation */
                    
                    $total_prod_wt = ($prevWt * $productionQty);
                    $rej_kg = ($prevWt * $total_rej_qty);
                    $total_scrap = ($prsScrapWt * $productionQty);
                    
                    $rej_per = (($total_rej_qty/$productionQty)*100);
                    $tbody .= '<tr class="text-center">
                        <td class="text-left">'.$i.'</td>
                        <td class="text-left">'.$row->emp_name.'</td>
                        <td class="text-left">'.$row->processor_name.'</td>
                        <td class="text-left">'.$row->prc_number.'</td>
                        <td class="text-left">'.$row->shift_name.'</td>
                        <td>'.$row->production_time.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td class="text-left">'.$row->material_grade.'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->prc_qty.'</td>
                        <td>'.$productionQty.'</td>
                        <td class="text-left">'.$row->process_name.'</td>
                        <td>'.$forge_wt.'</td>
                        <td>'.$prevWt.'</td>
                        <td>'.$total_prod_wt.'</td>
                        <td>'.$total_rej_qty.'</td>
                        <td>'.$rej_kg.'</td>
                        <td>'.$row->ok_qty.'</td>
                        <td>'.$prsScrapWt.'</td>
                        <td>'.$total_scrap.'</td>
                        <td>'.round($rej_per,2).'</td>
                    </tr>';
                    $i++;

                    $totalOkQty += floatval($row->ok_qty);
                    $totalRejQty += floatval($total_rej_qty);
                    $totalProdQty += floatVal($productionQty);
                    $totalScrap += floatVal($total_scrap);
                    $totalProdWt += floatVal($total_prod_wt);
                    $totalOkQty += floatVal($ok_qty);
                    $totalPrsScrapWt += floatVal($prsScrapWt);
                    $totalRejPer += round($rej_per,2);
                endif;
            endforeach;
			$tfoot .= '<tr>
                <th colspan="9" class="text-right">Total</th>
                <th class="text-center">'.$totalPrcQty.'</th>
                <th class="text-center">'.$totalProdQty.'</th>
                <th colspan="3"></th>
                <th class="text-center">'.$totalProdWt.'</th>
                <th class="text-center">'.$totalRejQty.'</th>
                <th></th>
                <th class="text-center">'.$totalOkQty.'</th>
                <th class="text-center">'.$totalPrsScrapWt.'</th>
                <th class="text-center">'.$totalScrap.'</th>
                <th class="text-center">'.$totalRejPer.'</th>
            </tr>';
        endif;
        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
	
    /* Job Costing Report */
	public function jobCosting(){
        $this->data['headData']->pageTitle = 'Job Costing After Production';
        $this->data['headData']->pageUrl = "reports/productionReport/jobCosting";
        $this->data['prcList'] = $this->sop->getPRCList(['status'=>3]);
        $this->load->view("reports/production_report/job_costing",$this->data);
    }

    public function getJobCostingData(){
        $data = $this->input->post();
        $prcData = $this->sop->getPRC(['id'=>$data['prc_id']]);

        $process = explode(",",$prcData->process_ids);

        $i=1; $tbody=""; $totalCosting=0; $stock=0;
        foreach($process as $process_id):
            $result = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'], 'process_id'=>$process_id, 'log_data'=>1]);
            if(!empty($result)) :
                foreach ($result as $row) :
                    $costing = (!empty($row->process_cost) && !empty($row->ok_qty))? $row->process_cost * $row->ok_qty : 0;
                    $tbody .= '<tr class="text-center">
                                <td>'.$i++.'</td>
                                <td>'.$row->current_process.'</td>
                                <td>'.floatval($row->ok_qty).'</td>
                                <td>'.floatVal($row->process_cost).'</td>
                                <td>'.floatVal($costing).'</td>
                            </tr>';
                    $totalCosting += $costing;
                endforeach;
            endif;
        endforeach;

        $tfoot = '<tr class="thead-info">
            <th colspan="4">TOTAL</th>
            <th>' . $totalCosting . '</th>
        </tr>';

       $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    /* Production Bom Report */
	public function productionBom(){
        $this->data['headData']->pageTitle = 'Production Bom Report';
        $this->data['headData']->pageUrl = "reports/productionReport/productionBom";
        $this->data['itemData'] = $this->item->getItemList([1,3]);
        $this->load->view("reports/production_report/production_bom",$this->data);
    }

    public function getItemBomData(){
        $data = $this->input->post();
        $result = $this->item->getProductKitData($data);
        $i = 1;
		$tbody = "";
        $thead =  "";
        $tfoot = ""; $totalQty = 0;
		
        if(!empty($data['item_id'])){
            $thead = '<tr>
                        <th>#</th>
                        <th>BOM Item</th>
                        <th>BOM Item Code</th>
                        <th>BOM Qty</th>
                    </tr>';
        }else{
            $thead = '<tr>
                         <th>#</th>
                        <th>Product Name</th>
                        <th>Product Code</th>
                        <th>BOM Qty</th>
                    </tr>';
        }
		foreach ($result as $row) :
            
			$tbody .= '<tr class="text-center">';
            if(!empty($data['item_id'])){
                $tbody .= ' <td>' . $i++ . '</td>
                            <td>' . $row->item_name . '</td>
                            <td>' . $row->item_code . '</td>
                            <td>' . $row->qty . '</td>';
            }else{
                $tbody .= ' <td>' . $i++ . '</td>
                            <td>' . $row->product_name . '</td>
                            <td>' . $row->product_code . '</td>
                            <td>' . $row->qty . '</td>';
            }
						
			$tbody .= '</tr>';
			
            $totalQty += $row->qty;
		endforeach;
		
        $tfoot .= '<tr class="thead-dark">
            <th colspan="3" class="text-right">Total</th>
            <th class="text-center">'.$totalQty.'</th> 
        </tr>';
		
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead, 'tfoot' => $tfoot]);
    }

    /* Material Requirement Planning */
	public function materialReqPlan(){
        $this->data['headData']->pageTitle = 'Material Requirement Planning';
        $this->data['headData']->pageUrl = "reports/productionReport/materialReqPlan";
        $this->data['itemList'] = $this->salesOrder->getPendingOrderItems(['group_by'=>'so_trans.item_id', 'mrp_report'=>1]);
        $this->load->view("reports/production_report/material_req_plan",$this->data);
    }

	public function getMaterialReqPlanData(){
        $data = $this->input->post();

        $stockData = $this->itemStock->getItemStockBatchWise(['item_id' => $data['item_id'], 'stock_required' => 1, 'group_by'=>'stock_trans.location_id,stock_trans.batch_no']);

        $packStockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_ids'=>[$this->PACKING_STORE->id,$this->FPCK_STORE->id],'single_row'=>1,'stock_required' => 1,'group_by'=>'stock_trans.location_id']);//28-03-25

        $stockTbody = ""; $stockTfoot = ""; $totalStockQty = 0;
		
        if(!empty($stockData)):
            $i=1;
            foreach($stockData as $row):
                $stockTbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>['.$row->store_name.'] '.$row->location.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->qty.'</td>
                </tr>';
                $totalStockQty += $row->qty; 
            endforeach;            
        else:
            $stockTbody .= '<tr>
                <td colspan="4" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;

        $stockTfoot = '<tr>
            <th colspan="3" class="text-right">Total</th>
            <th>'.$totalStockQty.'</th>
        </tr>';
        $data['pending_qty'] = $data['pending_qty'] - $totalStockQty;

        $wipTbody=''; $wipTfoot=''; $totalWIPQty=0;
        $wipData = $this->sop->getItemWiseWip(['item_id'=>$data['item_id'],'log_data'=>1,'pending_accepted'=>1,'group_by'=>'product_process.process_id','status'=>'1,2']);
        if(!empty($wipData)):
            $i=1;
            foreach($wipData as $row):
                $in_qty = (!empty($row->in_qty)?$row->in_qty:0);
                $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                $movement_qty = !empty($row->movement_qty)?$row->movement_qty:0;
                $rej_found_qty = !empty($row->rej_found)?$row->rej_found:0;
                $rej_qty = !empty($row->rej_qty)?$row->rej_qty:0;
                $rw_qty = !empty($row->rw_qty)?$row->rw_qty:0;
                $pendingReview = $rej_found_qty - $row->review_qty;
                $pending_production =(($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview));
                $pending_movement = $ok_qty - $movement_qty;
                $total = $pending_production + $pending_movement ;
                $wipTbody.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->current_process.'</td>
                    <td>'.floatVal($pending_production).'</td>
                    <td>'.floatVal($pending_movement).'</td>
                    <td>'.floatVal($total).' </td>
                </tr>';
                $totalWIPQty += $total;
            endforeach;
        else:
            $wipTbody .= '<tr>
                <td colspan="3" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;

        $wipTfoot = '<tr>
            <th colspan="4" class="text-right">Total</th>
            <th>'.$totalWIPQty.'</th>
        </tr>';
        $data['pending_qty'] = $data['pending_qty'] - $totalWIPQty;

        $materialTbody = "";$req_qty = 0;
        if($data['pending_qty'] > 0):
            $req_qty = $data['pending_qty'];
            $itemBom = $this->item->getProductKitData(['item_id'=>$data['item_id']]);
            if(!empty($itemBom)):
                $i=1;
                foreach($itemBom as $row):
                    $itemStock = $this->itemStock->getItemStockBatchWise(['item_id' => $row->ref_item_id,'single_row'=>1]);
                    $shortage = (($data['pending_qty'] * $row->qty) - $itemStock->qty);
                    $materialTbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.($data['pending_qty'] * $row->qty).' <small>'.$row->uom.'</small> </td>
                        <td>'.((!empty($itemStock->qty) && $itemStock->qty > 0)?$itemStock->qty:0).' <small>'.$row->uom.'</small></td>
                        <td>'.$shortage.' <small>'.$row->uom.'</small></td> 
                    </tr>';
                endforeach;
            else:
                $materialTbody .= '<tr>
                    <td colspan="5" class="text-center">
                        Material not found.
                    </td>
                </tr>';
            endif;
        else:
            $materialTbody .= '<tr>
                <td colspan="5" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;        
        
        $this->printJson(['status' => 1,'stockTbody'=>$stockTbody, 'stockTfoot'=>$stockTfoot, 'wipTbody'=>$wipTbody, 'wipTfoot'=>$wipTfoot, 'materialTbody'=>$materialTbody,'req_qty'=>$req_qty,'totalStockQty'=>$totalStockQty,'totalWIPQty'=>$totalWIPQty,'in_packing'=>(!empty($packStockData->qty) ? $packStockData->qty :0)]);//28-03-25
    }
	
	/* WIP RM Repor*/
    public function wipRawMaterialReport(){
        $this->data['headData']->pageTitle = 'WIP RM Report';
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,3"]);
        $this->load->view("reports/production_report/wip_raw_material", $this->data);
    }

    public function getWIPRawMaterialReport(){
        $data = $this->input->post();
        $prcData = $this->productionReport->getWIPRawMaterialData(['item_id'=>$data['item_id'],'stock_data'=>1,'production_data'=>1]);

        $tbody = '';$i=1; $tfoot=''; $totalIssueQty = 0;$totalUsedQty = 0;$totalReturnQty = 0;$totalStockQty = 0;
        foreach($prcData as $row):
            $usedQty=0; $returnQty=0; $stockQty=0; $issue_qty=0; $prcProcessData=[];
            if($row->prc_type == 2){
                $usedQty = (!empty($row->cutting_cons) ? floatval($row->cutting_cons) : 0);
                $stockQty = $row->issue_qty - ($usedQty + $row->cutting_return_qty);
                $returnQty = (!empty($row->cutting_return_qty) ? floatval($row->cutting_return_qty) : 0);
            }else{
                if($row->status > 1){ 
                    $prcProcessData = $this->sop->getPRCProcessList(['prc_id'=>$row->id,'process_id'=>$row->process_ids,'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'move_type'=>1]);
                    }
                elseif(!empty($row->process_ids)){ 
                    $prcProcessData = $this->sop->getProcessFromPRC(['process_ids'=>$row->process_ids,'item_id'=>$row->item_id]); 
                }
                if($row->process_id == 0 && $row->cutting_flow == 2){
                    $usedQty = ((!empty($prcProcessData))?(floatval(($prcProcessData[0]->ok_qty + $prcProcessData[0]->rej_found) * $row->ppc_qty)/$prcProcessData[0]->output_qty):0);
                }else{
                    $usedQty = floatval($row->production_qty * $row->ppc_qty)/$row->output_qty;
                }
                $stockQty = $row->issue_qty - ($usedQty + $row->return_qty);
                $returnQty = (!empty($row->return_qty) && $row->return_qty > 0)?floatval($row->return_qty):0;
            }
            if(round($stockQty) > 0){ 
                $tbody.='<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->prc_number.'</td>
                            <td>'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.'</td>
                            <td>'.(!empty($row->issue_qty) ? floatval($row->issue_qty) : 0).'</td>
                            <td>'.$usedQty.'</td>
                            <td>'.$returnQty.'</td>
                            <td>'.round($stockQty).'</td>
                        </tr>';
                        $i++;

                $totalIssueQty += floatval($row->issue_qty);
                $totalUsedQty += floatval($usedQty);
                $totalReturnQty += floatval($returnQty);
                $totalStockQty += floatval($stockQty);
            }
        endforeach;
        $tfoot .= '<tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-center">'.$totalIssueQty.'</th> 
                <th class="text-center">'.$totalUsedQty.'</th>
                <th class="text-center">'.$totalReturnQty.'</th>
                <th class="text-center">'.$totalStockQty.'</th>
         </tr>';
        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

	/* Cutting Register */
    public function cuttingRegister(){
        $this->data['headData']->pageTitle = "CUTTING REGISTER"; 
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->load->view('reports/production_report/cutting_register',$this->data);
    }

	public function getCuttingRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['prc_type'] = 2;
            $cuttingData = $this->productionReport->getCuttingPrcLogData($data);
            $tbody=""; $i=1; $tfoot=""; $totalQty=0; $totalAmt=0;
         
            if(!empty($cuttingData)):
                foreach($cuttingData as $row):
					$stockData = $this->cutting->getCuttingBomData(['prc_id'=>$row->prc_id,'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
					
					$total_cunsumption_wt = ($row->qty * $row->wt_nos);
					$processRate = (!empty($row->process_rate) ? $row->process_rate : $row->cut_rate);
					$total_amt = ($processRate * $row->qty);
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->prc_number.'</td>
                        <td>'.$row->operator_name.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.(!empty($row->machine_code) ? $row->machine_code : $row->machine_name).'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->material_grade.'</td>
                        <td>'.$row->cutting_dia.'</td>
                        <td>'.$row->cut_rate.'</td>
                        <td>'.(!empty($row->item_code) ? $row->item_code : $row->item_name).'</td>
                        <td>'.$row->qty.'</td>
						<td>'.$row->wt_nos.'</td>
                        <td>'.$row->cutting_length.'</td>
                        <td>'.$total_cunsumption_wt.'</td>
						<td>'.(!empty($stockData->total_end_pcs) ? round($stockData->total_end_pcs,3) : 0).'</td>
						<td>'.$total_amt.'</td>';
            
                    $tbody .= '</tr>';
					
                    $totalQty += $row->qty;
					$totalAmt += $total_amt;
                endforeach;
            endif;
			$tfoot .= '<tr class="thead-dark">
                    <th colspan="10" class="text-right">Total</th>
					<th class="text-center">'.numberFormatIndia($totalQty).'</th> 
					<th colspan="4"></th>
					<th class="text-center">'.numberFormatIndia($totalAmt).'</th> 
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    public function paretoAnalysis(){
        $this->data['headData']->pageTitle = 'PARETO ANALYSIS REPORT';
        $this->data['headData']->pageUrl = "reports/productionReport/paretoAnalysis";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/production_report/pareto_analysis",$this->data);
    }

	public function getParetoAnalysisData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $productionData = $this->productionReport->getParetoAnalysisData($data);
        
        $detailTbody = ''; $tfoot = '';$rejDetailTbody = ""; $sumDetailTbody = "";
		
        $total_ok_qty = 0;$total_rej_qty = 0;$total_rw_qty = 0;
        if(!empty($productionData)){
            $i=1;
            foreach($productionData AS $row){
                $ok_qty = (!empty($row->total_ok_qty))?$row->total_ok_qty:0;
                $rej_qty = (!empty($row->total_rej_qty))?$row->total_rej_qty:0;
                $rw_qty = (!empty($row->total_rw_qty))?$row->total_rw_qty:0;
                $totalProduction = $row->total_ok_qty + $rej_qty + $rw_qty;
                $ok_per = $rej_per = $rw_per = 0;
                if($totalProduction > 0){
                    $ok_per = round((($ok_qty * 100)/ $totalProduction),2);
                    $rej_per = round((($rej_qty * 100)/ $totalProduction),2);
                    $rw_per = round((($rw_qty * 100)/ $totalProduction),2);
                }
                $total_ok_qty += $ok_qty;
                $total_rej_qty += $rej_qty;
                $total_rw_qty += $rw_qty;
                $detailTbody .= '<tr>
					<td>'.$i++.'</td>
					<td class="text-left">'.$row->item_code.'</td>
					<td class="text-left">'.$row->process_name.'</td>
					<td class="text-right">'.$totalProduction.'</td>
					<td class="text-right">'.floatval($ok_qty).'</td>
					<td class="text-right">'.sprintf('%.2f',$ok_per).'</td>
					<td class="text-right">'.floatval($rw_qty).'</td>
					<td class="text-right">'.sprintf('%.2f',$rw_per).'</td>
					<td class="text-right">'.floatval($rej_qty).'</td>
					<td class="text-right">'.sprintf('%.2f',$rej_per).'</td>
				</tr>';
            }

            $rejDetail = $this->productionReport->getParetoAnalysisRejData($data);
            if(!empty($rejDetail)){
                $total_per =0;
                foreach($rejDetail AS $row){
                    $per = round((($row->rej_qty * 100)/$total_rej_qty),2);
                    $total_per += $per;
                    $rejDetailTbody .= '<tr>
						<td>'.$row->rej_reason.'</td>
						<td class="text-right">'.floatval($row->rej_qty).'</td>
						<td class="text-right">'.sprintf('%.2f',$per).'</td>             
					</tr>';
                }

                $rejDetailTbody .= '<tr class="bg-light">
					<td class="text-right border-bottom-0"><b>Total</b></td>
					<td class=" border-bottom-0 text-right">'.floatval(array_sum(array_column($rejDetail,'rej_qty'))).'</td>
					<td class="text-dark border-bottom-0 text-right"><strong>100.00</strong></td>
				</tr>';
            }

        }
        $totalQty = $total_ok_qty + $total_rw_qty + $total_rej_qty;
        $total_ok_per = $total_rej_per = $total_rw_per = 0;
        if($totalQty > 0){
            $total_ok_per = round((($total_ok_qty * 100)/ $totalQty),2);
            $total_rej_per = round((($total_rej_qty * 100)/ $totalQty),2);
            $total_rw_per = round((($total_rw_qty * 100)/ $totalQty),2);
        }
        $sumDetailTbody = ' <tr>
				<td>  <b>Ok</b>   </td>
				<td class="text-right">'.floatval($total_ok_qty).'</td>
				<td class="text-right">'.sprintf('%.2f',$total_ok_per).'</td>                                                        
			</tr>
			<tr>
				<td><b>Rework</b> </td>
				<td class="text-right">'.floatval($total_rw_qty).'</td>
				<td class="text-right">'.sprintf('%.2f',$total_rw_per).'</td>                                                       
			</tr>
			<tr>
				<td><b>Rejection</b> </td>
				<td class="text-right">'.floatval($total_rej_qty).'</td>
				<td class="text-right">'.sprintf('%.2f',$total_rej_per).'</td>                                                       
			</tr>
			<tr class="bg-light">
				<td><b>TOTAL</b> </td>
				<td class="text-right"><strong>'.floatval($total_ok_qty + $total_rw_qty + $total_rej_qty).'</strong></td>
				<td class="text-right"><strong>100.00</strong></td>                                                       
			</tr>';
      
        if(!empty($data['is_pdf'])){
			$process_name = '';
			$prsArr = array_unique(array_column($productionData,'process_name'));
			$process_name = implode(',',$prsArr);
			
			$report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date']));
            $processBy = ($data['process_by'] == 1) ? 'Inhouse' : 'Vendor';          
            $header = 'MONTH: '. $report_date.' ('.$processBy.' - '.$process_name.')';
			
            $logo = base_url('assets/images/logo.png');
             
            $thead = '<tr class="text-left">
                        <th colspan="8" style="padding:8px;" class="org_title">'.$header.'</th>
                        <th colspan="2" style="padding:8px;" class="org_title">DATE : '.date('d-m-Y').'</th>
                    </tr>';
 
            $thead .= '<tr>
                        <th rowspan="2" style="width:5%">Sr.</th>
                        <th rowspan="2" style="width:15%">Part Name</th> 
                        <th rowspan="2" style="width:15%">Process</th>
                        <th rowspan="2" style="width:10%">Inspected</th>
                        <th colspan="2" style="width:15%">Ok</th>
                        <th colspan="2" style="width:15%">Rework</th>
                        <th colspan="2" style="width:15%">Rejection</th>
                    </tr>
                    <tr>
                        <th>Qty</th>
                        <th>%</th>
                        <th>Qty</th>
                        <th>%</th>
                        <th>Qty</th>
                        <th>%</th>
                    </tr>';
            $theadRej = '<tr class="text-left">
                            <th>Details of Defect</th>
                            <th class="text-right">Quantity</th>                                                        
                            <th class="text-right">%</th>
                        </tr>';
                        
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                            <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                            <tbody>'.$detailTbody.'</tbody>
                            <tfoot>
                                <tr class="thead-dark">
                                    <th colspan="4" class="text-right">Total</th>
                                    <th class="text-right">'.$total_ok_qty.'</th> 
                                    <th class="text-right">'.sprintf('%.2f',round($total_ok_per,2)).'</th>
                                    <th class="text-right">'.$total_rw_qty.'</th>
                                    <th class="text-right">'.sprintf('%.2f',round($total_rw_per,2)).'</th>
                                    <th class="text-right">'.$total_rej_qty.'</th>
                                    <th class="text-right">'.sprintf('%.2f',round($total_rej_per,2)).'</th>
                                </tr>
                            </tfoot>
                        </table>';
            $pdfData .= '<table style="width:100%; margin-top:10px;">
                            <tr>
                                <td style="font-weight:bold; font-size:14px;">Total Rejection</td>
                            </tr>
                        </table>';
   
            $pdfData .= '<table class="table table-bordered item-list-bb" repeat_header="1">
                            <thead class="bg-light" id="theadData">'.$theadRej.'</thead>
                            <tbody>'.$rejDetailTbody.'</tbody>
                        </table>';
                    
            $htmlHeader = '<table class="table">
                            <tr>
                                <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">PARETO ANALYSIS REPORT</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">QA/F/20'.'<br>'.'(01/01.01.2025)</td>
                            </tr>
                        </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                                <tr>
                                    <td style="width:50%;font-size:12px;"></td>
                                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                                </tr>
                            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/paretoAnalysis.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(100,100));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->AddPage('L','','','','',3,3,18,3,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
        $this->printJson(['status' => 1, 'detailTbody' => $detailTbody,'tfoot' => $tfoot,'rejDetailTbody'=>$rejDetailTbody,'sumDetailTbody'=>$sumDetailTbody]);
    }
	
    public function poorQualityCost(){
        $this->data['headData']->pageTitle = 'COST OF POOR QUALITY';
        $this->data['headData']->pageUrl = "reports/productionReport/poorQualityCost";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/production_report/poor_quality_analysis",$this->data);
    }

	public function poorQualityCostData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $productionData = $this->productionReport->getPoorQualitydata($data);
        $tbody = '';$i=1; $tfoot = "";
        if(!empty($productionData)){
            $prcIds = array_unique(array_column($productionData,'prc_id'));
            $cuttingData = $this->productionReport->getPrcCutWeight(['prc_id'=>$prcIds]);
         
            $array1 = array_map(function($itm) { return (array) $itm; }, $productionData);
            $array2 = array_map(function($itm) { return (array) $itm; }, $cuttingData);
			foreach ($array1 as &$item1) { 
				foreach ($array2 as $item2) { 
					if ($item1['prc_id'] == $item2['prc_id']) { $item1 = array_merge($item1, $item2); } 
				} 
			}
			
            $groupedItems = array_reduce($array1, function ($result, $item) {
                $itemId = $item['item_id'];
                if($item['cutting_flow'] == 2){
                    $weight = explode("-",$item['cut_weight']);
                    $cut_weight = ((!empty($weight[1]) && $weight[1] > 0)?$weight[1]:((!empty($weight[0]))?$weight[0]:0));
                }else{
                    $cut_weight = $item['ppc_qty'];
                }
                
                if (isset($result[$itemId])) {
                    $result[$itemId]['ih_forge_qty'] += $item['ih_forge_qty'];
                    $result[$itemId]['v_forge_qty'] += $item['v_forge_qty'];
                    $result[$itemId]['ih_mc_qty'] += $item['ih_mc_qty'];
                    $result[$itemId]['v_mc_qty'] += $item['v_mc_qty'];
                    $result[$itemId]['ih_forge_cost'] += $item['ih_forge_cost'] + (($cut_weight * $item['ih_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['ih_forge_qty']);
                    $result[$itemId]['v_forge_cost'] += $item['v_forge_cost'] + (($cut_weight * $item['v_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_forge_qty']);
                    $result[$itemId]['ih_mc_cost'] += $item['ih_mc_cost'] + (($cut_weight * $item['ih_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['ih_mc_qty']);
                    $result[$itemId]['v_mc_cost'] += $item['v_mc_cost'] + (($cut_weight * $item['v_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_mc_qty']);
                } else {
                    $result[$itemId] = $item;
                    $result[$itemId]['ih_forge_cost'] = $item['ih_forge_cost'] + (($cut_weight * $item['ih_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['ih_forge_qty']);
                    $result[$itemId]['v_forge_cost'] = $item['v_forge_cost'] + (($cut_weight * $item['v_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_forge_qty']);
                    $result[$itemId]['ih_mc_cost'] = $item['ih_mc_cost'] + (($cut_weight * $item['ih_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] *  $item['ih_mc_qty']);
                    $result[$itemId]['v_mc_cost'] = $item['v_mc_cost'] + (($cut_weight * $item['v_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_mc_qty']);
                }
                return $result;
            }, []); 
            $groupedItems = array_filter($groupedItems, function($value) { return (object)$value; });
			$groupedItems = array_map(function($itm) { return (object) $itm; }, $groupedItems);

            // print_r($groupedItems);exit;
            $total_ih_forge = $total_v_forge = $total_ih_mc = $total_v_mc = $total_cost = 0;
            $total_ih_forge_qty = $total_v_forge_qty = $total_ih_mc_qty = $total_v_mc_qty = 0;
            foreach($groupedItems AS $row){
                $cost = $row->ih_forge_cost+$row->v_forge_cost+$row->ih_mc_cost+$row->v_mc_cost;
                $ih_forge_rate =((!empty($row->ih_forge_qty) && $row->ih_forge_qty > 0)?$row->ih_forge_cost/$row->ih_forge_qty:0);
                $v_forge_rate =((!empty($row->v_forge_qty) && $row->v_forge_qty > 0 )?$row->v_forge_cost/$row->v_forge_qty:0);
                $ih_mc_rate =((!empty($row->ih_mc_qty) && $row->ih_mc_qty > 0)?$row->ih_mc_cost/$row->ih_mc_qty:0);
                $v_mc_rate =((!empty($row->v_mc_qty) && $row->v_mc_qty > 0)?$row->v_mc_cost/$row->v_mc_qty:0);

                $tbody .= ' <tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_code.'</td>
                                <td class="text-right">'.round($row->ih_forge_qty,2).'</td>
                                <td class="text-right">'.round($ih_forge_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->ih_forge_cost,2)).'</td>

                                <td class="text-right">'.round($row->v_forge_qty,2).'</td>
                                <td class="text-right">'.round($v_forge_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->v_forge_cost,2)).'</td>

                                <td class="text-right">'.round($row->ih_mc_qty,2).'</td>
                                <td class="text-right">'.round($ih_mc_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->ih_mc_cost,2)).'</td>

                                <td class="text-right">'.round($row->v_mc_qty,2).'</td>
                                <td class="text-right">'.round($v_mc_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->v_mc_cost,2)).'</td>
                                
                                <td></td>
                                <td></td>
                                <td></td>

                                <td class="text-right">'.sprintf('%.2f',round($cost,2)).'</td>
                            </tr>';

                            $total_ih_forge_qty += $row->ih_forge_qty;
                            $total_v_forge_qty += $row->v_forge_qty;
                            $total_ih_mc_qty += $row->ih_mc_qty; 
                            $total_v_mc_qty += $row->v_mc_qty;

                            $total_ih_forge += $row->ih_forge_cost;
                            $total_v_forge += $row->v_forge_cost;
                            $total_ih_mc += $row->ih_mc_cost; 
                            $total_v_mc += $row->v_mc_cost;
                            $total_cost += $cost;
            }

            $tfoot = '<tr>
                        <th colspan="2" class="text-right">Total</th>
                        <th>'. $total_ih_forge_qty.'</th>                        
                        <th></th>                        
                        <th>'. round($total_ih_forge,2).'</th>   
                        
                        <th>'. $total_v_forge_qty.'</th>                        
                        <th></th>                        
                        <th>'. round($total_v_forge,2).'</th>
                        
                        <th>'. $total_ih_mc_qty.'</th>                        
                        <th></th>                        
                        <th>'. round($total_ih_mc,2).'</th>    
                        
                        <th>'. $total_v_mc_qty.'</th>                        
                        <th></th>                        
                        <th>'. round($total_v_mc,2).'</th>  
                        
                        <th></th>
                        <th></th>
                        <th></th>

                        <th>'. round($total_cost,2).'</th>      
                      </tr>';
        }
        if($data['is_pdf'] == 1){
            $logo = base_url('assets/images/logo.png');
            $report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date'])); 
			
            $thead = '<tr class="text-left">
                        <th colspan="11" style="padding:8px;" class="org_title">MONTH : '.$report_date.'</th>
                        <th colspan="7" style="padding:8px;" class="org_title">DATE : '.date('d-m-Y').'</th>
                    </tr>';
    
            $thead .= '<tr>
                        <th  rowspan="2">#</th>
                        <th  rowspan="2">Part</th>
                        <th  colspan="3">InHouse - Forging Rej.</th>
                        <th  colspan="3">Job Work - Forging Rej.</th>
                        <th  colspan="3">InHouse - Machining Rej.</th>
                        <th  colspan="3">Job Work - Machining Rej.</th>
                        <th  colspan="3">Customers Return Rej.</th>
                        <th  rowspan="2">Total Cost</th>
                    </tr>
                    <tr>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>
                    </tr>';
                        
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                            <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                            <tbody>'.$tbody.'</tbody>
                            <tfoot>'.$tfoot.'</tfoot>
                        </table>';
            $htmlHeader = '<table class="table">
                            <tr>
                                <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">COST OF POOR QUALITY</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">QA-F-25'.'<br>'.'(Rev.02 - 01-01.2025)</td>
                            </tr>
                        </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                                <tr>
                                    <td style="width:50%;font-size:12px;"></td>
                                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                                </tr>
                            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/poorQualityCost.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(100,100));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->AddPage('P','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    /* OEE Register */
    public function oeeRegister(){
        $this->data['pageHeader'] = 'OEE Register';
		$this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->load->view('reports/production_report/oee_register', $this->data);
    }

    public function getOeeRegister($postData = ""){
        $data = $this->input->post();

        $productionData = $this->productionReport->getOeeRegister($data);

        $tbody = '';$tfoot = ''; $totalPRoductionQty = $totalRejQty = $totalRwTime= 0 ;
        foreach($productionData as $row):
            $plan_qty = ((!empty($row->cycle_time))?ceil(($data['plan_prod_time']*3600)/$row->cycle_time):0);
            $actual_plan_qty = (!empty($row->total_production_time) && !empty($row->cycle_time))? (int) (($row->total_production_time * 60) / $row->cycle_time):0;

            $row->total_production_time = (!empty($row->total_production_time))?round($row->total_production_time/60,2):0;
            $production_qty = floatval($row->total_ok_qty + $row->total_rej_qty);
            $actual_ct = round(((!empty($production_qty))?(($row->total_production_time * 3600)/$production_qty):0),2);
            $availability_per = round((($row->total_production_time) * 100)/$data['plan_prod_time'],2);
            $effecincy_per = (!empty($actual_plan_qty))?round(($production_qty*100)/$actual_plan_qty,2):0;
            $qc_per =  (!empty($production_qty))?round((($row->total_ok_qty * 100)/$production_qty),2):0;
            $oee_per = round(($availability_per + $effecincy_per  + $qc_per)/3,2);
            $rw_production_time = ((!empty($row->rw_production_time))?round($row->rw_production_time/60,2):0);
            $tbody .= '<tr class="text-center">
                <td class="text-left"> '.$row->emp_name.' </td>
                <td class="text-left"> '.$row->machine_code.' '.$row->machine_name.' </td>
                <td> '.$row->product_name.' </td>
                <td> '.$row->prc_number.' </td>
                <td> '.$row->process_name.' </td>
                <td> '.round($row->cycle_time,2).' </td>
                <td> '.$data['plan_prod_time'].' </td>
                <td> '.$plan_qty.' </td>
                <td> '.$row->total_production_time.' </td>
                <td> '.$actual_plan_qty.' </td>
                <td> '. $actual_ct.' </td>
                <td> '.( $production_qty).' </td>
                <td> '.floatval($row->total_rej_qty).' </td>
                <td> '.$rw_production_time.' </td>
                <td> '.$availability_per.' </td>
                <td> '.$effecincy_per.' </td>
                <td> '.$qc_per.' </td>
                <td> '.$oee_per.' </td>
            </tr>';
            $totalPRoductionQty+=  ($production_qty);
            $totalRejQty+= ($row->total_rej_qty);
            $totalRwTime+=  ($rw_production_time);
        endforeach;
        $tfoot = '<tr>
            <th colspan="11" class="text-right thead-info">Total</th>
            <th>'.$totalPRoductionQty.'</th>
            <th>'.$totalRejQty.'</th>
            <th>'.$totalRwTime.'</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>';
        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
}
?>