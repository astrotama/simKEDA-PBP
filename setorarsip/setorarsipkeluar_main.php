<?php
function setorarsipkeluar_main($arg=NULL, $nama=NULL) {
	ini_set('memory_limit', '1024M');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				//$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$kodeuk = arg(2);
				
				

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		
		//$bulan = date('m');
		
		
	}
	if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			//$kodeuk = $_SESSION["setorarsipkeluar_kodeuk"];
			$kodeuk = '##';
		}
	//drupal_set_message($kodeuk);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('setorarsipkeluar_main_form');
	if (isSuperuser() || isDKK())
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			//array('data' => 'Rekening Detil', 'valign'=>'top', 'field'=> 'uraianrek'),
			array('data' => 'Tanggal', 'field'=> 'tgl_keluar', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'Jumlah', 'field'=> 'jumlahkeluar', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'field'=> 'tgl_keluar', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'Jumlah', 'field'=> 'jumlahkeluar', 'valign'=>'top'),
			array('data' => 'Keterangan', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);	
		
	$bulan = arg(1);
	$delete=arg(2);
	$kodero=arg(2);
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK() ){
		$kodeuk=arg(3);
	}
	if($delete=='delete'){
		//$res=db_query("select idmasuk from setor where setorid = ".$bulan);
		//foreach($res as $data){
		//	$datast=explode(',',$data->idmasuk);
		//}
		
		//drupal_set_message($bulan);
		
		$results=db_query("update  setor set idkeluar=0, jumlahkeluar=0, tgl_keluar=null where idkeluar=:setorid", array(':setorid'=>$bulan));

		drupal_goto('setorarsipkeluar');
	}
	if(isset($_SESSION["setorarsip_bulan"]))
		$bulan = $_SESSION["setorarsip_bulan"];
	if ($bulan=='') $bulan = date('m');
	if ($bulan !='0') 
		$where=' and month(d.tanggal)='.$bulan;
	else
		$where='';
	
	if($kodero!='delete' && $kodero!='' && $kodero!=0)
		$where.=' and d.kodero='.$kodero;
	else
		$where.='';
	
	//drupal_set_message($kodeuk);
	
	if (isUserSKPD()){

		$query = db_select('setor', 's')->extend('PagerDefault')->extend('TableSort');
		$query->join('setoridmaster', 'm', 's.idkeluar=m.id');
		$query->fields('s', array('kodeuk', 'tgl_keluar', 'idkeluar'));
		$query->fields('m', array('keterangan'));
		$query->addExpression('SUM(s.jumlahkeluar)', 'jumlahkeluar');
		if($bulan!=0 and $bulan!=null){
			$query->where('MONTH(s.tanggal) = :val', array('val'=>$bulan));
		}
		$query->condition('s.jumlahkeluar', 0, '>');
		//$query->condition('s.jumlahkeluar', 0, '<');
		$query->condition('s.kodeuk', $kodeuk, '=');
		
		if($kodero!='' and $kodero!=0){
			$query->condition('s.kodero', $kodero, '=');
		}
		$query->groupBy('s.idkeluar');	
		$query->orderByHeader($header);		
		$query->orderBy('s.tanggal', 'DESC');
		$query->orderBy('s.idkeluar', 'ASC');
		//$query->limit($limit);
		$query->limit($limit);
		 //dpq($query);
		$results = $query->execute();
		
	}
	else if(isSuperuser()){
		$query = db_select('setor', 's')->extend('PagerDefault')->extend('TableSort');
		$query->join('unitkerja', 'u', 's.kodeuk=u.kodeuk');
		$query->join('rincianobyek', 'ro', 'ro.kodero=s.kodero');
		$query->fields('s', array('kodeuk', 'jumlahkeluar','tanggal','uraian','setorid','kodero'));
		$query->fields('u', array('namasingkat'));
		$query->addField('ro', 'uraian', 'uraianrek');
		if($bulan!=0 and $bulan!=null){
			$query->where('MONTH(s.tanggal) = :val', array('val'=>$bulan));
		}
		$db_or = db_or();
		$db_or->condition('s.jumlahkeluar', 0, '<');
		$db_or->condition('s.jumlahkeluar', 0, '>');
		$query->condition($db_or);
		//$query->condition('s.jumlahkeluar', 0, '<');
		if($kodeuk!=0 and $kodeuk!=null){
			$query->condition('s.kodeuk', $kodeuk, '=');
		}
		
		if($kodero!='' and $kodero!=0){
			$query->condition('s.kodero', $kodero, '=');
		}
		$query->orderByHeader($header);
		$query->orderBy('s.tanggal', 'DESC');
		$query->orderBy('s.kodero', 'ASC');
		//$query->limit($limit);
		$query->limit($limit);
		 //dpq($query);
		$results = $query->execute();
		
	}else if(isDKK()){
		drupal_set_message("DKK");
		$query = db_select('setor', 's')->extend('PagerDefault')->extend('TableSort');
		$query->join('unitkerja', 'u', 's.kodeuk=u.kodeuk');
		$query->join('rincianobyek', 'ro', 'ro.kodero=s.kodero');
		$query->fields('s', array('kodeuk', 'jumlahkeluar','tanggal','uraian','setorid','kodero'));
		$query->fields('u', array('namasingkat'));
		$query->addField('ro', 'uraian', 'uraianrek');
		$query->condition('u.namasingkat', '%' . db_like("PKM") . '%', 'LIKE');
		if($bulan!=0 and $bulan!=null){
			$query->where('MONTH(s.tanggal) = :val', array('val'=>$bulan));
		}
		
		$query->condition('s.jumlahkeluar', 0, '>');
		if($kodeuk!=0 and $kodeuk!=null){
			$query->condition('s.kodeuk', $kodeuk, '=');
		}
		
		if($kodero!='' and $kodero!=0){
			$query->condition('s.kodero', $kodero, '=');
		}
		$query->orderByHeader($header);
		$query->orderBy('s.tanggal', 'DESC');		
		$query->orderBy('s.kodero', 'ASC'); 
		$query->limit($limit);
		 dpq($query);
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
		//$editlink=l('Baru','akuntansi/edit');
		if(notDKK()){
			$editlink='<a href="/laporan2/'.$data->idkeluar.'/0/kel"><button type="button"  class="btn btn-primary btn-sm" >Cetak</button></a>';
			$editlink2='<a href="/laporansts/'.$data->idkeluar.'/0"><button type="button"  class="btn btn-warning btn-sm" >Cetak Detil</button></a>';
			//$editlink.="&nbsp;";
			//$editlink2[0]=modal('Hapus'.$no,'Apakah anda akan menghapus data '.$data->uraian.' ?',array('data'=>'Ya','link'=>'setorarsipkeluar/'.$data->setorid.'/delete'));
			$editlink3 = modal($data->idkeluar,'Hapus','Apakah anda akan menghapus data '. apbd_fn($data->jumlahkeluar). ' ?',array('data'=>'Ya','link'=>'setorarsipkeluar/'.$data->idkeluar.'/delete'));
			//http://pbp.simkedajepara.net/setorarsipkeluar/'.$data->setorid.'/delete
		}
		else{
			$editlink="";
			$editlink2="";
			$editlink3="";
		}
		if(isSuperuser() || isDKK()){
			$rows[] = array (
                array('data' => $no, 'align' => 'right'),                
				array('data' => '', 'align' => 'left'),
				array('data' => $data->namasingkat, 'align' => 'left'),
				array('data' => $data->kodero, 'align' => 'left'),
                
				array('data' => '<a href="/setoredit/'.$data->idkeluar.'/1" >'.$data->uraianrek.'</a>','align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fd($data->tanggal), 'align' => 'left'),
				array('data' => $data->uraian, 'align' => 'left'),
				array('data' => apbd_fn($data->jumlahkeluar), 'align' => 'right'),
				$editlink,
				$editlink2,
				$editlink3,
				//array('data' => '', 'align' => 'right'),
				
            );
		}
		
		else{

			$keterangan = '<a href="/editsetorkeluar/'.$data->idkeluar .'">'. $data->keterangan .'</a>';
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fd($data->tgl_keluar),'align' => 'left', 'valign'=>'top'),
						array('data' => $keterangan,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlahkeluar), 'align' => 'right', 'valign'=>'top'),
						array('data' => get_keterangan($data->idkeluar), 'align' => 'left', 'valign'=>'top'),
						$editlink,
						$editlink2,
						$editlink3,
						//array('data' => '','align' => 'right', 'valign'=>'top'),
						//$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',							
			);
		}
			
			
	}
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	
	////BUTTON
	$btn = apbd_button_baru('setorkeluar');
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



function setorarsipkeluar_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	$rek = $form_state['values']['rek'];
	$uk='';
	//$uk = $form_state['values']['uk'];
	
	if(isSuperuser() || isDKK()){
		$uk= $form_state['values']['dropdown_first'];
		$rek= $form_state['values']['dropdown_second'];
	}
	
	$_SESSION["setorarsip_bulan"]=$bulan;
	$_SESSION["setorarsip_rek"]=$rek;
	$_SESSION["setorarsip_uk"]=$uk;
	
	//$kodeuk= $form_state['values']['kodeuk'];
	//if($kodeuk==null)
	//$kodeuk='81';
	drupal_goto('setorarsipkeluar/'.$bulan.'/'.$rek.'/'.$uk);
}


function setorarsipkeluar_main_form($form, &$form_state) {
	
	/*
	$kodeuk = '##';
	//$bulan = date('m');
	$bulan = '1';
	$spmok = '##';
	*/
	$bulan = arg(1);
	$kodeuk = arg(3);
	$rek=0;
	if(isset($_SESSION["setorarsip_bulan"]))
		$bulan = $_SESSION["setorarsip_bulan"];
	if(isset($_SESSION["setorarsip_uk"]))
		$uk = $_SESSION["setorarsip_uk"];
	if(isset($_SESSION["setorarsip_rek"]))
		$rek = $_SESSION["setorarsip_rek"];
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',		
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formdata']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
		//'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	/*$form['Baru']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Baru',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
	);
	;*/
	$form = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right"></small></em>',		//'#attributes' => array('class' => array('container-inline')),
		//'#collapsible' => TRUE,
		//'#collapsed' => TRUE,        
	);		
	if(isSuperuser() || isDKK()){
		$res=db_query("SELECT * FROM unitkerja as u where namasingkat like '%PKM%' order By u.namasingkat");
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
			drupal_set_message("DKK");
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
		drupal_set_message($uk);
		$options_first = set_dropdown_options($opt);
		  // If we have a value for the first dropdown from $form_state['values'] we use
		  // this both as the default value for the first dropdown and also as a
		  // parameter to pass to the function that retrieves the options for the
		  // second dropdown.
		  $selected = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : arg(3);

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
	$form['bulan'] = array(
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
	
	if(isUserSKPD()){
		$form['rek'] = array(
			'#type' => 'select',
			'#title' =>  t('Rekening'),
			'#options' => $option_rek,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$rek,
		);
	}
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}


function get_keterangan($idkeluar) {
	$str_ret = '';
	$pres = db_query('select uraian, jumlahmasuk from setor where idkeluar=:idkeluar order by tanggal', array(':idkeluar'=>$idkeluar));
	
	foreach ($pres as $prow) {
		$str_ret = $prow->uraian . ' (' . apbd_fn($prow->jumlahmasuk) . '); ' ;
	}	
	return $str_ret;
}
	
?>
