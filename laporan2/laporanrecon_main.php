<?php
function laporanrecon_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getlaporanrecon();
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
function getlaporanrecon(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'REKONSILIASI PENDAPATAN DINAS PERTANIAN DAN PETERNAKAN', 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'TRIWULAN IV TAHUN ANGGARAN 2016', 'width' => '875px','align'=>'center','style'=>'border:none;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'NO. REK', 'rowspan'=>2, 'width' => '85px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'URAIAN', 'rowspan'=>2, 'width' => '320px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'ANGGARAN', 'rowspan'=>2, 'width' => '130px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Realisasi Menurut','width' => '230px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Selisih', 'rowspan'=>2,'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	$rows[]=array(
		array('data' => 'Masuk', 'width' => '115px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Berkurang', 'width' => '115px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
	);
	$rows[]=array(
			array('data' => '', 'width' => '85px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '320px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '','width' => '130px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '115px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '115px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '110px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			
		);
	for($n=1;$n<5;$n++){
		$rows[]=array(
			array('data' => $n, 'width' => '85px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => 'Blj Gaji Pokok PNS/Uang Representasi', 'width' => '320px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => 'Ro','width' => '115px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => 'Ro','width' => '115px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;'),
			
		);
	}
	$rows[]=array(
			array('data' => 'JUMLAH', 'width' => '405px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => '','width' => '115px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
			array('data' => '','width' => '115px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
			
		);
	
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Keterangan Selisih','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Pendapatan yang telah dicatat oleh PPKD','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Belum dicatat SKPD','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '100px', 'align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[] = array(
					array('data' => 'Pindahan yang telah dicatat oleh PPKD','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Belum dicatat PPKD','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '100px', 'align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '100px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Selisih','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '100px', 'align'=>'left','style'=>'border-bottom:1px solid black;border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	$rows[] = array(
				array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	$rows[] = array(
				array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	$rows[] = array(
				array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	$rows[] = array(
				array('data' => '','width' => '300px', 'align'=>'left','style'=>'border:none;'),
					
	);
	
	$rows[] = array(
					array('data' => 'KEPALA SEKSI PENAGIHAN DAN PELAPORAN PAJAK','width' => '430px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'BENDAHARA PENERIMA','width' => '430px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'BPKAD KAB. JEPARA','width' => '430px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'DISTANNAK KAB. JEPARA','width' => '430px', 'align'=>'center','style'=>'border:none;'),
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
					array('data' => 'S.KENDAR PRAPTOMO,SH.MM.','width' => '435px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => 'SUKINI, SH.','width' => '440px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => 'NIP. 19711216 199703 1 005','width' => '435px', 'align'=>'center','style'=>'border:none;'),
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
