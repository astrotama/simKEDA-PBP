<?php
function laporanbk6_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getLaporanbk6();
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

function getLaporanbk6(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'BUKU REKAPITULASI PENGELUARAN', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'PER RINCIAN OBYEK', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;'),
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
		array('data' => 'Kegiatan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Asistensi Keluarga Miskin(AKM) menunjang Kesejahteraan Sosial', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Tahun Anggaran', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '2016', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Bulan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '1 Jan 2016 s/d 20 Mar 2016', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '5 2 2 01 005', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Nama Rekening', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Belanja Bahan Bakar Minyak/Gas/Pelumas', 'width' => '380px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Kredit APBD', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Rp', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => '1.900.000', 'width' => '350px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No. Urut', 'width' => '20px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '50px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian', 'width' => '130px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Keterangan', 'width' => '100px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
		array('data' => 'Keterangan', 'width' => '200px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'LS', 'width' => '65px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'UP/GU', 'width' => '65px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TU', 'width' => '70px','align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
	);
	//Content
	$rows[]=array(
		array('data' => '1', 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '29-02-16', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Biaya BBM dlm rangka pelaksanaan Keg. Bimbingan Sosialisasi bagi KBS Asistensi Keluarga Miskin (AKM) menunjang Kesejahteraan Sosial di Kab. Jepara (Ds. Cepogo, Kancilan, Banjaran)', 'width' => '130px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Keterangan', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
		
		array('data' => '0', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '485.000', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '130px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
		array('data' => '', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah tanggal 1 Jan 2016 s/d 20 Mar 2016)', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		
		
		array('data' => '0', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '485.000', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah s/d tanggal 31 Des 2015', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		
		
		array('data' => '0', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '0', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah s/d tanggal 20 Mar 2016', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		
		
		array('data' => '0', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '485.000', 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah Total Pengeluaran', 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		
		array('data' => '485.00', 'width' => '200px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-right:1px solid black;'),
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
