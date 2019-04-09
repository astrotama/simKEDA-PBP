<?php
function cetakrekap_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$setorid = arg(2);	
	if(arg(3)=='pdf'){		
		/*$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);*/
	
	} else {
	
		drupal_set_title('Cetak Rekap Penerimaan');
		$output_form = drupal_get_form('cetakrekap_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function cetakrekap_main_form($form, &$form_state) {
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	//drupal_set_message($kodeuk);
	$form['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);
	if(isSuperuser() || isDKK()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		if(isDKK()){
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 and u.namasingkat like '%pkm%' order By u.namasingkat");
		}
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
	}		
	$form['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => 10,
	);	
	//drupal_set_message(date('j F Y'));
	$bulan=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desemebr');
	//drupal_set_message($bulan[date('m')]);
	
	$form['bulan']= array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#options' => $bulan,
	);	
	$form['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' =>date('j F Y') ,
	);	
	$form['cetak']= array(
		'#type' => 'submit',
		'#value' => 'CETAK',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function cetakrekap_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function cetakrekap_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$tanggal = $form_state['values']['tanggal'];
	
	if(isSuperuser() || isDKK()){
		$kodeuk = $form_state['values']['uk'];
		$_SESSION['rekapkodeuk']=$kodeuk;
	}
		
	$bulan+=1;
	drupal_goto('laporanrekappen/'.$bulan.'/'.$margin.'/'.$tanggal);

}



?>
