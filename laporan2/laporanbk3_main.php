<?php
function laporanbk3_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getLaporanbk3();
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

function getLaporanbk3(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'BUKU PAJAK KESELURUHAN', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal 1 Jan 2016 s/d 20 Jan 2016', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'DINAS SOSIAL, TENAGA KERJA DAN TRANSMIGRASI', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Kepala SKPD', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Drs. MOHAMMAD ZAHID, M.Pd', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Bendahara Pengeluaran', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'YAWANAH', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No. Urut', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian', 'width' => '220px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Pemotongan (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Penyetoran (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'S a l d o  (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Saldo sebelumnya', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '1', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '04-01-16', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Gaji PNS & CPNS Dinsosnakertrans Kab Jepara bulan Januari 2016 / PPh Pasal 21/0', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '1.969.764', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '2', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '04-01-16', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Penyetoran pajak sejumlah 1.969.764 (Gaji PNS Dinsosnakertrans Bulan Januari 2016)/PPh PAsal 21 )', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '1.969.764', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '3', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '15-01-16', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Tambahan Penghasilan PNS & CPNS Dinsosnakertrans Kab Jepara Desember 2015 / PPh Pasal 21 /Pajak', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '4.070.000', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '4', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '15-01-16', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Penyetoran pajak sejumlah 4.070.000 (Tambahan Penghasilan PNS & CPNS Dinsosnakertrans Kab Jepara bulan Desember 2015) / PPh Pasal 21 / setor)', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '4.070.000', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '300px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-weight:bold;'),
		
		
		array('data' => '6.039.764', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight:bold;'),
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
