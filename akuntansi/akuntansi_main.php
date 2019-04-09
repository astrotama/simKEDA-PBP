<?php
function akuntansi_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
				$tahun = arg(2);
				
				$kodeuk = arg(3);
				$bulan = arg(4);
				$kategori = arg(5);
				$keyword = arg(6);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$kodeuk = '##';
		$bulan = '0';
		$keyword = '';
		$kategori = '';
	}

	drupal_set_title('Akuntansi '  . $tahun);
	
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	$output_form = drupal_get_form('akuntansi_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'width' => '160px', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px', 'field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'No.Bukti','field'=> 'nobukti', 'valign'=>'top'),
		array('data' => '#Ref','field'=> 'noref', 'valign'=>'top'),
		array('data' => 'Keterangan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '90px',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('jurnal' . $tahun, 'k')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja' . $tahun, 'u', 'k.kodeuk=u.kodeuk');
	
	# get the desired fields from the database
	  
	$query->fields('k', array('jurnalid', 'dokid', 'kodeuk', 'nobukti','noref','tanggal', 'keterangan', 'total'));
	$query->fields('u', array('namasingkat'));
	if ($kodeuk !='##') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM k.tanggal) = :month', array('month' => $bulan));
	//KATEGORI
	if ($kategori!='') {
		$subquery = db_select('jurnalitem'. $tahun, 'ji');
		$subquery->fields('ji', array('jurnalid'));
		$subquery->condition('ji.uraian', '%' . db_like($keyword) . '%', 'LIKE');
		$query->condition('k.jurnalid', $subquery, 'IN');
		
	}
	//KEYWORD AND KATEGORI
	if ($keyword=='') {
		if ($kategori!='') {
			$subquery = db_select('jurnalitem'. $tahun, 'ji');
			$subquery->fields('ji', array('jurnalid'));
			if ($kategori!='') $subquery->condition('ji.kodero', db_like($kategori) . '%', 'LIKE');
			$query->condition('k.jurnalid', $subquery, 'IN');		
		}
		
	} else {
		$db_or = db_or();
		$db_or->condition('k.keterangan', '%' . db_like($keyword) . '%', 'LIKE');
		$db_or->condition('k.nobukti', '%' . db_like($keyword) . '%', 'LIKE');		
		$query->condition($db_or);
		
		$subquery = db_select('jurnalitem'. $tahun, 'ji');
		$subquery->fields('ji', array('jurnalid'));
		$subquery->condition('ji.uraian', '%' . db_like($keyword) . '%', 'LIKE');
		if ($kategori!='') $subquery->condition('ji.kodero', db_like($kategori) . '%', 'LIKE');
		$query->condition('k.jurnalid', $subquery, 'IN');
		
	}
	//$query->condition($field2, $value2, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.tanggal', 'ASC');
	$query->limit($limit);
	
	//drupal_set_message($query->__toString());
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	
		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$editlink = apbd_button_jurnal('akuntansi/edit/'.$tahun.'/'.$data->jurnalid);
		
		if ($kategori=='5')
			$editlink .= apbd_button_sp2d('penata/edit/'.$tahun.'/'.$data->dokid);
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->nobukti, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->noref, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}

	//BUTTON
	$btn = apbd_button_print('/akuntansi/filter/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/'.arg(6).'/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('');	

	
	if(arg(7)=='pdf'){
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output=getData($tahun,$kodeuk,$bulan,$kategori,$keyword);
		print_pdf_l($output);
	}
	else{
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}

function getData($tahun,$kodeuk,$bulan,$kategori,$keyword){
	
	$header = array (
		array('data' => 'No','height'=>'20px','width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'SKPD','height'=>'20px','width' => '160px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Tanggal','height'=>'20px','width' => '60px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'No.Bukti','height'=>'20px','width' => '80px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => '#Ref','height'=>'20px','width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Keterangan','height'=>'20px', 'width' => '335px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Jumlah','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		
		
	);
	

	$query = db_select('jurnal' . $tahun, 'k');//->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja' . $tahun, 'u', 'k.kodeuk=u.kodeuk');
	
	# get the desired fields from the database
	  
	$query->fields('k', array('jurnalid', 'dokid', 'kodeuk', 'nobukti','noref','tanggal', 'keterangan', 'total'));
	$query->fields('u', array('namasingkat'));
	if ($kodeuk !='##') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM k.tanggal) = :month', array('month' => $bulan));
	//KATEGORI
	if ($kategori!='') {
		$subquery = db_select('jurnalitem'. $tahun, 'ji');
		$subquery->fields('ji', array('jurnalid'));
		$subquery->condition('ji.uraian', '%' . db_like($keyword) . '%', 'LIKE');
		$query->condition('k.jurnalid', $subquery, 'IN');
		
	}
	//KEYWORD AND KATEGORI
	if ($keyword=='') {
		if ($kategori!='') {
			$subquery = db_select('jurnalitem'. $tahun, 'ji');
			$subquery->fields('ji', array('jurnalid'));
			if ($kategori!='') $subquery->condition('ji.kodero', db_like($kategori) . '%', 'LIKE');
			$query->condition('k.jurnalid', $subquery, 'IN');		
		}
		
	} else {
		$db_or = db_or();
		$db_or->condition('k.keterangan', '%' . db_like($keyword) . '%', 'LIKE');
		$db_or->condition('k.nobukti', '%' . db_like($keyword) . '%', 'LIKE');		
		$query->condition($db_or);
		
		$subquery = db_select('jurnalitem'. $tahun, 'ji');
		$subquery->fields('ji', array('jurnalid'));
		$subquery->condition('ji.uraian', '%' . db_like($keyword) . '%', 'LIKE');
		if ($kategori!='') $subquery->condition('ji.kodero', db_like($kategori) . '%', 'LIKE');
		$query->condition('k.jurnalid', $subquery, 'IN');
		
	}
	//$query->condition($field2, $value2, '=');
	//$query->orderByHeader($header);
	$query->orderBy('k.tanggal', 'ASC');
	//$query->limit($limit);
	
	//drupal_set_message($query->__toString());
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	

	
		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$editlink = apbd_button_jurnal('akuntansi/edit/'.$tahun.'/'.$data->jurnalid);
		
		if ($kategori=='5')
			$editlink .= apbd_button_sp2d('penata/edit/'.$tahun.'/'.$data->dokid);
		
		
		$rows[] = array(
						array('data' => $no,'width' => '30px', 'align' => 'center', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->namasingkat,'width' => '160px',  'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),'width' => '60px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;;'),
						array('data' => $data->nobukti,'width' => '80px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;;'),
						array('data' => $data->noref,'width' => '120px',  'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;;'),
						array('data' => $data->keterangan,'width' => '335px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;;'),
						array('data' => apbd_fn($data->total),'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;;'),
						
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		
	}
	$rows[] = array(
						array('data' => '','width' => '905px', 'align' => 'center', 'valign'=>'top','style'=>'border-top:1px solid black;'),
						
					);
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	
}
function akuntansi_main_form_submit($form, &$form_state) {
	$tahun= $form_state['values']['tahun'];
	$skpd = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	$kategori = $form_state['values']['kategori'];
	$keyword = $form_state['values']['keyword'];
	
	//drupal_set_message($row[2014][1]); 
	$kodeuk = '##';
	$query = db_select('unitkerja'.$tahun, 'p');
	$query->fields('p', array('namasingkat','kodeuk'))
		  ->condition('namasingkat',$skpd,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$kodeuk = $data->kodeuk;
		}
	}
	$uri = 'akuntansi/filter/' . $tahun.'/'.$kodeuk.'/'.$bulan . '/' . $kategori . '/' . $keyword;
	drupal_goto($uri);
	
}


function akuntansi_main_form($form, &$form_state) {
	
	$bulanoption=array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	
	$tahun = 2015;
	$kodeuk = '##';
	$bulan = '0';
	$namasingkat = 'SELURUH SKPD';
	$kategori = '';
	$keyword = '';
	if(arg(2)!=null){
		
		$tahun = arg(2);
		$kodeuk = arg(3);
		$bulan=arg(4);
		$kategori = arg(5);
		$keyword = arg(6);
	}
	if ($kodeuk!='##') {
		$query = db_select('unitkerja' . $tahun, 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk', $kodeuk, '=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat = $data->namasingkat;
			}
		}
	}			
	
	// Get the list of options to populate the first dropdown.
	$option_tahun = _ajax_get_tahun_dropdown();
	// If we have a value for the first dropdown from $form_state['values'] we use
	// this both as the default value for the first dropdown and also as a
	// parameter to pass to the function that retrieves the options for the
	// second dropdown.
  
	$selected_tahun = isset($form_state['values']['tahun']) ? $form_state['values']['tahun'] : $tahun;
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> '<p>' . $tahun . '|' . $namasingkat . '</p>' . '<p><em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em></p>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		$form['formdata']['tahun'] = array(
		'#type' => 'select',
		'#title' => 'Tahun',
		'#options' => $option_tahun,
		'#default_value' => $tahun,		//$selected,
		// Bind an ajax callback to the change event (which is the default for the
		// select form type) of the first dropdown. It will replace the second
		// dropdown when rebuilt.
		'#ajax' => array(
		  // When 'event' occurs, Drupal will perform an ajax request in the
		  // background. Usually the default value is sufficient (eg. change for
		  // select elements), but valid values include any jQuery event,
		  // most notably 'mousedown', 'blur', and 'submit'.
		  // 'event' => 'change',
			'callback' => 'akuntansi_main_form_callback',
			'wrapper' => 'skpd-replace',
		),
	);

	$form['formdata']['skpd'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#prefix' => '<div id="skpd-replace">',
		'#suffix' => '</div>',
		// When the form is rebuilt during ajax processing, the $selected variable
		// will now have the new value and so the options will change.
		'#options' => _ajax_get_skpd_dropdown($selected_tahun),
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $namasingkat,
	);
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $bulanoption,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	$form['formdata']['kategori']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kategori'), 
		'#default_value' => $kategori,
		
		'#options' => array(	
			 '' => t('SEMUA'), 	
			 '4' => t('PENDAPATAN'), 	
			 '5' => t('BELANJA'),
			 '6' => t('PEMBIAYAAN'),	
		   ),
	);		
	
	$form['formdata']['keyword'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari jurnal akuntansi, bisa diisi nama kegiatan, no. sp2d/sts, atau nama rekening'),
		'#default_value' => $keyword, 
	);		
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
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
function akuntansi_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */
function _ajax_get_tahun_dropdown() {
  // drupal_map_assoc() just makes an array('String' => 'String'...).
  return drupal_map_assoc(
    array(
	  t('2015'),
	  t('2014'),
	  t('2013'),
	  t('2012'),
      t('2011'),
      t('2010'),
      t('2009'),
      t('2008'),
    )
  );
}

/**
 * Helper function to populate the second dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @param string $key
 *   This will determine which set of options is returned.
 *
 * @return array
 *   Dropdown options
 */
function _ajax_get_skpd_dropdown($key = '') {
	$row = array();
	for($n=2015;$n>=2008;$n--){
		$query = db_select('unitkerja'.$n, 'p');

		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');

		# execute the query
		$results = $query->execute();
		
			
		# build the table fields
		$row[$n]['##'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $row[$n][$data->kodeuk] = $data->namasingkat; 
			}
		}
	}
	
	$options = array(
		t('2008') => drupal_map_assoc(
			$row[2008]
		),
		t('2009') => drupal_map_assoc(
			$row[2009]
		),
		t('2010') => drupal_map_assoc(
			$row[2010]
		),
		t('2011') => drupal_map_assoc(
			$row[2011]
		),
		t('2012') => drupal_map_assoc(
			$row[2012]
		),
		t('2013') => drupal_map_assoc(
			$row[2013]
		),
		t('2014') => drupal_map_assoc(
			$row[2014]
		),
		t('2015') => drupal_map_assoc(
			$row[2015]
		),
	);
	
	if (isset($options[$key])) {
		return $options[$key];
	} else {
		return array();
	}
}


?>
