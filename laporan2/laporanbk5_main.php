<?php
function laporanbk5_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getLaporanbk5();
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

function getLaporanbk5(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'KARTU KENDALI KEGIATAN', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal 1 Januari 2016 s/d 20 Januari 2016', 'width' => '875px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'DINAS SOSIAL, TENAGA KERJA DAN TRANSMIGRASI', 'width' => '755px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Nama Kegiatan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Asistensi Keluarga Miskin(AKM) menunjang Kesejahteraan Sosial', 'width' => '755px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Nama PPTK', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Isikan nama PPTK disini. Bila tidak ingin selalu memasukkan PPTK secara manual, isikan PPTK Kegiatan melalui menu Master -> PPTK', 'width' => '755px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => 'No. Urut', 'rowspan'=>2, 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Kode Rekening', 'rowspan'=>2, 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian', 'rowspan'=>2, 'width' => '255px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Pagu Anggaran (Rp)', 'rowspan'=>2,'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Realisasi Sebelumnya (Rp)', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Realisasi Kegiatan (Rp)', 'width' => '270px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Sisa Pagu Anggaran (Rp)', 'rowspan'=>2, 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	$rows[]=array(
		array('data' => 'UP/GU', 'width' => '90px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TU', 'width' => '90px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'LS', 'width' => '90px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
	);
	for($n=1;$n<5;$n++){
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '04-01-16', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => 'Blj Gaji Pokok PNS/Uang Representasi', 'width' => '255px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => 'Ro','width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '151.893.440', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '0', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			
		);
	}
	$rows[]=array(
			array('data' => 'TOTAL', 'width' => '385px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
			array('data' => '35.000.000','width' => '70px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;'),
			array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;'),
			array('data' => '0', 'width' => '90px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;'),
			array('data' => '0', 'width' => '90px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;'),
			array('data' => '0', 'width' => '90px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;'),
			array('data' => '35.000.000', 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;'),
			
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ............','width' => '440px', 'align'=>'center','style'=>'border:none;'),
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
