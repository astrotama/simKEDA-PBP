<?php
function setorkeluar_main($arg=NULL, $nama=NULL) {
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
		$output_form = drupal_get_form('setorkeluar_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function setorkeluar_main_form($form, &$form_state) {
	$kodeuk=apbd_getuseruk();
	if(isSuperuser()){
		$kodeuk=arg(1);
	}
	
	if(isSuperuser()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		//$option_uk[0]='Semua';
		foreach($res as $data){
			$option_uk[$data->kodeuk]=$data->namasingkat;
		}
		$form['formd']['uk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);
		$form['formd']['cetak']= array(
			'#type' => 'submit',
			'#value' => 'Tampil',
		);
	}
	$form['formdokumen']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);	
	$form['formdokumen']['tablerek']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">No.</th><th>Rekening</th><th width="60px">Antrian</th><th width="50px"></th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	
	//$results = db_query('SELECT distinct ro.kodero,ro.uraian FROM setor  as s inner join rincianobyek as ro on s.kodero=ro.kodero where s.kodeuk= :kodeuk', array(':kodeuk'=>$kodeuk));

	$results = db_query('SELECT ro.kodero, ro.uraian, sum(s.jumlahmasuk) as jumlahmasuk FROM setor as s inner join rincianobyek as ro on s.kodero=ro.kodero where s.idkeluar=0 and s.kodeuk=:kodeuk group by ro.kodero, ro.uraian', array(':kodeuk'=>$kodeuk));
	
	$total = 0;
	foreach ($results as $data) {
		
		$total += $data->jumlahmasuk;
		
		$i++; 
		$form['formdokumen']['tablerek']['koderod' . $i]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
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
		$form['formdokumen']['tablerek']['antrian' . $i]= array(
				'#prefix' => '<td>',
				'#markup'=> '<p align="right">' . apbd_fn($data->jumlahmasuk) . '</p>' , 
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['pilih' . $i]= array(
			'#prefix' => '<td>',
			'#markup' => l('Pilih','/setorkeluarsub/'.$data->kodero),
			'#suffix' => '</td></tr>',
		);	

	}
	
	$i++; 
	$form['formdokumen']['tablerek']['koderod' . $i]= array(
			'#type' => 'value',
			'#value' => '',
	); 
	 
	$form['formdokumen']['tablerek']['nomor' . $i]= array(
			'#prefix' => '<tr><td>',
			'#markup' => '',
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablerek']['namarekening' . $i]= array(
			'#prefix' => '<td>',
			'#markup' => '<strong>TOTAL</strong>',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablerek']['antrian' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> '<p align="right"><strong>' . apbd_fn($total) . '<strong></p>' , 
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablerek']['pilih' . $i]= array(
		'#prefix' => '<td>',
		'#markup' => '',
		'#suffix' => '</td></tr>',
	);	
	$form['formdokumen']['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function setorkeluar_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function setorkeluar_main_form_submit($form, &$form_state) {
	$kodeuk=0;
	$kodeuk = $form_state['values']['uk'];
	if(isSuperuser())
		$_SESSION['superkodeuk']=$kodeuk;
	drupal_goto('setorkeluar/' . $kodeuk);

}



?>
