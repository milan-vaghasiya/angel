
<?php
class ExpenseModel extends MasterModel
{
    private $expense_manager = "expense_manager";	

    /********** Expense **********/
        public function getNextExpNo(){
            $data['tableName'] = $this->expense_manager;
            $data['select'] = "MAX(exp_no) as exp_no";
            $data['where']['YEAR(exp_date)'] = date("Y");
            $data['where']['MONTH(exp_date)'] = date("m");
            $maxNo = $this->specificRow($data)->exp_no;
            $nextExpNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
            return $nextExpNo;
        }

        public function getExpenseDTRows($data){ 
            $data['tableName'] = $this->expense_manager; 
            $data['select'] = "expense_manager.id, expense_manager.exp_number, DATE_FORMAT(expense_manager.exp_date,'%d-%m-%Y') as exp_date,expense_manager.demand_amount, expense_manager.approved_by, expense_manager.notes, employee_master.emp_name, select_master.label as exp_name,expense_manager.amount,expense_manager.exp_by_id,expense_manager.exp_type_id,expense_manager.proof_file,expense_manager.status"; //12-01-25
            $data['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
            $data['leftJoin']['select_master'] = "select_master.id = expense_manager.exp_type_id AND select_master.type = 3";

            if(isset($data['status'])):
                $data['where']['expense_manager.status'] = $data['status'];
            endif;
            
            if(!empty($data['id'])):
                $data['where']['expense_manager.id'] = $data['id'];
            endif;
            
            if(!in_array($this->userRole,[1,-1])):
                $data['customWhere'][] = '(find_in_set("'.$this->empId.'", employee_master.super_auth_id) > 0 OR employee_master.user_id = '.$this->loginId.')';
            endif;
		
            if(!empty($data['emp_id'])):
                $data['where']['expense_manager.exp_by_id'] = $data['emp_id'];
            endif;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(exp_date,'%d-%m-%Y')";
            $data['searchCol'][] = "exp_number";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "select_master.label";
            $data['searchCol'][] = "demand_amount";
            $data['searchCol'][] = "amount";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getExpense($data){
            $queryData['tableName'] = $this->expense_manager;
            $queryData['select'] = "expense_manager.id,  expense_manager.exp_type, expense_manager.exp_number, DATE_FORMAT(expense_manager.exp_date,'%d-%m-%Y') as exp_date, expense_manager.demand_amount, expense_manager.approved_by, expense_manager.notes, employee_master.emp_name, select_master.label as exp_name,expense_manager.amount,expense_manager.exp_by_id,expense_manager.exp_type_id,expense_manager.proof_file,expense_manager.exp_prefix,expense_manager.exp_no";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
            $queryData['leftJoin']['select_master'] = "select_master.id = expense_manager.exp_type_id AND select_master.type = 3";

            $queryData['where']['expense_manager.id'] = $data['id'];
            return $this->row($queryData);
        }

        public function saveExpense($data){
            try{
                $this->db->trans_begin();

                if(empty($data['id'])):
                    $data['exp_prefix'] = "EXP".n2y(date('Y')).n2m(date('m'));  
                    $data['exp_no'] = $this->expense->getNextExpNo(); 
                    $data['exp_number'] = $data['exp_prefix'].sprintf("%03d",$data['exp_no']);
			    endif;

                $result = $this->store($this->expense_manager,$data,'Expense');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Expense **********/
}
?>