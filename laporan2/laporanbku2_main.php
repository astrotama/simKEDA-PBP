<?php
function laporanbku2_main($arg=NULL, $nama=NULL) {
  
   
   
	if ($arg) {
		$kodeuk = arg(1);
		$bulan = arg(2);
		$margin = arg(3);
		$tanggal = arg(4);
		$index = arg(5);
		
	} else {
		$kodeuk = apbd_getuseruk();
		$bulan = date('n');
		$margin = '10';
		$tanggal = date('j F Y');
		$index = '0';
		
	}
	
	//drupal_set_message($kodeuk);
	//drupal_set_message($bulan);
	
	drupal_set_title('Laporan BKU');
	
	if (arg(6) == 'pdf'){
		$output = getlaporanbku_baru($kodeuk,$bulan);
		//$output2 footer();
		apbd_ExportPDFm($margin,'P', 'F4', $output, 'Laporan_BKU_' . $bulan . '.PDF');
		//print_pdf_p($output,$output2);
		 
	}else if (arg(6) == 'excel'){
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan_BKU_" . $bulan . ".xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = getlaporanbku_baru($kodeuk,$bulan);
		echo $output;
		
	} else {
		//drupal_goto('laporanbku2/' . $kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/'.$periode);
		
		$output_form = drupal_get_form('laporanbku2_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak</span>', 'laporanbku2/'.$kodeuk . '/' . $bulan .'/' . $margin . '/'. $tanggal . '/'. $index . '/pdf', array ('html' => true));
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Excel</span>', 'laporanbku2/'.$kodeuk . '/' . $bulan .'/' . $margin . '/'. $tanggal . '/'. $index . '/excel', array ('html' => true));
		
		$output = getlaporanbku_baru($kodeuk, $bulan);
		//$output = $kodeuk . '|'. $bulan;
		return drupal_render($output_form) . $output;
	}
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
function laporanbku2_main_form($form, &$form_state) {


	if(arg(1)!=null){
		$kodeuk = arg(1);
		$bulan = arg(2);
		$margin = arg(3);
		$tanggal = arg(4);
		$index = arg(5);
	} else {
		$kodeuk = apbd_getuseruk();
		$bulan = date('n');
		$margin = '10';
		$tanggal = date('j F Y');
		$index = '0';
	}
	/*
	$form = array (
		'#type' => 'fieldset',
		'#title'=> 'Laporan Buku Kas',
		//'#field_prefix' => _bootstrap_icon('envelope'),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,  	
	);
	*/
	if(isSuperuser() || isDKK()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u INNER JOIN anggperuk as a on a.kodeuk=u.kodeuk WHERE  a.jumlah>0 ORDER BY u.namasingkat");
		
		if(isDKK()){
			$res=db_query("SELECT kodeuk, namasingkat FROM unitkerja where namasingkat LIKE '%pkm%' ORDER BY namasingkat");
		}
		foreach($res as $data){
			$option_uk[$data->kodeuk]=$data->namasingkat;
		}
		$form['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);
		
	} else {
		$form['kodeuk']= array(
			'#type' => 'value',
			'#value' => apbd_getuseruk(),
		);	
	}	
	 
	$form['index']= array(
		'#type' => 'value',
		'#value' => $index,
	);	
	$form['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => $margin,
	);	
	//$bulan=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desemebr');
	$optbulan['1']= 'Januari';
	$optbulan['2']= 'Februari';
	$optbulan['3']= 'Maret';
	$optbulan['4']= 'April';
	$optbulan['5']= 'Mei';
	$optbulan['6']= 'Juni';
	$optbulan['7']= 'Juli';
	$optbulan['8']= 'Agustus';
	$optbulan['9']= 'September';
	$optbulan['10']= 'Oktober';
	$optbulan['11']= 'November';
	$optbulan['12']= 'Desember';
	
	$form['bulan']= array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#options' => $optbulan,
		'#default_value' => $bulan ,
	);
	/*
	$periode['0']= 'Satu Bulan';
	$periode['1']= '1 (Tanggal 1-7)';
	$periode['2']= '2 (Tanggal 8-15)';
	$periode['3']= '3 (Tanggal 16-23)';
	$periode['4']= '4 (Tanggal 24-30/31)';
	$form['periode']= array(
		'#type' => 'select',
		'#title' => 'Periode',
		'#options' => $periode,
		'#default_value' => $def_periode ,
	);
	*/
	
	$form['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' => $tanggal ,
	);	
	$form['cetak']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-default btn-sm')),
	);			
	$form['cetakexcel']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Excel',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	$form['cetakpdf']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);
    
	
	
	return $form;
}

function laporanbku2_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function laporanbku2_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$index = $form_state['values']['index'];
	$tanggal = $form_state['values']['tanggal'];
	$periode = 0;	//$form_state['values']['periode'];
	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['cetak']) {
		drupal_goto('laporanbku2/' . $kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/'.$periode);
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']) {
		drupal_goto('laporanbku2/'.$kodeuk . '/' . $bulan .'/' . $margin . '/'. $tanggal . '/'. $index . '/pdf');
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']) {
		drupal_goto('laporanbku2/'.$kodeuk . '/' . $bulan .'/' . $margin . '/'. $tanggal . '/'. $index . '/excel');		
	}
}

function getlaporanbku_baru($kodeuk, $bulan){
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
	$results=db_query('select u.namauk,u.pimpinannama ,u.pimpinannip, u.bendaharanama ,u.bendaharanip from  unitkerja as u where u.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namauk;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		//$total+=$data->jumlah;
	}

	
	$output = '';
	$header=array();
	$rows[]=array(
		array('data' => 'BUKU KAS UMUM', 'colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => $namauk, 'colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => "Bulan : " . $bulan . " - Tahun : 2019",'colspan'=>'6',  'width' => '500px','align'=>'center','style'=>'border:none;font-size:80%;'),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));

	$rows=null;
	$header=null;
	$header=array(
		array('data' => 'No.', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:65%;'),
		array('data' => 'Tanggal', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:65%;'),
		array('data' => 'Uraian', 'colspan'=>'2', 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:65%;'),
		array('data' => 'Rekening', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:65%;'),
		array('data' => 'Penerimaan(Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:65%;'),
		array('data' => 'Pengeluaran(Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:65%;'),
	);
	
	$tglawal = '2019-0' . $bulan . '-01';
	$tglakhir = '2019-0' . $bulan . '-' . date('t',strtotime($tglawal));;		
	$saldomasuk  =0;
	$saldokeluar = 0;
	
	$no=0;
	$tanggal = $tglawal;
	while($tanggal <= $tglakhir) {
		
		$masuk_harian = 0; $setor_harian = 0;
		
		//PENERIMAAN
		$results=db_query('select tanggal, jumlahmasuk, uraian, kodero, idkeluar from setor where kodeuk=:kodeuk and tanggal=:tanggal and jumlahmasuk>0 order by setorid',array('kodeuk'=>$kodeuk,':tanggal'=>$tanggal));
		foreach ($results as $data) {
						
			if ($data->idkeluar=='0')
				$uraian = $data->uraian . ' <em style="color:red">(belum setor)</em>';
			else
				$uraian = $data->uraian;
				
			$saldomasuk += $data->jumlahmasuk;
			$masuk_harian += $data->jumlahmasuk;
			
			$no++;
			$rows[]=array(
				array('data' => $no, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
				array('data' => apbd_fd($data->tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $uraian, 'colspan'=>'2', 'width' => '250px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $data->kodero, 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn($data->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn(0), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			);	
		}

		//PENGELUARAN	
		//drupal_set_message($tanggal);
		$results=db_query('select s.kodero, m.keterangan, sum(jumlahkeluar) jumlahkeluar from setor s inner join setoridmaster m on s.idkeluar=m.id where s.idkeluar>0 and s.jumlahkeluar>0 and s.kodeuk=:kodeuk and s.tgl_keluar=:tanggal group by s.kodero, m.keterangan', array(':kodeuk'=>$kodeuk, ':tanggal'=>$tanggal));		
		foreach ($results as $data) {
			
			//drupal_set_message($tanggal);
			
			$saldokeluar += $data->jumlahkeluar;
			$setor_harian += $data->jumlahkeluar;
			
			$no++;
			
			if ($data->keterangan=='') 
				$keterangan = 'Setoran';
			else
				$keterangan = $data->keterangan;
				
			$rows[]=array(
				array('data' => $no, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
				array('data' => apbd_fd($tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => '', 'width' => '20px','align'=>'left','style'=>'border-none;font-size:60%'),
				array('data' => $keterangan, 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $data->kodero, 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn($data->jumlahkeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			);	
		}
		
		if ($masuk_harian <> $setor_harian ) {
			$rows[]=array(
				array('data' => '', 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
				array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => '<em style="color:red">Transaksi harian ' . apbd_fd($tanggal) . ' tidak balance, masuk : ' . apbd_fn($masuk_harian) . ', keluar : ' . apbd_fn($setor_harian) . '</em>', 'colspan'=>'2', 'width' => '250px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			);				
		}	
		if ($masuk_harian>0) {
			$rows[]=array(
				array('data' => '', 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:30%'),
				array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:30%'),
				array('data' => '', 'colspan'=>'2', 'width' => '250px','align'=>'left','style'=>'border-right:1px solid black;font-size:30%'),
				array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:30%'),
				array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:30%'),
				array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:30%'),
			);				
		}
		$tanggal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal)));
	
	}	
	
	$rows[]=array(
			array('data' => '', 'width' => '20px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:60%'),
			array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%'),
			array('data' => 'Jumlah', 'colspan'=>'2', 'width' => '250px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:60%'),
			array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:60%'),
			array('data' => apbd_fn($saldomasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:60%'),
			array('data' => apbd_fn($saldokeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:60%'),
		);
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	
	$header=null;
	$rows=null;
	
	$sebelummasuk = 0;
	$sebelumkeluar = 0;
	if ($bulan > '1') {
		
		//masuk
		$results=db_query('select  sum(s.jumlahmasuk) as jumlahm from setor as s where  s.kodeuk= :kodeuk and s.tanggal<:tglawal',array(':tglawal'=>$tglawal,':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$sebelummasuk = $data->jumlahm;
		}
		//keluar
		$results=db_query('select  sum(s.jumlahkeluar) as jumlahk from setor as s where  s.kodeuk= :kodeuk and s.tgl_keluar<:tglawal',array(':tglawal'=>$tglawal,':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$sebelumkeluar = $data->jumlahk;
		}
	}
	$rows[] = array(
					array('data' => 'Jumlah bulan/tanggal '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '60px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldomasuk),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldokeluar),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Jumlah s/d bulan lalu','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '60px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($sebelummasuk),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($sebelumkeluar),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Jumlah kumulatif s/d tanggal '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '60px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldomasuk + $sebelummasuk),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldokeluar + $sebelumkeluar),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$saldoakhir = ($saldomasuk+$sebelummasuk)-($saldokeluar+$sebelumkeluar);
	$rows[] = array(
					array('data' => 'Sisa kas','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '90px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '50px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldoakhir),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '500px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Pada tanggal '.$bulantext[$bulan-1][1].' '.strtoupper($bulantext[$bulan-1][0]).' 2019','width' => '500px', 'align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'Oleh kami didapat dalam kas Rp ' . apbd_fn($saldoakhir),'width' => '500px', 'align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'dengan huruf(' . terbilang($saldoakhir) . ')','width' => '500px', 'align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '500px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Terdiri dari:','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => 'a.','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Tunai','width' => '140px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => apbd_fn($saldoakhir),'width' => '100px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => 'b.','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Sisa bank','width' => '140px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '0','width' => '100px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => 'c.','width' => '10px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => 'Surat berharga','width' => '140px', 'align'=>'left','style'=>'border:none;font-size:80%'),
					array('data' => '0','width' => '100px', 'align'=>'right','style'=>'border:none;font-size:80%'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara,' . apbd_fd_long($tanggal),'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);

	$rows[] = array(
					array('data' => 'Mengesahkan','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => 'KEPALA ' . $namauk,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'BENDAHARA ' . $namauk,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
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


function getlaporanbku($kodeuk,$bulan,$index){
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

	
	$output = '';
	if  (($index=='0') || ($index=='1')) {
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
	}
	$rows=null;
	$header=null;
	$header=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Uraian', 'width' => '180px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Kode Rekening', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Penerimaan (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:75%;'),
		array('data' => 'Pengeluaran  (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:73%;'),
	);
	
	if ($index=='1') {
		$tglawal = '2019-' . $bulan . '-01';
		$tglakhir = '2019-' . $bulan . '-07';
	} elseif ($index=='2') {
		$tglawal = '2019-' . $bulan . '-08';
		$tglakhir = '2019-' . $bulan . '-15';
	} elseif ($index=='3') {
		$tglawal = '2019-' . $bulan . '-16';
		$tglakhir = '2019-' . $bulan . '-23';		
	} elseif ($index=='4') {
		$tglawal = '2019-' . $bulan . '-24';
		$tglakhir = '2019-' . $bulan . '-31';		
	} else {
		$tglawal = '2019-' . $bulan . '-01';
		$tglakhir = '2019-' . $bulan . '-31';		
	}
	//drupal_set_message($tglawal);
	//drupal_set_message($tglakhir);
	$results=db_query('select s.setorid,u.namasingkat, s.tanggal,s.jumlahmasuk,s.idmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk  and s.tanggal>=:tglawal and s.tanggal<=:tglakhir and s.jumlahmasuk>0  order by s.tanggal,s.setorid',array('kodeuk'=>$kodeuk,':tglawal'=>$tglawal,':tglakhir'=>$tglakhir));
	$saldo=0;
	$saldop=0;
	$no=0;
	foreach ($results as $data) {
		
		
		$saldo+=$data->jumlahmasuk;
		$saldop+=$data->jumlahmasuk;

		
		$no++;
		$rows[]=array(
			array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
			array('data' => apbd_fd($data->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $data->uraian, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $data->kodero, 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => apbd_fn($data->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => apbd_fn(0), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
		);
		$no++;
		$rows[]=array(
			array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
			array('data' => apbd_fd($data->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $data->uraian, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $data->kodero, 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => apbd_fn(0), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => apbd_fn($data->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
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
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	
	if  (($index=='0') || ($index=='4')) {
		$header=null;
		$rows=null;
		$rows[] = array(
						array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
		);
		//$results=db_query("select sum(jumlahmasuk) as jumlahm, sum(jumlahkeluar) as jumlahk from setor where month(tanggal)<:bulan and year(tanggal)='2019' and kodeuk=:kodeuk",array(':bulan'=>$bulan+1,'kodeuk'=>$kodeuk));
		
		$sebelumm = 0;
		$sebelumk = 0;
		if (($bulan > '1') or ($index=='4')) {
		
			//drupal_set_message($bulan);
			
			//$results=db_query('select  sum(s.jumlahmasuk) as jumlahm,sum(s.jumlahkeluar) as jumlahk from setor as s where  s.kodeuk= :kodeuk and month(s.tanggal) < :bulan and (jumlahkeluar<>0 or idkeluar=1)',array(':bulan'=>$bulan+1,':kodeuk'=>$kodeuk));
			$results=db_query('select  sum(s.jumlahmasuk) as jumlahm,sum(s.jumlahkeluar) as jumlahk from setor as s where  s.kodeuk= :kodeuk and s.tanggal<:tglawal',array(':tglawal'=>$tglawal,':kodeuk'=>$kodeuk));
			foreach($results as $data){
				$sebelumm=$data->jumlahm;
				$sebelumk=$sebelumm;		//$data->jumlahk;
			}
		}
		$rows[] = array(
						array('data' => 'Jumlah bulan/tanggal '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
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
						array('data' => 'Jumlah kumulatif s/d tanggal '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
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
						array('data' => 'Pada hari ini tanggal '.$bulantext[$bulan-1][1].' '.strtoupper($bulantext[$bulan-1][0]).' 2019','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
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
	}
	return $output;
}

function getlaporanbku_lama($kodeuk,$bulan,$index){
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
	
	$output = '';
	if  (($index=='0') || ($index=='1')) {
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
	}
	$rows=null;
	$header=null;
	$header=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Uraian', 'width' => '180px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Kode Rekening', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Penerimaan (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:75%;'),
		array('data' => 'Pengeluaran  (Rp)', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:73%;'),
	);
	
	if ($index=='1') {
		$tglawal = '2019-' . $bulan . '-01';
		$tglakhir = '2019-' . $bulan . '-07';
	} elseif ($index=='2') {
		$tglawal = '2019-' . $bulan . '-08';
		$tglakhir = '2019-' . $bulan . '-15';
	} elseif ($index=='3') {
		$tglawal = '2019-' . $bulan . '-16';
		$tglakhir = '2019-' . $bulan . '-23';		
	} elseif ($index=='4') {
		$tglawal = '2019-' . $bulan . '-24';
		$tglakhir = '2019-' . $bulan . '-31';		
	} else {
		$tglawal = '2019-' . $bulan . '-01';
		$tglakhir = '2019-' . $bulan . '-31';		
	}
	//drupal_set_message($tglawal);
	//drupal_set_message($tglakhir);
	$results=db_query('select s.setorid,u.namasingkat, s.tanggal,s.jumlahmasuk,s.idmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk  and s.tanggal>=:tglawal and s.tanggal<=:tglakhir and s.jumlahkeluar<>0  order by s.tanggal,s.setorid',array('kodeuk'=>$kodeuk,':tglawal'=>$tglawal,':tglakhir'=>$tglakhir));
	$saldo=0;
	$saldop=0;
	$no=0;
	foreach ($results as $data) {
		
		
		$saldop+=$data->jumlahkeluar;
		//4045,4043,4042,4046
		$dataid=explode(",",$data->idmasuk);
		//$idmasuk='4045,4043,4042,4046';
		//$idmasuk=array('4045','4043','4042','4046');
		$result=db_query('select u.namasingkat, s.tanggal,s.jumlahmasuk,s.jumlahkeluar,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk  and s.setorid in(:idmasuk)  order by s.tanggal,s.jumlahkeluar ',array(':kodeuk'=>$kodeuk,':idmasuk'=>$dataid));
		$masukan=0;
		$masuksemen=0;
		foreach ($result as $dat) {
			$masukan++;
			$no++;
			$masuksemen+=$dat->jumlahmasuk;
			$saldo+=$dat->jumlahmasuk;
			$rows[]=array(
				array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
				array('data' => apbd_fd($dat->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $dat->uraian, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $dat->kodero, 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn($dat->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn($dat->jumlahkeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			);
		}

		$tm='';

		if($masuksemen!=$data->jumlahkeluar){
			$tm=$data->setorid.'#';
		}
		
		$no++;
		$rows[]=array(
			array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
			array('data' => apbd_fd($data->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $data->uraian, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $data->kodero, 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => apbd_fn($data->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			array('data' => $tm.apbd_fn($data->jumlahkeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
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
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	
	if  (($index=='0') || ($index=='4')) {
		$header=null;
		$rows=null;
		$rows[] = array(
						array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
		);
		//$results=db_query("select sum(jumlahmasuk) as jumlahm, sum(jumlahkeluar) as jumlahk from setor where month(tanggal)<:bulan and year(tanggal)='2019' and kodeuk=:kodeuk",array(':bulan'=>$bulan+1,'kodeuk'=>$kodeuk));
		
		$sebelumm = 0;
		$sebelumk = 0;
		if ($bulan != '1') {
		
			//drupal_set_message($bulan);
			
			//$results=db_query('select  sum(s.jumlahmasuk) as jumlahm,sum(s.jumlahkeluar) as jumlahk from setor as s where  s.kodeuk= :kodeuk and month(s.tanggal) < :bulan and (jumlahkeluar<>0 or idkeluar=1)',array(':bulan'=>$bulan+1,':kodeuk'=>$kodeuk));
			$results=db_query('select  sum(s.jumlahmasuk) as jumlahm,sum(s.jumlahkeluar) as jumlahk from setor as s where  s.kodeuk= :kodeuk and s.tanggal<:tglawal',array(':tglawal'=>$tglawal,':kodeuk'=>$kodeuk));
			foreach($results as $data){
				$sebelumm=$data->jumlahm;
				$sebelumk=$sebelumm;		//$data->jumlahk;
			}
		}
		$rows[] = array(
						array('data' => 'Jumlah bulan/tanggal '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
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
						array('data' => 'Jumlah kumulatif s/d tanggal '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0],'width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
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
						array('data' => 'Pada hari ini tanggal '.$bulantext[$bulan-1][1].' '.strtoupper($bulantext[$bulan-1][0]).' 2019','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
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
	}
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
