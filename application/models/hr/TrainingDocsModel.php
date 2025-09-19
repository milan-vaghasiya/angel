<?php
class TrainingDocsModel extends MasterModel{
    private $training_docs = "training_docs";

    public function getDTRows($data){
        $data['tableName'] = $this->training_docs;
        $data['select'] = "training_docs.*,department_master.name,emp_designation.title";
        $data['leftJoin']['department_master'] = "department_master.id = training_docs.dept_id";
        $data['leftJoin']['emp_designation'] = "emp_designation.id = training_docs.designation_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "training_docs.description";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getTrainingDocs($data){
        $queryData['tableName'] = $this->training_docs;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->training_docs, $data, 'Training Docs');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function delete($data){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->training_docs, ['id' => $data['id']], 'Training Docs');

            if (!empty($data['doc_file'])) {
                $old_file_path = FCPATH."assets/uploads/training_docs/" . $data['doc_file'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
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