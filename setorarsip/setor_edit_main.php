<?php
function setor_edit_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $setorid=arg(1);
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = date('y');		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Setor');
	//drupal_set_message($tahun);
	//drupal_set_message(getrole());
	
	
	$output_form = drupal_get_form('setor_edit_main_form');
	return drupal_render($output_form);//.$output;
	
	
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
function setor_edit_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

function setor_edit_main_form($form, &$form_state) {
	
	//$opt= array(1,2,3,4);
	//Set Array option first
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null){
		$kodeuk='81';
	}
	$query = db_select('anggperuk', 'a');
	$query->fields('a', array('uraian','kodero','anggaran'));
	$query->condition('a.kodeuk', $kodeuk, '=');
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;
	foreach ($results as $data) {
		$opt[$data->kodero]=$data->uraian;
		
	}
	//.......................
	//Set Array option second
	$pquery='SELECT ro.kodero as kodero,rod.koderod ,rod.uraian as uraian2 FROM `rincianobyekdetil` rod inner join rincianobyek ro  where ro.kodero=rod.kodero';
	$pres = db_query($pquery);
	
	//$kodej=null;
	//$opt2[] = "- Pilih Jenis Rekening - ";
	foreach ($pres as $prow) {
		$opt2[$prow->kodero][$prow->koderod]=$prow->uraian2;
	}
	///..............................................
	$setorid=arg(1);
	$type=arg(2);
	
	
	$results=db_query("select kodero,koderod,uraian, jumlahmasuk,jumlahkeluar, tanggal from setor where setorid= :setorid", array(':setorid'=>$setorid));
	foreach($results as $data){
		$kodero=$data->kodero;
		$koderod=$data->koderod;
		$uraian=$data->uraian;
		if($type==1)
			$jumlah=$data->jumlahkeluar;
		else
			$jumlah=$data->jumlahmasuk;
		$tanggal=strtotime($data->tanggal);
	}
	$options_first = set_dropdown_options($opt);
	  // If we have a value for the first dropdown from $form_state['values'] we use
	  // this both as the default value for the first dropdown and also as a
	  // parameter to pass to the function that retrieves the options for the
	  // second dropdown.
	  $selected = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : $kodero;

	  $form['dropdown_first'] = array(
		'#type' => 'select',
		'#title' => 'Rekening',
		'#options' => $options_first,
		'#default_value' => $selected,
		// Bind an ajax callback to the change event (which is the default for the
		// select form type) of the first dropdown. It will replace the second
		// dropdown when rebuilt.
		'#ajax' => array(
		  // When 'event' occurs, Drupal will perform an ajax request in the
		  // background. Usually the default value is sufficient (eg. change for
		  // select elements), but valid values include any jQuery event,
		  // most notably 'mousedown', 'blur', and 'submit'.
		  // 'event' => 'change',
		  'callback' => 'ajax_example_dependent_dropdown_callback',
		  'wrapper' => 'dropdown-second-replace',
		),
	  );

	  $form['dropdown_second'] = array(
		'#type' => 'select',
		'#title' => $options_first[$selected] . ' ' . t('Detil'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#prefix' => '<div id="dropdown-second-replace">',
		'#suffix' => '</div>',
		// When the form is rebuilt during ajax processing, the $selected variable
		// will now have the new value and so the options will change.
		'#options' => set_second_dropdown_options($selected,$opt2),
		'#default_value' => isset($form_state['values']['dropdown_second']) ? $form_state['values']['dropdown_second'] : $koderod,
	  );
	//$tanggal=strtotime(date("d/m/Y"));
	$form['formdata']['tanggal']= array(
		'#type'         => 'date', 
		'#title'        => 'Tanggal',
		'#default_value'=> array(
			'year' => format_date($tanggal, 'custom', 'Y'),
			'month' => format_date($tanggal, 'custom', 'n'), 
			'day' => format_date($tanggal, 'custom', 'j'), 
		  ), 
	);
	
	$form['formdata']['uraian'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Uraian'),
		'#default_value' => $uraian,
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#markup' => '<p>tanggal</p>',
	);
	$form['formdata']['jumlah'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Jumlah'),
		'#default_value' => $jumlah,
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#markup' => '<p>tanggal</p>',
	);
	if(notDKK()){
		$form['formdata']['submit'] = array(
			'#type' => 'submit',
			'#value' =>'Simpan',
		);
	}
	$form['formdata']['mark'] = array(
	'#markup' =>'<a class="btn btn-danger" href="/">Tutup</a>',
		//'#suffix' =>,
	);
	
	return $form;
}

function setor_edit_main_form_submit($form, &$form_state) {
	//$rekening= $form_state['values']['dropdown_second'];
	//drupal_set_message($rekening); 
	$kodeuk= apbd_getuseruk();
	if($kodeuk==null){
		$kodeuk='81';
	}
	$uraian= $form_state['values']['uraian'];
	$jumlah = $form_state['values']['jumlah'];
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];	
	$kodero= $form_state['values']['dropdown_first'];
	$koderod= $form_state['values']['dropdown_second'];
	//drupal_set_message($rekening); 
	if(arg(2)==1){
		db_update('setor')
		->fields(array(
				'kodero' => $kodero,
				'uraian' => $uraian,
				'jumlahkeluar' => $jumlah,
				'tanggal' => $tanggalsql,
				'koderod' => $koderod,
				
				))
		->condition('setorid',arg(1),'=')
		->execute();
	}
	else{
		db_update('setor')
		->fields(array(
				'kodero' => $kodero,
				'uraian' => $uraian,
				'jumlahmasuk' => $jumlah,
				'tanggal' => $tanggalsql,
				'koderod' => $koderod,
				
				))
		->condition('setorid',arg(1),'=')
		->execute();
	}
	drupal_set_message('Data Telah Tersimpan');
	
	
}



?>
