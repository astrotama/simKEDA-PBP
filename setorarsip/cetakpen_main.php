<?php
function cetakpen_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$setorid = arg(2);	
	if(arg(3)=='pdf'){		
		/*$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);*/
	
	} else {
	
		drupal_set_title('Cetak Penenerimaan');
		$output_form = drupal_get_form('cetakpen_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function cetakpen_main_form($form, &$form_state) {
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$form['kodeuk']= array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	if(isSuperuser() || isDKK()){
		//AJAX............................
		if(isSuperuser() || isDKK()){
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
			if(isDKK()){
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

			  $form['dropdown_first'] = array(
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

			  $form['dropdown_second'] = array(
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
				'#default_value' => isset($form_state['values']['dropdown_second']) ? $form_state['values']['dropdown_second'] : '',
			  );
			}
		//END AJAX .......................
	}
	$form['margin']= array(
		'#type' => 'textfield',
		'#title'=>'Margin',
		'#default_value' => 10,
	);
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$result=db_query('select s.kodero, ro.uraian from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero where s.kodeuk=:kodeuk',array(':kodeuk'=>$kodeuk));
	$optionrek=null;
	foreach($result as $data){
		$optionrek[$data->kodero]=$data->uraian;
	}
	$bulan=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desemebr');
	if(!isSuperuser() and notDKK()){
		$form['rek']= array(
			'#type' => 'select',
			'#options' => $optionrek,
			'#title'=>'Rekening',
		);
	}
	
	$form['bulan']= array(
		'#type' => 'select',
		'#options' => $bulan,
		'#title'=>'Bulan',
	);	
	$form['cetak']= array(
		'#type' => 'submit',
		'#value' => 'CETAK',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function cetakpen_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function cetakpen_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK()){
		$kodeuk= $form_state['values']['dropdown_first'];
		$rek= $form_state['values']['dropdown_second'];
	}else{
		$rek = $form_state['values']['rek'];
	}
	
	//if($kodeuk==null)
	//$kodeuk='81';
	$bulan+=1;
	drupal_goto('laporanpen/'.$rek.'/'.$bulan.'/'.$margin.'/'.$kodeuk);

}



?>
