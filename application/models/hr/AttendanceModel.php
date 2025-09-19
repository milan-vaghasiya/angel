<?php
class AttendanceModel extends MasterModel{
    private $attendance_log = "attendance_log";
    private $employee_master = "employee_master";
	
   
	/********** Attendance **********/
	public function getMonthlyAttendanceData($param = []){
        $data['tableName'] = $this->attendance_log;
        $data['select'] = "attendance_log.*,employee_master.emp_name,employee_master.emp_code,department_master.name,emp_designation.title,emp_category.category,attendance_log.attendance_date,attendance_log.type";
        
        $data['leftJoin']['employee_master'] = "attendance_log.emp_id =  employee_master.id AND employee_master.is_active = 1";
        $data['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";
        $data['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        if(!empty($param['emp_dept_id'])){
            $data['where']['attendance_log.dept_id'] = $param['emp_dept_id'];
        }
        if(!empty($param['emp_id'])){
            $data['where']['attendance_log.emp_id'] = $param['emp_id'];
        }
        if(!empty($param['from_date'])){$data['where']['DATE(attendance_log.attendance_date) >= '] = $param['from_date'];}
			
        if(!empty($param['to_date'])){$data['where']['DATE(attendance_log.attendance_date) <= '] = $param['to_date'];}
		$data['order_by']['attendance_log.attendance_date'] = 'ASC';
			
        $result = $this->row($data);
        return $result;
    }
	
    public function saveAttendance($data){  
        try{
            $this->db->trans_begin();

            foreach($data['attendance'] as $row):
                if(!empty($row['type'])){
                    $row['attendance_date'] = $data['attendance_date'];
                    $result = $this->store('attendance_log',$row,'Attendance');
                }
            endforeach;
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function save($data){ 
        try{
            $this->db->trans_begin();
            unset($data['emp_code'],$data['emp_name'],$data['department_name'],$data['designation_name'],$data['category']);
            $logData = $this->getEmpAttendanceLog(['emp_id'=>$data['emp_id'],'attendance_date'=>$data['attendance_date']]);
            if(!empty($logData->id)):
                $data['id'] = $logData->id;
            endif;

            $result = $this->store('attendance_log',$data,'Attendance Log');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getEmpAttendanceLog($data){
        $queryData = [];
        $queryData['tableName'] = 'attendance_log';

        $queryData['where']['attendance_log.emp_id'] = $data['emp_id'];
        $queryData['where']['DATE(attendance_log.attendance_date) '] = $data['attendance_date'];

        $result = $this->row($queryData);
        return $result;
    }
    /* Attendance End */
}
?>