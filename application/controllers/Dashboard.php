<?php
class Dashboard extends MY_Controller{

	private $hbd_msg = 'The warmest wishes to a great member of our team. May your special day be full of happiness, fun and cheer!\r\n-APPLIED AUTO PARTS PVT LTD';
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "dashboard";
	}
	
	public function index(){	    
		/** For Sales Analysis Line Chart */
		$salesAnalyisisData = $this->dashboard->getMonthlySalesAnalysisdata();
		$this->data['soAnalysis'] = array_reduce($salesAnalyisisData, function($soAnalysis, $row) {  $soAnalysis[$row->month] = $row->total_sales;  return $soAnalysis;  }, []);

		/** For Sales Order DAta  Total Orders, Order Avg. Value, Conversion Rate*/
		$this->data['soData'] = $this->dashboard->getSalesOrderData();
		$this->data['dispatchData'] = $this->dashboard->getDispatchQty();

		/** Delay Dispatch ( No. of Items delay in dispatch as per Delivery Date ) */
		$this->data['soDelayData'] = $this->dashboard->getDelaySoData();

		/** MACHINE ANALYSIS : Running, Free, Under Maintenance */
		$this->data['mcData'] = $this->dashboard->getMachineDashboardData();

		/** PROCESS ANALYSIS : Work Load Time & Qty Wise */
		$having = 'in_qty - (ok_qty+ rej_qty + rw_qty + (rej_found - review_qty))';
		$this->data['wipData'] = $this->sop->getProessWiseWip(['log_data'=>1,'pending_accepted'=>1,'having'=>$having,'group_by'=>'product_process.process_id','status'=>'1,2']);
	
		/** OVER INVENTORY : Amount of Total Stock ( AGE id > 30 Days ) */
		$this->data['overInvent'] = $this->dashboard->getOverInventoryData();

		/** LOW INVENTORY : No. of Items which is under Stock */
		$this->data['lowInvent'] = $this->dashboard->getLowerInventoryData();

		/** Defect Rate : % of Rejection against total production ( Current Month )  */
		$this->data['rejData'] = $this->dashboard->getTotalProdVsRejData();

		/** Customer Complaint Rate : No. of Customer, No. of Parts, No. of Complaints ( In Current Month ) */
		$this->data['complainData'] = $this->dashboard->getComplaintData();
		
		/** DEMAND Vs. INVENTORY : % of Stock Against Demand */
		$stockData = $this->purchaseIndent->getForecastDtRows(['rowData'=>'rows']);
		$invPer = 0;
		if(!empty($stockData)){
			$perSum = 0;$count  = 0;
			foreach($stockData AS $row){
				$rm_shortage = $row->required_material - ($row->rm_stock + $row->pending_po + $row->pending_grn);
				$reqMaterial = $row->required_material;
				$shortage_per = (($reqMaterial > 0)?($rm_shortage * 100)/$reqMaterial:0);
				$perSum += 100 - $shortage_per;
				$count++;
			}
			$invPer = $perSum/$count;
		}
		$this->data['inventory_per'] = sprintf('%.2f',$invPer);

		/**  PRODUCTION LOSS : In Amount ( Machine/Material/Man Power/QC-Rejection ) */
		$this->data['machineLoss'] = $this->dashboard->getMachineLossData();
		$this->data['rejLoss'] = $this->dashboard->getRejLossData();

		/* OEE : Total OEE */
		$productionData = $this->productionReport->getOeeRegister(['from_date'=>date("Y-m-1"),'to_date'=>date("Y-m-d")]);
		$countRow = 0;$totalOeePer = 0;$totalQkQty = 0;$totalFpy = 0; $totalRwOk = 0;$totalReworkTime = 0;
		if(!empty($productionData)){
			foreach($productionData as $row){
				$plan_prod_time = 24;
				$plan_qty = ((!empty($row->cycle_time))?ceil(($plan_prod_time*3600)/$row->cycle_time):0);
				$actual_plan_qty = (!empty($row->total_production_time) && !empty($row->cycle_time))? (int) (($row->total_production_time * 60) / $row->cycle_time):0;

				$row->total_production_time = (!empty($row->total_production_time))?round($row->total_production_time/60,2):0;
				$production_qty = floatval($row->total_ok_qty + $row->total_rej_qty);
				$actual_ct = round(((!empty($production_qty))?(($row->total_production_time * 3600)/$production_qty):0),2);
				$availability_per = round((($row->total_production_time) * 100)/$plan_prod_time,2);
				$effecincy_per = (!empty($actual_plan_qty))?round(($production_qty*100)/$actual_plan_qty,2):0;
				$qc_per =  (!empty($production_qty))?round((($row->total_ok_qty * 100)/$production_qty),2):0;

				$oee_per = round(($availability_per + $effecincy_per  + $qc_per)/3,2);
				$totalOeePer += $oee_per;
				$countRow++;

				$totalQkQty += $row->total_ok_qty + $row->rw_ok_qty;
				$totalFpy += $row->total_ok_qty;

				$totalReworkTime += $row->rw_production_time;
			}
			
			$this->data['oee_per'] = $totalOeePer/$countRow;
		}

		/* First Pass Yield (FPY) : % of OK Qty */ 
		$this->data['fpy_per'] = ((!empty($totalQkQty))?(round(($totalFpy*100)/$totalQkQty,2)):0);
		
		/** Rework Time Rate : % of Total Rework Time against 24 Hours */
		$totalTime = 24*date("d")*60;
		$this->data['rw_rate'] = round((($totalReworkTime*100)/$totalTime),2);
        $this->load->view('dashboard',$this->data);
    }
	
	public function getDailyAttendance(){
		$data = $this->input->post();
		$todayStats = $this->attendance->getAttendanceStatsByDate(formatDate($data['report_date'],'Y-m-d'));
		$i=1;$tBody='';
		if(!empty($todayStats['empInfo']))
		{
			foreach($todayStats['empInfo'] as $row)
			{
				$pnch = explode(',',$row->punch_time);
				if(!empty($pnch)){if($pnch[0] > $row->shiftStart){$row->attend_status = "Late Arrived";}}
				$statusCls = 'text-dark';$rowBg= '';
				if($row->attend_status == "Absent"){$statusCls = 'text-danger';}
				if($row->attend_status == "Late Arrived"){$statusCls = 'text-warning';}
				if($row->attend_status == "Week Off" OR $row->attend_status == "Holiday"){$rowBg = 'background:#efff0033';}
				$tBody .= '<tr style="'.$rowBg.'">';
					$tBody .= '<td class="text-center">'.$row->emp_code.'</td>';
					$tBody .= '<td>'.$row->emp_name.'</td>';
					$tBody .= '<td>'.$row->name.'</td>';
					$tBody .= '<td>'.$row->shift_name.'</td>';
					$tBody .= '<td>'.$row->title.'</td>';
					$tBody .= '<td>'.$row->category.'</td>';
					$tBody .= '<th class="'.$statusCls.'">'.$row->attend_status.'</th>';
					$tBody .= '<td class="text-center">'.$row->punch_time.'</td>';
				$tBody .= '</tr>';
			}
		}
		$this->printJson(['status'=>1,"totalEmp"=>$todayStats['totalEmp'],"present"=>$todayStats['present'],"late"=>$todayStats['late'],"absent"=>$todayStats['absent'],'tbody'=>$tBody]);
	}

    public function syncDeviceData(){
		$this->printJson($this->biometric->syncDeviceData());
    }

    public function decodeQRCode(){
        $qrValue = $this->input->post('qrValue');
        $decodeData='';$itemData=new stdClass();$part_no="";
        if(!empty($qrValue))
        {
            $code = explode('#',$qrValue);
            $part_no=substr($code[0], 1,8);
            $itemData=$this->item->getItemByPartNo($part_no);
        }
        $decodeData='<table class="table table-bordered table-striped">
            <tr><th style="width:100px;">Part No.</th><td>'.$part_no.'</td></tr>
            <tr><th>Part Name</th><td>'.((!empty($itemData->full_name)) ? $itemData->full_name : '').'</td></tr>
            <tr><th>Rev. No.</th><td>'.((!empty($itemData->rev_no)) ? $itemData->rev_no : substr($code[0], -1)).'</td></tr>
            <tr><th>Job No.</th><td>'.substr($code[1], 1).'</td></tr>
            <tr><th>Vendor Code</th><td>'.substr($code[2], 1).'</td></tr>
            <!--<tr><th>Customer</th><td>'.((!empty($itemData->party_name)) ? $itemData->party_name : '').'</td>--></tr>
        </table>';
		$this->printJson(['status'=>1,"decodeData"=>$decodeData]);
    }
}
?>