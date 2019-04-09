<?php
function laporanjenis2_main($arg=NULL, $nama=NULL) {

   
	if (arg(3)!=null) {
		$kodero=arg(1);
		$bulan=arg(2);
		$margin=arg(3);  
		$kodeuk=arg(4);  
		
	} else {
		$kodero= '41101004';
		$bulan= date('m');
		$margin=10;  
		$kodeuk='81'; 
		
	}
	
	drupal_set_title('Laporan Jenis');
	
	if (arg(5) == 'pdf'){
		
		$output = getlaporanjenishtml($kodero,$bulan);
		apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
		
	} else if (arg(5) == 'excel'){
		$output = getlaporanjenishtml($kodero,$bulan);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Penerimaan Jenis.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	
	} else {
		$output_form = drupal_get_form('laporanjenis2_main_form');
		$btn = "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Cetak Pdf</span>', 'laporanjenis2/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk.'/pdf', array ('html' => true));
		$btn .= "&nbsp;" . l('<span class="btn btn-primary pull-right" aria-hidden="true">Excel</span>', 'laporanjenis2/'.$kodero.'/'.$bulan.'/'.$margin.'/'.$kodeuk.'/excel', array ('html' => true));
		
		$output = getlaporanjenishtml($kodero,$bulan);
		return drupal_render($output_form) . $btn . $output;
	}
	
	
}

function laporanjenis2_main_form($form, &$form_state) {
	$kodeuk= apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$form['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);	
	$result=db_query('select s.kodero, ro.uraian from anggperuk as s inner join rincianobyek as ro on s.kodero=ro.kodero where s.kodeuk=:kodeuk',array(':kodeuk'=>$kodeuk));
	$optionrek=null;
	drupal_set_message();
	foreach($result as $data){
		$optionrek[$data->kodero]=$data->uraian;
	}
	$bulan=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desemebr');
	if(!isSuperuser() and !isDKK()){
		$form['rek']= array(
			'#type' => 'select',
			'#options' => $optionrek,
			'#title'=>'Rekening',
		);
	}

	//AJAX............................
		if(isSuperuser() || isDKK()){
			$res=db_query("SELECT distinct a.kodeuk,u.namasingkat FROM unitkerja as u inner join anggperuk as a on a.kodeuk=u.kodeuk where  a.jumlah>0 order By u.namasingkat");
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
				$uk=arg(4);
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
	
	$form['margin']= array(
		'#type' => 'textfield',
		'#title'=>'Margin',
		'#default_value' => 10,
	);
	$form['bulan']= array(
		'#type' => 'select',
		'#options' => $bulan,
		'#title'=>'Bulan',
	);	
	$form['cetak']= array(
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}
	
function laporanjenis2_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	//$rek = $form_state['values']['rek'];
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK()){
		$kodeuk= $form_state['values']['dropdown_first'];
		$rek= $form_state['values']['dropdown_second'];
	}else{
		$rek = $form_state['values']['rek'];
	}
	$bulan+=1;
	drupal_goto('laporanjenis2/'.$rek.'/'.$bulan.'/'.$margin.'/'.$kodeuk);

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

function getlaporanjenis($kodero,$bulan){

//drupal_set_message('x');

	$bulantext=array(
	array('Januari',31),
	array('Februari',28),
	array('Maret',31),
	array('April',30),
	array('Mei',31),
	array('Juni',30),
	array('Juli',31),
	array('Agustus',31),
	array('September',30),
	array('Oktober',31),
	array('November',30),
	array('Desember',31)
	);
	//$kodeuk=apbd_getuseruk();
	$kodeuk=apbd_getuseruk();
	if(isSuperuser()){
		$kodeuk=arg(4);
	}
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat,u.bendaharanama ,u.bendaharanip, s.jumlahmasuk, s.jumlahkeluar ,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
		//$total+=$data->jumlah;
	}
	$kodero=arg(1);
	$results=db_query("select ro.uraian from rincianobyek as ro where ro.kodero=:kodero",array(':kodero'=>$kodero));
	foreach($results as $data){
		$namarek=$data->uraian;
	}
	$ayat=substr_replace($kodero,'.',1,0);
	$ayat=substr_replace($ayat,'.',3,0);
	$ayat=substr_replace($ayat,'.',5,0);
	$ayat=substr_replace($ayat,'.',8,0);
	$header=array();
	$rows[]=array(
		array('data' => 'Bulan :', 'width' => '50px','align'=>'left','style'=>'border:none;font-size:100%;'),
		array('data' => $bulantext[$bulan-1][0], 'width' => '430px','align'=>'left','style'=>'border:none;font-size:100%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'BUKU PENERIMAAN SEJENIS', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'AYAT', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => $ayat, 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => 'URAIAN AYAT', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => $namarek, 'width' => '340px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
		//array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => '2019', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
		array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$rows=null;
	$header=null;
	$rows[]=array(
		array('data' => 'Nomor urut','rowspan'=>'2', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:75%'),
		array('data' => 'Diterima dari / uraian', 'rowspan'=>'2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:75%'),
		array('data' => 'Jumlah','rowspan'=>'1', 'width' => '220px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:75%'),
		
	);
	$rows[]=array(
		array('data' => 'Penerimaan','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:75%'),
		array('data' => 'Penyetoran', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:75%'),
		
	);
	$rows[]=array(
		array('data' => '1', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%'),
		array('data' => '2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%'),
		array('data' => '3','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%'),
		array('data' => '4', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%'),
		
	);
	
	$no=0;
	$totmasuk=0;
	$totkeluar=0;
	$totalmasuk=0;
	$totalkeluar=0;
	$totsetor=0;
	//ITEM................
	$result=db_query('select s.tanggal,s.kodero,s.uraian,s.setorid,s.idmasuk,s.jumlahkeluar from  setor as s  where s.kodero= :kodero and s.kodeuk=:kodeuk and month(s.tanggal)= :bulan order by s.tanggal', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':bulan'=>$bulan));
	
	foreach($result as $data){
		$no++;
		$rows[]=array(
				array('data' =>$no, 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;font-weight:bold'),
				//array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:70%'),
				array('data' => $data->uraian, 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;font-weight:bold'),
				array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
				array('data' => apbd_fn($data->jumlahkeluar), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;font-weight:bold'),
			);
		$dataid=explode(",",$data->idmasuk);
		$totmasuk=0;
		$results=db_query('select idkeluar,kodero,kodeuk,month(s.tanggal) as bulan,day(s.tanggal) as hari,s.setorid,s.uraian, s.jumlahmasuk  from setor as s where s.jumlahmasuk!=0  and s.setorid in(:idmasuk) and s.kodero=:kodero and month(tanggal)=:bulan', array(':idmasuk'=>$dataid,':kodero'=>$kodero,':bulan'=>$bulan));
		$x=0;
		$datasetorid=Null;
		foreach($results as $datas){
				if($datas->bulan==6){
					$str='('.$datas->kodeuk.')';
				}else{
					$str='';
				}
				$datasetorid[]=$datas->setorid;
				$x++;
				$double='';
				for($z=0;$z<sizeof($datasetorid)-1;$z++){
					if($datas->setorid==$datasetorid[$z]){
						$double='double';
					}
				}
				$sss='';
				if($datas->idkeluar!=1){
					$sss="Validasi";
				}
				//sizeof($datasetorid).''.$double
				$rows[]=array(
				array('data' =>$sss, 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%'),
				array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:70%'),
				array('data' => '-'.$datas->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%'),
				array('data' => apbd_fn($datas->jumlahmasuk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
				array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
			);
			$totmasuk+=$datas->jumlahmasuk;
			$totalmasuk+=$datas->jumlahmasuk;
		}
		//out
		$hilang=null;
		for($n=0;$n<sizeof($dataid);$n++){
			$ketemu=false;
			for($m=0;$m<sizeof($datasetorid);$m++){
				if($dataid[$n] == $datasetorid[$m] && $ketemu==false){
					$sambung='';$ketemu=true;
				}
				if($m==(sizeof($datasetorid)-1) && $ketemu==false){
					$hilang.=$dataid[$n].'&';
				}
				
			}
		}
		$totsetor=$data->jumlahkeluar;
		if($totmasuk!=$totsetor){
			$rows[]=array(
				array('data' =>sizeof($dataid).'%'.sizeof($datasetorid), 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%'),
				array('data' => $hilang.'', 'width' => '40px','align'=>'left','style'=>'font-size:70%;border-bottom:1px solid black;border-top:1px solid black;'),
				array('data' => 'Disetor', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
				array('data' => '('.apbd_fn($totmasuk).')', 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
				array('data' => '('.apbd_fn($totsetor).')', 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
			);
		}
		else{
			$rows[]=array(
				array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%'),
				array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:70%;border-bottom:1px solid black;border-top:1px solid black;'),
				array('data' => 'Disetor', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
				array('data' => apbd_fn($totmasuk), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
				array('data' => apbd_fn($totsetor), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
			);
		}
		
		
		//$totalkeluar+=$data->jumlahkeluar;
		$totalkeluar+=$totsetor;
	}
	$rows[]=array(
			array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%'),
			array('data' => 'TOTAL', 'width' => '40px','align'=>'left','style'=>'font-size:70%;border-bottom:1px solid black;border-top:1px solid black;font-weight:bold'),
			array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%'),
			array('data' => apbd_fn($totalmasuk), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%;font-weight:bold'),
			array('data' => apbd_fn($totalkeluar), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:70%;font-weight:bold'),
		);
	//................................
	
	if($no==0){
		$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%'),
			array('data' => 'Data Masih Kosong', 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '110px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
		);
	}
	$totmasuksebelum=0;
	$totkeluarsebelum=0;
	//$results=db_query('select  sum(s.jumlahmasuk) as masuk,sum(s.jumlahkeluar) as keluar from setor as s where s.kodero= '.$kodero.' and s.kodeuk= :kodeuk and month(s.tanggal) < '.$bulan.' and (jumlahkeluar<>0 or idkeluar=1)',array(':kodeuk'=>$kodeuk));
	$results=db_query('select  sum(s.jumlahmasuk) as masuk,sum(s.jumlahkeluar) as keluar from setor as s where s.kodero= '.$kodero.' and s.kodeuk= :kodeuk and month(s.tanggal) < '.$bulan  ,array(':kodeuk'=>$kodeuk));
		foreach($results as $datar){
			$totmasuksebelum=$datar->masuk;
			$totkeluarsebelum=$datar->keluar;
			
		}
		
	//drupal_set_message('laporanjenis2');		
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => 'Jumlah bulan ini', 'width' => '200px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-top:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => 'Jumlah s/d bulan lalu', 'width' => '200px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totmasuksebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totkeluarsebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'font-size:70%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:70%;font-weight:bold;'),
			array('data' => 'Jumlah s/d bulan ini '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk+$totmasuksebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar+$totkeluarsebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'font-size:70%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:70%;font-weight:bold;'),
			array('data' => 'Sisa', 'width' => '200px','align'=>'cen','style'=>'font-size:70%;font-weight:bold;'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
			array('data' => apbd_fn(($totalmasuk+$totmasuksebelum)-($totalkeluar+$totkeluarsebelum)), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:70%;font-weight:bold;'),
		);
	//................................
	
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => ucwords(strtolower('BENDAHARA khusus penerima')),'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => $bendaharanama,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

function getlaporanjenishtml($kodero,$bulan){
	$bulantext=array(
	array('Januari',31),
	array('Februari',28),
	array('Maret',31),
	array('April',30),
	array('Mei',31),
	array('Juni',30),
	array('Juli',31),
	array('Agustus',31),
	array('September',30),
	array('Oktober',31),
	array('November',30),
	array('Desember',31)
	);
	//$kodeuk=apbd_getuseruk();
	$kodeuk=apbd_getuseruk();
	if(isSuperuser() || isDKK()){
		if (arg(3)!=null) {
			$kodeuk=arg(4);
		} else {
			$kodeuk= '81';
		}
	}else {
		$kodeuk=apbd_getuseruk();
	}
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$results=db_query('select u.namasingkat,u.bendaharanama ,u.bendaharanip, s.jumlahmasuk, s.jumlahkeluar ,s.kodeuk,s.uraian,ro.uraian as namarek,s.kodero from setor as s inner join rincianobyek as ro on s.kodero=ro.kodero inner join unitkerja as u on u.kodeuk=s.kodeuk where s.kodeuk= :kodeuk',array('kodeuk'=>$kodeuk));
	$total=0;
	foreach ($results as $data) {
		$namauk=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
		//$total+=$data->jumlah;
	}
	$kodero=arg(1);
	$results=db_query("select ro.uraian from rincianobyek as ro where ro.kodero=:kodero",array(':kodero'=>$kodero));
	foreach($results as $data){
		$namarek=$data->uraian;
	}
	$ayat=substr_replace($kodero,'.',1,0);
	$ayat=substr_replace($ayat,'.',3,0);
	$ayat=substr_replace($ayat,'.',5,0);
	$ayat=substr_replace($ayat,'.',8,0);
	
	//
	$header=array();
	$rows[]=array(
		array('data' => 'Bulan :', 'colspan' => '1','width' => '50px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => $bulantext[$bulan-1][0], 'colspan' => '1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:00%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'BUKU PENERIMAAN SEJENIS','colspan' => '9', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '','colspan' => '9', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'AYAT','colspan' => '1', 'width' => '40px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => $ayat,'colspan' => '4', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'URAIAN','colspan' => '1', 'width' => '40px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => $namarek, 'colspan' => '4','width' => '340px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:00%;'),
		//array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN','colspan' => '1', 'width' => '40px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:80%;'),
		array('data' => '2019','colspan' => '4', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:80%;'),
	);	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$rows=null;
	$header=null;
	$rows[]=array(
		array('data' => 'Nomor urut','rowspan'=>'2', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Diterima dari / uraian','colspan'=>'2', 'rowspan'=>'2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah','rowspan'=>'1','colspan'=>'4', 'width' => '220px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => 'Penerimaan','colspan'=>'2','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Penyetoran','colspan'=>'2', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '1', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '2','colspan'=>'2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => '3','colspan'=>'2','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => '4','colspan'=>'2', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
	);
	
	$no=0;
	$totalmasuk=0;
	$totalkeluar=0;

	//ITEM................
	$result=db_query('select r.kodero,r.uraian, s.idkeluar, sum(s.jumlahkeluar) as jumlahkeluar from  setor as s inner join rincianobyek as r on s.kodero=r.kodero where s.kodero= :kodero and s.kodeuk=:kodeuk and month(s.tgl_keluar)= :bulan and s.jumlahkeluar>0 order by s.kodero', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':bulan'=>$bulan));
	
	foreach($result as $data){
		$no++;
		$totalkeluar += $data->jumlahkeluar;
		
		$rows[]=array(
				array('data' =>$no, 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;font-weight:bold'),
				array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
				array('data' => $data->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold'),
				array('data' => '', 'colspan'=>'2','width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($data->jumlahkeluar),'colspan'=>'2', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold'),
			);
		
		
		
	}
	//MASUK
	$result=db_query('select uraian, jumlahmasuk from  setor where kodero= :kodero and kodeuk=:kodeuk and month(tanggal)=:bulan and jumlahmasuk>0 and idkeluar>0 order by tanggal, setorid', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':bulan'=>$bulan));
	
	foreach($result as $data){
		//$no++;
		$totalmasuk += $data->jumlahmasuk;
		
		$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:70%'),
			array('data' => '-'.$data->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:70%'),
			array('data' => '', 'colspan'=>'2','width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
			array('data' => apbd_fn($data->jumlahmasuk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%'),
		);
	}	
	$rows[]=array(
			array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => 'TOTAL', 'width' => '40px','align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;font-weight:bold'),
			array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($totalmasuk),'colspan'=>'2', 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold'),
			array('data' => apbd_fn($totalkeluar), 'colspan'=>'2','width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold'),
		);
	//................................
	
	if($no==0){
		$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => 'Data Masih Kosong', 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
	}


	$totmasuksebelum=0;
	$totkeluarsebelum=0;
	//$results=db_query('select  sum(s.jumlahmasuk) as masuk,sum(s.jumlahkeluar) as keluar from setor as s where s.kodero= '.$kodero.' and s.kodeuk= :kodeuk and month(s.tanggal) < '.$bulan.' and (jumlahkeluar<>0 or idkeluar=1)',array(':kodeuk'=>$kodeuk));
	$results=db_query('select  sum(s.jumlahmasuk) as masuk,sum(s.jumlahkeluar) as keluar from setor as s where s.kodero= '.$kodero.' and s.kodeuk= :kodeuk and month(s.tanggal) < '.$bulan  ,array(':kodeuk'=>$kodeuk));
		foreach($results as $datar){
			$totmasuksebelum=$datar->masuk;
			$totkeluarsebelum=$datar->keluar;
			
		}
		
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => 'Jumlah bulan ini', 'width' => '200px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-top:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => 'Jumlah s/d bulan lalu', 'width' => '200px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totmasuksebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totkeluarsebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => 'Jumlah s/d bulan ini '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2019', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk+$totmasuksebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar+$totkeluarsebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => 'Sisa','colspan'=>'2', 'width' => '200px','align'=>'cen','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn(($totalmasuk+$totmasuksebelum)-($totalkeluar+$totkeluarsebelum)), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	//................................
	
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => ucwords(strtolower('BENDAHARA khusus penerima')),'colspan'=>'4','width' => '170px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'7','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => $bendaharanama,'colspan'=>'4','width' => '170px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	$rows[] = array(
					array('data' => '','colspan'=>'3','width' => '250px', 'align'=>'center','style'=>'border:none;font-size:80%'),
					array('data' => 'NIP.'.$bendaharanip,'colspan'=>'4','width' => '170px', 'align'=>'center','style'=>'border:none;font-size:80%'),
	);
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */



?>
