<?php
function importexcel_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('importexcel_edit_main_form');
	return drupal_render($output_form);//.$output;
	
	
}





function importexcel_edit_main_form($form, &$form_state) {
	

	
	$form['formdata']['uraian'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Uraian'),
		'#default_values' => null,
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#markup' => '<p>tanggal</p>',
	);

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' =>'Simpan',
		//'#suffix' =>,
	);

	
	
	
	return $form;
}


function importexcel_edit_main_form_submit($form, &$form_state) {

	//text_excel();
	test_excel2();
}

function text_excel() {
require_once ('files/Excel/reader.php');


// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();


// Set output Encoding.
$data->setOutputEncoding('CP1251');

$data->read('files/book.xls');

error_reporting(E_ALL ^ E_NOTICE);

for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
	}
	echo "\n";

}	
}

function test_excel2() {
//include 'excel_reader.php';     // include the class

require_once ('files/excel_reader/excel_reader.php');

// creates an object instance of the class, and read the excel file data
$excel = new PhpExcelReader;
$excel->read('files/excel_reader/test.xls');

// this function creates and returns a HTML table with excel rows and columns data
// Parameter - array with excel worksheet data
function sheetData($sheet) {
  $re = '<table>';     // starts html table

  $x = 1;
  while($x <= $sheet['numRows']) {
    $re .= "<tr>\n";
    $y = 1;
    while($y <= $sheet['numCols']) {
      $cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
      $re .= " <td>$cell</td>\n";  
      $y++;
    }  
    $re .= "</tr>\n";
    $x++;
  }

  return $re .'</table>';     // ends and returns the html table
}

$nr_sheets = count($excel->sheets);       // gets the number of worksheets
$excel_data = '';              // to store the the html tables with data of each sheet

// traverses the number of sheets and sets html table with each sheet data in $excel_data
for($i=0; $i<$nr_sheets; $i++) {
  $excel_data .= '<h4>Sheet '. ($i + 1) .' (<em>'. $excel->boundsheets[$i]['name'] .'</em>)</h4>'. sheetData($excel->sheets[$i]) .'<br/>';  
}

echo $excel_data;      // outputs HTML tables with excel file data	
}

?>
