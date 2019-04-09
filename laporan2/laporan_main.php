<?php
function laporan_main($arg=NULL, $nama=NULL) {
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
	
	if (arg(3)== 'mas'){
		$output = getLaporansts_mas(arg(1),arg(2));
	} else {
		$output = getLaporansts_kel(arg(1),arg(2));
	}
	
	apbd_ExportPDF('P', 'F4', $output, 'CEK');
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

function getLaporansts_mas($setorid,$jenis){
	//$kodeuk='81';
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$result=db_query("select namauk,namasingkat,bendaharanama,bendaharanip from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($result as $data){
		$namasingkat=$data->namauk;
		$namas=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}
	if($jenis==1)
		$jumlah='jumlahmasuk';
	else
		$jumlah='jumlahkeluar';
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	//GET TOTAL TEMP
	/*$query = db_select('setor', 's');
	$query->fields('s', array($jumlah,'kodeuk','uraian','kodero','tanggal'));
	$query->condition('setorid', $setorid, '=');
	$results = $query->execute();*/
	//$results=db_query('select jumlahmasuk,jumlahkeluar,kodeuk,uraian,kodero,tanggal from setor where setorid=:setorid', array(':setorid'=>$setorid));
	$setorid = arg(1);
	$kodeuk=apbd_getuseruk();
	
	$query = db_select('setor', 's');
		$query->fields('s', array('kodeuk', 'tgl_keluar', 'idkeluar','jumlahmasuk'));
		$query->addExpression('SUM(s.jumlahkeluar)', 'jumlahkeluar');
		//$query->condition('s.jumlahkeluar', 0, '>');
		//$query->condition('s.jumlahkeluar', 0, '<');
		$query->condition('s.setorid', $setorid, '=');
		$query->condition('s.kodeuk', $kodeuk, '=');
		 //dpq($query);
		$results = $query->execute();
	$total=0;
	foreach ($results as $data) {
		
		$total = $data->jumlahmasuk;
		
		$kodeuk=$data->kodeuk;
		$tgl=apbd_format_tanggal($data->tanggal);
	}
	//..................................
	$header=array();
	$rows[]=array(
		array('data' => 'No. STS', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT TANDA SETORAN', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'Harap diterima uang sebesar', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => apbd_fn2($total), 'width' => '180px','align'=>'right','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '(dengan huruf)', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => terbilang($total), 'width' => '320px','align'=>'left','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Penerimaan', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'left','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-weight:bold;'),
		array('data' => 'Uraian Rincian Obyek', 'width' => '260px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	

	$setorid = arg(1);
	$kodeuk=apbd_getuseruk();
	
	$query = db_select('setor', 's');
		$query->fields('s', array('kodeuk', 'tgl_keluar', 'idkeluar','jumlahmasuk'));
		//$query->addExpression('SUM(s.jumlahkeluar)', 'jumlahkeluar');
		//$query->condition('s.jumlahkeluar', 0, '>');
		//$query->condition('s.jumlahkeluar', 0, '<');
		$query->condition('s.setorid', $setorid, '=');
		$query->condition('s.kodeuk', $kodeuk, '=');
		 //dpq($query);
		$results = $query->execute();
	//$total=0;
	foreach ($results as $data) {
 
		$totalb =$data->jumlahmasuk;
		
	}
	$res = db_query('select r.kodero, r.uraian as namarek, s.uraian from setor as s inner join rincianobyek as r on s.kodero=r.kodero where s.setorid=:setorid limit 1', array('setorid'=> $setorid ));
	foreach ($res as $datako){
		
		$kodero = $datako->kodero;
		$uraian = $datako->uraian;
		$namarek = $datako->namarek;
		$keterangan = $datako->keterangan;
		
	}
	$rows[]=array(
			array('data' => $kodero, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $namarek .' - '. $uraian, 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn2($totalb), 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	$kas='';
	if(substr($kodero,0,8)=='41416002')
		$kas='';
	else
		$kas='Kas Daerah';
	
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '360px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;'),
		
		
		array('data' => apbd_fn2($totalb), 'width' => '140px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:2px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '170px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Uang tersebut diterima pada tanggal'.$tgl,'width' => '375px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Cap SKPD','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Bendahara Penerimaan','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => $kas,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Penyetor','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => 'Bank Mitra Operasional','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	/*$results=db_query('select bendaharanama, bendaharanip from unitkerja where kodeuk=:kodeuk',array(':kodeuk'=>apbd_getuseruk()));
	foreach($results as $data){
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}*/
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => $bendaharanama,'width' => '190px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '...............................................','width' => '190px', 'align'=>'center','style'=>'text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	//STS ke -2 ....................................................................................
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
		$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => 'No. STS', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT TANDA SETORAN', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'Harap diterima uang sebesar', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => apbd_fn2($total), 'width' => '180px','align'=>'right','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '(dengan huruf)', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => terbilang($total), 'width' => '320px','align'=>'left','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Penerimaan', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'left','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-weight:bold;'),
		array('data' => 'Uraian Rincian Obyek', 'width' => '260px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	/*$query = db_select('setor', 's');
	$query->fields('s', array('jumlah','kodeuk','uraian','kodero'));
	$query->condition('setorid', $setorid, '=');*/
	//$results = $query->execute();
	$results=db_query('select s.jumlahmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero where setorid= :setorid',array('setorid'=>$setorid));
	$total=0;
	foreach ($results as $data) {
			$jumlah = $data->jumlahmasuk;
		
		$total+=$jumlah;
		
		$rows[]=array(
			array('data' => $kodeuk.$data->kodero, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $data->namarek .' - '. $data->uraian, 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn2($jumlah), 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}
	
	
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '360px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;'),
		
		
		array('data' => apbd_fn2($total), 'width' => '140px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:2px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '170px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Uang tersebut diterima pada tanggal'.$tgl,'width' => '375px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => 'Cap SKPD','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Bendahara Penerimaan','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => $kas,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Penyetor','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => 'Bank Mitra Operasional','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	/*$results=db_query('select bendaharanama, bendaharanip from unitkerja where kodeuk=:kodeuk',array(':kodeuk'=>apbd_getuseruk()));
	foreach($results as $data){
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}*/
	
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => $bendaharanama,'width' => '190px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '...............................................','width' => '190px', 'align'=>'center','style'=>'text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}


function getLaporansts_kel($setorid,$jenis){
	//$kodeuk='81';
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$result=db_query("select namauk,namasingkat,bendaharanama,bendaharanip from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($result as $data){
		$namasingkat=$data->namauk;
		$namas=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}
	if($jenis==1)
		$jumlah='jumlahmasuk';
	else
		$jumlah='jumlahkeluar';
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	//GET TOTAL TEMP
	/*$query = db_select('setor', 's');
	$query->fields('s', array($jumlah,'kodeuk','uraian','kodero','tanggal'));
	$query->condition('setorid', $setorid, '=');
	$results = $query->execute();*/
	//$results=db_query('select jumlahmasuk,jumlahkeluar,kodeuk,uraian,kodero,tanggal from setor where setorid=:setorid', array(':setorid'=>$setorid));
	$idkeluar = arg(1);
	$kodeuk=apbd_getuseruk();
	
	$query = db_select('setor', 's');
		$query->fields('s', array('kodeuk', 'tgl_keluar', 'idkeluar'));
		$query->addExpression('SUM(s.jumlahkeluar)', 'jumlahkeluar');
		//$query->condition('s.jumlahkeluar', 0, '>');
		//$query->condition('s.jumlahkeluar', 0, '<');
		$query->condition('s.idkeluar', $idkeluar, '=');
		$query->condition('s.kodeuk', $kodeuk, '=');
		$query->groupBy('s.idkeluar');	
		 //dpq($query);
		$results = $query->execute();
	$total=0;
	foreach ($results as $data) {
		
			$total = $data->jumlahkeluar;
		
		$kodeuk=$data->kodeuk;
		$tgl=apbd_format_tanggal($data->tanggal);
	}
	//..................................
	$header=array();
	$rows[]=array(
		array('data' => 'No. STS', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT TANDA SETORAN', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'Harap diterima uang sebesar', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => apbd_fn2($total), 'width' => '180px','align'=>'right','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '(dengan huruf)', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => terbilang($total), 'width' => '320px','align'=>'left','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Penerimaan', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'left','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-weight:bold;'),
		array('data' => 'Uraian Rincian Obyek', 'width' => '260px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	

	$idkeluar = arg(1);
	$kodeuk=apbd_getuseruk();
	
	$query = db_select('setor', 's');
		$query->fields('s', array('kodeuk', 'tgl_keluar', 'idkeluar'));
		$query->addExpression('SUM(s.jumlahkeluar)', 'jumlahkeluar');
		//$query->condition('s.jumlahkeluar', 0, '>');
		//$query->condition('s.jumlahkeluar', 0, '<');
		$query->condition('s.idkeluar', $idkeluar, '=');
		$query->condition('s.kodeuk', $kodeuk, '=');
		$query->groupBy('s.idkeluar');	
		 //dpq($query);
		$results = $query->execute();
	//$total=0;
	foreach ($results as $data) {
 
		$totalb =$data->jumlahkeluar;
		
	}
	$res = db_query('select r.kodero, r.uraian  as namarek, s.uraian from setor as s inner join rincianobyek as r on s.kodero=r.kodero where s.idkeluar=:idkeluar limit 1', array('idkeluar'=> $idkeluar ));
	foreach ($res as $datako){
		
		$kodero = $datako->kodero;
		$uraian = $datako->uraian;
		$namarek = $datako->namarek;
		$keterangan = $datako->keterangan;
		
	}
	$rows[]=array(
			array('data' => $kodero, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $namarek .' - '.$uraian, 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn2($totalb), 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	$kas='';
	if(substr($kodero,0,8)=='41416002')
		$kas='';
	else
		$kas='Kas Daerah';
	
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '360px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;'),
		
		
		array('data' => apbd_fn2($totalb), 'width' => '140px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:2px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '170px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Uang tersebut diterima pada tanggal'.$tgl,'width' => '375px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Cap SKPD','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Bendahara Penerimaan','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => $kas,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Penyetor','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => 'Bank Mitra Operasional','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	/*$results=db_query('select bendaharanama, bendaharanip from unitkerja where kodeuk=:kodeuk',array(':kodeuk'=>apbd_getuseruk()));
	foreach($results as $data){
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}*/
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => $bendaharanama,'width' => '190px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '...............................................','width' => '190px', 'align'=>'center','style'=>'text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	//STS ke -2 ....................................................................................
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
		$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => 'No. STS', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT TANDA SETORAN', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'text-decoration:underline;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'Harap diterima uang sebesar', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => apbd_fn2($total), 'width' => '180px','align'=>'right','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '(dengan huruf)', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => terbilang($total), 'width' => '320px','align'=>'left','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'right','style'=>'border-bottom-style:dotted;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Penerimaan', 'width' => '180px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => '', 'width' => '320px','align'=>'left','style'=>'border:none;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-weight:bold;'),
		array('data' => 'Uraian Rincian Obyek', 'width' => '260px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	/*$query = db_select('setor', 's');
	$query->fields('s', array('jumlah','kodeuk','uraian','kodero'));
	$query->condition('setorid', $setorid, '=');*/
	//$results = $query->execute();
	$results=db_query('select s.jumlahmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero where idkeluar= :idkeluar',array('idkeluar'=>$idkeluar));
	$total=0;
	foreach ($results as $data) {

		$jumlah = $data->jumlahkeluar;	
		
		$total+=$jumlah;
		
		$rows[]=array(
			array('data' => $kodeuk.$data->kodero, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $data->namarek .' - '. $data->uraian, 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn2($jumlah), 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}
	
	
	$rows[]=array(
		array('data' => 'TOTAL', 'width' => '360px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;'),
		
		
		array('data' => apbd_fn2($total), 'width' => '140px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:2px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '170px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Uang tersebut diterima pada tanggal'.$tgl,'width' => '375px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => 'Cap SKPD','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Bendahara Penerimaan','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => $kas,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'Penyetor','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => 'Bank Mitra Operasional','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	/*$results=db_query('select bendaharanama, bendaharanip from unitkerja where kodeuk=:kodeuk',array(':kodeuk'=>apbd_getuseruk()));
	foreach($results as $data){
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}*/
	
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => $bendaharanama,'width' => '190px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '...............................................','width' => '190px', 'align'=>'center','style'=>'text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => '','width' => '110px', 'align'=>'left','style'=>'border:none;'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '190px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '10px', 'align'=>'center','style'=>'border:none;'),
					
					array('data' => '','width' => '190px', 'align'=>'center','style'=>'border:none;'),
	);
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}


?>
