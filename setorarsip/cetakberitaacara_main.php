<?php
function cetakberitaacara_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$setorid = arg(2);	
	if(arg(3)=='pdf'){		
		/*$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);*/
	
	} else {
	
		drupal_set_title('Cetak Berita Acara');
		$output_form = drupal_get_form('cetakberitaacara_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function cetakberitaacara_main_form($form, &$form_state) {

	$form['formdokumen']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);
	if(isSuperuser()){
		$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
		//$option_uk[0]='Semua';
		foreach($res as $data){
			$option_uk[$data->kodeuk]=$data->namasingkat;
		}
		$form['formdokumen']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('OPD'),
			'#options' => $option_uk,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' =>$kodeuk,
		);
	}		
	$triwulan=array('I','II','III','IV');
	$form['formdokumen']['triwulan']= array(
		'#type' => 'select',
		'#title'=>'Triwulan',
		'#options' => $triwulan,
	);	
	$form['formdokumen']['nomor']= array(
		'#type' => 'textfield',
		'#title'=>'Nomor Berita Acara',
		'#default_value' => '',
	);	
	
	$hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
	$form['formdokumen']['tanggal']= array(
		'#type' => 'textfield',
		'#title'=>'Tanggal',
		'#default_value' => $hari[date('w')] . ', ' . date('d F Y'),
	);	
	$form['formdokumen']['margin']= array(
		'#type' => 'textfield',
		'#title'=>'Margin',
		'#default_value' => 10,
	);	
	$form['formdokumen']['cetak']= array(
		'#type' => 'submit',
		'#value' => 'CETAK',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function cetakberitaacara_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function cetakberitaacara_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$triwulan = $form_state['values']['triwulan'];
	$margin = $form_state['values']['margin'];
	$nomor = $form_state['values']['nomor'];
	if ($nomor == '') $nomor = '. . . . . . . . . . . '; 
	$tanggal = $form_state['values']['tanggal'];

	drupal_goto('laporanBA/'. $kodeuk . '/' . $triwulan . '/'. $margin . '/' . $nomor . '/' . $tanggal);

}



?>
