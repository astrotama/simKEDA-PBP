<?php
function cetakbku_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	//drupal_set_message(isPusekesmas());
	//$setorid = arg(2);	
	if(arg(3)=='pdf'){		
		/*$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);*/
	
	} else {
		//drupal_set_message(substr("123456789",0,4));
		drupal_set_title('Cetak BKU');
		$output_form = drupal_get_form('cetakbku_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function cetakbku_main_form($form, &$form_state) {

	$form['formdokumen']['kodeuk']= array(
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
		$form['formdokumen']['uk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);
	}
	$form['formdokumen']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => 10,
	);	
	//$bulan=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desemebr');
	$bulan['1']= 'Januari';
	$bulan['2']= 'Februari';
	$bulan['3']= 'Maret';
	$bulan['4']= 'April';
	$bulan['5']= 'Mei';
	$bulan['6']= 'Juni';
	$bulan['7']= 'Juli';
	$bulan['8']= 'Agustus';
	$bulan['9']= 'September';
	$bulan['10']= 'Oktober';
	$bulan['11']= 'November';
	$bulan['12']= 'Desemeber';
	$form['formdokumen']['bulan']= array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#options' => $bulan,
	);
	$periode['0']= 'Satu Bulan';
	$periode['1']= '1 (Tanggal 1-7)';
	$periode['2']= '2 (Tanggal 8-15)';
	$periode['3']= '3 (Tanggal 16-23)';
	$periode['4']= '4 (Tanggal 24-30/31)';
	$form['formdokumen']['periode']= array(
		'#type' => 'select',
		'#title' => 'Periode',
		'#options' => $periode,
	);	
	$form['formdokumen']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' =>date('j F Y') ,
	);	
	$form['formdokumen']['cetak']= array(
		'#type' => 'submit',
		'#value' => 'CETAK',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function cetakbku_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function cetakbku_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$tanggal = $form_state['values']['tanggal'];
	$periode = $form_state['values']['periode'];
	
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK())
		$kodeuk = $form_state['values']['uk'];
	/*if($kodeuk==null)
		$kodeuk='81';*/
	drupal_goto('laporanbku2/' . $kodeuk.'/'.$bulan.'/'.$margin.'/'.$tanggal.'/'.$periode);

}



?>
