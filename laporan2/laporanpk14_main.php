<?php
function laporanpfk14_main.php($arg=NULL, $nama=NULL) {
   $kodeuk=arg(1);
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
	
	
	$output = getlaporan($kodeuk);
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

function getlaporan($kodeuk){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat, s.jumlah,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$total+=$data->jumlah;
	}
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'DINAS KESEHATAN KABUPATEN', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT TANDA SETORAN', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'STS No.', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => '2.01.01.000.000', 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => 'Harap diterima uang', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => 'Retribusi', 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => '(dengan huruf)', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => 'Retribusi', 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => 'Penerimaan Tgl.', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => '2017', 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'Kode REkening', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => 'Uraian Rincian Obyek', 'width' => '220px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'Jumlah', 'width' => '150px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
		
	);

	
	$results=db_query('select u.namasingkat, s.tanggal,s.jumlah,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$saldo=0;
	$saldop=0;
	$no=0;
	foreach ($results as $data) {
		$no++;
		$saldo+=$data->jumlah;
		$saldop+=$data->jumlah;
		$rows[]=array(
			array('data' => $no, 'width' => '140px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => $data->jumlah, 'width' => '220px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $saldo, 'width' => '150px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
	}
	$rows[]=array(
			array('data' => 'TOTAL', 'width' => '360px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => $saldop, 'width' => '150px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
		);
	
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ..............','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Mengesahkan','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'NIP.','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
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
