<?php
function akuntansi_edit_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	drupal_add_js('files/js/rupiah.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = date('y');		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Penerimaan');
	//drupal_set_message($tahun);
	//drupal_set_message(getrole());
	
	
	/*
	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '80px','valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'Debet', 'width' => '120px', 'valign'=>'top'),
		array('data' => 'Kredit', 'width' => '120px', 'valign'=>'top'),
		
		
	);
	*/
	
	# get the desired fields from the database

	//$tahun='2017';
	/*
	$no=0;
	$rows = array();
	for ($n=0;$n<5;$n++) {
		$no++;  
		
		
		
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					//$total+=$data->debet;
					//$total2+=$data->kredit;
	}
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('akuntansi_edit_main_form');
		$script=	" <script src='https://code.jquery.com/jquery-1.10.2.js'></script>
				<style>
					div .rupiah{
						 position: fixed;
						bottom: 100;
						right: 0;
						color
					}
				</style>
				
					
				</script>";
		echo $script;
		*/
		
		$output_form = drupal_get_form('akuntansi_edit_main_form');
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
function akuntansi_edit_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

function akuntansi_edit_main_form($form, &$form_state) {
	
	//$opt= array(1,2,3,4);
	//Set Array option first
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',		
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null){
		$kodeuk='81';
	}
	$query = db_select('anggperuk', 'a');
	$query->fields('a', array('uraian','kodero','anggaran'));
	$query->condition('a.kodeuk', $kodeuk, '=');
	//drupal_set_message($kodeuk);
	# execute the query
	//dpq($query);
	$results = $query->execute();
		
	# build the table fields
	$no=0;
	foreach ($results as $data) {
		if(isPusekesmas()){
			if(isBlud()){
				if($data->kodero!='41416002'){
					$arr_rekening[$data->kodero] = $data->uraian;
					$opt[$data->kodero]=$data->uraian;
					$opt2[$data->kodero]['0']='[KOSONG]';
				}
			}
			else{
				$arr_rekening[$data->kodero] = $data->uraian;
				$opt[$data->kodero]=$data->uraian;
				$opt2[$data->kodero]['0']='[KOSONG]';
			}
			
		}
		else{
			$arr_rekening[$data->kodero] = $data->uraian;
			$opt[$data->kodero]=$data->uraian;
			$opt2[$data->kodero]['0']='[KOSONG]';
		}
		//$opt[$data->kodero]=$data->uraian;
		//$opt2[$data->kodero]['0']='[KOSONG]';
		
	}
	//.......................
	//Set Array option second
	//drupal_set_message($kodeuk);
	$pquery='SELECT ro.kodero as kodero,rod.koderod ,rod.uraian as uraian2 FROM `rincianobyekdetil` rod inner join anggperuk ro  where ro.kodero=rod.kodero  order by length(rod.koderod),rod.koderod asc';
	$pres = db_query($pquery);
	
	//$kodej=null;
	//$opt2[] = "- Pilih Jenis Rekening - ";
	foreach ($pres as $prow) {
		//$opt2[$prow->kodero]['00']='[KOSONG]';
		$opt2[$prow->kodero][$prow->koderod]=$prow->uraian2;
	}
	///..............................................
	$options_first = set_dropdown_options($opt);
	  $selected = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : key($options_first);

	  $form['formdata']['dropdown_first'] = array(
		'#type' => 'select',
		'#title'        => 'Rekening', 
		'#options' => $arr_rekening,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekening',
			'wrapper' => 'rekening-wrapper',
		),			
	);	
	$form['formdata']['wrapperrekening'] = array(
		'#prefix' => '<div id="rekening-wrapper">',
		'#suffix' => '</div>',
	);	

	if (isset($form_state['values']['dropdown_first'])) {
		$arr_rekeningdetil = _load_rekening($form_state['values']['dropdown_first']);
	} else
		$arr_rekeningdetil = _load_rekening($selected);
	
	  $form['formdata']['wrapperrekening']['dropdown_second'] = array(
		'#type' => 'select',
		'#title' => $options_first[$selected] . ' ' . t('Detil'),
		'#options' => $arr_rekeningdetil,
		'#description' => '<p></p>',		
	);	
	//$tanggal=strtotime(date("d/m/Y"));

	$form['formdata']['detil'] = array(
		'#type' => 'submit',
		'#value' =>'Detil Rekening',
		//'#suffix' =>,
	);
	
	$tanggal = apbd_date_create_currdate_form();
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
		'#default_values' => null,
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#markup' => '<p>tanggal</p>',
	);
	$java='
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
			<script type="text/javascript">
				
				$("input#edit-jumlah").focus(function(){
					var rp=$( this ).val();
					
					$( this ).val(rp.split(".").join(""));
					
					});
				$("[id^=edit-jumlah]").blur(function(){
					var rp=$( this ).val();
					temp=rp;
					if(rp>0){
						$( this ).val(toRp(rp));
					}
					else{
						$( this ).val("0");
					}
					
				})
				function toRp(angka){
					var rev     = parseInt(angka, 10).toString().split("").reverse().join("");
					var rev2    = "";
					for(var i = 0; i < rev.length; i++){
						rev2  += rev[i];
						if((i + 1) % 3 === 0 && i !== (rev.length - 1)){
							rev2 += ".";
						}
					}
					return rev2.split("").reverse().join("");
				}	
	</script>';
	$form['formdata']['jumlah'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Jumlah'),
		'#default_values' => null,
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#markup' => '<p>tanggal</p>',
		/*'#attached'=>array(
							'js'=>array(
									//drupal_get_path('module', 'akuntansi') . '/akuntansi.js',
									drupal_get_path('module', 'akuntansi') . '/jquery-1.10.2.js',
									
							)
					),*/
		//'#validated'=>true,			
		//'#suffix' => $java,
	);
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' =>'Simpan',
		//'#suffix' =>,
	);
	$form['formdata']['mark'] = array(
		'#markup' =>'<a class="btn btn-danger" href="/setorarsip">Tutup</a>',
		//'#suffix' =>,
	);
	
	
	
	return $form;
}


function _ajax_rekening($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata']['wrapperrekening'];
}
function _load_rekening($rekening) {
	$arr_rekeningdetil = array('- PILIH REKENING -');

	$results = db_query('select koderod,kodero,uraian from {rincianobyekdetil} where kodero=:rekening', array(':rekening'=>$rekening));
	foreach ($results as $data) {
		$arr_rekeningdetil[$data->koderod] = $data->uraian;
	}

	return $arr_rekeningdetil;
}

function akuntansi_edit_main_form_validate($form, &$form_state) {
	/*$uraian= $form_state['values']['uraian'];
	$jumlah = $form_state['values']['jumlah'];
	//$jumlah=getnilairp($jumlah);
	$tanggal = $form_state['values']['tanggal'];
	//$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];	
	$kodero= $form_state['values']['dropdown_first'];
	$koderod= $form_state['values']['dropdown_second'];
	if ($uraian=='') form_set_error('uraian', 'Uraian harap diisi');
	if ($jumlah=='') form_set_error('jumlah', 'Jumlah harap diisi');
	if ($koderod=='') form_set_error('detil', 'Detil rekening harap diisi');*/
		
}
function akuntansi_edit_main_form_submit($form, &$form_state) {
	//$rekening= $form_state['values']['dropdown_second'];
	//drupal_set_message($rekening); 
	$kodeuk= apbd_getuseruk();
	if($kodeuk==null){
		$kodeuk='81';
	}
	$uraian= $form_state['values']['uraian'];
	$jumlah = $form_state['values']['jumlah'];
	$jumlah=getnilairp($jumlah);
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];	
	$kodero= $form_state['values']['dropdown_first'];
	$koderod= $form_state['values']['dropdown_second'];
	
	//drupal_set_message($kodero); 
	//drupal_set_message($koderod); 
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		db_insert('setor')
		->fields(array('kodero', 'uraian', 'jumlahmasuk', 'tanggal','koderod','kodeuk'))
		->values(array(
				'kodero' => $kodero,
				'uraian' => $uraian,
				'jumlahmasuk' => $jumlah,
				'tanggal' => $tanggalsql,
				'koderod' => $koderod,
				'kodeuk' => $kodeuk,
				))
		->execute();
		drupal_set_message('Data Telah Tersimpan');
	}
	else if($form_state['clicked_button']['#value'] == $form_state['values']['detil']) {
		
		drupal_goto('/detilrekening/'.$kodero);
	}
	
}



?>
