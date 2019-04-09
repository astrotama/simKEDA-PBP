<?php
function setorkeluar_edit_main($arg=NULL, $nama=NULL) {
	
	$output_form = drupal_get_form('setorkeluar_edit_main_form');
	return drupal_render($output_form);// . $output;	
	
}

function setorkeluar_edit_main_form($form, &$form_state) {
	
	$idkeluar = arg(1);
	//drupal_set_message($kodero);
	$query = db_select('setor', 's');
	$query->fields('s', array('idkeluar', 'tgl_keluar','keterangan'));
	$query->addExpression('SUM(s.jumlahkeluar)', 'jumlahkeluar');
	$query->condition('s.idkeluar', $idkeluar, '=');
	$results = $query->execute();
	foreach($results as $data){
		$tgl_keluar= strtotime($data->tgl_keluar);
		$keterangan=$data->keterangan;
		$namasubrek='';
		$jumlah=$data->jumlahkeluar;
	}
	$form['idkeluar'] = array(
		'#type' => 'textfield',
		'#title' =>  t('idkeluar'),
		'#default_value' => $idkeluar,		
	);
	$form['jumlah'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Jumlah'),
		'#default_value' => $jumlah,
	);
	$form['keterangan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keterangan'),
		'#default_value' => $keterangan,
	);
	//drupal_set_message($tgl_keluar);
	$form['tgl_keluar'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal Keluar'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $tgl_keluar,
		'#default_value'=> array(
			'year' => format_date($tgl_keluar, 'custom', 'Y'),
			'month' => format_date($tgl_keluar, 'custom', 'n'), 
			'day' => format_date($tgl_keluar, 'custom', 'j'), 
		  ), 
		
	);
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
		$form['formdata']['mark'] = array(
		'#markup' =>'<a class="btn btn-danger" href="/setorarsipkeluar">Tutup</a>',
		//'#suffix' =>,
	);
	return $form;
}

function setorkeluar_edit_main_form_validate($form, &$form_state) {
	
}
	
function setorkeluar_edit_main_form_submit($form, &$form_state) {
	

	$idkeluar = $form_state['values']['idkeluar'];
	$keterangan = $form_state['values']['keterangan'];
	$tanggal = $form_state['values']['tgl_keluar'];
	$tgl_keluar = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
	
	drupal_set_message('Data Telah Disimpan');	
	
	$result=db_query("UPDATE setor SET tgl_keluar='". $tgl_keluar ."', keterangan='". $keterangan ."' where idkeluar=:idkeluar", array(':idkeluar'=>$idkeluar));
	
	$result=db_query("UPDATE setoridmaster SET keterangan='". $keterangan ."' where id=:idkeluar", array(':idkeluar'=>$idkeluar));
	
    //drupal_goto('');

}



?>
