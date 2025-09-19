<?php
class SalesReport extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Sales Report";
        $this->data['headData']->controller = "reports/salesReport";
    }

    public function orderMonitoring(){
        $this->data['headData']->pageUrl = "reports/salesReport/orderMonitoring";
        $this->data['headData']->pageTitle = "ORDER MONITORING";
        $this->data['pageHeader'] = 'ORDER MONITORING';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/sales_report/order_monitoring",$this->data);
    }

    public function getOrderMonitoringData(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->salesReport->getOrderMonitoringData($data);;
            $tbody=""; $i=1; $blankInTd='';$tfoot=""; $totalQty=0;$totalInvQty=0;$totalDeviationQty=0;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
         
            if(!empty($result)):
				foreach($result as $row):
					$data['ref_id'] = $row->id;
					$invoiceData = $this->salesReport->getSalesInvData($data);
					$invoiceCount = count($invoiceData);

					$tbody .= '<tr>
						<td class="text-center">'.$i++.'</td>
						<td>'.formatDate($row->trans_date).'</td>
						<td>'.$row->trans_number.'</td>
						<td>'.$row->doc_no.'</td>
						<td>'.$row->party_name.'</td>
						<td>'.$row->item_name.'</td>
						<td>'.floatval($row->qty).'</td>
						<td>'.formatDate($row->cod_date).'</td>';
		  
						if($invoiceCount > 0):
							$j=1; $dis_qty = 0;
							foreach($invoiceData as $invRow):
								$daysDiff = '';
								if(!empty($row->cod_date) AND !empty($invRow->invDate)){
									$cod_date = new DateTime($row->cod_date);
									$invDate = new DateTime($invRow->invDate);
									$due_days = $cod_date->diff($invDate)->format("%r%a");
									$daysDiff = ($due_days > 0) ? $due_days : 'On Time';
								}
								$dis_qty += $invRow->invQty;
								$dev_qty = $row->qty - $dis_qty;
								$tbody.='<td>'.formatDate($invRow->invDate).'</td>
										<td>'.$invRow->invNo.'</td>
										<td>'.floatval($invRow->invQty).'</td>
										<td>'.$daysDiff.'</td>
										<td>'.($dev_qty).'</td>';

								if($j != $invoiceCount){$tbody.='</tr><tr><td>'.$i++.'</td>'.$blankInTd;}
								
								$totalInvQty += $invRow->invQty;
								$j++;
							endforeach;
						else:
							$daysDiff = '';
							if(!empty($row->cod_date)){
								$cod_date = new DateTime($row->cod_date);
								$invDate = new DateTime(date('Y-m-d'));
								$due_days = $cod_date->diff($invDate)->format("%r%a");
								$daysDiff = ($due_days > 0) ? $due_days : 'On Time';
							}
							
							$tbody.='<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>'.$daysDiff.'</td><td>'.floatval($row->qty).'</td>';
						endif;
					$tbody.='</tr>';
					
					$totalQty += $row->qty;
				endforeach;
            endif;
			$tfoot .= '<tr class="thead-dark">
				<th colspan="6" class="text-right">Total</th>
				<th class="text-center">'.$totalQty.'</th> 
				<th colspan="3"></th>
				<th class="text-center">'.$totalInvQty.'</th> 
				<th></th>
				<th class="text-center">'.($totalQty - $totalInvQty).'</th>
			</tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    public function salesAnalysis(){
        $this->data['headData']->pageUrl = "reports/salesReport/salesAnalysis";
        $this->data['headData']->pageTitle = "SALES ANALYSIS";
        $this->data['pageHeader'] = 'SALES ANALYSIS';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->load->view("reports/sales_report/sales_analysis",$this->data);
    }

    public function getSalesAnalysisData(){
        $data = $this->input->post();
        $result = $this->salesReport->getSalesAnalysisData($data);

        $thead = $tbody = $tfoot = ''; $i=1;
        if($data['report_type'] == 1):
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Customer Name</th>
                <th class="text-right">Taxable Amount</th>
                <th class="text-right">GST Amount</th>
                <th class="text-right">Net Amount</th>
            </tr>';

            $taxableAmount = $gstAmount = $netAmount = 0;
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-right">'.floatval($row->taxable_amount).'</td>
                    <td class="text-right">'.floatval($row->gst_amount).'</td>
                    <td class="text-right">'.floatval($row->net_amount).'</td>
                </tr>';
                $i++;
                $taxableAmount += floatval($row->taxable_amount);
                $gstAmount += floatval($row->gst_amount);
                $netAmount += floatval($row->net_amount);
            endforeach;

            $tfoot .= '<tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">'.$taxableAmount.'</th>
                <th class="text-right">'.$gstAmount.'</th>
                <th class="text-right">'.$netAmount.'</th>
            </tr>';
        else:
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Item Name</th>
                <th class="text-right">Qty.</th>
                <th class="text-right">Price</th>
                <th class="text-right">Taxable Amount</th>
            </tr>';

            $totalQty = $taxableAmount = 0;
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td class="text-right">'.floatVal($row->qty).'</td>
                    <td class="text-right">'.floatVal($row->price).'</td>
                    <td class="text-right">'.floatVal($row->taxable_amount).'</td>
                </tr>';
                $i++;
                $totalQty += floatval($row->qty);
                $taxableAmount += floatval($row->taxable_amount);
            endforeach;

            $tfoot .= '<tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">'.$totalQty.'</th>
                <th></th>
                <th class="text-right">'.$taxableAmount.'</th>
            </tr>';
        endif;

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function mrpReport(){
        $this->data['headData']->pageUrl = "reports/salesReport/mrpReport";
        $this->data['headData']->pageTitle = "MRP REPORT";
        $this->data['pageHeader'] = 'MRP REPORT';
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2']);
        $this->data['itemList'] = $this->salesOrder->getPendingOrderItems();
        $this->load->view("reports/sales_report/mrp_report",$this->data);
    }

    public function getPendingPartyOrders() {
        $data = $this->input->post();
        $result = $this->salesOrder->getPendingOrderItems($data);

        $itemIds = array_unique(array_column($result, 'item_id'));
        $itemName = array_unique(array_column($result, 'item_name'));

        $options = '<option value="">Select Item</option>';
        foreach($itemIds as $key => $row):
            $options .= '<option value="'.$row.'">'.$itemName[$key].'</option>';
        endforeach;

        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getMrpReport(){
        $data = $this->input->post();
        $result = $this->salesReport->getMrpReportData($data);

        $tfoot=""; $totalQty=0;$i=1;$tbody="";
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-center">'.$i++.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.$row->trans_date.'</td>
                <td class="text-left">'.$row->bom_item_name.'</td>
                <td class="text-right">'.floor($row->plan_qty).'</td>
            </tr>';
			$totalQty += floor($row->plan_qty);
        endforeach;
		$tfoot .= '<tr class="thead-dark">
			<th colspan="4" class="text-right">Total</th>
			<th class="text-right">'.$totalQty.'</th> 
        </tr>';
        $this->printJson(['status'=>1,'tbody'=>$tbody, 'tfoot'=>$tfoot]);
    }

    /* Customer Complaints Report*/
    public function customerComplaints(){
        $this->data['headData']->pageUrl = "reports/salesReport/customerComplaints";
        $this->data['headData']->pageTitle = "Customer Complaints";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/sales_report/customer_complaints",$this->data);
    }

    public function getCustomerComplaintsData($jsonData=''){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
      
        $result = $this->salesReport->getCustomerComplaintsData($data);
        $tbody=""; $i=1;
        if(!empty($result)):
            foreach($result as $row):
                $imgFile = '';
                if(!empty($row->defect_image)):
                    $imgPath = base_url('assets/uploads/defect_image/'.$row->defect_image);
                    $imgFile='<div class="picture-item" >
                        <a href="'.$imgPath.'" class="lightbox" target="_blank">
                            <img src="'.$imgPath.'" alt="" class="img-fluid"  width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;"/>
                        </a> 
                        </div> ';
                endif;

                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->inv_number.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->complaint.'</td>
                    <td>'.$imgFile.'</td>
                    <td>'.$row->report_no.'</td>
                    <td>'.$row->action_taken.'</td>
                    <td>'.$row->ref_feedback.'</td>
                    <td>'.$row->remark.'</td>';
                $tbody.='</tr>';
            endforeach;
        endif;
         
        if(!empty($data['pdf'])):
            $reportTitle = 'Customer Complaints';
            $report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .='<tr>
                        <th>#</th>
                        <th>Complaint Received Date</th>
                        <th>Complaint No.</th>
                        <th>Customer Name</th>
                        <th>Reference of Complaint</th>
                        <th>Part No.</th>
                        <th>Details of Complaint</th>
                        <th>Defect photos</th>
                        <th>Corrective/ Preventive Action Report No.</th>
                        <th>Action Taken Details</th>
                        <th>Effectiveness</th>
                        <th>Remarks</th>
                    </tr>';

            $logo = base_url('assets/images/logo.png');
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table">
                <tr>
                   <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">Customer Complaints Register</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">MKT/F/04'.'<br>'.'(Rev.01 dtd. 01.01.25</td>
                </tr>
            </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/customerComplaints.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    /* Expense Register Report */
    public function expenseRegister(){
        $this->data['headData']->pageTitle = "EXPENSE REGISTER REPORT";
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/sales_report/expense_register",$this->data);
    }

    public function getExpenseRegister(){
        $data = $this->input->post();
        $result = $this->salesReport->getExpenseData($data);
    
        $i=1;$tbody='';$tfoot='';$totalAmt = 0;
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->exp_date).'</td>
                    <td>'.$row->exp_number.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->exp_name.'</td>
                    <td>'.$row->demand_amount.'</td>
                    <td>'.$row->notes.'</td>
				</tr>';
                $totalAmt += $row->demand_amount;
            endforeach; 
            $tfoot .= '<tr class="thead-dark">
                <th colspan="5" class="text-right">Total</th>
                <th>'.$totalAmt.'</th> 
                <th></th>
            </tr>';
        endif;  
        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    /* Monthly Sales Analysis Report */
    public function monthlySalesAnalysis(){
        $this->data['headData']->pageTitle = 'MONTHLY SALES ANALYSIS REPORT';
        $this->load->view("reports/sales_report/monthly_sales_analysis",$this->data);
    }

    public function getMonthlySalesAnalysisData($jsonData=""){ 
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
        if($data['report_type'] == 1){
            $data['group_by'] = 'item_master.category_id';
        }elseif($data['report_type'] == 2){
            $data['group_by'] = 'party_master.sales_zone';
        }elseif($data['report_type'] == 3){
            $data['group_by'] = 'party_master.source';
        }elseif($data['report_type'] == 4){
            $data['group_by'] = 'party_master.business_type';
        }elseif($data['report_type'] == 5){
            $data['group_by'] = 'employee_master.id';
        }
        $result = $this->salesReport->getMonthlySalesAnalysisData($data);
        
		$tbodyData = ""; $tfootData ="";
        if(!empty($result)){
            foreach($result as $row){
            $tbodyData .= '<tr>
					<th>'.$row->category_name.'</th>
					<td>'.$row->apr_amt.'</td>
					<td>'.$row->may_amt.'</td>
					<td>'.$row->jun_amt.'</td>
					<td>'.$row->jul_amt.'</td>
					<td>'.$row->aug_amt.'</td>
					<td>'.$row->sep_amt.'</td>
					<td>'.$row->oct_amt.'</td>
					<td>'.$row->nov_amt.'</td>
					<td>'.$row->dec_amt.'</td>
					<td>'.$row->jan_amt.'</td>
					<td>'.$row->feb_amt.'</td>
					<td>'.$row->mar_amt.'</td>
				</tr>';
            }
            $tfootData = '<tr>
				<th text-right">Total</th>
				<th>'.array_sum(array_column($result,'apr_amt')).'</th>
				<th>'.array_sum(array_column($result,'may_amt')).'</th>
				<th>'.array_sum(array_column($result,'jun_amt')).'</th>
				<th>'.array_sum(array_column($result,'jul_amt')).'</th>
				<th>'.array_sum(array_column($result,'aug_amt')).'</th>
				<th>'.array_sum(array_column($result,'sep_amt')).'</th>
				<th>'.array_sum(array_column($result,'oct_amt')).'</th>
				<th>'.array_sum(array_column($result,'nov_amt')).'</th>
				<th>'.array_sum(array_column($result,'dec_amt')).'</th>
				<th>'.array_sum(array_column($result,'jan_amt')).'</th>
				<th>'.array_sum(array_column($result,'feb_amt')).'</th>
				<th>'.array_sum(array_column($result,'mar_amt')).'</th>
			 </tr>';
            $footerArray = [
                'apr_amt'=>round(array_sum(array_column($result,'apr_amt')),2),
                'may_amt'=>round(array_sum(array_column($result,'may_amt')),2),
                'jun_amt'=>round(array_sum(array_column($result,'jun_amt')),2),
                'jul_amt'=>round(array_sum(array_column($result,'jul_amt')),2),
                'aug_amt'=>round(array_sum(array_column($result,'aug_amt')),2),
                'sep_amt'=>round(array_sum(array_column($result,'sep_amt')),2),
                'oct_amt'=>round(array_sum(array_column($result,'oct_amt')),2),
                'nov_amt'=>round(array_sum(array_column($result,'nov_amt')),2),
                'dec_amt'=>round(array_sum(array_column($result,'dec_amt')),2),
                'jan_amt'=>round(array_sum(array_column($result,'jan_amt')),2),
                'feb_amt'=>round(array_sum(array_column($result,'feb_amt')),2),
                'mar_amt'=>round(array_sum(array_column($result,'mar_amt')),2)
            ];
        }

        $reportTitle = 'Monthly Sales Analysis Report';
        $logo = base_url('assets/images/logo.png'); 
        if($data['pdf_type'] == 1) {
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%"></td>
                        </tr>
                    </table>';

            $htmlData .= '<table class="table table-bordered item-list-bb"  style="margin-top:10px;">
                            <thead class="thead-dark">
                                <tr style="font-weight: bold; background-color: #9f9b9bff;">
                                    <th> # </th>
                                    <th>Apr</th>
                                    <th>May</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Aug</th>
                                    <th>Sep</th>
                                    <th>Oct</th>
                                    <th>Nov</th>
                                    <th>Dec</th>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>  
                                </tr>
                            </thead>
                            <tbody>
                                '.$tbodyData.'
                                <tr>
                                    <td>Total</td>
                                    <td>'.round(array_sum(array_column($result, 'apr_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'may_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'jun_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'jul_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'aug_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'sep_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'oct_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'nov_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'dec_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'jan_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'feb_amt')), 2).'</td>
                                    <td>'.round(array_sum(array_column($result, 'mar_amt')), 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
            $pdfData = $this->generatePDF($htmlData,'L');
		}else { 
            $this->printJson(['status'=>1,'soData'=>$result,'totalData'=>$footerArray,'tbodyData'=>$tbodyData,'tfootData'=>$tfootData]);
        } 
    }

    /* Party Budget Analysis Data */
    public function partyBudgetAnalysis(){
        $this->data['headData']->pageTitle = "Party Budget Analysis";
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['executiveList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/sales_report/party_budget_analysis",$this->data);
    }

    public function getPartyBudgetDetails($jsonData = ""){
        $data = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post(); 

        $reportType = (!empty($data['report_type']))?$data['report_type']:1;
        $result = $this->salesReport->getPartyBudgetAnalysis($data);

        $monthColumn = $monthSubColumn = "";$i= 1;
        $monthList = $this->getMonthListFY();
        if($reportType == 2):
            foreach($monthList as $row):
                $monthColumn .= '<th colspan="3" class="text-center">'.date("M",strtotime($row['label'])).'</th>';

                $monthSubColumn .= '<th>Taxa.<br>Amt.</th>';
                $monthSubColumn .= '<th>Budget</th>';
                $monthSubColumn .= '<th>Per (%)</th>';
            endforeach;
        endif;

        $responseHeader = '<tr>';
            $responseHeader .= '<th rowspan="2">Party Name</th>';
            $responseHeader .= '<th rowspan="2">Contact No.</th>';
            $responseHeader .= '<th rowspan="2">Address</th>';
            $responseHeader .= '<th rowspan="2">Sales Executive</th>';
            $responseHeader .= $monthColumn;
            $responseHeader .= '<th colspan="3" class="text-center">Total</th>';
        $responseHeader .= '</tr>';

        
        $responseHeader .= '<tr>';
            $responseHeader .= $monthSubColumn;
            $responseHeader .= '<th>Taxa.<br>Amt.</th>';
            $responseHeader .= '<th>Budget</th>';
            $responseHeader .= '<th>Per (%)</th>';
        $responseHeader .= '</tr>';

        $responseHtml = "";

        $groupedResult = array_reduce($result, function($itemData, $row) {
            $taxableAmount = round($row->taxable_amount,0);
            $monthlyCapacity = round(($row->business_capacity / 12),0);
            $shortAddress = $row->city_name;
			$shortAddress .= (!empty($row->state_name) ? ', '.$row->state_name : '');
			$shortAddress .= (!empty($row->country_name) ? ', '.$row->country_name : '');
            if(isset($itemData[$row->party_id])):
                $itemData[$row->party_id]['monthData'][date("Y-m",strtotime($row->month))] = [
                    'taxable_amount' => $taxableAmount,
                    'monthly_capacity' => $monthlyCapacity,
                    'per' => ($taxableAmount > 0 && $monthlyCapacity > 0)?round((($taxableAmount * 100) / $monthlyCapacity),0):0
                ];
            else:
                $itemData[$row->party_id] = [
                    'party_name' => $row->party_name,
                    'contact_no' => $row->party_mobile,
                    'address' => $shortAddress,
                    'business_capacity' => $row->business_capacity,
                    'executive_name' => $row->executive_name,
                    'monthData' => [
                        date("Y-m",strtotime($row->month)) => [
                           'taxable_amount' => $taxableAmount,
                            'monthly_capacity' => $monthlyCapacity,
                            'per' => ($taxableAmount > 0 && $monthlyCapacity > 0)?round((($taxableAmount * 100) / $monthlyCapacity),0):0                 
                        ]
                    ]
                ];
            endif;

            return $itemData;
        }, []); 
        $totalAmt = $totalCapacity =0; 
        foreach($groupedResult as $row):
            $responseHtml .= '<tr>';
                $responseHtml .= '<td>'.$row['party_name'].'</td>';
                $responseHtml .= '<td>'.$row['contact_no'].'</td>';
                $responseHtml .= '<td>'.$row['address'].'</td>';
                $responseHtml .= '<td>'.$row['executive_name'].'</td>';

                $totalTaxableAmt = $businessCapacity = $taxableAmount = $monthlyCapacity = $per = 0;
                foreach($monthList as $monthRow):
                    $month = date("Y-m",strtotime($monthRow['label']));
                    $taxableAmount = (!empty($row['monthData'][$month]['taxable_amount']))?$row['monthData'][$month]['taxable_amount']:0;
                    $monthlyCapacity = (!empty($row['monthData'][$month]['monthly_capacity']))?$row['monthData'][$month]['monthly_capacity']:0;
                    $per = (!empty($row['monthData'][$month]['per']))?$row['monthData'][$month]['per']:0;

                    if($reportType == 2):
                        if(date("Y-m") >= $month):
                            $responseHtml .= '<td>'.$taxableAmount.'</td>';
                            $responseHtml .= '<td>'.$monthlyCapacity.'</td>';
                            $responseHtml .= '<td>'.$per.'</td>';
                        else:
                            $responseHtml .= '<td>-</td>';
                            $responseHtml .= '<td>-</td>';
                            $responseHtml .= '<td>-</td>';

                            $taxableAmount = $monthlyCapacity = 0;
                        endif;
                    endif;

                    $totalTaxableAmt += $taxableAmount;
                    $businessCapacity += $monthlyCapacity;
                     
                    if (!isset($footerData[$month])) {
                        $footerData[$month] = ['taxable_amount' => 0, 'monthly_capacity' => 0];
                    }

                    $footerData[$month]['taxable_amount'] += $taxableAmount;
                    $footerData[$month]['monthly_capacity'] += $monthlyCapacity;
                endforeach;
                
                $totalBudget = $row['business_capacity'];
                $avgPer = ($totalTaxableAmt > 0 && $totalBudget > 0)?round((($totalTaxableAmt * 100) / $totalBudget),0):0;

                $responseHtml .= '<td>'.$totalTaxableAmt.'</td>';
                $responseHtml .= '<td>'.$totalBudget.'</td>';
                $responseHtml .= '<td>'.$avgPer.'</td>';

            $responseHtml .= '</tr>';

            $totalAmt += $totalTaxableAmt;
            $totalCapacity += $totalBudget;
        endforeach;
        $footer ="";
        if ($reportType == 2) {
            foreach ($monthList as $monthRow) {
                $month = date("Y-m", strtotime($monthRow['label']));
                $footer .= '<th>' . $footerData[$month]['taxable_amount'] . '</th>';
                $footer .= '<th>' . $footerData[$month]['monthly_capacity'] . '</th>';
                $footer .= '<th>0</th>';
            }
        }

        $responseFooter = '<tr>';
        $responseFooter .= '<th colspan="4" text-right">Total</th>';
        $responseFooter .=  $footer;
        $responseFooter .= '<th>'.$totalAmt.'</th>';
        $responseFooter .= '<th> '.$totalCapacity.'</th>';
        $responseFooter .= '<th>0</th>';
        $responseFooter .= '</tr>';
        
        $reportTitle = 'Party Budget Analysis Report';
        $logo = base_url('assets/images/logo.png');
        $htmlData = '';
        if(!empty($data['pdf_type']) && $data['pdf_type'] == 1)
        {
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%"></td>
                        </tr>
                    </table>';

            $htmlData.= '<table class="table item-list-bb" style="margin-top:10px;">
                        	<thead class="gradient-theme">
								'.$responseHeader.'
							</thead>
                            <tbody> '.$responseHtml.'</tbody>
                            <tfoot> '.$responseFooter.'</tfoot>
                        </table>';
            $pdfData= $this->generatePDF($htmlData,'L');
        }else { 
            $this->printJson(['status'=>1,'thead' => $responseHeader,'tbody'=>$responseHtml,'tfoot'=>$responseFooter]);
        }
    }

    /* Inactive Party Analysis Report */
    public function inactivePartyAnalysis(){
        $this->data['headData']->pageTitle = "Inactive Party Analysis";
        $this->data['executiveList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/sales_report/inactive_party_analysis",$this->data);
    }

    public function getInactivePartyList($jsonData = ""){
        $data = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post();
		
        $result = $this->salesReport->getInactivePartyDetail($data);

        $responseHtml = "";$i=1;
        foreach($result as $row):
			$add = Array();
			if(!empty($row->city_name)){$add[] = $row->city_name;}
			if(!empty($row->state_name)){$add[] = $row->state_name;}
			if(!empty($row->country_name)){$add[] = $row->country_name;}
            $responseHtml .= '<tr>
                <td> '.$i.' </td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->business_type.'</td>
                <td>'.$row->contact_person.'</td>
                <td>'.$row->party_mobile.'</td>
                <td>'.$row->executive_name.'</td>
                <td>'.implode(', ',$add).'</td>
                <td>'.$row->inactive_days.'</td>
                <td>'.formatDate($row->last_activity_date,'d-m-Y h:i:s A').'</td>
            </tr>';

            $i++;
        endforeach;

        $reportTitle = 'Inactive Party Analysis Report';
        $logo = base_url('assets/images/logo.png');
        $htmlData = '';
        if(!empty($data['pdf_type']) && $data['pdf_type'] == 1)
        {
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%"></td>
                        </tr>
                    </table>';

            $htmlData.= '<table class="table item-list-bb" style="margin-top:10px;">
                        	<thead class="gradient-theme">
								<tr>
                                    <th>#</th>
                                    <th>Party Name</th>
                                    <th>Business Type</th>
                                    <th>Contact Person</th>
                                    <th>Contact No.</th>
                                    <th>Sales Executive</th>
                                    <th>Address</th>
                                    <th>Inactive Days</th>
                                    <th>Last Activity Date</th>
                                </tr>
                               
							</thead>
                            <tbody> '.$responseHtml.'</tbody>
                        </table>';
            $pdfData= $this->generatePDF($htmlData,'L');
		}else { 
            $this->printJson(['status'=>1,'tbody'=>$responseHtml]);
        }
    }

    /* Customer History Report */
	public function customerHistory(){
        $this->data['headData']->pageTitle = 'CUSTOMER HISTORY REPORT';
		$this->data['customerData'] = $this->party->getpartyList(['party_type'=>1]);
        $this->load->view("reports/sales_report/customer_history",$this->data);
    }

    public function getCustomerHistory(){
        $postData = $this->input->post();
        $slData = $this->party->getPartyActivity(['party_id'=>$postData['party_id']]);  
        $partyData =$this->party->getParty(['id'=>$postData['party_id'],'partyDetail'=>1]); 

        $html = '<img src="'.base_url('assets/images/background/dnf_1.png').'" style="width:100%;">
        <h3 class="text-danger text-center font-24 fw-bold line-height-lg">Sorry!<br><span class="text-dark">Data Not Found</span></h3>';

        if(!empty($slData))
		{   $html="";
            $totalOrder = 0; $totalVisit = 0; $totalAmt = 0; $refDate = ""; 
            
			foreach($slData as $row)
			{
                if($row->lead_stage == 7){ $totalOrder ++; } 
                if($row->lead_stage == 13){ $totalVisit ++; } 

                if($row->lead_stage == 10){ 
                    $refDate = (!empty($row->ref_date) ? date_create($row->ref_date) : ""); 
                } 

                $iconClass = ['','far fa-check-circle','far fa-bell','fas fa-comment-dots','mdi mdi-help-circle','','mdi mdi-file-document','mdi mdi-shopping','mdi mdi-account-cancel','mdi mdi-account-check','fa fa-smile-o','mdi mdi-emoticon-sad','fa fa-refresh'];

                $link = $icon = $iconColor = ''; 
                if(in_array($row->lead_stage,[4,6,7]))
                {
                    $linkUrl = '';
                    if($row->lead_stage == 6){$linkUrl = base_url('salesQuotation/printQuotation/'.$row->ref_id);}
                    if($row->lead_stage == 7){$linkUrl = base_url('salesOrders/printOrder/'.encodeurl(['id'=>$row->ref_id]));}
                    $link =' #<a href="'.$linkUrl.'" target="_blank"><span>'.$row->ref_no.'</span></a>';
                }
                if($row->lead_stage >= 13){
                    $icon = 'fa fa-smile-o';
                    $iconColor = 'bg-polo-blue';
                }else{
                    $icon = $iconClass[$row->lead_stage];
                    $iconColor = $this->iconColor[$row->lead_stage];
                }
                
                $html .= '<div class="timeline-line">
                            <div class="item-timeline timeline-new">
                                <div class="t-dot">
                                    <div class="'.$iconColor.'"><i class="'.$icon.' text-white"></i></div>
                                </div>
                                <div class="t-content">
                                    <div class="t-uppercontent">
                                        <h5 class="font-bold w-100">'.$row->notes.$link.'</h5>
                                    </div>
                                    '.(!empty($row->remark) ? '<p class="text-dark">'.$row->remark.'</p>' : '').'
                                    <div class="timeline-bottom">
                                        <div class="tb-section-1">
                                            <p>'.date("d F, y",strtotime($row->created_at)).'</p>
                                        </div>
                                    </div>
                                </div>
                            </div>';
			}
         
            $html .= '</div>';
		}
        $html2="";
        $logCountData = $this->salesReport->getLogCountForCustHistory(['party_id'=>$postData['party_id']]);
        
         
        $leadDate = date_create($partyData->created_at);
        $todate = date_create();
        $orderDays = date_diff($todate, $leadDate)->days;
        $convDays = (!empty($refDate) ? date_diff($leadDate, $refDate)->days : 0);
        $lastDate = (!empty($slData) ? date_create($slData[0]->ref_date) : "");
        $inActDays = (!empty($lastDate) ? date_diff($lastDate, $todate)->days : "");
        $totalSo = (!empty($totalOrder) ? $totalOrder : 0 );
        $totalSoAmt = (!empty($logCountData->total_ord_amt) ? $logCountData->total_ord_amt : 0 );

		$html2 = '<table class="table table-borderless table-striped">
            <tr> 
                <td><b> Party Name : </b>'. $partyData->party_name.'</td>
                <td><b> Executives  :</b> '.$partyData->executive.'</td>
            </tr>
            <tr> 
                <td><b> Source  :</b> '.$partyData->source.'</td>
                <td><b> Sales Region  :</b> '.$partyData->sales_zone.'</td>
            </tr>
            <tr> 
                <td><b> Business Segment  :</b> '.$partyData->business_type.'</td>
                <td><b> Contact Person  :</b> '.$partyData->contact_person.'</td>
            </tr>   
            <tr> 
                <td><b> Contact No  :</b> '.$partyData->party_mobile.'</td>
                <td><b> City  :</b> '.$partyData->city_name.'</td>
            </tr>    
            <tr> 
                <td><b> State  :</b> '.$partyData->state_name.'</td>
                <td><b> Country  :</b> '.$partyData->country_name.'</td>
            </tr>   
            <tr> <td colspan="2"><b> Address  :</b> '.$partyData->party_address.'</td></tr> 
            <tr> 
                <td colspan="2"><b> Lead Created At :</b> '.(!empty($partyData->created_at) ? date('d-m-Y H:i:s',strtotime($partyData->created_at)) : '').'</td>
            </tr>
            <tr> 
                <td><b> Total Orders  :</b> '.$totalSo.'</td>
                <td><b> Total Amount  :</b> '.$totalSoAmt.'</td>
            </tr>
            <tr> 
                <td><b> Total Visit  :</b> '.(!empty($totalVisit) ? $totalVisit : 0).'</td>
                <td><b> Current Lead Stage  :</b> '.$partyData->stage_type.'</td>
            </tr>
            <tr>
                <td><b> Average Order Value  :</b> '.(!empty($totalSo) ? round(($totalSoAmt / $totalSo),2) : "").'</td>
                <td><b> Repeat Order Age  :</b> '.(!empty($totalSo) ? round(($orderDays / $totalSo),0) : "") .'</td>
            </tr>
            <tr> 
                <td><b> Conversion Time :</b> '.(!empty($convDays) ? $convDays .' Days ' :"" ).' </td>
                <td><b> In Activity Days  :</b> '. (!empty($inActDays) ? $inActDays .' Days ' :"" ).' </td>
            </tr> 
        </table>';
         
        $this->printJson(['status'=>1, 'html'=>$html, 'html2'=>$html2]);
    }

    
    /* Target V/S Achieve Report */    
    public function targetVsAchieve(){
        $this->data['headData']->pageTitle = 'TARGET V/S ACHIEVEMENT REPORT(SALES EXECUTIVE)';
        $this->data['monthData'] = $this->getMonthListFY();
        $this->load->view("reports/sales_report/sales_target", $this->data);
    }

    public function getTargetVsAchieveData($jsonData = ""){
        $postData = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post();
        $errorMessage = array();
        if(empty($postData['target_month']))
            $errorMessage['target_month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            $resultData = $this->salesReport->getTargetVsAchieveData($postData);
            $targetData = ""; $tfoot=""; $totalSalesAmt=0; $totalSalesAcheive=0; $totalLead =0;$totalLeadAcheive=0;
            $isSystem=""; $totalVisit=0;$totalVisitAcheive=0;
            if(!empty($resultData)):
                $i=1;
                foreach($resultData as $row):
                   
                    $total_new_lead = (!empty($row->new_lead)?$row->new_lead:0);
                    $visit = (!empty($row->new_visit)?$row->new_visit:0);
                    $sales_amount = (!empty($row->sales_amount)?$row->sales_amount:0);
                    $leadAcheive = (!empty($row->achieve_new_lead)?$row->achieve_new_lead:0);
                    $salesAcheive = (!empty($row->achieve_sales_amount)?$row->achieve_sales_amount:0);
                    $visitAcheive = (!empty($row->achieve_visit)?$row->achieve_visit:0);

                
                    $leadRatio = 0;$salesRatio = 0;$visitRatio = 0;
                    if($leadAcheive > 0 && $total_new_lead > 0){ $leadRatio = ($leadAcheive*100)/$total_new_lead; }
                    if($salesAcheive > 0 && $sales_amount > 0){ $salesRatio = ($salesAcheive*100)/$sales_amount; }
                    if($visitAcheive > 0 && $visit > 0){ $visitRatio = ($visitAcheive*100)/$visit; }
                    
                    $targetData .= '<tr>';
                    $targetData .= '<td>'.$i++.'</td>';
                    $targetData .= '<td class="text-left">'.'['.$row->emp_code.'] '.$row->emp_name.'</td>';
                    
                    $targetData .= '<td class="text-center">'.$visit.'</td>';
                    $targetData .= '<td class="text-center">'.$visitAcheive.'</td>';
                    $targetData .= '<td class="text-center">'.round($visitRatio,2).'%</td>';

                    $targetData .= '<td class="text-center">'.$total_new_lead.'</td>';
                    $targetData .= '<td class="text-center">'.$leadAcheive.'</td>';
                    $targetData .= '<td class="text-center">'.round($leadRatio,2).'%</td>';
                    $targetData .= '<td class="text-center">'.$sales_amount.'</td>';
                    $targetData .= '<td class="text-center">'.$salesAcheive.'</td>';
                    $targetData .= '<td class="text-center">'.round($salesRatio,2).'%</td>';
                    $targetData .= '</tr>';
                    
                    $totalSalesAmt += $sales_amount;
                    $totalSalesAcheive += $salesAcheive; 
                    $totalLead += $total_new_lead; 
                    $totalLeadAcheive += $leadAcheive; 
                    $totalVisit += $visit; 
                    $totalVisitAcheive += $visitAcheive; 
                endforeach;
                $tfoot = '<tr>
                            <th colspan="2" class="text-white text-right">TOTAL</th>
                            <th class="text-white">'.$totalVisit.'</th>
                            <th class="text-white">'. $totalVisitAcheive.'</th>
                            <th class="text-white"></th>
                            <th class="text-white">'.$totalLead.'</th>
                            <th class="text-white">'. $totalLeadAcheive.'</th>
                            <th class="text-white"></th>
                            <th class="text-white">'.$totalSalesAmt.'</th>
                            <th class="text-white">'.$totalSalesAcheive.'</th>
                            <th class="text-white"></th>
                        </tr>';
            endif;
            $reportTitle = 'TARGET V/S ACHIEVEMENT REPORT';

            $logo = base_url('assets/images/logo.png');
            $htmlData = '';
            if(!empty($postData['pdf_type']) && $postData['pdf_type'] == 1)
            {
                $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                            <tr>
                                <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%"></td>
                            </tr>
                        </table>';
    
                $htmlData.= '<table class="table item-list-bb" style="margin-top:10px;">
                                <thead class="gradient-theme">
                                    <tr>
                                        <th class="checkbox-column" rowspan="2"> # </th>
                                        <th rowspan="2">Sales Executive</th>
										<th colspan="3" class="text-center">Visit Target</th>
										<th colspan="3" class="text-center">Lead Target</th>
										<th colspan="3" class="text-center">Amount Target</th>
									</tr>
									<tr>
										<th>Target</th>
										<th>Achievement</th>
										<th>Achievement Ratio</th>
                                        <th>Target</th>
										<th>Achievement</th>
										<th>Achievement Ratio</th>
										<th>Target</th>
										<th>Achievement</th>
										<th>Achievement Ratio</th>
                                    </tr>
                                </thead>
                                <tbody > '.$targetData.'</tbody>
                                <tfoot>
									<tr>
										<th colspan="2" class="text-dark text-right">TOTAL</th>
                                        <th class="text-dark">'.$totalVisit.'</th>
										<th class="text-dark">'. $totalVisitAcheive.'</th>
										<th class="text-dark"></th>
										<th class="text-dark">'.$totalLead.'</th>
										<th class="text-dark">'. $totalLeadAcheive.'</th>
										<th class="text-dark"></th>
										<th class="text-dark">'.$totalSalesAmt.'</th>
										<th class="text-dark">'.$totalSalesAcheive.'</th>
										<th class="text-dark"></th>
									</tr>
								</tfoot>
                            </table>';
                $pdfData= $this->generatePDF($htmlData,'L');
            }else { 
                $this->printJson(['status'=>1,'tbody'=>$targetData,'tfoot'=>$tfoot]);
            }
        endif;
    }

    
    /* Lead Analysis Report */
    public function leadAnalysis(){
        $this->data['headData']->pageTitle = 'LEAD ANALYSIS REPORT';
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['executiveList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/sales_report/lead_analysis",$this->data);
    }

    public function getLeadAnalysis($jsonData = ""){
        $postData = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post();
		
		$leadStages = $this->leadStages->getLeadStagesList();
		
	    $heading = [];$sourceList = [];$sourceLabel = 'label';$totalCount = [];
	    if($postData['group_by'] == "business_type")
	    {
	        $sourceList = $this->selectOption->getSelectOptionList(['type'=>7]);
	        $sourceLabel = 'label';
	    }
	    else
	    {
	        $sourceList = $this->selectOption->getSelectOptionList(['type'=>1]);
	        $sourceLabel = 'label';
	    }
	    $postData['executive_id'] = ($postData['executive_id'] == 'ALL') ? '' : $postData['executive_id'];
	    
	    $headRow = '<table class="table border-dashed mb-0 table-striped"><tr><th class="fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;">SOURCE/BUSINESS TYPE</th>';
	    $xAxise = [];
		if(!empty($leadStages)):
			foreach($leadStages as $row):
				$heading[] = $row->lead_stage;$totalCount[] = 0;
				$headRow .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;padding:0.50rem 0.20rem;">'.$row->stage_type.'</th>';
				$xAxise[] = $row->stage_type;
			endforeach;
		endif;
	    $headRow .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;">TOTAL</th>';
	    $headRow .= '</tr>';
        
        $laData = $this->salesReport->getLeadAnalysisCount($postData);
        $laDetail = $headRow;
        $leadCounts = [];
		if(!empty($laData)):
			foreach($laData as $row):
				$leadCounts[$row->{$postData['group_by']}][$row->lead_stage] = $row->lead_count;
			endforeach;
		endif;

		$lCount = [];
        if(!empty($sourceList))
        {
            foreach($sourceList as $row)
            {
				$stageArray = [];
				$stageArray[]= $row->{$sourceLabel};
                $rowTotal = 0;
                $laDetail .= '<tr>';
				$laDetail .= '<th class=" fw-bold" style="">'.$row->{$sourceLabel}.'</th>';
				for($i=0; $i<count($heading); $i++):
				    $leadCount = (!empty($leadCounts[$row->{$sourceLabel}][$heading[$i]]) ? $leadCounts[$row->{$sourceLabel}][$heading[$i]] : 0);
					$laDetail .= '<th class="text-center" style="padding:0.50rem 0.20rem;">'.$leadCount.'</th>';
					$rowTotal += $leadCount;
					$totalCount[$i] += $leadCount;
					$stageArray[] = $leadCount;
				endfor;
				$laDetail .= '<th class="text-center">'.$rowTotal.'</th>';
				$laDetail .= '</tr>';
				$lCount[] =$stageArray;
            }
        }
        $rowTotal = 0;
        $laDetail .= '<tr><th class="fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;">TOTAL</th>';
        foreach($totalCount as $lc):
			$laDetail .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;padding:0.50rem 0.20rem;">'.$lc.'</th>';
			$rowTotal += $lc;
		endforeach;
		$laDetail .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;padding:0.50rem 0.20rem;">'.$rowTotal.'</th>';
		$laDetail .= '</tr>';
		$laDetail .= '</table>';

        $reportTitle = 'Lead Analysis Report';
        $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);
        $logo = base_url('assets/images/logo.png'); //05-05-25
        $htmlData = '';
        if(!empty($postData['pdf_type']) && $postData['pdf_type'] == 1)
        {
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';

            $htmlData.= $laDetail;
                        
            $pdfData= $this->generatePDF($htmlData,'P');
        }else { 
            $this->printJson(['laDetail'=>$laDetail,'result'=>$lCount,'xAxise'=>$xAxise]);
        }
	}

     /* Executive Performance Report */
    public function executivePerformance(){
        $this->data['headData']->pageTitle = 'EXECUTIVE PERFORMANCE REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/sales_report/executive_performance",$this->data);
    }

    public function getExecutivePerformanceData($jsonData = ""){
        $data = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post();
	
		$result = $this->salesReport->getExecutivePerformanceData($data);
		$tbodyData = "";$tfootData ="";
		if(!empty($result)){
			foreach($result as $row){
				$tbodyData .= '<tr>
								<th class="text-left">'.$row->emp_name.'</th>
								<td class="text-center">'.$row->apr_amt.'</td>
								<td class="text-center">'.$row->may_amt.'</td>
								<td class="text-center">'.$row->jun_amt.'</td>
								<td class="text-center">'.$row->jul_amt.'</td>
								<td class="text-center">'.$row->aug_amt.'</td>
								<td class="text-center">'.$row->sep_amt.'</td>
								<td class="text-center">'.$row->oct_amt.'</td>
								<td class="text-center">'.$row->nov_amt.'</td>
								<td class="text-center">'.$row->dec_amt.'</td>
								<td class="text-center">'.$row->jan_amt.'</td>
								<td class="text-center">'.$row->feb_amt.'</td>
								<td class="text-center">'.$row->mar_amt.'</td>
							</tr>';
			}
			$tfootData = '<tr>
						<th class="text-right">Total</th>
						<th>'.array_sum(array_column($result,'apr_amt')).'</th>
						<th>'.array_sum(array_column($result,'may_amt')).'</th>
						<th>'.array_sum(array_column($result,'jun_amt')).'</th>
						<th>'.array_sum(array_column($result,'jul_amt')).'</th>
						<th>'.array_sum(array_column($result,'aug_amt')).'</th>
						<th>'.array_sum(array_column($result,'sep_amt')).'</th>
						<th>'.array_sum(array_column($result,'oct_amt')).'</th>
						<th>'.array_sum(array_column($result,'nov_amt')).'</th>
						<th>'.array_sum(array_column($result,'dec_amt')).'</th>
						<th>'.array_sum(array_column($result,'jan_amt')).'</th>
						<th>'.array_sum(array_column($result,'feb_amt')).'</th>
						<th>'.array_sum(array_column($result,'mar_amt')).'</th>
						</tr>';
		}
        $reportTitle = 'Executive Performance Report';
        $report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);
        $logo = base_url('assets/images/logo.png');
        $htmlData = '';
        if(!empty($data['pdf_type']) && $data['pdf_type'] == 1)
        {
            
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';

            $htmlData.= '<table class="table item-list-bb" style="margin-top:10px;">
                        	<thead>
								<tr style="font-weight: bold; background-color: #9f9b9bff;">
                                    <th class="text-left" style="width:30%;"> # </th>
                                    <th>Apr</th>
                                    <th>May</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Aug</th>
                                    <th>Sep</th>
                                    <th>Oct</th>
                                    <th>Nov</th>
                                    <th>Dec</th>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                </tr>
							</thead>
                            <tbody> '.$tbodyData.'</tbody>
                            <tfoot> '.$tfootData.'</tfoot>
                        </table>';
            $pdfData= $this->generatePDF($htmlData,'L');
        }else { 
            $this->printJson(['status'=>1,'performData'=>$result,'tbodyData'=>$tbodyData,'tfootData'=>$tfootData]);
        }
    }

     /* Appointment Register Report */
    public function appointmentRegister(){
		$this->data['headData']->pageTitle = "APPOINTMENT REGISTER REPORT";
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['salesExecutives'] = $this->employee->getEmployeeList();
        $this->load->view("reports/sales_report/appointment_register",$this->data);
    }

    public function getAppointmentRegister($jsonData = ""){
        $data = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post();

        $result = $this->salesReport->getAppointmentRegister($data);
        $i=1; $tbody='';
        if(!empty($result)):
            foreach($result as $row):
                $daysDiff = '';
				$respond_date = (!empty($row->updated_at))? $row->updated_at : date('Y-m-d');
                if(!empty($row->ref_date) AND !empty($respond_date)){
                    $ref_date = new DateTime($row->ref_date);
                    $resDate = new DateTime($respond_date);
                    $due_days = $ref_date->diff($resDate)->format("%r%a");
                    $daysDiff = ($due_days > 0) ? $due_days : 'On Time';
                }
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->mode.'</td>
                    <td>'.$row->notes.'</td>
                    <td>'.$row->remark.'</td>
                    <td>'.formatDate($row->updated_at).'</td>
                    <td>'.$daysDiff.'</td>';
                $tbody .= '</tr>';
            endforeach; 
        endif;   

        $reportTitle = 'Appointment Register Report';
        $report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);
        $logo = base_url('assets/images/logo.png'); 
        $htmlData = '';
        if(!empty($data['pdf_type']) && $data['pdf_type'] == 1){
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';

            $htmlData.= '<table class="table item-list-bb" style="margin-top:10px;">
                        	<thead>
								 <tr>
                                    <th>#</th>
                                    <th>Reminder Date</th>
                                    <th>Executive Name</th>
                                    <th>Party Name</th>
                                    <th>Mode</th>
                                    <th>Notes</th>
                                    <th>Response</th>
                                    <th>Response Date</th>
                                    <th>Due Days</th>
                                </tr>
							</thead>
                            <tbody> '.$tbody.'</tbody>
                        </table>';
            $pdfData= $this->generatePDF($htmlData,'L');
        }else{
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        }
    }

    /* FollowUp Register Report */
    public function followUpRegister(){
		$this->data['headData']->pageTitle = "FOLLOWUP REGISTER REPORT";
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['partyList'] = $this->party->getPartyList(); 
        $this->data['businessTypeList'] = $this->selectOption->getSelectOptionList(['type'=>7]);
        $this->load->view("reports/sales_report/followup_register",$this->data);
    }

    public function getFollowUpRegister($jsonData = ""){
        $data = (!empty($jsonData))?decodeUrl($jsonData,true):$this->input->post();
        
        $result = $this->salesReport->getFollowUpRegister($data);
		$i=1;$tbody='';
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->created_at).'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->business_type.'</td>
                    <td>'.$row->notes.'</td>
                    </tr>';
            endforeach; 
        endif; 
        
        $reportTitle = 'FOLLOWUP REGISTER REPORT';
        $report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);
        $logo = base_url('assets/images/logo.png');
        
        $htmlData = '';
        if(!empty($data['pdf_type']) && $data['pdf_type'] == 1){
            $htmlData = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';

            $htmlData.= '<table class="table item-list-bb" style="margin-top:10px;">
                        	<thead class="gradient-theme">
								 <tr>
                                    <th> # </th>
                                    <th>Date</th>
                                    <th>Executive Name</th>
                                    <th>Party Name</th>
                                    <th>Business Segment</th>
                                    <th>FollowUp Massage</th>
                                </tr>
							</thead>
                            <tbody> '.$tbody.'</tbody>
                        </table>';
            $pdfData= $this->generatePDF($htmlData,'P');
        }else{ 
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        }
    }
    
}
?>