<?php
function laporanbukubesar_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getlaporanbukubesar();
	//$output2= footer();
	apbd_ExportPDF('P', 'F4', $output, 'CEK');
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

function getlaporanbukubesar(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'BUKU BESAR', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'Kode', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Uraian', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Anggaran', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Uraian', 'width' => '290px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'S a l d o  (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	$rows[]=array(
		array('data' => '04-01-16', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Gaji PNS & CPNS Dinsosnakertrans Kab Jepara bulan Januari 2016 / PPh Pasal 21/0', 'width' => '290px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '04-01-16', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Gaji PNS & CPNS Dinsosnakertrans Kab Jepara bulan Januari 2016 / PPh Pasal 21/0', 'width' => '290px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '04-01-16', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Gaji PNS & CPNS Dinsosnakertrans Kab Jepara bulan Januari 2016 / PPh Pasal 21/0', 'width' => '290px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '370px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-weight:bold;'),
		
		
		array('data' => '6.039.764', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight:bold;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-bottom:2px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ..............','width' => '250px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Mengesahkan','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
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
					array('data' => 'NIP.','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP.','width' => '250px', 'align'=>'center','style'=>'border:none;'),
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
