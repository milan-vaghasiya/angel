<?php
class SkillMasterModel extends MasterModel{
    private $skillMaster = "skill_master";
    private $staffSkill = "staff_skill"; 

    public function getDTRows($data){		
        $data['tableName'] = $this->skillMaster;
        $data['select'] = 'skill_master.*,department_master.name,emp_designation.title';
		$data['leftJoin']['department_master'] = "department_master.id = skill_master.dept_id";
		$data['leftJoin']['emp_designation'] = "emp_designation.id = skill_master.designation_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "skill_name";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "skill_master.req_skill";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getSkillList($data=array()){
        $queryData['tableName'] = $this->skillMaster;
        $queryData['select'] = 'skill_master.*,department_master.name,emp_designation.title';
		$queryData['leftJoin']['department_master'] = "department_master.id = skill_master.dept_id";
		$queryData['leftJoin']['emp_designation'] = "emp_designation.id = skill_master.designation_id";

        if(!empty($data['oldData'])){
            $queryData['select'] .=',staff_skill.m1,staff_skill.m2,staff_skill.m3,staff_skill.m4,staff_skill.m5,staff_skill.m6,staff_skill.m7,staff_skill.m8,staff_skill.m9,staff_skill.m10,staff_skill.m11,staff_skill.m12,staff_skill.id as trans_id,staff_skill.prev_skill';
		    $queryData['leftJoin']['staff_skill'] = "staff_skill.skill_id = skill_master.id AND staff_skill.emp_id = '".$data['emp_id']."'";
        }

        if(!empty($data['id'])){
            $queryData['where']['skill_master.id'] = $data['id'];
        }

        $queryData['group_by'][] ='skill_master.id';
        if(!empty($data['dept_id'])){$queryData['where']['skill_master.dept_id'] = $data['dept_id'];}
        if(!empty($data['designation_id'])){$queryData['where']['skill_master.designation_id'] = $data['designation_id'];}
      
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function save($data){
		try {
            $this->db->trans_begin();
            if($this->checkSkillDuplicate($data) > 0):
                $errorMessage['skill_name'] = "Skill Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
            $result = $this->store($this->skillMaster,$data,'Skill');
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        }catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }        
    }

    public function checkSkillDuplicate($data){
        $queryData['tableName'] = $this->skillMaster;
        $queryData['where']['skill_master.skill_name'] = $data['skill_name'];
        $queryData['where']['skill_master.dept_id'] = $data['dept_id'];
        $queryData['where']['skill_master.designation_id'] = $data['designation_id'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['skill_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Skill is currently in use. you cannot delete it.'];
            endif;
            $result = $this->trash($this->skillMaster,['id'=>$id],'Skill');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
   
	public function getStaffSkillData($data = array()){
		$queryData['tableName'] = $this->staffSkill;
        $queryData['select'] = "staff_skill.*, skill_master.skill_name,skill_master.req_skill,department_master.name,emp_designation.title,employee_master.emp_name, employee_master.emp_joining_date, employee_master.emp_contact,employee_master.emp_code";
		$queryData['leftJoin']['skill_master'] = "skill_master.id = staff_skill.skill_id";
		
		$queryData['leftJoin']['department_master'] = "department_master.id = skill_master.dept_id";
		$queryData['leftJoin']['emp_designation'] = "emp_designation.id = skill_master.designation_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = staff_skill.emp_id";

        if(!empty($data['emp_id'])){ $queryData['where']['staff_skill.emp_id'] = $data['emp_id']; }		
        if(!empty($data['skill_id'])){ $queryData['where']['staff_skill.skill_id'] = $data['skill_id']; }		

        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
	}
}
?>