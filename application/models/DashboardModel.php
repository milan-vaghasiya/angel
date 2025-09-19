<?php
class DashboardModel extends MasterModel{
    
    private $transMain = "trans_main"; 
    
    public function sendSMS($mobiles,$message){
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://sms.scubeerp.in/sendSMS?");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "username=9427235336&message=".$message."&sendername=NTVBIT&smstype=TRANS&numbers=".$mobiles."&apikey=7d37fc6d-a141-4f81-9d79-159cf37c3342");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);
	}
	
	public function getInvoiceData($data){		
        $queryData = array();
		$queryData['tableName'] = $this->transMain;
		
        $queryData['select'] = "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=4 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si4,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=5 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si5,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=6 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si6,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=7 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si7,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=8 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si8,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=9 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si9,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=10 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si10,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=11 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si11,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=12 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si12,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=1 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as si1,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=2 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as si2,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=3 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as si3,";
		
		$queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=4 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi4,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=5 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi5,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=6 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi6,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=7 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi7,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=8 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi8,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=9 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi9,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=10 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi10,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=11 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi11,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=12 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi12,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=1 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as pi1,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=2 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as pi2,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=3 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as pi3";
		
        $queryData['where_in']['entry_type'] = [$data['si_entry_type'],$data['pi_entry_type']];
		$result = $this->row($queryData);
		return $result;
    }

    public function getMonthlySalesAnalysisdata(){
        $result = $this->db->query("SELECT
                                    DATE_FORMAT(DATE_ADD('".$this->startYearDate."', INTERVAL (months.n_month - 1) MONTH), '%Y%m') AS month,
                                    COALESCE(SUM(so_master.taxable_amount), 0) AS total_sales
                                FROM
                                    (
                                        SELECT 1 AS n_month UNION ALL
                                        SELECT 2 UNION ALL
                                        SELECT 3 UNION ALL
                                        SELECT 4 UNION ALL
                                        SELECT 5 UNION ALL
                                        SELECT 6 UNION ALL
                                        SELECT 7 UNION ALL
                                        SELECT 8 UNION ALL
                                        SELECT 9 UNION ALL
                                        SELECT 10 UNION ALL
                                        SELECT 11 UNION ALL
                                        SELECT 12
                                    ) months
                                LEFT JOIN so_master
                                    ON DATE_FORMAT(so_master.trans_date, '%Y-%m') = DATE_FORMAT(DATE_ADD('".$this->startYearDate."', INTERVAL (months.n_month - 1) MONTH), '%Y-%m')
                                    AND so_master.is_delete = 0
                                GROUP BY months.n_month
                                ORDER BY months.n_month;
                                ")->result();

        return $result;
    }

    public function getSalesOrderData(){
        $queryData['tableName'] = 'so_trans';
        $queryData['select'] = 'COUNT(so_trans.id) AS no_of_items,(SUM(so_trans.taxable_amount)/COUNT(DISTINCT so_master.id)) AS avg_order,SUM(so_trans.qty) AS total_order_qty,SUM(so_trans.taxable_amount) AS total_sum';
        $queryData['leftJoin']['so_master'] = 'so_master.id = so_trans.trans_main_id';
        $queryData['where']['so_master.trans_date >='] = $this->startYearDate;
        $queryData['where']['so_master.trans_date <='] = $this->endYearDate;
        return $this->row($queryData);
    }

    public function getDispatchQty(){
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = 'SUM(stock_trans.qty) AS total_dispatch_qty';
        $queryData['where_in']['stock_trans.trans_type'] = "'INV','DLC'";
        $queryData['where']['stock_trans.trans_date >='] = $this->startYearDate;
        $queryData['where']['stock_trans.trans_date <='] = $this->endYearDate;
        return $this->row($queryData);
    }

    public function getDelaySoData(){
        $queryData['tableName'] = 'so_trans';
        $queryData['select'] = 'COUNT(so_trans.id) AS delayed_item';
        $queryData['leftJoin']['so_master'] = 'so_master.id = so_trans.trans_main_id';
        $queryData['where']['so_trans.trans_status'] = 0;
        $queryData['customWhere'][] = 'so_trans.qty > so_trans.dispatch_qty AND DATE(so_trans.cod_date) < "'.date('Y-m-d').'"';
        $queryData['where']['so_master.trans_date >='] = $this->startYearDate;
        $queryData['where']['so_master.trans_date <='] = $this->endYearDate;
        return $this->row($queryData);
    }

    public function getMachineDashboardData(){
        $queryData['tableName'] = 'item_master';
        $queryData['select'] = 'SUM(CASE WHEN item_master.mc_status = 1 THEN 1 ELSE 0 END) AS free_mc,
                                SUM(CASE WHEN item_master.mc_status = 2 THEN 1 ELSE 0 END) AS inprocess_mc,
                                SUM(CASE WHEN item_master.mc_status = 3 THEN 1 ELSE 0 END) AS maintance_mc,';
        $queryData['where']['item_master.item_type'] = 5;
        return $this->row($queryData);
    }

    public function getOverInventoryData(){
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = 'SUM((stock_trans.qty * stock_trans.p_or_m)) AS stock_qty,SUM((stock_trans.qty * stock_trans.p_or_m) * grn_trans.price) as over_stock_amount,grn_master.trans_date';
        $queryData['leftJoin']['grn_trans'] = 'grn_trans.item_id = stock_trans.item_id AND grn_trans.batch_no = stock_trans.batch_no';
        $queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_trans.grn_id';
        $queryData['group_by'][] = 'stock_trans.item_id,stock_trans.batch_no';
        $queryData['having'][] = '(grn_master.trans_date <= NOW() - INTERVAL 30 DAY)  AND stock_qty > 0';
        return $this->row($queryData);
    }

    public function getLowerInventoryData(){
        $queryData['tableName'] = 'item_master';
        $queryData['select'] = 'IFNULL(st.stock_qty,0) AS stock_qty,item_master.min_qty';
        $queryData['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,item_id,location_id FROM stock_trans WHERE is_delete = 0  GROUP BY item_id) as st'] = "item_master.id = st.item_id";
        $queryData['having'][] = 'item_master.min_qty > stock_qty';
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getTotalProdVsRejData(){
        $queryData['tableName'] = 'prc_log';
        $queryData['select'] = 'SUM(qty+rej_found) AS total_production_qty,SUM(rej_qty) AS total_rej_qty';
        $queryData['customWhere'][] = 'prc_log.trans_date BETWEEN "'.date("Y-m-1").'" AND "'.date("Y-m-d").'"';
        return $this->row($queryData);
    }

    public function getComplaintData(){
        $queryData['tableName'] = 'customer_complaints';
        $queryData['select'] = 'COUNT(DISTINCT party_id) AS comp_counstomer,COUNT(DISTINCT item_id) AS comp_items,COUNT(*) AS total_complaint';
        $queryData['customWhere'][] = 'customer_complaints.trans_date BETWEEN "'.date("Y-m-1").'" AND "'.date("Y-m-d").'"';
        return $this->row($queryData);
    }

    public function getMachineLossData(){
        $result = $this->db->query("SELECT SUM(
                                                (TIMESTAMPDIFF(
                                                    MINUTE,
                                                    GREATEST(trans_date, DATE_FORMAT(CURDATE(), '%Y-%m-01')),
                                                    LEAST(
                                                        IFNULL(end_date, NOW()),
                                                        LAST_DAY(CURDATE()) + INTERVAL 1 DAY
                                                    )
                                                )/60) *  item_master.mhr
                                            )  AS total_machine_loss,
                                            (TIMESTAMPDIFF(
                                                    MINUTE,
                                                    GREATEST(trans_date, DATE_FORMAT(CURDATE(), '%Y-%m-01')),
                                                    LEAST(
                                                        IFNULL(end_date, NOW()),
                                                        LAST_DAY(CURDATE()) + INTERVAL 1 DAY
                                                    )
                                                )/60) AS total_time
                                        FROM machine_breakdown
                                        JOIN item_master ON item_master.id = machine_breakdown.machine_id
                                        WHERE 
                                            -- Only breakdowns that started or are ongoing in the current month
                                            ( trans_date <= LAST_DAY(CURDATE()) AND 
                                              (end_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') OR end_date IS NULL)
                                            )
                                            AND machine_breakdown.is_delete = 0;")->row();
        return $result;
    }

    public function getRejLossData(){
        $queryData['tableName'] = 'rejection_log';
        $queryData['select'] = 'SUM(rejection_log.qty * item_master.price) AS total_rej_loss';
        $queryData['leftJoin']['item_master'] = 'item_master.id = rejection_log.item_id';
        $queryData['where']['rejection_log.decision_type'] = 1;
        $queryData['where']['rejection_log.source'] = 'MFG';
        $queryData['customWhere'][] = 'DATE(rejection_log.created_at) BETWEEN "'.date("Y-m-1").'" AND "'.date("Y-m-d").'"';
        return $this->row($queryData);
    }
}
?>