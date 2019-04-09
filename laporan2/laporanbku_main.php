<?php
function laporanbku_main($arg=NULL, $nama=NULL) {
   $kodeuk=arg(1);
   $bulan=arg(2);
   if(arg(3)!=null)
	   $margin=arg(3);  
   else
	  $margin=10; 
   
   
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = 2019;		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	$output = getlaporanbku($kodeuk,$bulan, 1);
	//$output2 footer();
	//apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
	//print_pdf_p($output,$output2);
	return $output;
	
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

function getlaporanbku($kodeuk,$bulan, $index){
	ini_set('memory_limit', '1024M');
	$tanggal=arg(4);
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$bulantext=array(
	array('Januari',31),
	array('Februari',28),
	array('Maret',31),
	array('April',30),
	array('Mei',31),
	array('Juni',30),
	array('Juli',31),
	array('Agustus',31),
	array('September',30),
	array('Oktober',31),
	array('November',30),
	array('Desember',31)
	);
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat,u.pimpinannama ,u.pimpinannip, u.bendaharanama ,u.bendaharanip from  unitkerja as u where u.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		//$total+=$data->jumlah;
	}
	/*$results=db_query('select u.namasingkat,u.pimpinannama ,u.pimpinannip, u.bendaharanama ,u.bendaharanip, s.jumlahmasuk, s.jumlahkeluar ,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		//$total+=$data->jumlah;
	}*/
	$header=array();
	$rows[]=array(
		array('data' => 'Buku Kas', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'BUKU KAS UMUM', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$rows=null;
	$header=null;
	$header=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian', 'width' => '180px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Kode Rekening', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Penerimaan (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Pengeluaran  (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	//if ($index=='1') {
		$tglawal = '2019-08-01';
		$tglakhir = '2019-08-11';
	/*} elseif ($index=='1') {
		$tglawal = '2018-' . $bulan . '-11';
		$tglakhir = '2018-' . $bulan . '-21';
	} else {
		$tglawal = '2018-' . $bulan . '-21';
		$tglakhir = '2018-' . $bulan+1 . '-01';		
	}*/
	
	//$results=db_query('select u.namasingkat, s.tanggal,s.jumlahmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk  and MONTH(s.tanggal) =:bulan order by s.tanggal,s.jumlahkeluar ',array('kodeuk'=>$kodeuk,':bulan'=>$bulan+1));
	
	$results=db_query('select u.namasingkat, s.tanggal,s.jumlahmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk  and s.tanggal=:tglawal  order by s.tanggal,s.jumlahkeluar ',array('kodeuk'=>$kodeuk,':tglawal'=>$tglawal,':tglakhir'=>$tglakhir));
	$saldo=0;
	$saldop=0;
	$no=0;
	foreach ($results as $data) {
		$no++;
		$saldo+=$data->jumlahmasuk;
		$saldop+=$data->jumlahkeluar;
		$rows[]=array(
			array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => apbd_format_tanggal($data->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $data->namarek, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => $data->kodero, 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($data->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($data->jumlahkeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
	}
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => 'Jumlah', 'width' => '180px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => apbd_fn($saldo), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => apbd_fn($saldop), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
		);
	
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$results=db_query("select sum(jumlahmasuk) as jumlahm, sum(jumlahkeluar) as jumlahk from setor where month(tanggal)<:bulan and year(tanggal)='2019' and kodeuk=:kodeuk",array(':bulan'=>$bulan+1,'kodeuk'=>$kodeuk));
	foreach($results as $data){
		$sebelumm=$data->jumlahm;
		$sebelumk=$data->jumlahk;
	}
	$rows[] = array(
					array('data' => 'Jumlah bulan/tanggal '.$bulantext[$bulan][1].' '.$bulantext[$bulan][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '60px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldo),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldop),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Jumlah s/d bulan lalu','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '60px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($sebelumm),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($sebelumk),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Jumlah kumulatif s/d tanggal '.$bulantext[$bulan][1].' '.$bulantext[$bulan][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '60px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldo+$sebelumm),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldop+$sebelumk),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Sisa kas','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '90px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '50px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn(($saldo+$sebelumm)-($saldop+$sebelumk)),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
				//	array('data' => apbd_fn($saldo+$sebelumm-$saldop-$sebelumk),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Pada hari ini tanggal '.$bulantext[$bulan][1].' '.strtoupper($bulantext[$bulan][0]).' 2019','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '50px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Oleh kami didapat dalam kas Rp 0','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '50px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'dengan huruf(nol)','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '50px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Terdiri dari:','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '120px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => 'a.','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Tunai','width' => '140px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '0','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => 'b.','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Sisa bank','width' => '140px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '0','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => 'c.','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Surat berharga','width' => '140px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '0','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara,'.$tanggal,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);

	$rows[] = array(
					array('data' => 'Mengesahkan','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => ucwords(strtolower('KEPALA ')).$namauk,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => ucwords(strtolower('BENDAHARA ')).$namauk,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
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
					array('data' => $pimpinannama,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => $bendaharanama,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'NIP.'.$pimpinannip,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
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
