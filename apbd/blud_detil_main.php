<?php
function blud_detil_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	//drupal_add_js('files/js/blud.js');
	$namauk="120.123";
	if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			//$kodeuk = $_SESSION["setorarsip_kodeuk"];
			$kodeuk = '37';
		}
		
	$res=db_query("select namauk from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($res as $data){
		$namauk=$data->namauk;
	}
	
	drupal_set_title($namauk);
	//drupal_set_message(arg(1));
	$output_form = drupal_get_form('blud_detil_main_form');
	return drupal_render($output_form);// . $output;
	
}

function blud_detil_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	/*$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjgajilastpage"] = $referer;
	else
		$referer = $_SESSION["spjgajilastpage"];*/
	
	//db_set_active('penatausahaan');
	if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			//$kodeuk = $_SESSION["setorarsip_kodeuk"];
			$kodeuk = '37';
		}

	/*$form['kodero'] = array(
		'#type' => 'value',
		'#value' => $kodero,
	);*/
	$result=db_query("select anggaran from anggperuk where kodero in('41416002','41416001') and kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($result as $data){
		$anggaran=$data->anggaran;
	}
	//drupal_set_message($kodeuk);
	//PAJAK	
	$form['formdetil']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="140px">KODE</th><th>REKENING</th><th width="200px">ANGGARAN</th></tr>',
		 '#suffix' => '</table>',
	);	 
	$java='<script type="text/javascript">
				
				$("[id^=edit-jumlah]").blur(function(){
					//alert("CE");
					var n1=parseInt($("[id^=edit-jumlah1]").val().split(".").join(""));
					var n2=parseInt($("[id^=edit-jumlah2]").val().split(".").join(""));
					var n3=parseInt($("[id^=edit-jumlah3]").val().split(".").join(""));
					var n4=parseInt($("[id^=edit-jumlah4]").val().split(".").join(""));
					//var n5=parseInt($("[id^=edit-jumlah5]").val().split(".").join(""));
					var rp=$( this ).val();
					$("[id^=jumlahtotal]").text(toRp(n1+n2+n3+n4));
					var angg=$("[id^=jumlahanggaran]").text().split(".").join("");
					if((parseInt(angg)-(n1+n2+n3+n4))<0){
						$("[id^=selisih3]").text("Jumlah Melebihi Anggaran");
						$("[id^=selisih3]").css("color", "red");
					}
					else{
						$("[id^=selisih3]").text("Jumlah Selisih");
						$("[id^=selisih3]").css("color", "black");
					}
					$("[id^=jumlahselisih]").text(toRp(parseInt(angg)-(n1+n2+n3+n4)).split("-").join(""));
					temp=rp;
					if(rp>0){
						$( this ).val(toRp(rp));
					}
					else{
						$( this ).val("0");
					}
					$("[id^=jumlahtotal]").text(toRp(n1+n2+n3+n4));
					
				})
				$("[id^=edit-jumlah]").focus(function(){
					//alert("CE");
					var rp=$( this ).val();
					$( this ).val(rp.split(".").join(""));
					var n1=parseInt($("[id^=edit-jumlah1]").val().split(".").join(""));
					var n2=parseInt($("[id^=edit-jumlah2]").val().split(".").join(""));
					var n3=parseInt($("[id^=edit-jumlah3]").val().split(".").join(""));
					var n4=parseInt($("[id^=edit-jumlah4]").val().split(".").join(""));
					//var n5=parseInt($("[id^=edit-jumlah5]").val().split(".").join(""));
					
					
					$("[id^=jumlahtotal]").text(toRp(n1+n2+n3+n4));
					var angg=$("[id^=jumlahanggaran]").text().split(".").join("");
					if((parseInt(angg)-(n1+n2+n3+n4))<0){
						$("[id^=selisih3]").text("Jumlah Melebihi Anggaran");
						$("[id^=selisih3]").css("color", "red");
					}
					else{
						$("[id^=selisih3]").text("Jumlah Selisih");
						$("[id^=selisih3]").css("color", "black");
					}
					$("[id^=jumlahselisih]").text(toRp(parseInt(angg)-(n1+n2+n3+n4)).split("-").join(""));
					//$("[id^=jumlahselisih]").text(angg);
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
	$i = 0;
	$koderodat=array(4141600201,4141600202,4141600203,4141600204,4141600205);
	$narek=array('Pelayanan Kesehatan','Pendapatan JKN Kapitasi','Pendapatan JKN Klaim','Lain-lain Pendapatan BLUD yang Sah','Klaim Rawat Inap Pemkab');
	$tampanggaran=0;
	for ($i = 1; $i <=5; $i++)  {
		$datanggaran=0;
		$result=db_query("select anggaran from anggperuk where kodero=:kodero and kodeuk=:kodeuk",array(':kodero'=>$koderodat[$i-1], ':kodeuk'=>$kodeuk));
		foreach($result as $dataa){
			$datanggaran=$dataa->anggaran;
		}
		$tampanggaran+=$datanggaran;
		//$i++;
		
		$form['formdetil']['e_koderod' . $i]= array(
				'#type' => 'value',
				'#value' => 'new',
		); 
		
		$form['formdetil']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['koderod' . $i]= array(
				'#type'		=> 'textfield', 
				'#prefix' 	=> '<td>',
				'#default_value'=> $koderodat[$i-1], 
				'#disabled' =>true,
				'#size' => 2,
				'#maxlength' => 12,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['uraian' . $i]= array(
				'#type'		=> 'textfield', 
				'#prefix' 	=> '<td>',
				'#default_value'=> $narek[$i-1], 
				'#disabled' =>true,
				'#size' => 2,
				//'#maxlength' => 2,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['jumlah' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#attributes' => array('style' => 'text-align: right'),
			'#default_value'=> apbd_fn($datanggaran), 
			'#suffix' => '</td></tr>',
		); 

	}
//ROW Jumlah...........................	
	$form['formdetil']['juml']= array(
				'#prefix' => '<tr><td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['juml2']= array(
				'#prefix' 	=> '<td>',
				'#markup' => '',
				'#suffix' => '</td>',
		); 
		$form['formdetil']['juml3']= array(
				'#prefix' 	=> '<td>',
				'#markup' => "<h4 style='font-weight:bold'>Jumlah Sementara</h4>",
				'#suffix' => '</td>',
		); 
		$form['formdetil']['jumlahtotal']= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td align="right">',
			'#disabled'=>true,
			'#markup'=> "<h4 style='font-weight:bold;color:#00f'><div id='jumlahtotal'>".apbd_fn($tampanggaran)."</div></h4>", 
			'#suffix' => '</td></tr>',
		); 
//.................................................
//ROW ANGGARAN...........................	
	$form['formdetil']['angg']= array(
				'#prefix' => '<tr><td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['angg2']= array(
				'#prefix' 	=> '<td>',
				'#markup' => '',
				'#suffix' => '</td>',
		); 
		$form['formdetil']['angg3']= array(
				'#prefix' 	=> '<td>',
				'#markup' => "<h4 style='font-weight:bold;'>Jumlah Anggaran</h4>",
				'#suffix' => '</td>',
		); 
		$form['formdetil']['jumlahanggaran']= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td align="right">',
			'#disabled'=>true,
			'#markup'=> "<h4 style='font-weight:bold;color:#f00'><div id='jumlahanggaran'>".apbd_fn($anggaran)."</div></h4>", 
			'#suffix' => '</td></tr>',
		); 
//.................................................
//ROW SELISIH...........................	
	$form['formdetil']['selisih']= array(
				'#prefix' => '<tr><td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['selisih2']= array(
				'#prefix' 	=> '<td>',
				'#markup' => '',
				'#suffix' => '</td>',
		); 
		$form['formdetil']['selisih3']= array(
				'#prefix' 	=> '<td>',
				'#markup' => "<h4 style='font-weight:bold;'><div id='selisih3'>Jumlah Selisih</div></h4>",
				'#suffix' => '</td>',
		); 
		$form['formdetil']['jumlahselisih']= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td align="right">',
			'#disabled'=>true,
			'#markup'=> "<h4 style='font-weight:bold;'><div id='jumlahselisih'>".apbd_fn($anggaran-$tampanggaran)."</div></h4>", 
			'#suffix' => '</td></tr>',
		); 
//.................................................
	$form['formdetil']['jumlahdetil']= array(
		'#type' => 'value',
		'#value' => $i-1,
	);
	//drupal_set_message($i);
	$form['formdetil']['kodeuk']= array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['formdetil']['anggaran']= array(
		'#type' => 'value',
		'#value' => $anggaran,
		'#attached'=>array(
							'js'=>array(
									//drupal_get_path('module', 'akuntansi') . '/akuntansi.js',
									drupal_get_path('module', 'akuntansi') . '/jquery-1.10.2.js',
									
							)
					),
		'#suffix' => $java,
	);	
	
	//SIMPAN
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='/' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	
	return $form;
}

function blud_detil_main_form_validate($form, &$form_state) {
	$jumlahdetil = $form_state['values']['jumlahdetil'];
	$anggaran = $form_state['values']['anggaran'];
	$total=0;
	for($n=1; $n<=$jumlahdetil; $n++){
		$jumlah = $form_state['values']['jumlah'.$n];
		$total	+=getnilairp($jumlah);
		
	}
	if ($total!=$anggaran) form_set_error('jumlah', 'Jumlah Masih '.apbd_fn($total).' dari Anggaran '.apbd_fn($anggaran));
}
	
function blud_detil_main_form_submit($form, &$form_state) {
$kodeuk = $form_state['values']['kodeuk'];
$jumlahdetil = $form_state['values']['jumlahdetil'];

for($n=1; $n<=$jumlahdetil; $n++){
	//$e_koderod = $form_state['values']['e_koderod' . $n];
	$kodero = $form_state['values']['koderod' . $n];
	$uraian = $form_state['values']['uraian' . $n];
	$jumlah = $form_state['values']['jumlah'.$n];
	$res=db_query("SELECT count(kodero) as total FROM anggperuk WHERE kodero =:kodero and kodeuk=:kodeuk",array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
	foreach($res as $data){
		$total=$data->total;
	}
	if ($total==0) {
		
		$query = db_insert('anggperuk') // Table name no longer needs {}
					->fields(array(
					  'tahun' => 2018,
					  'kodero' => $kodero,
					  'kodeuk' => $kodeuk,
					  'uraian' => $uraian,				  
					  'jumlah' =>getnilairp($jumlah),
					  'anggaran' => getnilairp($jumlah),
			))
			//dpq($query);
			->execute();
		 
	} else {
											//new
			$query = db_update('anggperuk') // Table name no longer needs {}
					->fields(array(
					  'jumlah' =>getnilairp($jumlah),
					  'anggaran' => getnilairp($jumlah),
			))
			//dpq($query);
			
			->condition('kodero', $kodero, '=')
			->condition('kodeuk', $kodeuk, '=')
			//drupal_set_message("C");
			->execute();
					
			
	}

}
	
drupal_goto('');
	
}



?>
