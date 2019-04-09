<?php
function laporanpenerimaan_main($arg=NULL, $nama=NULL) {
   //$kodeuk='57';
   
   $margin=arg(3);
   if($margin==null){
	   $margin=10;
   }
   
	if ($arg) {
		$kodero=arg(1);
		$bulan=arg(2);
		$margin=arg(3);
		$kodeuk=arg(4);
		
	} else {
		//$kodero='41101004';
		
		$kodero='X';
		$bulan = date('n');
		$margin=10;
		if(isSuperuser() || isDKK()){
			$kodeuk='X';
		}else {
			$kodeuk=apbd_getuseruk();
		}
	}
	
	drupal_set_title('Laporan Penerimaan');
	//drupal_set_message($tahun);
	//drupal_set_message('123');
	//print_pdf_p($output,$output2);
	if (arg(5) == 'pdf'){
		$output = getlaporan($kodeuk,$kodero,$bulan);
		//$output2 footer();
		apbd_ExportPDFm($margin,'P', 'F4', $output, 'Laporan Penerimaan_' . $bulan . '.PDF');
		//print_pdf_p($output,$output2);
	} else if (arg(5) == 'excel'){
		$output = getlaporan($kodeuk, $kodero, $bulan);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Penerimaan_" . $bulan . ".xls");
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	
	} else {
		$output_form = drupal_get_form('laporanpenerimaan_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak</span>', 'laporanpen/'. $kodero .'/' . $bulan . '/'. $margin . '/' . $kodeuk . '/pdf', array ('html' => true));	
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Excel</span>', 'laporanpen/'. $kodero .'/' . $bulan . '/'. $margin . '/' . $kodeuk . '/excel', array ('html' => true));
		
		if ($kodero=='X')
			$output = '';
		else
			$output = getlaporan($kodeuk, $kodero, $bulan);
		
		//$output = $kodeuk . '|' . $bulan;
		return drupal_render($output_form) .  $output;
	}
	
}

function laporanpenerimaan_main_form($form, &$form_state) {

	if(arg(1)!=null){
		
		//drupal_set_message('x');
		
		$kodero=arg(1);
		$bulan=arg(2);
		$margin=arg(3);
		if(isSuperuser() || isDKK()){
			$kodeuk=arg(4);
		}else {
			$kodeuk=apbd_getuseruk();
		}
	} else {
		//drupal_set_message('y');
		
		$kodero = 'X';
		$bulan = date('n');
		$margin=10;
		if(isSuperuser() || isDKK()){
			$kodeuk = 'X';
		}else {
			$kodeuk = apbd_getuseruk();
		}	
	}
	
	if(isSuperuser() || isDKK()){
		//AJAX............................
		if(isSuperuser())
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		else
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 and u.namasingkat like '%pkm%' order By u.namasingkat");
			
		$option_uk['X'] = 'PILIH OPD';
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
		
		$pquery='SELECT r.kodero, r.uraian FROM rincianobyek r inner join anggperuk a  on r.kodero=a.kodero order by r.kodero';
		$pres = db_query($pquery);			
		foreach ($pres as $prow) {
			$opt_rekening[$prow->kodero] = $prow->uraian;
		}
		$form['kodero'] = array(
			'#type' => 'select',
			'#title' =>  t('Rekening'),
			'#options' => $opt_rekening,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodero,
		);


	} else {
		$form['kodeuk']= array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);	

		$pres = db_query('SELECT r.kodero, r.uraian FROM rincianobyek r inner join anggperuk a on r.kodero=a.kodero  where a.kodeuk=:kodeuk order by r.kodero', array(':kodeuk'=>$kodeuk));			
		foreach ($pres as $prow) {
			$opt_rekening[$prow->kodero] = $prow->uraian;
		}
		$form['kodero'] = array(
			'#type' => 'select',
			'#title' =>  t('Rekening'),
			'#options' => $opt_rekening,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodero,
		);
		
	}	
	
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
	$form['margin']= array(
		'#type' => 'textfield',
		'#title'=>'Margin',
		'#default_value' => $margin,
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
    
	//CETAK BAWAH
	
	
	return $form;
}

	
function laporanpenerimaan_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$kodeuk= $form_state['values']['kodeuk'];
	$kodero = $form_state['values']['kodero'];

	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['cetak']) {
		drupal_goto('laporanpen/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk);
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']) {
		drupal_goto('laporanpen/'. $kodero .'/' . $bulan . '/'. $margin . '/' . $kodeuk . '/pdf');
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']) {
		drupal_goto('laporanpen/'. $kodero .'/' . $bulan . '/'. $margin . '/' . $kodeuk . '/excel');		
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

function getlaporan($kodeuk, $kodero, $bulan){
	set_time_limit (1024);
	//set_time_limit(0);
	ini_set('memory_limit','940M');

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
	
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$results=db_query('select namauk, bendaharanama, bendaharanip from unitkerja where kodeuk=:kodeuk',array(':kodeuk'=>$kodeuk));

	foreach ($results as $data) {
		$namauk=$data->namauk;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}

	$results=db_query('select uraian from rincianobyek where kodero=:kodero', array(':kodero'=>$kodero));
	foreach ($results as $data) {
		$rekeningnama=$data->uraian;
	}

	$header=array();
	$rows[]=array(
		array('data' => 'BUKU PENERIMAAN', 'colspan'=>'5', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'PER RINCIAN OBYEK PENDAPATAN', 'colspan'=>'5', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px', 'colspan'=>'5', 'align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '80px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => apbd_format_rekening($kodero), 'colspan'=>'3', 'width' => '420px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => 'Nama Rekening', 'width' => '80px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => $rekeningnama, 'colspan'=>'3', 'width' => '420px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => 'Bulan/Tahun', 'width' => '80px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => $bulan . '/2019', 'colspan'=>'3', 'width' => '420px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	
	$rows[]=array(
		array('data' => '', 'colspan'=>'5', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$output = createT($header,$rows,null);
	$header=null;
	$rows=null;
	$header[]=array(
		array('data' => 'NO', 'rowspan'=>'2','width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => 'TANGGAL','rowspan'=>'2', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'URAIAN', 'rowspan'=>'2','colspan'=>'2','width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'JUMLAH','colspan'=>'2', 'width' => '180px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
		
	);
	$header[]=array(
		array('data' => 'PENERIMAAN', 'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'PENYETORAN', 'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
	);
	
	$no=0; 

	$tglawal = '2019-0' . $bulan . '-01';
	$tglakhir = '2019-0' . $bulan . '-' . date('t',strtotime($tglawal));	
	$saldomasuk  =0;
	$saldokeluar = 0;
	
	//drupal_set_message($kodeuk);
	//drupal_set_message($kodero);
	
	$tanggal = $tglawal;
	while($tanggal <= $tglakhir) {
		
		$masuk_harian = 0; $setor_harian = 0;
		
		//drupal_set_message($tanggal);
		
		//masuk
		$result=db_query('select tanggal, jumlahmasuk, uraian, koderod, idkeluar from setor where jumlahmasuk>0 and kodeuk=:kodeuk and tanggal=:tanggal and kodero=:kodero order by setorid', array(':kodeuk'=>$kodeuk, ':tanggal'=>$tanggal, ':kodero'=>$kodero));
		
		foreach ($result as $data) {
			
			$datanamarek = $data->uraian;
			if (strlen($data->koderod)>2) {
				$resultsd=db_query('select uraian from rincianobyekdetil where koderod=:koderod',array(':koderod'=>$data->koderod));
				foreach ($resultsd as $datad) {
					$datanamarek .= ' (' . $datad->uraian . ')';
				}				
			}	

			if ($data->idkeluar=='0') $datanamarek .= ' <em style="color:red"> *belum setor</em>';
				
			$no++;

			$saldomasuk += $data->jumlahmasuk;
			$masuk_harian += $data->jumlahmasuk;			

			$rows[]=array(
				array('data' => $no, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => apbd_fd($data->tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => $datanamarek, 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => apbd_fn($data->jumlahmasuk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => apbd_fn(0), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			);
		}	

		//keluar
		//$result=db_query('select sum(jumlahkeluar) jumlahkeluar from setor where idkeluar>0 and jumlahkeluar>0 and kodeuk=:kodeuk and tgl_keluar=:tanggal and kodero=:kodero', array(':kodeuk'=>$kodeuk,':tanggal'=>$tanggal, ':kodero'=>$kodero));
		
		$results=db_query('select m.id, m.keterangan, sum(jumlahkeluar) jumlahkeluar from setor s inner join setoridmaster m on s.idkeluar=m.id where s.idkeluar>0 and s.jumlahkeluar>0 and s.kodeuk=:kodeuk and s.tgl_keluar=:tanggal and s.kodero=:kodero group by m.id, m.keterangan', array(':kodeuk'=>$kodeuk, ':tanggal'=>$tanggal, ':kodero'=>$kodero));		
		foreach ($results as $data) {
			$no++;
				
			$saldokeluar += $data->jumlahkeluar;
			$setor_harian += $data->jumlahkeluar;

			if ($data->keterangan=='') 
				$keterangan = 'Setoran';
			else
				$keterangan = $data->keterangan;
			
			$rows[]=array(
				array('data' => $no++, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => apbd_fd($tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => '', 'width' => '20px','align'=>'left','style'=>'border-bottom:0.5px solid black;font-size:70%'),
				array('data' => $keterangan, 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => apbd_fn(0), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => apbd_fn($data->jumlahkeluar), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			);		
		}

		if ($masuk_harian <> $setor_harian ) {
			$rows[]=array(
				array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => '<em style="color:red">Transaksi harian ' . apbd_fd($tanggal) . ' tidak balance, masuk : ' . apbd_fn($masuk_harian) . ', keluar : ' . apbd_fn($setor_harian) . '</em>', 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
				array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			);			
		}	

		if ($masuk_harian > 0) {
			$rows[]=array(
				array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.5px solid black;font-size:30%'),
				array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:30%'),
				array('data' => '', 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:30%'),
				array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:30%'),
				array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:30%'),
			);	
		}
	
		$tanggal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal)));
		$x++;
	} 
	
	

	if($no==0){
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			array('data' => 'Data kosong', 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.5px solid black;font-size:70%'),
		);	
	} 

	
	
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-top:1px solid black;border-left:1px solid black;font-size:70%'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%'),
		array('data' => 'Jumlah bulan ini('.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019)', 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%'),
		array('data' => apbd_fn($saldomasuk), 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%'),
		array('data' => apbd_fn($saldokeluar), 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%'),
	);
	
	//Bulan lalu
	$sebelummasuk = 0;
	$sebelumkeluar = 0;
	if ($bulan>1) { 
		//masuk
		$results=db_query('select  sum(jumlahmasuk) as jumlahm from setor where kodeuk=:kodeuk and kodero=:kodero and tanggal<:tglawal',array(':tglawal'=>$tglawal, ':kodero'=>$kodero,':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$sebelummasuk = $data->jumlahm;
		}
		//keluar
		$results=db_query('select  sum(jumlahkeluar) as jumlahk from setor where kodeuk=:kodeuk and kodero=:kodero and tanggal<:tglawal',array(':tglawal'=>$tglawal, ':kodero'=>$kodero,':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$sebelumkeluar = $data->jumlahk;
		}
	}
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-left:1px solid black;font-size:70%'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%'),
		array('data' => 'Jumlah (penyetoran yang lalu)', 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%'),
		array('data' => apbd_fn($sebelummasuk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
		array('data' => apbd_fn($sebelumkeluar), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
	);
	
	$totalm = $saldomasuk + $sebelummasuk;
	$totalk = $saldokeluar + $sebelumkeluar;
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-left:1px solid black;font-size:70%'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%'),
		array('data' => 'Jumlah seluruhnya', 'colspan'=>'2','width' => '250px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;font-weight:bold;'),
		array('data' => apbd_fn($totalm), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;font-weight:bold;'),
		array('data' => apbd_fn($totalk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;font-weight:bold;'),
	);
		
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%'),
			array('data' => 'Sisa', 'colspan'=>'3', 'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totalm-$totalk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
		);
	
	$rows[] = array(
					array('data' => '', 'colspan'=>'6', 'width' => '500px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan'=>'3', 'width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara,'.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019', 'colspan'=>'6', 'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'6', 'width' => '500px','align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan'=>'3', 'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => $bendaharanama, 'colspan'=>'6', 'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%;text-decoration:underline;'),
	); 
	$rows[] = array(
					array('data' => '', 'colspan'=>'3', 'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.'.$bendaharanip, 'colspan'=>'6', 'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	
	//$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= createT($header,$rows,null);
		return $output;
}
/*
function getlaporanhtml($kodeuk,$kodero,$bulan){
	set_time_limit (1024);
	//set_time_limit(0);
	ini_set('memory_limit','940M');
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat,u.bendaharanama,u.bendaharanip,ro.uraian as namarek, s.jumlahmasuk as jumlah,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk and s.kodero= :kodero ',array('kodeuk'=>$kodeuk,':kodero'=>$kodero));
	$total=0;
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
//	db_query("select u.namasingkat, ")
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$total+=$data->jumlah;
		$rekeningnama=$data->namarek;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}
	
	$header=array();
	$rows[]=array(
		array('data' => 'BUKU PENERIMAAN', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'PERINCIAN OBYEK PENDAPATAN', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Kode Rekening', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => apbd_format_rekening($kodero), 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => 'Nama Rekening', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => $rekeningnama, 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => 'Tahun Anggaran', 'width' => '120px','align'=>'left','style'=>'border:none;font-size:80%'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-size:80%'),
		array('data' => '2019', 'width' => '370px','align'=>'left','style'=>'border:none;font-size:80%'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$output = createT($header,$rows,null);
	$header=null;
	$rows=null;
	$rows[]=array(
		array('data' => 'NO', 'rowspan'=>'2','width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => 'TGL/BLN','rowspan'=>'2', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'URAIAN', 'rowspan'=>'2','width' => '180px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'JUMLAH','colspan'=>'2', 'width' => '220px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
		
	);
	$rows[]=array(
		array('data' => 'PENERIMAAN', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'PENYETORAN', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
	);
	
	$tanggal = '0000-00-00';
	$results=db_query('select u.namasingkat, s.uraian,s.tanggal,s.jumlahmasuk ,s.setorid, s.jumlahkeluar ,s.kodeuk,s.uraian,s.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.jumlahmasuk<>0 and s.kodeuk= :kodeuk and s.kodero = :kodero and month(s.tanggal) = :bulan order by s.tanggal, s.jumlahkeluar',array(':kodeuk'=>$kodeuk,':kodero'=>$kodero,':bulan'=>$bulan));
	$saldo=0;
	$saldop=0;
	$saldos=0;
	$saldops=0;
	$totalk=0;
	$totalm=0;
	$no=0;
	
	
	foreach ($results as $data) {
		$namarod='';
		$resultsd=db_query('select rod.uraian from rincianobyekdetil as rod inner join setor as s on s.koderod=rod.koderod where setorid=:setorid',array(':setorid'=>$data->setorid));
		foreach ($resultsd as $datad) {
			$namarod=$datad->uraian;
		}
		$no++;
		
		
		$jumlahkeluar=0;
		
		if ($tanggal == '0000-00-00') {
			$tanggal = $data->tanggal;
		} else if ($tanggal != $data->tanggal)  {
			
			$keluarsudah = true;
			
			$res_keluar =db_query('select sum(jumlahkeluar) as jumlahkeluar from setor where jumlahkeluar<>0 and tanggal=:tanggal and kodeuk= :kodeuk and kodero = :kodero',array(':tanggal'=>$tanggal, ':kodeuk'=>$kodeuk,':kodero'=>$kodero));
			foreach ($res_keluar as $data_keluar) {
				$jumlahkeluar = $data_keluar->jumlahkeluar;
			}	
			//$saldop+=$jumlahkeluar;
			
			$rows[]=array(
				array('data' => $no, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => apbd_format_tanggal($tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => 'SETOR', 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => apbd_fn(0), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => apbd_fn($jumlahkeluar), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
			);	
			$tanggal = $data->tanggal;	
		} 
	
		$saldo+=$data->jumlahmasuk;
		$saldop+=$jumlahkeluar;
		$datanamarek='';
		if($data->jumlahmasuk>0)
			$datanamarek=$namarod.'('.$data->uraian.')';
		else
			$datanamarek=$data->namarek;
		

		$keluarsudah = false;
		$rows[]=array(
			array('data' => $no, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:none solid black;font-size:80%'),
			array('data' => apbd_format_tanggal($data->tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
			array('data' => $datanamarek, 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
			array('data' => apbd_fn($data->jumlahmasuk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
			array('data' => apbd_fn(0), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
		);	
	}
	if($no==0){
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => 'Data Masih Kosong', 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
	} 
		if ($keluarsudah==false) {
			$res_keluar =db_query('select sum(jumlahkeluar) as jumlahkeluar from setor where jumlahkeluar>0 and tanggal=:tanggal and kodeuk= :kodeuk and kodero = :kodero',array(':tanggal'=>$tanggal, ':kodeuk'=>$kodeuk,':kodero'=>$kodero));
			foreach ($res_keluar as $data_keluar) {
				$jumlahkeluar = $data_keluar->jumlahkeluar;
			}	
			$saldop+=$jumlahkeluar;
			
			$rows[]=array(
				array('data' => $no, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => apbd_format_tanggal($tanggal), 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => 'SETOR', 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => apbd_fn(0), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
				array('data' => apbd_fn($jumlahkeluar), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:none solid black;font-size:80%'),
			);
		}
	
	
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => 'Jumlah bulan ini('.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019)', 'width' => '180px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => apbd_fn($saldo), 'width' => '110px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
		array('data' => apbd_fn($saldop), 'width' => '110px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%'),
	);
	//Bulan lalu
	$results=db_query('select u.namasingkat, s.tanggal,s.jumlahmasuk , s.jumlahkeluar ,s.kodeuk,s.uraian,s.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk and s.kodero = :kodero and month(s.tanggal) < :bulan ',array('kodeuk'=>$kodeuk,':kodero'=>$kodero,':bulan'=>$bulan));
	foreach ($results as $datas) {
		$saldos+=$datas->jumlahmasuk;
		$saldops+=$datas->jumlahkeluar;
	}
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
		array('data' => 'Jumlah (penyetoran yang lalu)', 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
		array('data' => apbd_fn($saldos), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		array('data' => apbd_fn($saldops), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
	);
	$totalm=$saldos+$saldo;
	$totalk=$saldops+$saldop;
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
		array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
		array('data' => 'Jumlah seluruhnya', 'width' => '180px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold;'),
		array('data' => apbd_fn($totalm), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		array('data' => apbd_fn($totalk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
	);
		
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => 'Sisa','colspan' => '2', 'width' => '290px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%'),
			array('data' => apbd_fn($totalm-$totalk), 'width' => '110px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:80%'),
		);
	
	$rows[] = array(
					array('data' => '','colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '3','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara,'.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019','colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => '','colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => $bendaharanama,'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%;text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => '','colspan' => '3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.'.$bendaharanip,'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	
	//$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}
*/
/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */



?>
