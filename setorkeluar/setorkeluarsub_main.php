<?php
function setorkeluarsub_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$setorid = arg(2);	
	if(arg(3)=='pdf'){		
		/*$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);*/
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('setorkeluarsub_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function setorkeluarsub_main_form($form, &$form_state) {
	//$datases=$_SESSION[$GLOBALS['user']->uid.'keluar'.$kodeuk];
	
	$kodeuk = apbd_getuseruk();
	$kodero = arg(1);
	
	//drupal_set_message($_SESSION['superkodeuk']);
	if(isSuperuser()){
		$kodeuk =$_SESSION['superkodeuk'];
	}
		//$kodeuk='81';
	$form['formdokumen']['kodero']= array(
		'#type' => 'value',
		'#value' => $kodero,
	);	
	$form['formdokumen']['tablerek']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">No.</th><th >Nama  Rincian Rekening</th><th  width="90px">Tanggal</th><th width="110px">Jumlah</th><th width="40px">Pilih</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	//$data=explode(',',$_SESSION[$GLOBALS['user']->uid.'keluar'.'81']);
	
	//$results = db_query('SELECT s.setorid, s.koderod,ro.uraian ,s.tanggal,s.jumlahmasuk FROM `setor`  as s  inner join rincianobyekdetil as ro on s.koderod=ro.koderod where s.koderod= :koderod', array(':koderod'=>arg(1)));
	
	$tag_tgl = '0000-00-00';
	$last_tgl = '0000-00-00';
	
	$results = db_query('SELECT s.setorid, s.kodero,s.uraian ,s.tanggal,s.jumlahmasuk FROM setor  as s  inner join rincianobyek as ro on s.kodero=ro.kodero where s.kodero= :kodero and s.idkeluar=0 and s.kodeuk= :kodeuk order by s.tanggal limit 24', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
	
	foreach ($results as $data) {		
		$i++; 
		$form['formdokumen']['tablerek']['kodero' . $i]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
		); 
		$form['formdokumen']['tablerek']['setorid' . $i]= array(
				'#type' => 'value',
				'#value' => $data->setorid,
		); 
		 
		$form['formdokumen']['tablerek']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['namarekening' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $data->uraian,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['tanggal' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_fd($data->tanggal),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['jumlah' . $i]= array(
				'#prefix' => '<td align="right">',
				'#markup' => apbd_fn($data->jumlahmasuk),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		
		if ($last_tgl != $data->tanggal) {			
			if ($last_tgl == '0000-00-00') {
				$select = '1';
			} else {	
				$select = '0';
			}				
			$last_tgl = $data->tanggal;
		} 	
		
		$form['formdokumen']['tablerek']['pilih' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $select,
			'#prefix' => '<td align="center">',
			'#suffix' => '</td></tr>',
		);	
			

		

	}
	
	$form['formdokumen']['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
    
	//CETAK BAWAH
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Setor',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}

function setorkeluarsub_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function setorkeluarsub_main_form_submit($form, &$form_state) {
	$jumlahdok = $form_state['values']['jumlahrek'];
	$kodero = $form_state['values']['kodero'];
	$str='';$item=0;
	for($n=1;$n<=$jumlahdok;$n++){
		//$str='CEK';
		//$str.=$form_state['values']['bendid' . $n];
		if($form_state['values']['pilih' . $n]!=0){
			$str.=$form_state['values']['setorid' . $n];
			if($n!=$jumlahdok)$str.=',';
			//$item++;
			/*db_update('setor')
			->fields(array(
					'idkeluar' => 1,
					
					))
			->condition('setorid',$form_state['values']['setorid' . $n],'=')
			->execute();*/
		}
		
		
	}
	$_SESSION[$GLOBALS['user']->uid.'keluar'.$kodero]=$str;
	
	//drupal_set_message($_SESSION[$GLOBALS['user']->uid.'keluar'.$koderod]);

    drupal_goto('setorkeluaredit/' . $kodero);

}



?>
