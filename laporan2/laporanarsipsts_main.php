<?php
function laporanarsipsts_main($arg=NULL, $nama=NULL) {
   $kodeuk=arg(1);
   $jenis=arg(2);
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
	
	
	$output = getLaporanarsip($kodeuk,$jenis);
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

function getLaporanarsip($kodeuk,$jenis){
	if($jenis==1){
		$jumlah='s.jumlahmasuk';
		$ket = 'Masuk';
	}
	else{
		$jumlah='s.jumlahkeluar';
	    $ket = 'Keluar';
	}
		
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat, '.$jumlah.' as jumlah,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk and '.$jumlah.'>0',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$total+=$data->jumlah;
	}
	$header=array();
	$rows[]=array(
		array('data' => 'Buku Kas', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => strtoupper($namauk), 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'Kode', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'Uraian', 'width' => '120px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'Detil', 'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => $ket.' (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'S a l d o  (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
	);
	
	
	$results=db_query('select u.namasingkat, s.tanggal,'.$jumlah.' as jumlah, s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk and '.$jumlah.'>0',array('kodeuk'=>$kodeuk));
	$saldo=0;
	$no=0;
	foreach ($results as $data) {
		$no++;
		$saldo+=$data->jumlah;
		$rows[]=array(
			array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => apbd_format_tanggal($data->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $data->kodero, 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $data->namarek, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $data->uraian, 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $data->jumlah, 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $saldo, 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
	}
	
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '440px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-weight:bold;font-size:80%'),
		
		
		array('data' => $saldo, 'width' => '70px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:2px solid black;font-weight:bold;border-right:1px solid black;font-size:80%'),
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
