
<?php
class MeetingModel extends MasterModel
{
    private $meeting = "meeting";	

    public function getDTRows($data){
        $data['tableName'] = $this->meeting;
		$data['select'] = "meeting.*,";
		$data['where']['meeting.status'] = $data['status']; 
		$data['group_by'][] = 'meeting.id';
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(meeting.me_date,'%d-%m-%Y %H:%i:%s')";
        $data['searchCol'][] = "meeting.duration";
        $data['searchCol'][] = "meeting.title";
        $data['searchCol'][] = "meeting.location";
        $data['searchCol'][] = "meeting.host_by";
        $data['searchCol'][] = "meeting.guest";
        $data['searchCol'][] = "meeting.key_contact";
    
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMeeting($data){
        $queryData['tableName'] = $this->meeting;
        $queryData['select'] = "meeting.*,COUNT(employee_master.id) as empCount";
        $queryData['leftJoin']['employee_master'] = "FIND_IN_SET(employee_master.id, meeting.emp_id) > 0";
        $queryData['where']['meeting.id'] = $data['id'];
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->meeting, $data, 'Meeting');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->meeting, ['id' => $id], 'Meeting');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changeMeetStatus($data) {
        try{
            $this->db->trans_begin();

            $this->store($this->meeting, ['id'=> $data['id'], 'status' => $data['status']]);
            $result = ['status' => 1, 'message' => 'Meeting '.$data['msg'].' Successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getMeetingData($data){
        $queryData['tableName'] = $this->meeting;
        $queryData['select'] = "employee_master.id as emp_id,employee_master.emp_name,employee_master.emp_code,department_master.name,emp_designation.title";
        $queryData['leftJoin']['employee_master'] = "FIND_IN_SET(employee_master.id, meeting.emp_id) > 0";
		$queryData['leftJoin']['department_master'] = "department_master.id = employee_master.dept_id";
		$queryData['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.designation_id";
        $queryData['where']['meeting.id'] = $data['id'];
        $queryData['group_by'][] = "employee_master.id";
        return $this->rows($queryData);
    }
}
?>