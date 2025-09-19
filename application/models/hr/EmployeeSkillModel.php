<?php
class EmployeeSkillModel extends MasterModel
{
	private $staffSkill = "staff_skill";
    private $staff_skill_log = "staff_skill_log";

    public function getDTRows($data){
        $data['tableName'] = $this->staffSkill;       
        $data['select'] = "staff_skill.*,department_master.name as dept_name,emp_designation.title,employee_master.emp_name,employee_master.emp_code,staff_skill_log.month,staff_skill_log.approve_by,staff_skill_log.approve_at,approvedBy.emp_name as approved_name,createdBy.emp_name as created_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = staff_skill.emp_id";
        $data['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";
        $data['leftJoin']['staff_skill_log'] = "staff_skill.id = staff_skill_log.staff_skill_id";
        $data['leftJoin']['employee_master createdBy'] = "createdBy.id = staff_skill.created_by";
        $data['leftJoin']['employee_master approvedBy'] = "approvedBy.id = staff_skill_log.approve_by";

        $data['group_by'][] = "staff_skill.emp_id,employee_master.dept_id,employee_master.designation_id,staff_skill_log.month";

        if(empty($data['status'])):
			$data['where']['staff_skill_log.approve_by'] = 0;
		else:
			$data['where']['staff_skill_log.approve_by !='] = 0;
		endif; 

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "CONCAT(createdBy.emp_name,DATE_FORMAT(staff_skill.created_at,'%d-%m-%Y %H:%i:%s'))";
        $data['searchCol'][] = "CONCAT(approvedBy.emp_name,DATE_FORMAT(staff_skill_log.approve_at,'%d-%m-%Y %H:%i:%s'))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function save($data){
        try{ 
			$this->db->trans_begin();

            if(!empty($data['current_skill'])){
				$i = 0;  
                           
				foreach($data['current_skill'] as $row){
                    $cMonth = 'm' .(int)date('m', strtotime($data['month']));
                    $cdate = date('Y-m-01', strtotime($data['month']));
                    $staffSkillData = $this->getEmpSkillDetails(['emp_id'=>$data['emp_id'],'skill_id'=>$data['skill_id'][$i],'single_row'=>1]);
					if(isset($row)){

						$storeData = [
							'id' => $data['id'][$i],
							'skill_id' => $data['skill_id'][$i],
							'emp_id' => $data['emp_id'],
							'prev_skill' => $row,
							'prev_month' => $cMonth,
						];
						if(empty($data['id'][$i])){ 
                            $storeData['created_by'] = $this->loginId;
                            $storeData['created_at'] = date('Y-m-d H:i:s'); 
                        }
						else{ 
                            $storeData['updated_by'] = $this->loginId;
                            $storeData['updated_at'] = date('Y-m-d H:i:s'); 
                        } 
                        $storeData[$cMonth] = $row;
						$result = $this->store($this->staffSkill,$storeData,'Staff Skill');

                        if(!empty($result)) {
                        $skillLogData = $this->getStafffSkillLogData(['emp_id'=>$data['emp_id'],'staff_skill_id'=>$result['id'],'month'=>$cdate,'single_row'=>1]);
                            $logData = [
                                'id' => (!empty($skillLogData->id) ? $skillLogData->id : ''),
                                'staff_skill_id' => $result['id'], 
                                'emp_id' => $data['emp_id'],
                                'month' => $cdate
                            ]; 
                            $this->store($this->staff_skill_log, $logData, 'Emp Skill');
                        }

                        /* update prev data */
                        if(!empty($staffSkillData)){
                            $month = intval(substr($cMonth, 1)); 
                            $prev_month = intval(substr($staffSkillData->prev_month, 1));

                            for ($x = $prev_month; $x < $month; $x++) {
                                $month_key = 'm' . $x;

                                if (empty($staffSkillData->{$month_key})) {
                                    $updateData[$month_key] = $staffSkillData->prev_skill;
                                    $this->edit($this->staffSkill,['id'=>$staffSkillData->id],$updateData);
                                } 
                            }
                        }
                       
					}
					$i++;
				}
			}
            
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }              
    }

    public function getStafffSkillLogData($data){
        $queryData['tableName'] = $this->staff_skill_log; 

        if(!empty($data['month'])){
            $queryData['where']['staff_skill_log.month'] = $data['month'];
        }
        if(!empty($data['emp_id'])){
            $queryData['where']['staff_skill_log.emp_id'] = $data['emp_id'];
        } 
        if(!empty($data['staff_skill_id'])){
            $queryData['where']['staff_skill_log.staff_skill_id'] = $data['staff_skill_id'];
        } 
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
	
    public function getEmpSkillDetails($data=array()){
        $queryData['tableName'] = $this->staffSkill; 
        $queryData['select'] = "staff_skill.*,employee_master.emp_name,employee_master.emp_code,department_master.name as dept_name,emp_designation.title,skill_master.skill_name,skill_master.req_skill,skill_master.id as trans_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = staff_skill.emp_id";
        $queryData['leftJoin']['skill_master'] = "skill_master.id = staff_skill.skill_id";
        $queryData['leftJoin']['department_master'] = "employee_master.dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.designation_id = emp_designation.id";

        if(!empty($data['emp_id'])){$queryData['where']['staff_skill.emp_id'] = $data['emp_id'];} 
        if(!empty($data['skill_id'])){$queryData['where']['staff_skill.skill_id'] = $data['skill_id'];} 

        $queryData['where']['staff_skill.created_at >='] = $this->startYearDate;
        $queryData['where']['staff_skill.created_at <='] = $this->endYearDate;
        
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
	}

    public function approveEmpSkill($data){ 
        try{
            $this->db->trans_begin();

            $oldData = $this->getStafffSkillLogData(['month'=>$data['month'],'emp_id'=>$data['emp_id']]); 
            foreach($oldData as $row){
                $result = $this->edit($this->staff_skill_log, ['month'=> $row->month,'emp_id'=> $row->emp_id], ['approve_by' => $this->loginId, 'approve_at'=>date('Y-m-d H:i:s')]);
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

}
?>