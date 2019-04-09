<?php
function setorarsip_main($arg=NULL, $nama=NULL) {
	ini_set('memory_limit', '1024M');
	$qlike='';
	$limit = 10;
    
	$bulan = arg(1);
	$delete = arg(2);
	$kodero = arg(2);
	$kodeuk = arg(3);
	$status = arg(4);
	$koderod = arg(5);
	
	if ($bulan=='') {
		if(isset($_SESSION["setorarsip_bulan"])) $bulan = $_SESSION["setorarsip_bulan"];
		if ($bulan=='') $bulan = date('m');

		if(isset($_SESSION["setorarsip_uk"])) $kodeuk = $_SESSION["setorarsip_uk"];
		if(isset($_SESSION["setorarsip_rek"])) $kodero = $_SESSION["setorarsip_rek"];
		if(isset($_SESSION["setorarsip_status"])) $status = $_SESSION["setorarsip_status"];
		if(isset($_SESSION["setorarsip_koderod"])) $koderod = $_SESSION["setorarsip_koderod"];

	}	
	if (isUserSKPD()) $kodeuk = apbd_getuseruk();
	
	
	if($delete=='delete'){
		
		$res = db_query("delete from setor where setorid = ".$bulan);
		drupal_goto('setorarsip');
		
	}
	
	$output_form = drupal_get_form('setorarsip_main_form');
	if (isSuperuser() || isDKK())
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Rekening', 'field'=> 'kodero', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'Rekening Detil', 'valign'=>'top', 'field'=> 'uraianrek'),
			array('data' => 'Tanggal', 'field'=> 'tanggal', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Jumlah', 'field'=> 'jumlahmasuk', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px', 'field'=> 'tanggal', 'valign'=>'top'),
			array('data' => 'Rekening', 'field'=> 'uraianrek', 'valign'=>'top'),
			array('data' => 'Detil', 'valign'=>'top'),
			array('data' => 'Jumlah', 'field'=> 'jumlahmasuk', 'valign'=>'top'),
			array('data' => 'Keterangan', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
		

	if (isUserSKPD()){

		$query = db_select('setor', 's')->extend('PagerDefault')->extend('TableSort');
		$query->leftJoin('rincianobyek', 'ro', 'ro.kodero=s.kodero');
		$query->fields('s', array('kodeuk', 'jurnalsudah','jumlahmasuk','tanggal','uraian','setorid','kodero', 'idkeluar', 'koderod'));
		$query->addField('ro', 'uraian', 'uraianrek');
		if($bulan!=0 and $bulan!=null){
			$query->where('MONTH(s.tanggal) = :val', array('val'=>$bulan));
		}
		
		$query->condition('s.jumlahmasuk', 0, '<>');
		if($kodeuk!=0 and $kodeuk!=null){
			$query->condition('s.kodeuk', $kodeuk, '=');
		}
		
		if($kodero!='' and $kodero!=0){
			$query->condition('s.kodero', $kodero, '=');
		}
		$query->condition('s.kodeuk', $kodeuk, '=');
		
		if($koderod!='' and $koderod!=0){
			$query->condition('s.koderod', $koderod, '=');
		}
		
		if ($status=='0')
			$query->condition('s.idkeluar', $status, '=');			
		elseif ($status=='1')
			$query->condition('s.idkeluar', $status, '>=');	
		
		
		$query->orderByHeader($header);
		$query->orderBy('s.kodero', 'ASC');
		$query->orderBy('s.tanggal', 'DESC');
		$query->limit($limit);
		 //dpq($query);
		$results = $query->execute();
		
	}
	else{
		
		$query = db_select('setor', 's')->extend('PagerDefault')->extend('TableSort');
		$query->join('unitkerja', 'u', 's.kodeuk=u.kodeuk');
		$query->join('rincianobyek', 'ro', 'ro.kodero=s.kodero');
		$query->fields('s', array('kodeuk', 'jurnalsudah','jumlahmasuk','tanggal','uraian','setorid','kodero', 'idkeluar'));
		$query->fields('u', array('namasingkat'));
		$query->addField('ro', 'uraian', 'uraianrek');
		if($bulan!=0 and $bulan!=null){
			$query->where('MONTH(s.tanggal) = :val', array('val'=>$bulan));
		}
		
		$query->condition('s.jumlahmasuk', 0, '<>');
		if($kodeuk!=0 and $kodeuk!=null){
			$query->condition('s.kodeuk', $kodeuk, '=');
		}
		
		if($kodero!='' and $kodero!=0){
			$query->condition('s.kodero', $kodero, '=');
		}
		if($koderod!='' and $koderod!=0){
			$query->condition('s.koderod', $koderod, '=');
		}
		$query->orderByHeader($header);
		$query->orderBy('s.kodero', 'ASC');
		$query->orderBy('s.tanggal', 'DESC');
		//$query->limit($limit);
		$query->limit($limit);
		 //dpq($query);
		$results = $query->execute();
	}
		
	
	
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
		
		
		//icon
		if(notDKK()){
			$linkcetak = '<a href="/laporan2/'.$data->setorid.'/0/mas"><button type="button"  class="btn btn-primary btn-sm" >Cetak</button></a>';
			$linkcetak_detil = '<a href="/laporansts/'.$data->setorid.'/0"><button type="button"  class="btn btn-warning btn-sm" >Cetak Detil</button></a>';
			
			if ($data->idkeluar=='0' or $data->jurnalsudah == '0')
				$linkhapus = modal($data->setorid,'Hapus','Apakah anda menghapus penerimaan '. $data->uraian . '?',array('data'=>'Ya','link'=>'setorarsip/'.$data->setorid.'/delete'));
			else
				$linkhapus = '<p style="text-align:right">Hapus</p>';
			//$editlink2 .= modal('Hapus'.$no,'Apakah anda akan menghapus data '.$data->uraian.' ?',array('data'=>'Ya','link'=>'setorarsip/'.$data->setorid.'/delete'));

			
		}
		else{
			$linkcetak = "";
			$linkcetak_detil = "";
			$linkhapus = "";
		}
		//http://pbp.simkedajepara.net/setorarsip/'.$data->setorid.'/delete
		
		if(isSuperuser() || isDKK()){
			$rows[] = array (
                array('data' => $no, 'align' => 'right'),                
				array('data' => $data->namasingkat, 'align' => 'left'),
				array('data' => $data->kodero, 'align' => 'left'),
                
				array('data' => '<a href="/setoredit/'.$data->setorid.'/1" >'.$data->uraianrek.'</a>','align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fd($data->tanggal), 'align' => 'left'),
				array('data' => $data->uraian, 'align' => 'left'),
				array('data' => apbd_fn($data->jumlahmasuk), 'align' => 'right'),
				$linkcetak,
				$linkcetak_detil,
				
            );
		}
		
		else{
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fd($data->tanggal),'align' => 'left', 'valign'=>'top'),
						array('data' => '<a href="/setoredit/'.$data->setorid.'/0" >'.$data->uraianrek.'</a>','align' => 'left', 'valign'=>'top'),
						array('data' => get_detil($data->koderod),'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlahmasuk), 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->uraian,'align' => 'left', 'valign'=>'top'),
						$linkcetak,
						$linkcetak_detil,
						$linkhapus,
						
			);
		}
			
			
	}
	//$kodeuk=apbd_getuseruk();
	//if($kodeuk==null)
	//	$kodeuk='81';
	
	////BUTTON
	$btn = apbd_button_baru('akuntansi/edit');
	$btn .= "&nbsp;" . apbd_button_print('laporanarsip/'.$kodeuk);
	//$btn .= "&nbsp;" .apbd_button_custom('Cetak BKU','cetakbku');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form).'</br>' . $btn . $output . $btn;
	}
	
}



function setorarsip_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	$kodero = $form_state['values']['kodero'];
	$status = $form_state['values']['status'];
	$uk='XX';
	$koderod = $form_state['values']['koderod'];
	//$uk = $form_state['values']['uk'];
	
	if(isSuperuser() || isDKK()){
		$uk= $form_state['values']['dropdown_first'];
		$kodero= $form_state['values']['dropdown_second'];
	}
	
	$_SESSION["setorarsip_bulan"] = $bulan;
	$_SESSION["setorarsip_rek"] = $kodero;
	$_SESSION["setorarsip_uk"] = $uk;
	$_SESSION["setorarsip_status"] = $status;
	$_SESSION["setorarsip_koderod"] = $koderod;
	
	drupal_goto('setorarsip/' . $bulan . '/' . $kodero . '/' . $uk . '/' . $status . '/' . $koderod);
}


function setorarsip_main_form($form, &$form_state) {
	
	/*
	$kodeuk = '##';
	//$bulan = date('m');
	$bulan = '1';
	$spmok = '##';
	*/
	$bulan = arg(1);
	$kodeuk = arg(3);
	$status = arg(4);
	$koderod = arg(5);
	
	$kodero=0;
	if(isset($_SESSION["setorarsip_bulan"])) $bulan = $_SESSION["setorarsip_bulan"];
	if(isset($_SESSION["setorarsip_uk"])) $uk = $_SESSION["setorarsip_uk"];
	if(isset($_SESSION["setorarsip_rek"])) $kodero = $_SESSION["setorarsip_rek"];
	if(isset($_SESSION["setorarsip_status"])) $status = $_SESSION["setorarsip_status"];
	if(isset($_SESSION["setorarsip_koderod"])) $koderod = $_SESSION["setorarsip_koderod"];
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',		
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formdata']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
		//'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	if(isSuperuser() || isDKK()){
		
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		$option_uk[0]='Semua';
		foreach($res as $data){
			$option_uk[$data->kodeuk]=$data->namasingkat;
		}
		/*$form['uk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);*/
	}
	//AJAX............................
		if(isSuperuser() || isDKK()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		if(isDKK()){
			//drupal_set_message("DKK");
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 and u.namasingkat like '%pkm%' order By u.namasingkat");
		}
		$option_uk[0]='Semua';
		foreach($res as $data){
			$opt['0']='[semua]';
			$opt[$data->kodeuk]=$data->namasingkat;
			
		}
		$pquery='SELECT a.kodeuk ,ro.kodero ,ro.uraian FROM `rincianobyek` ro inner join anggperuk as a  where ro.kodero=a.kodero order By ro.kodero';
		$pres = db_query($pquery);
		
		//$kodej=null;
		//$opt2[] = "- Pilih Jenis Rekening - ";
		foreach ($pres as $prow) {
			//$opt2[$prow->kodero]['00']='[KOSONG]';
			$opt2[$prow->kodeuk][$prow->kodero]=$prow->uraian;
		}
		///..............................................
		$uk=$uk=key($options_first);
		if($kodeuk!=''){
			$uk=arg(3);
		}
		
		$options_first = set_dropdown_options($opt);
		  // If we have a value for the first dropdown from $form_state['values'] we use
		  // this both as the default value for the first dropdown and also as a
		  // parameter to pass to the function that retrieves the options for the
		  // second dropdown.
		  $selected = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : arg(3);
		  ////drupal_set_message($selected);
		  $form['formdata']['dropdown_first'] = array(
			'#type' => 'select',
			'#title' => 'OPD',
			'#options' => $options_first,
			'#default_value' => $selected,
			'#validated'=>true,
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

		  $form['formdata']['dropdown_second'] = array(
			'#type' => 'select',
			'#title' => t('Rekening'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#prefix' => '<div id="dropdown-second-replace">',
			'#validated'=>true,
			'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => set_second_dropdown_options($selected,$opt2),
			'#default_value' => isset($form_state['values']['dropdown_second']) ? $form_state['values']['dropdown_second'] : arg(2),
		  );
		}
	//END AJAX .......................
	//..................
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);
	$kodeuk=apbd_getuseruk();
	$res=db_query("SELECT a.kodero,ro.uraian FROM `anggperuk` as a inner join rincianobyek as ro on a.kodero=ro.kodero WHERE a.kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	$option_rek[0]='Semua';
	foreach($res as $data){
		$option_rek[$data->kodero]=$data->uraian;
	}
	
	$form['formdata']['kodero'] = array(
		'#type' => 'select',
		'#title' =>  t('Rekening'),
		'#options' => $option_rek,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		// '#default_value' =>$kodero,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_kodero',
			'wrapper' => 'kodero-wrapper',
		),
	);
	
	$form['formdata']['wrapperkodero'] = array(
		'#prefix' => '<div id="kodero-wrapper">',
		'#suffix' => '</div>',
	);
	
	$options = array('- Pilih Detil -');
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_koderod($form_state['values']['kodero']);
	// Detil dropdown list
	$form['formdata']['wrapperkodero']['koderod'] = array(
		'#title' => t('Detil'),
		'#type' => 'select',
		'#options' => $options,
		//'#validated' => TRUE,
		// '#default_value' =>$koderod,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_koderod',
			'wrapper' => 'koderod-wrapper',
		),
		
	);

	// Wrapper for rekdetil dropdown list
	$form['formdata']['wrapperkoderod'] = array(
		'#prefix' => '<div id="koderod-wrapper">',
		'#suffix' => '</div>',
	);
	
	$form['formdata']['status'] = array(
		'#type' => 'select',
		'#title' =>  t('Status'),
		'#options' => array(
						'all' => 'Semua',
						'0' => 'Belum Setor',
						'1' => 'Sudah Setor',
						),
		'#default_value' => $status,
	);
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}

function get_detil($koderod) {
	$str_ret = '';
	if (strlen($koderod)>8) {
		$pres = db_query('select uraian from rincianobyekdetil where koderod=:koderod', array(':koderod'=>$koderod));
		
		foreach ($pres as $prow) {
			$str_ret = $prow->uraian;
		}	
	}
	return $str_ret;
}

function _load_koderod($kodero) {
	$obyeks = array('- Pilih Detil -');


	// Select table
	$query = db_select("rincianobyekdetil", "rod");
	// Selected fields
	$query->fields("rod", array('koderod', 'uraian'));
	// Filter the active ones only
	$query->condition("rod.kodero", $kodero, '=');
	
	// Order by name
	$query->orderBy("rod.koderod");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$obyeks[$row->koderod] = $row->uraian;
	}

	return $obyeks;
}

function _ajax_kodero($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata']['wrapperkodero'];
}


function _ajax_koderod($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata']['wrapperkoderod'];
}

?>
