<?php
function laporanregister_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 140px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	$output = getlaporanregister();
	//$output2= footer();
	apbd_ExportPDF('L', 'F4', $output, 'CEK');
	//print_pdf_p($output,$output2);
	
	
}



/**
 * Selects just the second dropdown to be returned for re-rendering.
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return array
 *   Renderable array (the second dropdown)
 */
function getlaporanregister(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER    SP2D', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN 2017', 'width' => '875px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'No. Urut',  'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tanggal',  'width' => '105px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'No SP2D',  'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'UP', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'GU', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'TU', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'GAJI', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'LS', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian', 'width' => '280px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	
	for($n=1;$n<5;$n++){
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '04-01-16', 'width' => '105px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Blj Gaji Pokok PNS/Uang Representasi', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Ro','width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Ro','width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Ro','width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Ro','width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Ro','width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '280px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '', 'width' => '140px','align'=>'center','style'=>'border-right:1px solid black;'),
			
		);
	}
	$rows[]=array(
		array('data' => '', 'width' => '315px','align'=>'center','style'=>'border-top:1px solid black;'),
		
		
		array('data' => '195.226.926', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '195.226.926', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '195.226.926', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah pada tanggal 1 Jan 2016 s/d 5 Jan 2016', 'width' => '315px','align'=>'right','style'=>'border-right:1px solid black;'),
		
		
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '195.226.926', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah s/d tanggal 31 Des 2015', 'width' => '315px','align'=>'right','style'=>'border-right:1px solid black;'),
		
		
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '195.226.926', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah kumulatif s/d tanggal 5 Jan 2016', 'width' => '315px','align'=>'right','style'=>'border-right:1px solid black;'),
		
		
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '195.226.926', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Sisa Kas', 'width' => '315px','align'=>'right','style'=>'border-right:1px solid black;'),
		
		
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Total Sisa Kas', 'width' => '315px','align'=>'right','style'=>'border-right:1px solid black;font-weight:bold;'),
		
		
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '0', 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, 5 Januari 2016','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Mengesahkan','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'NIP.','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP.','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}


/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */



?>
