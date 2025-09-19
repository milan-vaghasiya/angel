<?php
class SalesReportModel extends MasterModel{
    private $soMaster = "so_master";
    private $soTrans = "so_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getOrderMonitoringData($data){
        $queryData = array();
        $queryData['tableName'] = $this->soMaster;
        $queryData['select'] = "so_master.trans_number,so_master.trans_date,so_master.doc_no,so_trans.qty,item_master.item_name,party_master.party_name,so_trans.id,so_trans.cod_date";
        $queryData['leftJoin']['so_trans'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']['party_master'] = "so_master.party_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "so_trans.item_id = item_master.id";
        $queryData['customWhere'][] = "so_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id'])){
            $queryData['where']['so_master.party_id'] = $data['party_id'];
		}
        if(!empty($data['item_id'])){
            $queryData['where']['so_trans.item_id'] = $data['item_id'];
		}
		if(isset($data['status']) && $data['status'] !== ''){
            $queryData['where_in']['so_trans.trans_status'] = $data['status'];
        }
        $queryData['order_by']['so_master.trans_date'] = "ASC";
        $queryData['order_by']['so_master.trans_number'] = "ASC";

        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesInvData($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.trans_number as invNo,trans_main.trans_date as invDate,trans_child.qty as invQty,trans_child.ref_id";
        $queryData['leftJoin']['trans_child'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where']['trans_child.ref_id'] = $data['ref_id'];
        $queryData['where']['trans_main.from_entry_type'] = 14;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesAnalysisData($data){
        $queryData = array();
        if($data['report_type'] == 1):
            $queryData['tableName'] = $this->transMain;
            $queryData['select'] = "party_name,SUM(taxable_amount) as taxable_amount,SUM(gst_amount) as gst_amount,SUM(net_amount) as net_amount";
            $queryData['where']['trans_date >='] = $data['from_date'];
            $queryData['where']['trans_date <='] = $data['to_date'];
            $queryData['where']['vou_name_s'] = "Sale";
            $queryData['group_by'][] = 'party_id';
            $queryData['order_by']['SUM(taxable_amount)'] = $data['order_by'];
            $result = $this->rows($queryData);
        else:
            $queryData['tableName'] = $this->transChild;
            $queryData['select'] = "trans_child.item_name,SUM(trans_child.qty) as qty,SUM(trans_child.taxable_amount) as taxable_amount,ROUND((SUM(trans_child.taxable_amount) / SUM(trans_child.qty)),2) as price";
            $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_date >='] = $data['from_date'];
            $queryData['where']['trans_date <='] = $data['to_date'];
            $queryData['where']['vou_name_s'] = "Sale";
            $result = $this->rows($queryData);
        endif;
        return $result;
    }

    public function getMrpReportData($data) {
        $queryData['tableName'] = $this->soTrans;  
        $queryData['select'] = 'so_master.trans_number,so_master.trans_date,bom.item_name as bom_item_name,(stock_data.stock_qty / item_kit.qty) AS plan_qty';
        $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']['item_kit'] = "item_kit.item_id = so_trans.item_id AND item_kit.is_delete = 0";
        $queryData['leftJoin']['item_master AS bom'] = "bom.id = item_kit.ref_item_id";
        $queryData['leftJoin']['(SELECT SUM(`stock_trans`.`qty` * `stock_trans`.`p_or_m`) AS stock_qty,`stock_trans`.`item_id` FROM `stock_trans` WHERE is_delete = 0 GROUP BY `stock_trans`.`item_id`) AS stock_data'] = 'stock_data.item_id = item_kit.ref_item_id';
        if(!empty($data['party_id']) && $data['party_id'] != 'ALL'){ $queryData['where']['so_master.party_id'] = $data['party_id']; }
        if(!empty($data['item_id'])){ $queryData['where']['so_trans.item_id'] = $data['item_id']; }
        $queryData['where']['(so_trans.qty - IFNULL(so_trans.dispatch_qty, 0.000)) >'] = 0;
        $queryData['order_by']['so_master.trans_no'] = 'ASC';
        $queryData['order_by']['so_trans.id'] = 'ASC';
        return $this->rows($queryData);
    }

	/* Customer Complaints Report*/
    public function getCustomerComplaintsData($data){
        $queryData = array();
        $queryData['tableName'] = 'customer_complaints';
        $queryData['select'] = "customer_complaints.trans_number,customer_complaints.trans_date,,item_master.item_name,party_master.party_name,trans_main.trans_number as inv_number,customer_complaints.complaint,customer_complaints.report_no,customer_complaints.action_taken,customer_complaints.ref_feedback,customer_complaints.remark,customer_complaints.defect_image";
        $queryData['leftJoin']['party_master'] = "customer_complaints.party_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "customer_complaints.item_id = item_master.id";
        $queryData['leftJoin']['trans_child'] = "trans_child.id = customer_complaints.inv_trans_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['customWhere'][] = "customer_complaints.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id'])):
            $queryData['where']['customer_complaints.party_id'] = $data['party_id'];
        endif;
        if(!empty($data['item_id'])):
            $queryData['where']['customer_complaints.item_id'] = $data['item_id'];
        endif;
        $queryData['order_by']['customer_complaints.trans_date'] = "ASC";
        $queryData['order_by']['customer_complaints.trans_number'] = "ASC";

        $result = $this->rows($queryData);
        return $result;
    }

    /* Expense Register Report */
    public function getExpenseData($data){
        $queryData['tableName'] = "expense_manager";
        $queryData['select'] = "expense_manager.id, expense_manager.exp_type, expense_manager.exp_number, DATE_FORMAT(expense_manager.exp_date,'%d-%m-%Y') as exp_date, expense_manager.demand_amount, expense_manager.approved_by, expense_manager.notes, employee_master.emp_name, select_master.label as exp_name,expense_manager.amount,expense_manager.exp_by_id,expense_manager.exp_type_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
        $queryData['leftJoin']['select_master'] = "select_master.id = expense_manager.exp_type_id AND select_master.type = 3";
        if(!empty($data['emp_id'])){
            $queryData['where']['expense_manager.exp_by_id'] = $data['emp_id'];
        }
        $queryData['customWhere'][] = "expense_manager.exp_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        return $this->rows($queryData);
    }

    /* Monthly Sales Analysis Data */
	public function getMonthlySalesAnalysisData($data){
        $queryData = array();
        $queryData['tableName'] = "so_trans";
        $queryData['select'] = "SUM(CASE WHEN MONTH(so_master.trans_date) = 1  THEN so_trans.taxable_amount ELSE 0 END) AS jan_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 2  THEN so_trans.taxable_amount ELSE 0 END) AS feb_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 3  THEN so_trans.taxable_amount ELSE 0 END) AS mar_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 4  THEN so_trans.taxable_amount ELSE 0 END) AS apr_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 5  THEN so_trans.taxable_amount ELSE 0 END) AS may_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 6  THEN so_trans.taxable_amount ELSE 0 END) AS jun_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 7  THEN so_trans.taxable_amount ELSE 0 END) AS jul_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 8  THEN so_trans.taxable_amount ELSE 0 END) AS aug_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 9  THEN so_trans.taxable_amount ELSE 0 END) AS sep_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 10  THEN so_trans.taxable_amount ELSE 0 END) AS oct_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 11  THEN so_trans.taxable_amount ELSE 0 END) AS nov_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 12  THEN so_trans.taxable_amount ELSE 0 END) AS dec_amt,
                                (CASE WHEN  ".$data['report_type']."= 1 THEN item_category.category_name ELSE  (CASE WHEN  ".$data['report_type']." = 5 THEN employee_master.emp_name ELSE (CASE WHEN  ".$data['report_type']." = 2 THEN IFNULL(party_master.sales_zone,'NIL') ELSE (CASE WHEN  ".$data['report_type']." = 3 THEN IFNULL(party_master.source,'NIL') ELSE IFNULL(party_master.business_type,'NIL') END) END)  END) END) AS category_name";
								
        $queryData['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id ";
        $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id ";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = so_master.sales_executive";

        $queryData['where']['so_master.trans_date >='] = $this->startYearDate;
        $queryData['where']['so_master.trans_date <='] = $this->endYearDate;
        $queryData['where']['so_master.sales_executive >'] = 0;		
        $queryData['group_by'][] = $data['group_by'];
        return $this->rows($queryData);
    }

	/* Party Budget Analysis Data */
    public function getPartyBudgetAnalysis($data){
        $queryData = [];
        $queryData['tableName'] = "so_master";

        $queryData['select'] = "so_master.party_id, party_master.party_name, party_master.party_mobile, DATE_FORMAT(so_master.trans_date,'%Y-%m') as month,SUM(so_master.taxable_amount) as taxable_amount,party_master.business_capacity,executive_master.emp_name as executive_name, party_master.city_name, countries.name as country_name, states.name as state_name";

        $queryData['leftJoin']['party_master'] = "so_master.party_id = party_master.id";
        $queryData['leftJoin']['employee_master as executive_master'] = "executive_master.id = party_master.sales_executive";
        $queryData['leftJoin']['countries'] = "party_master.country_id = countries.id";
        $queryData['leftJoin']['states'] = "party_master.state_id = states.id";
		

        $queryData['where']['so_master.trans_date >='] = $data['from_date'];
        $queryData['where']['so_master.trans_date <='] = $data['to_date'];

        if(!empty($data['executive_id'])):
            $queryData['where']['party_master.sales_executive'] = $data['executive_id'];
        endif;
        $queryData['group_by'][] = "DATE_FORMAT(so_master.trans_date,'%Y-%m'),so_master.party_id";
        $result = $this->rows($queryData);
        return $result;
    }
    
    /* Inactive Party Analysis Data */
    public function getInactivePartyDetail($data){
        $data['inactive_days'] = (!empty($data['inactive_days']))?$data['inactive_days']:10; 
        $queryData = [];
        $queryData['tableName'] = "party_master";
        $queryData['select'] = "party_master.id, party_master.party_code, party_master.party_name, party_master.business_type, party_master.contact_person, party_master.party_mobile, executive_master.emp_name as executive_name, countries.name as country_name, states.name as state_name,party_master.city_name, IFNULL(pa.inactive_days, DATEDIFF(NOW(), party_master.created_at)) as inactive_days, IFNULL(pa.last_activity_date,party_master.created_at) as last_activity_date";

        $queryData['leftJoin']['employee_master as executive_master'] = "executive_master.id = party_master.sales_executive";
        $queryData['leftJoin']['(SELECT party_id, DATEDIFF(NOW(), MAX(ref_date)) as inactive_days, MAX(ref_date) as last_activity_date FROM party_activities WHERE is_delete = 0  GROUP BY party_id) as pa'] = "pa.party_id = party_master.id"; 
        $queryData['leftJoin']['countries'] = "party_master.country_id = countries.id";
        $queryData['leftJoin']['states'] = "party_master.state_id = states.id";
		
        $queryData['customWhere'][] = 'IFNULL(pa.inactive_days,0) >'. $data['inactive_days'];

        if(!empty($data['executive_id'])):
            $queryData['where']['party_master.sales_executive'] = $data['executive_id'];
        endif;

        if(empty($data['order_by'])):
            $queryData['order_by']['party_master.party_name'] = "ASC";
        endif;
        $queryData['group_by'][] = "party_master.id";
            
        $result = $this->rows($queryData);
        return $result;
    }

    /* Customer History Data */
	public function getLogCountForCustHistory($data){
        $queryData['tableName'] = "party_activities";
        $queryData['select'] = "(CASE WHEN party_activities.lead_stage = 1 THEN party_activities.created_at ELSE 0 END) as lead_created,SUM(CASE WHEN party_activities.lead_stage = 7 THEN 1 ELSE 0 END) as total_orders,soMaster.taxable_amount as total_ord_amt";
        $queryData['leftJoin']['(SELECT SUM(taxable_amount) as taxable_amount,party_id FROM so_master WHERE is_delete = 0 GROUP BY party_id) as soMaster'] = "soMaster.party_id = party_activities.party_id";

        $queryData['where']['party_activities.party_id'] = $data['party_id'];
        $queryData['order_by']['party_activities.id'] = 'ASC';
        
        $result = $this->row($queryData);
        return $result;
    }

    /* Target V/S Achieve Data */
	public function getTargetVsAchieveData($param = []){ 
        $targetMonth = isset($param['target_month']) ? $param['target_month'] : date('Y-m');  
        $startDate = date("Y-m-01", strtotime($targetMonth));
        $endDate = date("Y-m-t", strtotime($targetMonth));

        $queryData['tableName'] = 'executive_targets';
		$queryData['select'] = "executive_targets.*,employee_master.emp_name,employee_master.emp_code, IFNULL(visits.achieve_visit,0) as achieve_visit, soMaster.achieve_sales_amount";
		
		$queryData['select'] .= ",IFNULL(partyMaster.achieve_new_lead,0) as achieve_new_lead";
		$queryData['leftJoin']['employee_master'] = "executive_targets.emp_id = employee_master.id";
        $queryData['leftJoin']["(SELECT 
				SUM(taxable_amount) as achieve_sales_amount,so_master.sales_executive 
			FROM so_master  
			WHERE so_master.is_delete = 0 AND 
				so_master.trans_date BETWEEN '{$startDate}' AND '{$endDate}'
			GROUP BY so_master.sales_executive) as soMaster"] = "soMaster.sales_executive = employee_master.id";

        $queryData['leftJoin']["(SELECT COUNT(*) AS achieve_visit, created_by FROM visits WHERE DATE(end_at) BETWEEN '{$startDate}' AND '{$endDate}' AND end_at IS NOT NULL AND is_delete = 0 GROUP BY created_by) as visits"] = "visits.created_by = employee_master.id";

        $queryData['leftJoin']["(SELECT created_by,
                                IFNULL(SUM(CASE WHEN lead_stage = 1 THEN 1 ELSE 0 END), 0) as achieve_new_lead
                          FROM party_activities 
                          WHERE DATE(ref_date) BETWEEN '{$startDate}' AND '{$endDate}'
                          GROUP BY created_by) as partyMaster"] = "partyMaster.created_by = employee_master.id";
		
        $queryData['where']['executive_targets.target_month'] = $targetMonth;
		
        if(!empty($param['emp_id'])){
            $queryData['where']['executive_targets.emp_id'] = $param['emp_id'];
        }
        $queryData['group_by'][] = 'employee_master.id';
        $result = $this->rows($queryData);
        return $result;
    }

    /* Lead Analysis Data */
    public function getLeadAnalysisCount($param=[]){ 
        $queryData['tableName'] = "party_master";
        $queryData['select'] = "count(*) as lead_count,party_type,lead_stage";        
        $queryData['leftJoin']['employee_master'] = "party_master.sales_executive = employee_master.id";

        if(!empty($param['executive_id'])){ $queryData['where']['party_master.sales_executive'] = $param['executive_id']; }
        
        if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", executive_master.super_auth_id) >0 OR executive_master.id = '.$this->loginId.')';
        endif;
        
        if(!empty($param['from_date'])){ $queryData['where']['party_master.created_at >= '] = date('Y-m-d H:i:s',strtotime($param['from_date'].' 00:00:00')); }
        if(!empty($param['to_date'])){ $queryData['where']['party_master.created_at <= '] = date('Y-m-d H:i:s',strtotime($param['to_date'].' 23:59:59')); }
        
        if(!empty($param['group_by'])){
            
            $queryData['select'] .= ",party_master.".$param['group_by']; 
            $queryData['group_by'][] = $param['group_by'];
            $queryData['group_by'][] ='lead_stage';
            $queryData['order_by'][$param['group_by']] = 'ASC';
        }
        $result = $this->rows($queryData);
        return $result;
    }

    /* Executive Performance Data */
    public function getExecutivePerformanceData($data){
        $queryData = array();
        $queryData['tableName'] = "employee_master";
        $queryData['select'] = "employee_master.emp_name,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 1  THEN so_master.taxable_amount ELSE 0 END) AS jan_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 2  THEN so_master.taxable_amount ELSE 0 END) AS feb_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 3  THEN so_master.taxable_amount ELSE 0 END) AS mar_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 4  THEN so_master.taxable_amount ELSE 0 END) AS apr_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 5  THEN so_master.taxable_amount ELSE 0 END) AS may_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 6  THEN so_master.taxable_amount ELSE 0 END) AS jun_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 7  THEN so_master.taxable_amount ELSE 0 END) AS jul_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 8  THEN so_master.taxable_amount ELSE 0 END) AS aug_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 9  THEN so_master.taxable_amount ELSE 0 END) AS sep_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 10  THEN so_master.taxable_amount ELSE 0 END) AS oct_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 11  THEN so_master.taxable_amount ELSE 0 END) AS nov_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 12  THEN so_master.taxable_amount ELSE 0 END) AS dec_amt";
        $queryData['leftJoin']['so_master'] = "so_master.sales_executive = employee_master.id";
		$queryData['where']['employee_master.is_active'] = 1;
		$queryData['group_by'][] = 'employee_master.id';
        $result = $this->rows($queryData);
        return $result; 
    }

    /* Appointment Register Data */
    public function getAppointmentRegister($data){ 
        $queryData = array();
        $queryData['tableName'] = "party_activities";
        $queryData['select'] = "party_activities.id,party_activities.ref_date,party_activities.lead_stage,party_activities.notes,party_activities.remark,party_activities.updated_at,party_activities.mode,party_activities.party_id,party_master.party_name ,employee_master.emp_name,party_activities.created_by";

        $queryData['leftJoin']['party_master'] = "party_master.id = party_activities.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = party_master.sales_executive";
        
        if(!empty($data['executive_id'])):
            $queryData['where']['party_master.sales_executive'] = $data['executive_id'];
        endif;
        
        if(!empty($data['mode'])):
            $queryData['where']['party_activities.mode'] = $data['mode'];
        endif;

        if(!empty($data['status'])) {
            if($data['status'] == 1){
                $queryData['customWhere'][] = 'party_activities.updated_at IS NULL';
            }elseif($data['status'] == 2){
                $queryData['customWhere'][] = 'party_activities.updated_at IS NOT NULL';
            }elseif($data['status'] == 3){ 
                $queryData['customWhere'][] = 'DATE(party_activities.ref_date) < DATE(party_activities.updated_at)';
            }
        }

        if(!empty($data['from_date'])){
            $queryData['where']['DATE(party_activities.ref_date) >='] = $data['from_date'];
        }

        if(!empty($data['to_date'])){
            $queryData['where']['DATE(party_activities.ref_date) <='] = $data['to_date'];
        }
        $queryData['where']['party_activities.lead_stage'] = 2;
		$queryData['order_by']['party_activities.ref_date'] = 'ASC';

        $result = $this->rows($queryData);
        return $result;
    }

    /*  Followup Register Data*/
    public function getFollowUpRegister($data){
        $queryData = array();
        $queryData['tableName'] = "party_activities";
        $queryData['select'] = "party_activities.id,party_activities.created_at,party_master.sales_executive,party_activities.notes,party_master.party_type,party_activities.party_id,party_master.party_name,employee_master.emp_name,party_master.business_type";
        $queryData['leftJoin']['party_master'] = "party_master.id = party_activities.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = party_master.sales_executive";

		if(!empty($data['business_type'])){
            $queryData['where']['party_master.business_type'] = $data['business_type'];
        }
        if(!empty($data['from_date'])){
            $queryData['where']['DATE(party_activities.created_at) >='] = $data['from_date'];
        }
        if(!empty($data['to_date'])){
            $queryData['where']['DATE(party_activities.created_at) <='] = $data['to_date'];
        }
        if(!empty($data['party_id'])){
            $queryData['where']['party_activities.party_id'] = $data['party_id'];
        }
        $queryData['where']['party_activities.lead_stage'] = 3;

        $result = $this->rows($queryData);
        return $result;
    }

}
?>