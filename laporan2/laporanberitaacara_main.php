<?php
function laporanberitaacara_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 140px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
		$kodeuk = apbd_getuseruk();
		$tw = arg(2);
		$margin = arg(3);
		$nomor = arg(4);
		$tanggal = arg(5);
		
	} else {
		$tw = '0';
		$kodeuk = apbd_getuseruk();
		$margin = '10';
		$nomor = '. . . . . . . . . . . . . . .';
		$tanggal = date('d F y');
		
	}
	
	//drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	if (arg(6) == 'pdf'){
		$output = getlaporanberitaacara($kodeuk, $tw, $nomor, $tanggal);
		apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
	} else if (arg(6) == 'excel'){
		$output = getlaporanberitaacara($kodeuk, $tw, $nomor, $tanggal);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Berita Acara.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	
	} else {
		$output_form = drupal_get_form('laporanberitaacara_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak Pdf</span>', 'laporanBA/'.arg(1).'/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/pdf', array ('html' => true));
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak Excel</span>', 'laporanBA/'.arg(1).'/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/excel', array ('html' => true));
		$output = getlaporanberitaacarahtml($kodeuk, $tw, $nomor, $tanggal);
		return drupal_render($output_form) . $btn . $output;
	}
	
	
}
function laporanberitaacara_main_form($form, &$form_state) {

	$form['formdokumen']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);
	$form['formdokumen'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Laporan Buku Kas',
		//'#field_prefix' => _bootstrap_icon('envelope'),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,  	
	);
	if(isSuperuser()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		//$option_uk[0]='Semua';
		foreach($res as $data){
			$option_uk[$data->kodeuk]=$data->namasingkat;
		}
		$form['formdokumen']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);
	}		
	$triwulan=array('I','II','III','IV');
	$form['formdokumen']['triwulan']= array(
		'#type' => 'select',
		'#title'=>'Triwulan',
		'#options' => $triwulan,
	);	
	$form['formdokumen']['nomor']= array(
		'#type' => 'textfield',
		'#title'=>'Nomor Berita Acara',
		'#default_value' => '',
	);	
	
	$hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
	$form['formdokumen']['tanggal']= array(
		'#type' => 'textfield',
		'#title'=>'Tanggal',
		'#default_value' => $hari[date('w')] . ', ' . date('d F Y'),
	);	
	$form['formdokumen']['margin']= array(
		'#type' => 'textfield',
		'#title'=>'Margin',
		'#default_value' => 10,
	);	
	$form['formdokumen']['cetak']= array(
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}
	
function laporanberitaacara_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$triwulan = $form_state['values']['triwulan'];
	$margin = $form_state['values']['margin'];
	$nomor = $form_state['values']['nomor'];
	if ($nomor == '') $nomor = '. . . . . . . . . . . '; 
	$tanggal = $form_state['values']['tanggal'];

	drupal_goto('laporanBA/'. $kodeuk . '/' . $triwulan . '/'. $margin . '/' . $nomor . '/' . $tanggal);

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

function getlaporanberitaacara($kodeuk, $tw, $nomor, $tanggal){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	

	$result=db_query("select namauk,namasingkat,bendaharanama,bendaharanip from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($result as $data){
		$namasingkat=$data->namauk;
		$namas=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}
	$header=array();
	if($tw==0){
		$month=array(1,2,3);
		$triwulan='I';
	}
	else if($tw==1){
		$month=array(1,2,3,4,5,6);
		$triwulan='II';
	}
	else if($tw==2){
		$month=array(1,2,3,4,5,6,7,8,9);
		$triwulan='III';
	}
	else if($tw==3){
		$month=array(1,2,3,4,5,6,7,8,9,10,11,12);
		$triwulan='IV';
	}
	$rows[]=array(
		array('data' => 'BERITA ACARA REKONSILIASI', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:110%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $nomor, 'width' => '500px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => 'TENTANG', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => 'DATA PENERIMAAN '.strtoupper($namasingkat), 'width' => '500px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	//$bulantw=array("April","Juli","Oktober","Januari");
	$rows[]=array(
array('data' => 'Pada hari/tanggal ' . $tanggal . ' bertempat di BPKAD Kab. Jepara telah dilaksanakan. Rekonsiliasi Data Penerimaan Pendapatan sampai Triwulan '.$triwulan.' antara BPKAD Dan '.ucwords(strtolower($namasingkat)).' dimana dalam rekonsiliasi dimaksud telah dicapai kesepakatan kedua belah pihak antara lain:', 'width' => '500px','align'=>'justify','style'=>'border:none;font-size:80%'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%'),
	);
	$rows[]=array(
		array('data' => 'NO', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
		array('data' => 'URAIAN', 'width' => '280px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'ANGGARAN', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'REALISASI', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '%', 'width' => '35px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:70%;'),
	);
	
	
	//$result=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  kodeuk=:kodeuk and s.kodero=ro.kodero) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where kodeuk=:kodeuk  and month(s.tanggal) in (:month)",array(':kodeuk'=>$kodeuk,':month'=>$month));
	$where="";
	if($kodeuk=='81')
		$where="";
	else
		$where=" or  a.kodero like '414%'";
	if(isPusekesmas()){
		$where.=" or  a.kodero like '416%'";
	}
	
	$result=db_query("select a.kodero,ro.uraian, a.anggaran from anggperuk as a inner join rincianobyek as ro on a.kodero=ro.kodero where a.kodeuk=:kodeuk and (a.kodero like '411%' or a.kodero like '412%' ".$where." ) order by a.kodero",array(':kodeuk'=>$kodeuk));
	//Content
	$no=0;
	
	$agg_total = 0; $rea_total = 0; 
	$anggblud=0;
	foreach($result as $data){
		

		if(isPusekesmas()){
			
			if(isBlud()){
				if($data->kodero=='41416002'){
				}
				else{
					$no++;	
					$realisasi=0;
					$results=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  s.kodeuk=:kodeuk and s.kodero=ro.kodero  and month(s.tanggal) in (:month)) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where s.kodeuk=:kodeuk  and month(s.tanggal) in (:month) and s.kodero=:kodero",array(':kodeuk'=>$kodeuk,':month'=>$month,':kodero'=>$data->kodero));
					foreach($results as $datauk){
						$realisasi=$datauk->juml;
						
					}
					$rows[]=array(
						array('data' => $no.'. ', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
						array('data' => $data->uraian, 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;'),
						array('data' => apbd_fn($data->anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
						array('data' => apbd_fn($realisasi), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran,$realisasi)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
					);
					//$realisasi=0;
					$anggblud+=$data->anggaran;
				}
			}
			else{
				$no++;	
					$realisasi=0;
					$results=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  s.kodeuk=:kodeuk and s.kodero=ro.kodero  and month(s.tanggal) in (:month)) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where s.kodeuk=:kodeuk  and month(s.tanggal) in (:month) and s.kodero=:kodero",array(':kodeuk'=>$kodeuk,':month'=>$month,':kodero'=>$data->kodero));
					foreach($results as $datauk){
						$realisasi=$datauk->juml;
						
					}
					$rows[]=array(
						array('data' => $no.'. ', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
						array('data' => $data->uraian, 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;'),
						array('data' => apbd_fn($data->anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
						array('data' => apbd_fn($realisasi), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran,$realisasi)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
					);
			}
		}else{
			$no++;	
			$realisasi=0;
					$results=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  s.kodeuk=:kodeuk and s.kodero=ro.kodero  and month(s.tanggal) in (:month)) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where s.kodeuk=:kodeuk  and month(s.tanggal) in (:month) and s.kodero=:kodero",array(':kodeuk'=>$kodeuk,':month'=>$month,':kodero'=>$data->kodero));
					foreach($results as $datauk){
						$realisasi=$datauk->juml;
						
					}
					
			$rows[]=array(
				array('data' => $no.'. ', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
				array('data' => $data->uraian, 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;'),
				array('data' => apbd_fn($data->anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
				array('data' => apbd_fn($realisasi), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran,$realisasi)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			);
		}
				
			
		$agg_total += $data->anggaran;
		$rea_total += $realisasi;
		
	}
	if($no==0){
		$rows[]=array(
				array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
				array('data' => 'Data Masih Kosong', 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;'),
				array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
				array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
				array('data' => '', 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			);
	}
	if(isBlud()){
		$agg_total=$anggblud;
	}
	$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;font-size:70%;'),
			array('data' => 'TOTAL', 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:70%;'),
			array('data' => apbd_fn($agg_total), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:70%;'),
			array('data' => apbd_fn($rea_total), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:70%;'),
			array('data' => apbd_fn1(apbd_hitungpersen($agg_total,$rea_total)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:70%;'),
		);
		
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border-top:2px solid black;'),
	);
	$rows[] = array(
					array('data' => 'Sebagai mana data terlampir,','width' => '670px', 'align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'Demikian Berita Acara ini dibuat untuk dipergunakan sebagaimana mestinya.','width' => '510px', 'align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Kepala Sub Bidang Pelaporan','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'Bendahara Penerima','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'Retribusi dan Dana Transfer','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => strtoupper($namas).' KAB. JEPARA','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'Kabupaten Jepara','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
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
					array('data' => 'S.KENDAR PRAPTOMO,SH.MM.','width' => '250px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;font-size:90%;'),
					array('data' => $bendaharanama,'width' => '250px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'NIP. 19711216 199703 1 005','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

function getlaporanberitaacarahtml($kodeuk, $tw, $nomor, $tanggal){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	

	$result=db_query("select namauk,namasingkat,bendaharanama,bendaharanip from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($result as $data){
		$namasingkat=$data->namauk;
		$namas=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}
	$header=array();
	if($tw==0){
		$month=array(1,2,3);
		$triwulan='I';
	}
	else if($tw==1){
		$month=array(1,2,3,4,5,6);
		$triwulan='II';
	}
	else if($tw==2){
		$month=array(1,2,3,4,5,6,7,8,9);
		$triwulan='III';
	}
	else if($tw==3){
		$month=array(1,2,3,4,5,6,7,8,9,10,11,12);
		$triwulan='IV';
	}
	$rows[]=array(
		array('data' => 'BERITA ACARA REKONSILIASI1', 'colspan' => '5', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $nomor,  'colspan' => '5','width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'TENTANG',  'colspan' => '5','width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'DATA PENERIMAAN '.strtoupper($namasingkat), 'colspan' => '5', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'colspan' => '5', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	//$bulantw=array("April","Juli","Oktober","Januari");
	$rows[]=array(
array('data' => 'Pada hari/tanggal ' . $tanggal . ' bertempat di BPKAD Kab. Jepara telah dilaksanakan. Rekonsiliasi Data Penerimaan Pendapatan sampai Triwulan '.$triwulan.' antara BPKAD Dan '.ucwords(strtolower($namasingkat)).' dimana dalam rekonsiliasi dimaksud telah dicapai kesepakatan kedua belah pihak antara lain:', 'colspan' => '5','width' => '500px','align'=>'justify','style'=>'border:none;font-size:90%'),
	);
	$rows[]=array(
		array('data' => '',  'colspan' => '5','width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%'),
	);
	$rows[]=array(
		array('data' => 'NO', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
		array('data' => 'URAIAN', 'width' => '1200px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'ANGGARAN', 'width' => '300px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'REALISASI', 'width' => '300px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => '%', 'width' => '35px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-size:80%;'),
	);
	
	
	//$result=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  kodeuk=:kodeuk and s.kodero=ro.kodero) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where kodeuk=:kodeuk  and month(s.tanggal) in (:month)",array(':kodeuk'=>$kodeuk,':month'=>$month));
	$where="";
	if($kodeuk=='81')
		$where="";
	else
		$where=" or  a.kodero like '414%'";
	if(isPusekesmas()){
		$where.=" or  a.kodero like '416%'";
	}
	
	$result=db_query("select a.kodero,ro.uraian, a.anggaran from anggperuk as a inner join rincianobyek as ro on a.kodero=ro.kodero where a.kodeuk=:kodeuk and (a.kodero like '411%' or a.kodero like '412%' ".$where." ) order by a.kodero",array(':kodeuk'=>$kodeuk));
	//Content
	$no=0;
	
	$agg_total = 0; $rea_total = 0; 
	$anggblud=0;
	foreach($result as $data){
		

		if(isPusekesmas()){
			
			if(isBlud()){
				if($data->kodero=='41416002'){
				}
				else{
					$no++;	
					$realisasi=0;
					$results=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  s.kodeuk=:kodeuk and s.kodero=ro.kodero  and month(s.tanggal) in (:month)) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where s.kodeuk=:kodeuk  and month(s.tanggal) in (:month) and s.kodero=:kodero",array(':kodeuk'=>$kodeuk,':month'=>$month,':kodero'=>$data->kodero));
					foreach($results as $datauk){
						$realisasi=$datauk->juml;
						
					}
					$rows[]=array(
						array('data' => $no.'. ', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
						array('data' => $data->uraian, 'width' => '1200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
						array('data' => apbd_fn($data->anggaran), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
						array('data' => apbd_fn($realisasi), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran,$realisasi)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					);
					//$realisasi=0;
					$anggblud+=$data->anggaran;
				}
			}
			else{
				$no++;	
					$realisasi=0;
					$results=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  s.kodeuk=:kodeuk and s.kodero=ro.kodero  and month(s.tanggal) in (:month)) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where s.kodeuk=:kodeuk  and month(s.tanggal) in (:month) and s.kodero=:kodero",array(':kodeuk'=>$kodeuk,':month'=>$month,':kodero'=>$data->kodero));
					foreach($results as $datauk){
						$realisasi=$datauk->juml;
						
					}
					$rows[]=array(
						array('data' => $no.'. ', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
						array('data' => $data->uraian, 'width' => '1200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
						array('data' => apbd_fn($data->anggaran), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
						array('data' => apbd_fn($realisasi), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran,$realisasi)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					);
			}
		}else{
			$no++;	
			$realisasi=0;
					$results=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  s.kodeuk=:kodeuk and s.kodero=ro.kodero  and month(s.tanggal) in (:month)) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where s.kodeuk=:kodeuk  and month(s.tanggal) in (:month) and s.kodero=:kodero",array(':kodeuk'=>$kodeuk,':month'=>$month,':kodero'=>$data->kodero));
					foreach($results as $datauk){
						$realisasi=$datauk->juml;
						
					}
					
			$rows[]=array(
				array('data' => $no.'. ', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				array('data' => $data->uraian, 'width' => '1200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($data->anggaran), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($realisasi), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran,$realisasi)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
		}
				
			
		$agg_total += $data->anggaran;
		$rea_total += $realisasi;
		
	}
	if($no==0){
		$rows[]=array(
				array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				array('data' => 'Data Masih Kosong', 'width' => '1200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => '', 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => '', 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => '', 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
	}
	if(isBlud()){
		$agg_total=$anggblud;
	}
	$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;font-size:80%;'),
			array('data' => 'TOTAL', 'width' => '1200px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:80%;'),
			array('data' => apbd_fn($agg_total), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:80%;'),
			array('data' => apbd_fn($rea_total), 'width' => '300px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:80%;'),
			array('data' => apbd_fn1(apbd_hitungpersen($agg_total,$rea_total)), 'width' => '35px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-weight:bold;font-size:80%;'),
		);
		
	$rows[]=array(
		array('data' => '', 'colspan' => '5', 'width' => '500px','align'=>'center','style'=>'border-top:2px solid black;'),
	);
	$rows[] = array(
					array('data' => 'Sebagai mana data terlampir,', 'colspan' => '5','width' => '670px', 'align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'Demikian Berita Acara ini dibuat untuk dipergunakan sebagaimana mestinya.', 'colspan' => '5','width' => '510px', 'align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
				array('data' => '', 'colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '', 'colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Kepala Sub Bidang Pelaporan', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'Bendahara Penerima', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'Retribusi dan Dana Transfer', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => strtoupper($namas).' KAB. JEPARA', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'Kabupaten Jepara', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => '', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '', 'colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '', 'colspan' => '5','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => 'S.KENDAR PRAPTOMO,SH.MM.', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;font-size:90%;'),
					array('data' => $bendaharanama, 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => 'NIP. 19711216 199703 1 005', 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'NIP.'.$bendaharanip, 'colspan' => '2','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
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
