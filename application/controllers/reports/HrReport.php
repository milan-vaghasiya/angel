<?php
class HrReport extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "HR Report";
        $this->data['headData']->controller = "reports/hrReport";
    }


    public function recruitmentAnalysis(){
		// $this->data['headData']->pageUrl = "reports/hrReport/recruitmentAnalysis";
        $this->data['headData']->pageTitle = "Recruitment Analysis";
        $this->load->view('reports/hr_report/recruitment_analysis',$this->data);
    }

	public function getRecruitmentData(){
        $data = $this->input->post();
		
        $result = $this->employee->getRecruitmentData($data);
        $tbody = ''; $i =1; $totalDays = 0;
		if(!empty($result)):
			foreach($result as $row):
				if($row->status == 1){
					$diffInSeconds = strtotime($row->appointed_ref_date) - strtotime($row->new_ref_date);
					$totalDays = ($diffInSeconds / (60 * 60 * 24));
				}else if($row->status == 7){
					$diffInSeconds = strtotime($row->rej_ref_date) - strtotime($row->new_ref_date);
					$totalDays = ($diffInSeconds / (60 * 60 * 24));
				}else{
					$diffInSeconds = strtotime(date("Y-m-d")) - strtotime($row->new_ref_date);
					$totalDays = ($diffInSeconds / (60 * 60 * 24));
				}
				
				if($row->new_ref_date == "" OR $totalDays < 0){$totalDays=0;}
				
				$tbody .= '<tr>
					<td>'.$i++.'</td>
					<td><b>'.$row->emp_name.'</b><br/>'.$row->dept_name.' - '.$row->dsg_title.'</td>
					<td>'.$row->emp_contact.'</td>
					<td>'.formatDate($row->new_ref_date).'</td>
					<td>'.formatDate($row->doc_ref_date).'</td>
					<td>'.formatDate($row->tech_ref_date).'</td>
					<td>'.formatDate($row->hr_ref_date).'</td>
					<td>'.formatDate($row->appointed_ref_date).'</td>
					<td>'.formatDate($row->rej_ref_date).'</td>
					<td>'.$totalDays.'</td>
				</tr>';
			endforeach;
		endif;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }


	public function monthlyAttendance(){
		$this->data['headData']->pageUrl = "reports/hrReport/monthlyAttendance";
        $this->data['headData']->pageTitle = "MONTHLY ATTENDANCE";
		$this->data['deptList'] = $this->department->getDepartmentList();
        $this->load->view('reports/hr_report/monthly_attendance',$this->data);
    }

	public function getMonthlyAttendance($jsonData=''){
        if(!empty($jsonData)){$data = (Array) decodeURL($jsonData);}
        else{$data = $this->input->post();}
		
		$data['is_active'] = 1;
        $empData = $this->employee->getEmployeeList($data);
        $lastDay = intVal(date('t',strtotime($data['month'])));
        
		$thead='<tr style="background:#dddddd;">
			<th style="width:50px;">#</th>
			<th style="width:50px;">Code</th>
			<th style="">Emp Name</th>
			<th>Department</th>
			<th>Designation</th>
			<th>Category</th>
			';
        
		for($d=1;$d<=$lastDay;$d++):	
            $thead.='<th class="text-center">'.$d.'</th>'; 
        endfor;
        
        $thead.='<th class="text-center">Week Of</th>';
        $thead.='<th class="text-center">Present <br> Days</th>';
        $thead.='<th class="text-center">Half Day</th>';
        $thead.='<th class="text-center">Leave</th>';
        $thead.='<th class="text-center">Absent <br> Days</th>';    
        $thead.='<th class="text-center">Total <br> Days</th>';
        $thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);

        $tbody='';$i=1;$lCount = 0;
        foreach($empData as $emp):
            $tbody.='<tr>';
            $tbody.='<td class="text-center" style="vertical-align:middle;font-size:12px;" >'.$i++.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->emp_code.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->emp_name.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->department_name.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->designation_name.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->category.'</td>';
            
            $totalDays = date("t",strtotime($data['month'])); 
            $holiday = countDayInMonth("Wednesday",$data['month']);
            $totalDays -= $holiday; 
            $presentDays = 0;$absentDays = 0; $halfDays = 0;$leaveDays = 0;$weekOff =0;$wp = 0;
			
            for($d=1;$d<=$lastDay;$d++):
                
                $a_day=1; $p_day=0; $h_day=0; $l_day=0; $text="A"; $class="bg-danger text-white";
				$dt = str_pad($d, 2, '0', STR_PAD_LEFT);

				$currentDate = date('Y-m-'.$dt,strtotime($data['month']));				
				$dayName = date("D", strtotime($currentDate));

				$logData = $this->attendance->getMonthlyAttendanceData(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);

				// foreach($logData as $log){
				if(!empty($logData)){
					if($logData->type == "P"){
						$p_day = 1;
						$text = "P";
						$class = "bg-success ";
					}elseif($logData->type == "A"){
						$a_day = 1;
						$text = "A";
						$class = "bg-danger text-white";
					}elseif($logData->type == "HD"){
						$h_day = 0.5;
						$text = "HD";
						$class = "bg-warning";
					}elseif($logData->type == "L"){
						$l_day = 1;
						$text = "L";
						$class = "bg-dark text-white";
					}
				}
						
                
				if(date("D",strtotime(date($d."-m-Y",strtotime($data['month'])))) == "Wed"){
					if($text == "A"){$text = "W";$class = "bg-gray";}
                    if($text == "P"){$text = "WP";$wp++;$class = "bg-success";}
                    if($text == "HD"){$text = "W-HD";$wp++;$class = "bg-warning";}
                    if($text == "L"){$text = "L-W";$wp++; $class = "bg-dark text-white";}
                    $weekOff ++;
                    $day = 0;
                }
					
				$tbody .= '<td class="text-center '.$class.'" style="font-size:12px;">'.$text.'</td>';
				
                $presentDays += $p_day;
                $absentDays += $a_day;
                $leaveDays += $l_day;
                $halfDays += $h_day;
            endfor;
            $absentDays = (($totalDays - $presentDays -$leaveDays - $halfDays ) > 0)?($totalDays - $presentDays - $leaveDays - $halfDays):0;
			
            
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$weekOff.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$presentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$halfDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$leaveDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$absentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$totalDays.'</td>'; 
            $tbody .= '</tr>';
			
        endforeach;
        $reportTitle = 'Attendance Report';
        $report_date = $data['month'].' to '.date('t-m-Y',strtotime($data['month']));
		$logo = base_url('assets/images/logo.png');

        $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1">
                        <thead class="thead-info" id="theadData">'.$thead.'</thead>
                        <tbody id="tbodyData">'.$tbody.'</tbody>
                    </table>';

                
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';
					
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

        if(!empty($data['file_type']) && $data['file_type'] == 'PDF')
        {
            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,15,10,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            $mpdf->Output($pdfFileName, 'I');
        }elseif(!empty($data['file_type']) && $data['file_type'] == 'excel'){
            $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1" border="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="tbodyData">'.$tbody.'</tbody>
                        </table>';
            $xls_filename='AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.xls';        
										
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$xls_filename);
			header('Pragma: no-cache');
			header('Expires: 0');
	
			echo $pdfData; exit;
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}


	public function skillMatrix(){
        $this->data['headData']->pageTitle = 'SKILL MATRIX REPORT';
        $this->data['headData']->pageUrl = "reports/hrReport/skillMatrix";
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/hr_report/skill_matrix",$this->data);
    }

	public function getSkillMatrixData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        } else {
            $data = $this->input->post();
        }
        $thead = '<tr class="bg-light">
                    <th style="width:5%">Sr.</th>
                    <th style="width:15%">Employee Name</th>
                    <th style="width:10%">Employee Code</th>
                    <th style="width:10%">Department</th>
                    <th style="width:15%" >Designation</th>
                    <th style="width:15%" class="text-center">Date Of Joining</th>
                    <th style="width:15%" class="text-center">Phone</th>
                </tr>';

        $skillHeader = '<tr class="bg-light">
                            <th>#</th>
                            <th colspan="2">Skill Name</th>
                            <th class="text-center">Req. Skill (%)</th>
                            <th class="text-center">Current Skill(%)</th>
                            <th class="text-center">Result</th>
                            <th class="text-center">PR(%)</th>
                        </tr>';

        $skillData = $this->skillMaster->getStaffSkillData($data);
        
        $skillTable = '';$i = 1; $j=1;
        $empArray = array_reduce($skillData, function($emp, $employee) {
                    $emp[$employee->emp_name][] = $employee;
                    return $emp;
                }, []);

        if (!empty($empArray)) {
            foreach ($empArray as $emp_name => $row) {
                
            $skillTable .= '<table class="table table-bordered item-list-bb" style="margin-bottom: 20px;">
                                <thead >'.$thead.'</thead>
                                <tbody>';
                                        
            $skillTable .= '<tr>
                                <td>'.$i++.'</td>
                                <td class="text-left">'.$emp_name.'</td>
                                <td class="text-left">'.$row[0]->emp_code.'</td>
                                <td class="text-left">'.$row[0]->name.'</td>
                                <td class="text-left">'.$row[0]->title.'</td>
                                <td class="text-center">'.formatDate($row[0]->emp_joining_date).'</td>
                                <td class="text-center">'.$row[0]->emp_contact.'</td>
                            </tr>';

            $skillTable .= '<tr><td colspan="7"><b>Skills for '.$emp_name.':</b></td></tr>';

            $skillTable .= '<thead >'.$skillHeader.'</thead><tbody>';
                if (!empty($row)){
                    foreach ($row as $skill) {
                        if ($skill->req_skill > 0) {
                            $prPercentage = ($skill->prev_skill / $skill->req_skill) * 100;
                        } else {
                            $prPercentage = 0;
                        }
                        if ($prPercentage <= 70) {
                            $result = 'Needs Training';
                        } elseif ($prPercentage <= 80) {
                            $result = 'Average';
                        } elseif ($prPercentage <= 90) {
                            $result = 'Good';
                        } elseif($prPercentage >= 90) {
                            $result = 'Excellent';
                        }  
                
                $skillTable .= '<tr>
                            <td>'.$j++.'</td>
                            <td colspan="2">'.$skill->skill_name.'</td>
                            <td class="text-center">'.$skill->req_skill.'</td>
                            <td class="text-center">'.$skill->prev_skill.'</td>
                            <td class="text-center">'.$result.'</td> 
                            <td class="text-center">'.number_format($prPercentage, 2).'</td> 
                        </tr>';
                    }
                }else{
                    $skillTable .= '<tr><td colspan="7" class="text-center">No Data Available</td></tr>';
                }
                $skillTable .= '</tbody></table>';
            }
        }

        if(!empty($data['is_pdf'])){
            $htmlData ="";
            $logo = base_url('assets/images/logo.png');
            $htmlData .= '<table class="table">
                                <tr>
                                    <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">SKILL MATRIX REPORT</td>
                                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">DATE : '.date('d-m-Y').'</td>
                                </tr>
                            </table>';
                        $htmlData .= $skillTable;
                
                $pdfData= $this->generatePDF($htmlData,'L');
            }
        $this->printJson(['status' => 1, 'tblData' => $skillTable]);
    }
    
	
    public function taskReport(){
        $this->data['headData']->pageTitle = "Task Report";
		$this->data['groupList'] = $this->taskManager->getGroupList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->load->view('reports/hr_report/task_report',$this->data);
    }

    public function getTaskManager(){
        $data = $this->input->post();
        $customWhere = "DATE(task_master.due_date) BETWEEN '".date("Y-m-d",strtotime($data['from_date']))."' AND '".date("Y-m-d",strtotime($data['to_date']))."'";
        $postData = [
			'assign_to'=>$data['assign_to'],
			'group_id'=>$data['group_id'],
			'status'=>$data['status'],
			'created_by'=>$data['created_by'],
			'customWhere'=>$customWhere,
		]; 
       	$taskData = $this->taskManager->getTaskList($postData); 
        $i=1; $tbody=''; 
        if(!empty($taskData)){
            foreach($taskData as $row):
                $status = ($row->status == 1)?'Pending':(($row->status == 2)?'Completed':'Cancelled');
                $group_name = ($row->group_id == 0) ? 'Individual' : $row->group_name; 

                $due_days = '';
				if(!empty($row->due_date) AND !empty($row->complete_on)){
					$due_date = new DateTime($row->due_date);
					$complete_on = new DateTime($row->complete_on);
					$due_days = $due_date->diff($complete_on)->format("%r%a");
				}

                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->task_number.'</td>
                    <td>'.$group_name.'</td>
                    <td>'.$row->assign_name.'</td>
                    <td>'.$row->task_title.'</td>
                    <td>'.$row->notes.'</td>
                    <td>'.$row->repeat_type.'</td>
                    <td>'.formatDate($row->due_date).'</td>
                    <td>'.formatDate($row->complete_on).'</td>
                    <td>'.floatVal($due_days).'</td>
                    <td>'.$status.'</td>
                    <td>'.$row->assign_by_name.'</td>
                </tr>';
            endforeach;
        } 
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }


	public function empPerfomance(){
        $this->data['headData']->pageTitle = 'Employee Perfomance Report';
        $this->data['headData']->pageUrl = "reports/hrReport/empPerfomance";
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['monthList'] = $this->getMonthListFY();
        $this->load->view("reports/hr_report/emp_perfomance",$this->data);
    }

	public function getEmpPerfomanceData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $perfomanceData = $this->empPerfomance->getEmpPerfomanceDetails($data);
        $monthList = $this->getMonthListFY();

        $i=1; $tbody="";
        if(!empty($perfomanceData)):
            $kpiList = array_reduce($perfomanceData, function($kpiList, $kpi) { $kpiList[$kpi->kpi_name][] = $kpi; return $kpiList; }, []);
            foreach($kpiList as $kpi_name=>$rows):
                $tbody .= '<tr><td colspan="19" style="font-size:15px;"><b>'.$kpi_name.'</b></td></tr>';
                if(!empty($rows)){
                    foreach ($rows as $row) {
                        $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->emp_code.'</td>
                                <td>'.$row->emp_name.'</td>
                                <td>'.$row->dept_name.'</td>
                                <td>'.$row->title.'</td>
                                <td>'.$row->kpi_desc.'</td>
                                <td class="text-center">'.$row->req_per.'</td>
                                <td class="text-center">'.$row->m4.'</td>
                                <td class="text-center">'.$row->m5.'</td>
                                <td class="text-center">'.$row->m6.'</td>
                                <td class="text-center">'.$row->m7.'</td>
                                <td class="text-center">'.$row->m8.'</td>
                                <td class="text-center">'.$row->m9.'</td>
                                <td class="text-center">'.$row->m10.'</td>
                                <td class="text-center">'.$row->m11.'</td>
                                <td class="text-center">'.$row->m12.'</td>
                                <td class="text-center">'.$row->m1.'</td>
                                <td class="text-center">'.$row->m2.'</td>
                                <td class="text-center">'.$row->m3.'</td>
                            </tr>';
                        }
                }
            endforeach;
        else:
           $tbody .= '<tr><td colspan="19" class="text-center">No Data Available</td></tr>';
        endif;
        if(!empty($data['is_pdf'])){
            $htmlData = ''; 
            $logo = base_url('assets/images/logo.png');
            $htmlData .= '<table class="table">
                                <tr>
                                    <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">Employee Perfomance REPORT</td>
                                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">DATE : '.date('d-m-Y').'</td>
                                </tr>
                            </table>';
            
            $htmlData .= '<table class="table table-bordered">
                            <thead id="theadData">
                                <tr class="bg-light">
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Emp. Code</th>
                                    <th rowspan="2">Emp. Name</th>
                                    <th rowspan="2">Department</th>
                                    <th rowspan="2">Designation</th>
                                    <th rowspan="2">KPI</th>
                                    <th rowspan="2">Weightage</th>
                                    <th colspan="12" class="text-center">Achieved</th>
                                </tr>';
                                
                                $htmlData .= '<tr class="bg-light">';
                                    foreach($monthList as $row){ 
                                    $htmlData .= '<th class="text-left">'.str_replace('-','<br>',$row['label']).'</th>';
                                    } 
                                $htmlData .= '</tr>';
                            $htmlData .= '</thead>';
                        $htmlData .= '<tbody> '.$tbody.'</tbody>';
                        $htmlData .= '</table>';
            $pdfData= $this->generatePDF($htmlData,'L');
        }
        $this->printJson(['status' => 1, 'tbodyData' => $tbody]);
    }
}
?>