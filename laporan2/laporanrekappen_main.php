<?php
function laporanrekappen_main($arg=NULL, $nama=NULL) {
	if ($arg) {
		$kodeuk = arg(1);
		$bulan = arg(2);
		$margin = arg(3);
		$tanggal = arg(4);
		
	} else {
		$kodeuk = apbd_getuseruk();
		$bulan = date('n');
		$margin = '10';
		$tanggal = date('j F Y');
		
	}
	
	drupal_set_title('Rekap Penerimaan');
	
	
	if (arg(5) == 'pdf'){
		
		$output = getLaporan($kodeuk, $bulan, $tanggal);
		apbd_ExportPDFm($margin,'L', 'F4', $output, 'CEK');
		
	} else if (arg(5) == 'excel'){
		$output = getLaporan($kodeuk, $bulan, $tanggal);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Rekap Penerimaan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	
	} else {
		
		//drupal_goto('laporanrekappen/' . $kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal);
		$output_form = drupal_get_form('laporanrekappen_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak</span>', 'laporanrekappen/'.$kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/pdf', array ('html' => true));
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Excel</span>', 'laporanrekappen/'.$kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/excel', array ('html' => true));
		
		$output = getLaporan($kodeuk, $bulan, $tanggal);
		
		//$output2 footer();
		//apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
		//print_pdf_p($output,$output2);
		return drupal_render($output_form) . $output;
	}
	
}
function laporanrekappen_main_form($form, &$form_state) {


	if(arg(1)!=null){
		$kodeuk = arg(1);
		$bulan = arg(2);
		$margin = arg(3);
		$tanggal = arg(4);
		
	} else {
		$kodeuk = apbd_getuseruk();
		$bulan = date('n');
		$margin = '10';
		$tanggal = date('j F Y');
		
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
	
function laporanrekappen_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$tanggal = $form_state['values']['tanggal'];
	
	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['cetak']) {
		drupal_goto('laporanrekappen/' . $kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal);
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']) {
		drupal_goto('laporanrekappen/'.$kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/pdf');
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']) {
		drupal_goto('laporanrekappen/'.$kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/excel');		
	}
}




function getLaporan($kodeuk, $bulan, $tanggal){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'REKAPITULASI PENERIMAAN', 'colspan' => '8', 'colspan' => '8', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:100%;'),
	);
	$tamp_bulan=array("JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER");
	$rows[]=array(
		array('data' => 'BULAN '.$tamp_bulan[$bulan-1].' 2019',  'colspan' => '8', 'colspan' => '8', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:100%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'colspan' => '8',  'colspan' => '8', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '',  'colspan' => '8', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	); 
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output = createT($header,$rows,null);
	
	$rows = null;
	$header[]=array(
		array('data' => 'NO',  'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:90%;font-weight:bold;'),
		array('data' => 'KODE',   'width' => '65px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:90%;border-right:1px solid black;font-weight:bold;'),
		array('data' => 'JENIS PENERIMAAN',   'width' => '340px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:90%;border-right:1px solid black;font-weight:bold;'),
		array('data' => 'ANGGARAN',  'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-size:90%;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' => 'S/D BULAN LALU',  'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:90%;border-bottom:1px solid black;font-weight:bold;'),
		array('data' => 'BULAN INI',  'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:90%;border-left:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' => 'S/D BULAN INI',  'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:90%;border-bottom:1px solid black;font-weight:bold;'),
		array('data' => 'PRSN',  'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;font-size:90%;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	);
	
	
	$no = 0;
	
	$agg_total = 0;
	$rea_total_ini = 0;
	$rea_total_lalu = 0;

	//CONTENT
	$res_jenis 	= db_query("select kodej, uraian from jenis where kodej in ('411', '412', '414') order by kodej");
	foreach($res_jenis as $data_jenis)  {
		$agg_jenis = 0;
		$rea_jenis_ini = 0;
		$rea_jenis_lalu = 0;

		$res_agg = db_query("select r.kodero,r.uraian, sum(a.anggaran) as anggaran from anggperuk as a inner join rincianobyek as r on a.kodero=r.kodero where a.kodeuk=:kodeuk and a.kodero like :kodej group by r.kodero,r.uraian order by a.kodero", array(':kodeuk'=>$kodeuk, ':kodej'=>$data_jenis->kodej . '%'));
		
		foreach($res_agg as $data_agg)  {
			
			//rea lalu
			$realisasi_lalu = 0;
			if ($bulan>1) {
				$res = db_query('select sum(jumlahmasuk) as jumlah from setor where kodero like :kodero and kodeuk=:kodeuk and month(tanggal)<:bulan', array(':kodero'=>$data_agg->kodero . '%',':kodeuk'=>$kodeuk,':bulan'=>$bulan));
				foreach($res as $dat)  {
					$realisasi_lalu = $dat->jumlah;
				}	
			}
			//drupal_set_message($data_agg->kodero);
			//rea skrg
			$realisasi_ini = 0;
			$res = db_query('select sum(jumlahmasuk) as jumlah from setor where kodero like :kodero and kodeuk=:kodeuk and month(tanggal)=:bulan', array(':kodero'=>$data_agg->kodero . '%',':kodeuk'=>$kodeuk,':bulan'=>$bulan));
			foreach($res as $dat)  {
				$realisasi_ini = $dat->jumlah;
			}	
			
			if (strlen($data_agg->kodero)==8) {
				$agg_jenis += $data_agg->anggaran;
				$rea_jenis_ini += $realisasi_ini;
				$rea_jenis_lalu += $realisasi_lalu;

				$agg_total += $data_agg->anggaran;
				$rea_total_ini += $realisasi_ini;
				$rea_total_lalu += $realisasi_lalu;
				
				$no++;
				$rows[]=array(
					array('data' => $no,  'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;border-left:1px solid black;'),
					array('data' => $data_agg->kodero,  'width' => '65px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => $data_agg->uraian,  'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => apbd_fn($data_agg->anggaran), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					//array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => apbd_fn($realisasi_lalu),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
					array('data' => apbd_fn($realisasi_ini),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => apbd_fn($realisasi_lalu+$realisasi_ini),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),					
					array('data' => apbd_fn2(apbd_hitungpersen($data_agg->anggaran, $realisasi_lalu+$realisasi_ini)),  'width' => '40px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					
				);			
				
			} else {
			
				$rows[]=array(
					array('data' => '',  'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;border-left:1px solid black;'),
					array('data' => '<em>'. $data_agg->kodero . '</em>',  'width' => '65px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => '<em>'. $data_agg->uraian . '</em>',  'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => '<em>'. apbd_fn($data_agg->anggaran) . '</em>', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => '<em>'. apbd_fn($realisasi_lalu) . '</em>',  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
					array('data' => '<em>'. apbd_fn($realisasi_ini) . '</em>',  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					array('data' => '<em>'. apbd_fn($realisasi_lalu+$realisasi_ini) . '</em>',  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),					
					array('data' => '<em>'. apbd_fn2(apbd_hitungpersen($data_agg->anggaran, $realisasi_lalu+$realisasi_ini)) . '</em>',  'width' => '40px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
					
				);			
			}	
		}		
		
		//JENIS
		if (($agg_jenis+$rea_jenis_lalu+$rea_jenis_ini)>0) {
			$rows[]=array(
				array('data' => '',  'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;border-left:1px solid black;border-top:1px solid black;'),
				array('data' => '',  'width' => '65px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),
				array('data' => 'JUMLAH ' . $data_jenis->uraian,  'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),
				array('data' => apbd_fn($agg_jenis), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),
				array('data' => apbd_fn($rea_jenis_lalu),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;border-top:1px solid black;'),
				array('data' => apbd_fn($rea_jenis_ini),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),
				array('data' => apbd_fn($rea_jenis_lalu+$rea_jenis_ini),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),					
				array('data' => apbd_fn2(apbd_hitungpersen($agg_jenis, $rea_jenis_lalu+$rea_jenis_ini)),  'width' => '40px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),
				
			);		
			$rows[]=array(
				array('data' => '',  'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:50%;border-left:1px solid black;'),
				array('data' => '',  'width' => '65px','align'=>'left','style'=>'border-right:1px solid black;font-size:50%;'),
				array('data' => '',  'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;font-size:50%;'),
				array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:50%;'),
				array('data' => '',  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:50%;'),
				array('data' => '',  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:50%;'),
				array('data' => '',  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:50%;'),
				array('data' => '',  'width' => '40px','align'=>'right','style'=>'border-right:1px solid black;font-size:50%;'),
				
			);			
		}
	}	
	$rows[]=array(
		array('data' => '',  'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '',  'width' => '65px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;'),
		array('data' => 'TOTAL',  'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;font-weight:bold;'),
		array('data' => apbd_fn($agg_total), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;font-weight:bold;'),
		array('data' => apbd_fn($rea_total_lalu),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;border-top:1px solid black;font-weight:bold;'),
		array('data' => apbd_fn($rea_total_ini),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;font-weight:bold;'),
		array('data' => apbd_fn($rea_total_lalu+$rea_total_ini),  'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;font-weight:bold;'),					
		array('data' => apbd_fn2(apbd_hitungpersen($agg_total, $rea_total_lalu+$rea_total_ini)),  'width' => '40px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;border-top:1px solid black;font-weight:bold;'),
		
	);			
	
	$rows[]=array(
		array('data' => '',  'colspan' => '8', 'width' => '875px','align'=>'center','style'=>'border-top:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '',  'colspan' => '8', 'width' => '875px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '',  'colspan' => '8', 'width' => '875px','align'=>'center','style'=>'border:none;'),
	);

	$results=db_query("select pimpinannama,pimpinannip,pimpinanjabatan,bendaharanama,bendaharanip from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
		foreach($results as $datas){
			$pimpinannama=$datas->pimpinannama;
			$pimpinanjabatan=$datas->pimpinanjabatan;
			$pimpinannip=$datas->pimpinannip;
			$bendaharanama=$datas->bendaharanama;
			$bendaharanip=$datas->bendaharanip;
		}
	
	$rows[] = array(
					array('data' => 'Mengetahui', 'colspan' => '7', 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'Jepara, '.$tanggal, 'colspan' => '7', 'width' => '440px', 'align'=>'center','style'=>'font-size:90%;border:none;'),
	);
	$rows[] = array(
					array('data' => ucwords(strtolower($pimpinanjabatan)), 'colspan' => '7', 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'Bendahara Penerimaan', 'colspan' => '7', 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan' => '7', 'width' => '670px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan' => '7', 'width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '', 'colspan' => '7', 'width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => $pimpinannama, 'colspan' => '7', 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => $bendaharanama, 'colspan' => '7', 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					
	);
	$rows[] = array(
					array('data' => 'NIP.'.$pimpinannip, 'colspan' => '7', 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
					array('data' => 'NIP.'.$bendaharanip, 'colspan' => '7', 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	
	//$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= createT($header,$rows,null);
		return $output;
}






?>
