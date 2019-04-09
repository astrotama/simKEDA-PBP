<?php
function laporanjenis2_main($arg=NULL, $nama=NULL) {

   
	if (arg(3)!=null) {
		$kodero=arg(1);
		$bulan=arg(2);
		$margin=arg(3);
		$kodeuk=arg(4);
		
	} else {
		if(isSuperuser() || isDKK()){
			$kodeuk='X';
		}else {
			$kodeuk=apbd_getuseruk();
		}		
		$kodero= 'X';
		$bulan= date('n');
		$margin=10;  
		
	}
	
	drupal_set_title('Laporan Sejenis');
	
	if (arg(5) == 'pdf'){
		
		$output = getlaporanjenishtml($kodero,$bulan);
		apbd_ExportPDFm($margin,'P', 'F4', $output, 'Laporan Penerimaan Sejenis_' . $bulan . '.xls');
		
	} else if (arg(5) == 'excel'){
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Penerimaan Sejenis_" . $bulan . ".xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = getlaporanjenishtml($kodero,$bulan);
		echo $output;
	} else {
		$output_form = drupal_get_form('laporanjenis2_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak</span>', 'laporanjenis2/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk.'/pdf', array ('html' => true));
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Excel</span>', 'laporanjenis2/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk.'/excel', array ('html' => true));

		if ($kodero=='X')
			$output = '';
		else		
			$output = getlaporanjenishtml($kodero,$bulan);
		
		return drupal_render($output_form) . $output;
	}
	
	
}

function laporanjenis2_main_form($form, &$form_state) {
	
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
	
function laporanjenis2_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$kodeuk= $form_state['values']['kodeuk'];
	$kodero = $form_state['values']['kodero'];
	
	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['cetak']) {
		drupal_goto('laporanjenis2/' . $kodero . '/' . $bulan . '/' . $margin . '/' . $kodeuk);
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']) {
		drupal_goto('laporanjenis2/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk.'/pdf');
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']) {
		drupal_goto('laporanjenis2/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk.'/excel');		
	}

}


function getlaporanjenishtml($kodero,$bulan){
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
	//$kodeuk=apbd_getuseruk();
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK()){
		if (arg(3)!=null) {
			$kodeuk=arg(4);
		} else {
			$kodeuk= '81';
		}
	}else {
		$kodeuk=apbd_getuseruk();
	}
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat,u.bendaharanama ,u.bendaharanip, s.jumlahmasuk, s.jumlahkeluar ,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
		//$total+=$data->jumlah;
	}
	$kodero=arg(1);
	$results=db_query("select ro.uraian from rincianobyek as ro where ro.kodero=:kodero",array(':kodero'=>$kodero));
	foreach($results as $data){
		$namarek=$data->uraian;
	}
	$ayat=substr_replace($kodero,'.',1,0);
	$ayat=substr_replace($ayat,'.',3,0);
	$ayat=substr_replace($ayat,'.',5,0);
	$ayat=substr_replace($ayat,'.',8,0);
	
	//
	$header=array();
	/*
	$rows[]=array(
		array('data' => 'Bulan', 'width' => '50px','align'=>'left','style'=>'border:none;font-size:60%;'),
		array('data' => ': ' . $bulantext[$bulan-1][0], 'width' => '440px','align'=>'left','style'=>'border:none;font-size:70%;font-weight:bold;text-decoration:underline;'),
	);
	*/
	$rows[]=array(
		array('data' => 'BUKU PENERIMAAN SEJENIS','colspan' => '9', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '','colspan' => '9', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'AYAT', 'width' => '50px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%;'),
		array('data' => $ayat,'colspan' => '4', 'width' => '440px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'URAIAN', 'width' => '50px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%;'),
		array('data' => $namarek, 'colspan' => '4','width' => '440px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'BULAN', 'width' => '50px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%;'),
		array('data' =>  $bulantext[$bulan-1][0],'colspan' => '4', 'width' => '440px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);	
	$rows[]=array(
		array('data' => 'TAHUN', 'width' => '50px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:80%;'),
		array('data' =>  '2019','colspan' => '4', 'width' => '440px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	
	$rows=null;
	$header=array();
	$header[]=array(
		array('data' => 'No.','rowspan'=>'2', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
		array('data' => 'Diterima dari / uraian','colspan'=>'2', 'rowspan'=>'2','width' => '280px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Jumlah','rowspan'=>'1','colspan'=>'4', 'width' => '200px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		
	);
	$header[]=array(
		array('data' => 'Penerimaan','colspan'=>'2','width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'Penyetoran','colspan'=>'2', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		
	);
	/*
	$rows[]=array(
		array('data' => '1', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
		array('data' => '2','colspan'=>'2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => '3','colspan'=>'2','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => '4','colspan'=>'2', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		
	);
	*/
	
	$no = 0;
	$totalmasuk = 0;
	$totalkeluar = 0;
	$totsetor = 0;

	$result=db_query('select tanggal, kodero, uraian, setorid, idmasuk, jumlahmasuk, idkeluar, tgl_keluar from setor where kodero=:kodero and kodeuk=:kodeuk and month(tanggal)=:bulan order by tanggal', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':bulan'=>$bulan));
	
	foreach($result as $data){
		$no++;
		
		$totalmasuk += $data->jumlahmasuk;
		$rows[]=array(
				array('data' =>$no, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				array('data' => $data->uraian, 'colspan'=>'2', 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($data->jumlahmasuk), 'colspan'=>'2','width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => '','colspan'=>'2', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);

		if ($data->idkeluar=='0') { 
			$setor = 0;
			$setoran = '<em>Disetor</em>';
		} else {
			$setor = $data->jumlahmasuk;
			$totalkeluar += $data->jumlahmasuk;
			$setoran = '<em>Disetor tanggal ' . apbd_fd($data->tgl_keluar) . '</em>';
		}	
		$rows[]=array(
			array('data' =>'', 'width' => '30px','align'=>'right','style'=>'border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '20px','align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-top:0.3px solid black;'),
			array('data' => $setoran, 'width' => '260px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:0.3px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => '','colspan'=>'2', 'width' => '100px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:0.3px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($setor),'colspan'=>'2', 'width' => '100px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:0.3px solid black;border-right:1px solid black;font-size:80%'),
		);
		
		
	}
	$rows[]=array(
			array('data' =>'', 'width' => '30px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => 'TOTAL', 'width' => '40px','align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;font-weight:bold'),
			array('data' => '', 'width' => '240px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($totalmasuk),'colspan'=>'2', 'width' => '100px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold'),
			array('data' => apbd_fn($totalkeluar), 'colspan'=>'2','width' => '100px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold'),
		);
	//................................
	
	if($no==0){
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => 'Data Masih Kosong', 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
	}


	$totmasuksebelum = 0;
	$totkeluarsebelum = 0;
	
	if ($bulan>1) { 
		//masuk
		$results=db_query('select  sum(jumlahmasuk) as jumlahm from setor where kodeuk=:kodeuk and kodero=:kodero and month(tanggal)<:bulan',array(':bulan'=>$bulan, ':kodero'=>$kodero,':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$totmasuksebelum = $data->jumlahm;
		}
		//keluar
		$results=db_query('select  sum(jumlahkeluar) as jumlahk from setor where kodeuk=:kodeuk and kodero=:kodero and month(tanggal)<:bulan',array(':bulan'=>$bulan, ':kodero'=>$kodero,':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$totkeluarsebelum = $data->jumlahk;
		}
	}
		
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-none'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-none'),
			array('data' => 'Jumlah bulan ini', 'width' => '240px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-none'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-none'),
			array('data' => 'Jumlah s/d bulan lalu', 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totmasuksebelum), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totkeluarsebelum), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => 'Jumlah s/d bulan ini '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019', 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk+$totmasuksebelum), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar+$totkeluarsebelum), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => 'Sisa','colspan'=>'2', 'width' => '240px','align'=>'cen','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn(($totalmasuk+$totmasuksebelum)-($totalkeluar+$totkeluarsebelum)), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	//................................
	
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => ucwords(strtolower('BENDAHARA khusus penerima')),'colspan'=>'4','width' => '170px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => $bendaharanama,'colspan'=>'4','width' => '170px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.'.$bendaharanip,'colspan'=>'4','width' => '170px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	 
	$output .= createT($header,$rows,null);
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
