<?php
function akuntansi_buku_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
		$ntitle = 'Buku Besar';
		$tahun = arg(2);
		
		$kodekeg = arg(3);
		$kodero = arg(4);
		$kodeuk = arg(5);
				

	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$kodekeg = '##';
		$kodeuk = '##';
		
	}
	if ($kodeuk=='') $kodeuk='##';
	
	drupal_set_title($ntitle);
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	if ($kodeuk=='##') {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'field'=> 'tanggal', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'SKPD','field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'No. Bukti', 'valign'=>'top'),
			array('data' => 'No. Ref', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Debet', 'field'=> 'debet','width' => '90px', 'valign'=>'top'),
			array('data' => 'Kredit', 'field'=> 'kredit','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		);	
	} else {	
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'field'=> 'tanggal', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'No. Bukti', 'valign'=>'top'),
			array('data' => 'No. Ref', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Debet', 'field'=> 'debet','width' => '90px', 'valign'=>'top'),
			array('data' => 'Kredit', 'field'=> 'kredit','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		);
	}		
		
	
	
	//$tahun='2015';
	$query = db_select('jurnalitem' . $tahun, 'ji')->extend('TableSort');
	$query->innerJoin('jurnal' . $tahun, 'j', 'ji.jurnalid=j.jurnalid');
	$query->innerJoin('unitkerja' . $tahun, 'u', 'j.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatan' . $tahun, 'k', 'j.kodekeg=k.kodekeg');
	
	$query->fields('ji', array('kodero','debet','kredit'));
	$query->fields('j', array('jurnalid', 'kodekeg', 'dokid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
	$query->fields('k', array('kegiatan'));
	$query->fields('u', array('namasingkat'));
	
	if ($kodeuk !='##') $query->condition('j.kodeuk', $kodeuk, '=');
	if ($kodekeg !='##') $query->condition('j.kodekeg', $kodekeg, '=');
	if ($kodero !='##') $query->condition('ji.kodero', $kodero, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('j.tanggal', 'ASC');
	//$query->limit($limit);
	//drupal_set_message($ne);	
	//drupal_set_message($query);	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	
	$totalkdebet=0;$totalkredit=0;		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else {
			if ($data->nobuktilain!='') $nobukti .= '/' . $data->nobuktilain;
		}
		
		$editlink = apbd_button_jurnal('akuntansi/edit/'.$tahun.'/'.$data->jurnalid);

		if ($data->dokid!='') {
			$editlink .= apbd_button_sp2d('penata/edit/'.$tahun.'/'.$data->dokid);
		}		
		
		$kegiatan = '';
		if ($data->kegiatan!='') {
			$kegiatan = l($data->kegiatan , 'belanja/rekening/' . $tahun . '/' . $data->kodekeg , array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));
		}

		if ($kodeuk=='##') {
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keterangan . $kegiatan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $nobukti, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->noref, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		} else {
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keterangan . $kegiatan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $nobukti, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->noref, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		}
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	if ($kodeuk=='##') {
		$rows[] = array(
							array('data' => '<strong>TOTAL</strong>', 'colspan'=>'6', 'align' => 'center', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
		if ($totalkdebet>=$totalkredit)
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'6', 'align' => 'center', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkdebet-$totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);
		else
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'6', 'align' => 'center', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkredit-$totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);
			
			
	} else {
		$rows[] = array(
							array('data' => '<strong>TOTAL</strong>', 'colspan'=>'5', 'align' => 'center', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
						
		if ($totalkdebet>=$totalkredit)
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'5', 'align' => 'center', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkdebet-$totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);
		else
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'5', 'align' => 'center', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkredit-$totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);						
	}
	
	$btn = apbd_button_print('/akuntansi/buku/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('');	
	
	if(arg(6)=='pdf'){
			  
			  $output = getTable($tahun,$kodeuk,$kodekeg,$kodero);
			  print_pdf_p($output);
				
		}
	else{
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('akuntansi_buku_main_form');
		return drupal_render($output_form).$btn . $output . $btn;	
	}
	
}

function getTable($tahun,$kodeuk,$kodekeg,$kodero){
	
	$styleheader='border:1px solid black;font-size:75%';
	$style='border-left:1px solid black;border-right:1px solid black;font-size:75%';
	if (substr($kodero,0,1)=='5') {
		
		if ($kodekeg=='##') {		//SELURUH KEGIATAN
			$kegiatan = 'SELURUH KEGIATAN';
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('kegiatanrekening' . $tahun, 'ki');
			$query->fields('ki', array('kodero','uraian'));
			$query->addExpression('SUM(ki.anggaran2)', 'anggaran');
			$query->condition('ki.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {
			$query = db_select('kegiatanrekening' . $tahun, 'ki');
			$query->innerJoin('kegiatan' . $tahun, 'k', 'ki.kodekeg=k.kodekeg');
			$query->fields('ki', array('kodero','uraian', 'anggaran2'));
			$query->fields('k', array('kodeuk','kegiatan'));
			$query->condition('k.kodekeg', $kodekeg, '=');
			$query->condition('ki.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran2;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4')
				$kegiatan = 'PENDAPATAN';
			else 
				$kegiatan = 'PEMBIAYAAN';
			
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('apbdrekap' . $tahun, 'a');
			$query->fields('a', array('koderincian','namarincian'));
			$query->addExpression('SUM(a.anggaran2)', 'anggaran');
			$query->condition('a.koderincian', $kodero, '=');
			if ($kodeuk !='##') $query->condition('a.kodeskpd', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->koderincian . ' - ' . $data->namarincian;
				$anggaran= $data->anggaran;
				
			}
		
	} else {		//NON APBD
		$kegiatan = 'NON ANGGARAN';
		$rekening= $data->kodero;
		$anggaran = 0;
	}
	
	if ($kodeuk=='##') 
		$namauk= 'SELURUH SKPD';
	else {
		$query = db_select('unitkerja' . $tahun, 'u');
		$query->fields('u', array('namauk'));
		$query->condition('u.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$namauk= $data->namauk;
		}		
	}
	$top=array();
	$top[] = array(
						array('data' => 'SKPD','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:75%'),
						array('data' => $namauk,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						
	);
	$top[] = array(
						array('data' => 'Kegiatan','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:75%'),
						array('data' => $kegiatan,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						
	);
	$top[] = array(
						array('data' => 'Rekening','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:75%'),
						array('data' => $rekening,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						
	);
	$top[] = array(
						array('data' => 'Anggaran','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:75%'),
						array('data' => apbd_fn($anggaran),'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:75%'),
						
	);
	$header = array ();
	$output = theme('table', array('header' => $header, 'rows' => $top ));
	
	
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'Uraian', 'width' => '140px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'No. Bukti', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'No. Ref', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '90', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '90','align'=>'center','style'=>$styleheader),
		
		
		
	);
	//$tahun='2015';
	$query = db_select('jurnalitem' . $tahun, 'ji')->extend('TableSort');
	$query->innerJoin('jurnal' . $tahun, 'j', 'ji.jurnalid=j.jurnalid');
	//$query->leftJoin('kegiatan' . $tahun, 'k', 'j.kodekeg=k.kodekeg');
	$query->fields('ji', array('kodero','debet','kredit'));
	$query->fields('j', array('jurnalid', 'dokid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
	//$query->fields('k', array('kegiatan'));
	
	if ($kodeuk !='##') $query->condition('j.kodeuk', $kodeuk, '=');
	if ($kodekeg !='##') $query->condition('j.kodekeg', $kodekeg, '=');
	if ($kodero !='##') $query->condition('ji.kodero', $kodero, '=');
	
	//$query->orderByHeader($header);
	$query->orderBy('j.tanggal', 'ASC');
	//$query->limit($limit);
	//drupal_set_message($ne);	
	
	//drupal_set_message($query);	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;


	
	$totalkdebet=0;$totalkredit=0;		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else {
			if ($data->nobuktilain!='') $nobukti .= '/' . $data->nobuktilain;
		} 
		
		$kegiatan = '';
		if ($data->kegiatan!='') {
			$kegiatan = '<p>' . $data->kegiatan . '</p>';
		}
		
		$rows[] = array(
						array('data' => $no,'width' => '40px', 'align'=>'right','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:75%'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),'width' => '80px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:75%'),
						array('data' => $data->keterangan . $kegiatan,'width' => '140px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:75%'),
						array('data' => $nobukti,'width' => '80px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:75%'),
						array('data' => $data->noref,'width' => '80px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:75%'),
						array('data' => apbd_fn($data->debet),'width' => '90px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:75%'),
						array('data' => apbd_fn($data->kredit),'width' => '90px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:75%'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
		);
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	$rows[] = array(
						array('data' => 'JUMLAH', 'width' => '420px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
						array('data' => apbd_fn($totalkdebet), 'width' => '90px','colspan'=>'3', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
						array('data' => apbd_fn($totalkredit), 'width' => '90px','colspan'=>'3', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
	);
	
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	//$output = 'cek';
	return $output;
}

 

function akuntansi_buku_main_form($form, &$form_state) {

	$tahun = arg(2);
	
	$kodekeg = arg(3);
	$kodero = arg(4);
	$kodeuk = arg(5);
					
	if (!isset($kodeuk)) $kodeuk='##';
	
	
	if (substr($kodero,0,1)=='5') {
		
		if ($kodekeg=='##') {		//SELURUH KEGIATAN
			$kegiatan = 'SELURUH KEGIATAN';
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('kegiatanrekening' . $tahun, 'ki');
			$query->fields('ki', array('kodero','uraian'));
			$query->addExpression('SUM(ki.anggaran2)', 'anggaran');
			$query->condition('ki.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {
			$query = db_select('kegiatanrekening' . $tahun, 'ki');
			$query->innerJoin('kegiatan' . $tahun, 'k', 'ki.kodekeg=k.kodekeg');
			$query->fields('ki', array('kodero','uraian', 'anggaran2'));
			$query->fields('k', array('kodeuk','kegiatan'));
			$query->condition('k.kodekeg', $kodekeg, '=');
			$query->condition('ki.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran2;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4')
				$kegiatan = 'PENDAPATAN';
			else 
				$kegiatan = 'PEMBIAYAAN';
			
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('apbdrekap' . $tahun, 'a');
			$query->fields('a', array('koderincian','namarincian'));
			$query->addExpression('SUM(a.anggaran2)', 'anggaran');
			$query->condition('a.koderincian', $kodero, '=');
			if ($kodeuk !='##') $query->condition('a.kodeskpd', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->koderincian . ' - ' . $data->namarincian;
				$anggaran= $data->anggaran;
				
			}
		
	} else {		//NON APBD
		$kegiatan = 'NON ANGGARAN';
		$rekening= $data->kodero;
		$anggaran = 0;
	}
	
	if ($kodeuk=='##') 
		$namauk= 'SELURUH SKPD';
	else {
		$query = db_select('unitkerja' . $tahun, 'u');
		$query->fields('u', array('namauk'));
		$query->condition('u.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$namauk= $data->namauk;
		}		
	}	
	$form['skpd'] = array(
		'#type' => 'item',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $namauk . '</p>',
	);
	$form['keg'] = array(
		'#type' => 'item',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $kegiatan . '</p>',
	);
	$form['rekening'] = array(
		'#type' => 'item',
		'#title' =>  t('Rekening'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $rekening . '</p>',
	);
	$form['anggaran'] = array(
		'#type' => 'item',
		'#title' =>  t('Anggaran'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p class="text-right">' . apbd_fn($anggaran) . '</p>',
		//'#markup' => '<p>' . apbd_fn($anggaran) . '</p>',
	);
	
	$kodeakun = substr($kodero,0,1);
	if (($kodeakun =='4') or ($kodeakun =='5') or ($kodeakun =='6')) {
		$form['chart'] = array(
			'#type' => 'item',
			'#title' =>  t('Anggaran'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			'#markup' =>  draw_chart_buku_besar($tahun, $kodeuk, $kodekeg, $kodero),
		);
	}
	
	return $form;
}



 


?>
