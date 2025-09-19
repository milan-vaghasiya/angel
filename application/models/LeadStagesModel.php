<?php
class LeadStagesModel extends MasterModel{
    private $lead_stages = "lead_stages";

    public function getMaxStageSequence(){
        $queryData['tableName'] = $this->lead_stages;
        $queryData['select'] = "sequence as next_seq_no";
        $queryData['where']['stage_type'] = 'Lost';
        return $this->row($queryData);
    }

    public function getNextStage(){ 
        $queryData['tableName'] = $this->lead_stages;
        $queryData['select'] = "MAX(lead_stage) as max_lead_stage";
        $queryData['where']['is_system'] = 0;

        $max_lead_stage = $this->row($queryData)->max_lead_stage;
        return (!empty($max_lead_stage)?$max_lead_stage+1:21);
    }


    public function getDTRows($data){
        $data['tableName'] = $this->lead_stages;
        $data['order_by']['sequence'] = 'ASC';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "stage_type";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }


    public function getLeadStagesList($data=[]){
        $queryData['tableName'] = $this->lead_stages;

        if(!empty($data['stage_type'])) { $queryData['where']['stage_type'] = $data['stage_type']; }
        if(!empty($data['not_in'])) { $queryData['where_not_in']['id'] = $data['not_in']; }
        if(!empty($data['id'])) { $queryData['where']['id'] = $data['id']; }
        if(!empty($data['lead_stage'])) { $queryData['where']['lead_stage'] = $data['lead_stage']; }


        $queryData['order_by']['sequence'] ='ASC';

        if(!empty($data['single_row'])):
            $result = $this->row($queryData);
        else:
           $result =  $this->rows($queryData);
        endif;

        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
             $param['checkDuplicate'] = ['stage_type'];

            if(empty($data['id'])){
                $data['lead_stage'] = $this->getNextStage();
                $lostStagePosition = $this->getLeadStagesList(['stage_type'=>'Lost','single_row'=>'1']);
                $data['sequence'] = (!empty($lostStagePosition) ? $lostStagePosition->sequence : 1);
            }
        
            $result = $this->store($this->lead_stages, $data, 'Lead Stage');

            if(empty($data['id'])){
                $this->edit($this->lead_stages, ['stage_type'=>'Lost'], ['sequence'=>($data['sequence']+1)]);
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

    
    public function delete($id){
        try{
            $this->db->trans_begin();

            $stageData = $this->getLeadStagesList(['id'=>$id,'single_row'=>'1']);
            $result = $this->trash($this->lead_stages, ['id'=>$id], 'Lead Stage');

            $setData = array();
            $setData['tableName'] = $this->lead_stages;
            $setData['where']['sequence > '] = $stageData->sequence;
            $setData['set']['sequence'] = 'sequence, -1';
            $this->setValue($setData);

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