
<?php
class TrainingModel extends MasterModel
{
    private $training = "training";	

    public function getDTRows($data){
        $data['tableName'] = $this->training;
        $data['select'] = "training.*,GROUP_CONCAT(DISTINCT skill_master.skill_name SEPARATOR ',') as skill_name";
        $data['leftJoin']['skill_master'] = "FIND_IN_SET(skill_master.id,training.skill_id) > 0";

        $data['where']['training.status'] = $data['status']; 
        $data['group_by'][] = "training.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(training.start_date,'%d-%m-%Y %H:%i:%s')";
        $data['searchCol'][] = "DATE_FORMAT(training.end_date,'%d-%m-%Y %H:%i:%s')";
        $data['searchCol'][] = "training.title";
        $data['searchCol'][] = "training.type";
        $data['searchCol'][] = "skill_master.skill_name";
        $data['searchCol'][] = "training.trainer_name";
    
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getTraining($data){
        $queryData['tableName'] = $this->training;
        $queryData['select'] = "training.*,GROUP_CONCAT(DISTINCT skill_master.skill_name SEPARATOR ',') as skill_name, COUNT(DISTINCT employee_master.id) as empCount";
        $queryData['leftJoin']['employee_master'] = "FIND_IN_SET(employee_master.id, training.emp_id) > 0";
        $queryData['leftJoin']['skill_master'] = "FIND_IN_SET(skill_master.id, training.skill_id) > 0";
        $queryData['where']['training.id'] = $data['id'];
        return $this->row($queryData);
    }

    public function save($data){ 
        try{
            $this->db->trans_begin();

            $result = $this->store($this->training, $data, 'Training');

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

            $result = $this->trash($this->training, ['id' => $id], 'Training');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changeTrainingStatus($data) {
        try{
            $this->db->trans_begin();

            $this->store($this->training, ['id'=> $data['id'], 'status' => $data['status']]);
            $result = ['status' => 1, 'message' => 'Training '.$data['msg'].' Successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getTrainingData($data){
        $queryData['tableName'] = $this->training;
        $queryData['select'] = "training.*,employee_master.id as emp_id,employee_master.emp_name as emp_name,employee_master.emp_code as emp_code,skill_master.skill_name,department_master.name,emp_designation.title";
        
        $queryData['leftJoin']['skill_master'] = "FIND_IN_SET(skill_master.id, training.skill_id) > 0";
        $queryData['leftJoin']['employee_master'] = "employee_master.dept_id = skill_master.dept_id AND employee_master.designation_id = skill_master.designation_id";
		$queryData['leftJoin']['department_master'] = "department_master.id = employee_master.dept_id";
		$queryData['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.designation_id";

        if(!empty($data['id'])){
            $queryData['where']['training.id'] = $data['id'];
        }
        if(!empty($data['skill_id'])){
            $queryData['where_in']['skill_master.id'] = $data['skill_id'];
        }
        $queryData['group_by'][] = "employee_master.id";
        return $this->rows($queryData);
    }
}
?>