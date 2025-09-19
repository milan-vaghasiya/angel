<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Attendance extends MY_Controller{
    private $indexPage = "hr/attendance/index";
	
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "hr/attendance";
	}
	
	 /* Attendance Start */
    public function index(){
        $this->data['DT_TABLE'] = true;
		$this->data['headData']->pageTitle = "Attendance";
        $this->load->view($this->indexPage,$this->data);
    }

    public function getAttendanceData(){
        $data = $this->input->post();
        $result = $this->employee->getEmployeeList($data);
        $tbody = '';$i=1; 
        foreach($result as $row):
              
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$row->emp_code.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->department_name.'</td>
                    <td>'.$row->designation_name.'</td>
                    <td>'.$row->category.'</td>
                    <td>
                        <select class="form-control select2" name="attendance['.$i.'][type]">
                            <option value="A" '.((!empty($row->type) && $row->type == 'A')?'selected':'').'>Absent</option>
                            <option value="P" '.((!empty($row->type) && $row->type == 'P')?'selected':'').'>Present</option>
                            <option value="HD" '.((!empty($row->type) && $row->type == 'HD')?'selected':'').'>Half Day</option>
                            <option value="L" '.((!empty($row->type) && $row->type == 'L')?'selected':'').'>Leave</option>
                        </select>
                        <input type="hidden" name="attendance['.$i.'][id]" value="'.$row->log_id.'">
                        <input type="hidden" name="attendance['.$i.'][emp_id]" value="'.$row->id.'">
                    </td>
                </tr>';
                $i++;
            
        endforeach;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function saveAttendance(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['attendance']))
            $errorMessage['general_error'] = "Attendance Data is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->attendance->saveAttendance($data));
        endif;
    }

     public function uploadAttendance(){

        $this->load->view('hr/attendance/attendance_excel_form',$this->data);
    }

	public function createAttendanceExcel(){
		$data['is_active'] = 1;
        $empList = $this->employee->getEmployeeList($data);
		
        $tableColumns = ['id','emp_code', 'emp_name','department_name','designation_name','category','type'];
		
		$html = "<tr>
            <th>id</th>
            <th>emp_code</th>
            <th>emp_name</th>
            <th>department_name</th>
            <th>designation_name</th>
            <th>category</th>
            <th>type</th>";        
		$html.="</tr>"; 
        $i=1;
		if(!empty($empList)){
			foreach($empList as $row){
				$html .= '<tr>
                    <td>'.$row->id.'</td>
                    <td>'.$row->emp_code.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->department_name.'</td>
                    <td>'.$row->designation_name.'</td>
                    <td>'.$row->category.'</td>
                    <td>
                    </td>
                </tr>';
			}
		}
		
		$exlData = '<table>' . $html . '</table>'; 
		$spreadsheet = new Spreadsheet();
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		
        $spreadsheet = $reader->loadFromString($exlData);
        $excelSheet = $spreadsheet->getActiveSheet();
        $excelSheet = $excelSheet->setTitle('EmployeeAttendance');

		 $hcol = $excelSheet->getHighestColumn();
        $hrow = $excelSheet->getHighestRow();
        $packFullRange = 'A1:' . $hcol . $hrow;
        foreach (range('A', $hcol) as $col) :
            $excelSheet->getColumnDimension($col)->setAutoSize(true);
        endforeach;
        for ($i = 2; $i <= count($empList)+ 1; $i++) {            
    
            $objValidation2 = $excelSheet->getCell('G' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"Absent ,Present ,Half Day ,Leave"');
            $objValidation2->setShowDropDown(true);
         }

		$fileDirectory = realpath(APPPATH . '../assets/uploads/attendance');
        $fileName = '/attendance_' . time() . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/attendance') . $fileName);
	}

    public function importAttendanceExcel(){
        $postData = $this->input->post();

        if(empty($_FILES['attendance_excel']['tmp_name']) || $_FILES['attendance_excel']['tmp_name'] == null):
            $errorMessage['attendance_excel'] = "Please select attendance excel file.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $tableColumns = ['id','emp_code', 'emp_name','department_name','designation_name','category','type'];

            $xlCol = 'A';$tbColumn = [];// Define the columns you want to read
            foreach ($tableColumns as $tCol):                
                $columnsToRead[] = $xlCol;
                $tbColumn[$xlCol] = $tCol;
                $xlCol++;
            endforeach;
            $tableColumns = $tbColumn;

            // Path to the Excel file
            $filePath = $_FILES['attendance_excel']['tmp_name'];

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);

            // Get the active sheet (or specify a specific sheet if needed)
            $sheet = $spreadsheet->getSheetByName("EmployeeAttendance");            

            $attendanceData = [];
            // Loop through rows and columns
            foreach ($sheet->getRowIterator() as $row):
                $rowIndex = $row->getRowIndex(); // Get the row index (1, 2, 3, etc.)
                                
                if($rowIndex > 1):
                    foreach ($columnsToRead as $column): 
                        $cellValue = $sheet->getCell($column . $rowIndex)->getValue(); // Get the value of a specific column in the current row
                       
                        if($column == 'A' && empty($cellValue)): break; endif;
						
                        $attendanceData[$rowIndex][$tableColumns[$column]] = $cellValue;
                    endforeach;
                    
                endif;
            endforeach;

            $attendanceLog = [];$i=0; $empType="";
         
             //get employee list
            $empList = $this->employee->getEmployeeList(['is_active' =>1 ]);
            $groupedEmpList = array_reduce($empList, function($itemData, $empRow) {
                $itemData[$empRow->emp_code] = $empRow->id;
                return $itemData;
            }, []);

            $insertCount = $updateCount = 0; $notFoundEmp = [];
            foreach($attendanceData as $key=>$row):

                if(!empty($groupedEmpList[$row['emp_code']])):
                    $row['emp_id'] = $groupedEmpList[$row['emp_code']];
                    $row['id'] = "";
                    $row['type'] = trim($row['type']);
                    $row['attendance_date'] = $postData['attendance_date'];
                    if(!empty($row['type'])):

                        if($row['type'] == "Absent"){
                            $row['type'] = "A";
                        }elseif($row['type'] == "Present"){
                            $row['type'] = "P";
                        }elseif($row['type'] == "Half Day"){
                            $row['type'] = "HD";
                        }elseif($row['type'] == "Leave"){
                            $row['type'] = "L";
                        }

                        $this->attendance->save($row);
                        if(empty($row['id'])): 
                            $insertCount++; 
                        else: 
                            $updateCount++; 
                        endif;

                    endif;
                else:
                    $notFoundEmp[] = $row['emp_code'];
                endif;

            endforeach;
            $message = '<h4 class="text-success">Excel uploaded successfully.</h4>';
            $message .= '<span class="text-dark">New Records : '.$insertCount.'</span>';
            // $message .= '<br><span class="text-dark">Updated Records : '.$updateCount.'</span>';
            if(!empty($notFoundEmp)):
                $message .= '<br><span class="text-danger">Not Found Emp Codes : '.implode(", ",array_unique($notFoundEmp)).'</span>';
            endif;

            $this->printJson(['status'=>1,'message'=>$message]);
        endif;
    }

   
}
?>