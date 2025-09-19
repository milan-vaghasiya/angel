<?php
class SelectOptionModel extends MasterModel{
    private $select_master = "select_master";

    public function getDTRows($data){
        $data['tableName'] = $this->select_master;
        $data['where']['type'] = $data['type'];
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "label";
        $data['searchCol'][] = "remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getSelectOption($data){
        $queryData['tableName'] = $this->select_master;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function getSelectOptionList($data){
        $queryData['tableName'] = $this->select_master;
        $queryData['where']['type'] = $data['type'];
        return $this->rows($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->select_master,$data,'Select Option');

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

            $result = $this->trash($this->select_master, ['id' => $id], 'Select Option');

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