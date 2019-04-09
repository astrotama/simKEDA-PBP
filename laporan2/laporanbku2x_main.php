<?php
function laporanbku2_main($arg=NULL, $nama=NULL) {
  
   if(arg(3)!=null)
	   $margin=arg(3);  
   else
	  $margin=10; 
   
   
	if ($arg) {
		$kodeuk=arg(1);
		$bulan=arg(2);
		$index = arg(5);
		
	} else {
		$kodeuk = '81';
		$bulan = date('m');
		$index = '1';
		
	}
	
	drupal_set_title('Laporan BKU');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	if (arg(6) == 'pdf'){
		$output = getlaporanbku($kodeuk,$bulan,$index);
		//$output2 footer();
		apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
		//print_pdf_p($output,$output2);
	}else if (arg(6) == 'excel'){
		$output = getlaporanbku($kodeuk,$bulan,$index);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan BKU " . $kodeuk . "_" . $bulan . ".xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	
	} else {
		$output_form = drupal_get_form('laporanbku2_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak Pdf</span>', 'laporanbku2/'.arg(1).'/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/pdf', array ('html' => true));
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak Excel</span>', 'laporanbku2/'.arg(1).'/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/excel', array ('html' => true));
		$output = getlaporanbku($kodeuk,$bulan,$index);
		//$output2 footer();
		//apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
		//print_pdf_p($output,$output2);
		return drupal_render($output_form) . $btn . $output;
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

	$form['formdokumen']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);	
	if (arg(3) !== '') {
		$kodeuk = arg(1);
		$def_bulan = arg(2);
		$margin = arg(3);
		$tanggal = arg(4);
		$def_periode = arg(5);
		
	} else {
		$kodeuk = '81';
		$def_bulan = date('m');
		$margin = '10';
		$def_periode = '0';
		$tanggal = date('j F Y');
		
	}
	$form['formdokumen'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Laporan Buku Kas',
		//'#field_prefix' => _bootstrap_icon('envelope'),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,  	
	);
	if(isSuperuser() || isDKK()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		if(isDKK()){
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 and u.namasingkat like '%pkm%' order By u.namasingkat");
		}
		//$option_uk[0]='Semua';
		foreach($res as $data){
			$option_uk[$data->kodeuk]=$data->namasingkat;
		}
		$form['formdokumen']['uk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);
	}
	$form['formdokumen']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => 10,
	);	
	//$bulan=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desemebr');
	$bulan['1']= 'Januari';
	$bulan['2']= 'Februari';
	$bulan['3']= 'Maret';
	$bulan['4']= 'April';
	$bulan['5']= 'Mei';
	$bulan['6']= 'Juni';
	$bulan['7']= 'Juli';
	$bulan['8']= 'Agustus';
	$bulan['9']= 'September';
	$bulan['10']= 'Oktober';
	$bulan['11']= 'November';
	$bulan['12']= 'Desemeber';
	$form['formdokumen']['bulan']= array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#options' => $bulan,
		'#default_value' => $def_bulan ,
	);
	$periode['0']= 'Satu Bulan';
	$periode['1']= '1 (Tanggal 1-7)';
	$periode['2']= '2 (Tanggal 8-15)';
	$periode['3']= '3 (Tanggal 16-23)';
	$periode['4']= '4 (Tanggal 24-30/31)';
	$form['formdokumen']['periode']= array(
		'#type' => 'select',
		'#title' => 'Periode',
		'#options' => $periode,
		'#default_value' => $def_periode ,
	);	
	$form['formdokumen']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' =>date('j F Y') ,
	);	
	$form['formdokumen']['cetak']= array(
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function laporanbku2_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function laporanbku2_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$tanggal = $form_state['values']['tanggal'];
	$periode = $form_state['values']['periode'];
	
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK())
		$kodeuk = $form_state['values']['uk'];
	/*if($kodeuk==null)
		$kodeuk='81';*/
	drupal_goto('laporanbku2/' . $kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/'.$periode);

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
			array('data' => 'Buku Kas', 'colspan'=>'6', 'width' => '500px','align'=>'left','style'=>'border:none;font-size:100%;'),
		);
		$rows[]=array(
			array('data' => 'BUKU KAS UMUM', 'colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
		);
		$rows[]=array(
			array('data' => '', 'colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
		);
		$rows[]=array(
			array('data' => '', 'colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
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
	$saldomasuk  =0;
	$saldokeluar = 0;
	$no=0;
	
	$tanggal = $tglawal;
	while($tanggal <= $tglakhir) {
		
		//masuk
		$result=db_query('select s.tanggal,s.jumlahmasuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero where s.jumlahmasuk>0 and s.kodeuk= :kodeuk  and s.tanggal=:tanggal order by s.setorid', array(':kodeuk'=>$kodeuk,':tanggal'=>$tanggal));
		
		foreach ($result as $dat) {
			$no++;
			$saldomasuk += $dat->jumlahmasuk;
			$rows[]=array(
				array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
				array('data' => apbd_fd($tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $dat->uraian, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => $dat->kodero, 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn($dat->jumlahmasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
				array('data' => apbd_fn(0), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
			);			
		}	

		//masuk
		$result=db_query('select sum(jumlahkeluar) jumlahkeluar from setor where idkeluar>0 and jumlahkeluar>0 and kodeuk=:kodeuk and tgl_keluar=:tanggal', array(':kodeuk'=>$kodeuk,':tanggal'=>$tanggal));
		foreach ($result as $dat) {
			if ($dat->jumlahkeluar>0) {
				$no++;
				$saldokeluar += $dat->jumlahkeluar;
				$rows[]=array(
					array('data' => $no, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%'),
					array('data' => apbd_fd($tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
					array('data' => 'Penyetoran', 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%'),
					array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:60%'),
					array('data' => apbd_fn(0), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
					array('data' => apbd_fn($dat->jumlahkeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%'),
				);			
			}
		}	
		
		$tanggal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal)));
		$x++;
	} 
	

	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => 'Jumlah', 'width' => '180px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => apbd_fn($saldomasuk), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => apbd_fn($saldokeluar), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
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
		
		$sebelummasuk = 0;
		$sebelumkeluar = 0;
		if ($bulan != '1') {
		
			$results=db_query('select  sum(s.jumlahmasuk) as jumlahm,sum(s.jumlahkeluar) as jumlahk from setor as s where  s.kodeuk= :kodeuk and s.tanggal<:tglawal',array(':tglawal'=>$tglawal,':kodeuk'=>$kodeuk));
			foreach($results as $data){
				$sebelummasuk = $data->jumlahm;
				$sebelumkeluar =$data->jumlahk;
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
		$rows[] = array(
						array('data' => 'Sisa kas','width' => '250px', 'align'=>'left','style'=>'border:none;font-size:80%'),
						array('data' => '','width' => '90px', 'align'=>'center','style'=>'border:none;font-size:80%'),
						array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
						array('data' => '','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
						array('data' => '','width' => '50px', 'align'=>'right','style'=>'border:none;font-size:80%'),
						array('data' => '','width' => '5px', 'align'=>'left','style'=>'border:none;font-size:80%'),
						array('data' => 'Rp','width' => '15px', 'align'=>'left','style'=>'border:none;font-size:80%'),
						array('data' => apbd_fn(($saldomasuk + $sebelummasuk) - ($saldokeluar + $sebelumkeluar)),'width' => '80px', 'align'=>'right','style'=>'border:none;font-size:80%'),
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
