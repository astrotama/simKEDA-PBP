<?php
function laporanberitaacara_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 140px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	$margin=arg(2);
	$output = getlaporanberitaacara();
	//$output2= footer();
	apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
	//apbd_ExportPDF('P', 'F4', $output, 'CEK');
	//print_pdf_p($output,$output2);
	
	
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

function getlaporanberitaacara(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$result=db_query("select namauk,namasingkat,bendaharanama,bendaharanip from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($result as $data){
		$namasingkat=$data->namauk;
		$namas=$data->namasingkat;
		$bendaharanama=$data->bendaharanama;
		$bendaharanip=$data->bendaharanip;
	}
	$header=array();
	if(arg(1)==1){
		$month=array(1,2,3);
		$triwulan='I';
	}
	else if(arg(1)==2){
		$month=array(4,5,6);
		$triwulan='II';
	}
	else if(arg(1)==3){
		$month=array(7,8,9);
		$triwulan='III';
	}
	else if(arg(1)==4){
		$month=array(10,11,12);
		$triwulan='IV';
	}
	$rows[]=array(
		array('data' => 'BERITA ACARA REKONSILIASI', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor :', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'TENTANG', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'DATA PENERIMAAN '.strtoupper($namasingkat), 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$bulantw=array("April","Juli","Oktober","Januari");
	$rows[]=array(
		array('data' => '  Pada hari ini senin tanggal lima '.$bulantw[arg(1)-1].' tahun 2017 bertempat di BPKAD Kab. Jepara telah dilaksanakan. Rekonsiliasi Data Penerimaan Pendapatan Triwulan '.$triwulan.' antara BPKAD Dan '.ucwords(strtolower($namasingkat)).' dimana dalam rekonsiliasi dimaksud telah dicapai kesepakatan kedua belah pihak antara lain:', 'width' => '500px','align'=>'justify','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Uraian', 'width' => '200px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Anggaran', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah Realisasi', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => '%', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	$result=db_query("SELECT  distinct s.kodeuk,ro.uraian,ro.kodero as kodero, (select sum(s.jumlahmasuk) from setor as s where  kodeuk=:kodeuk and s.kodero=ro.kodero) as juml FROM `setor` as s inner join rincianobyek as ro on ro.kodero=s.kodero where kodeuk=:kodeuk  and month(s.tanggal) in (:month)",array(':kodeuk'=>$kodeuk,':month'=>$month));
	//Content
	$no=0;
	foreach($result as $data){
		if(isPusekesmas()){
			if(isBlud()){
				if($data->kodero=='41416002'){
					
				}
				else{
					$results=db_query("SELECT  jumlah from anggperuk where kodero=:kodero and kodeuk=:kodeuk",array(':kodero'=>$data->kodero,':kodeuk'=>$kodeuk));
					foreach($results as $datauk){
						$no++;
						$rows[]=array(
							array('data' => $no.' ', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
							array('data' => $data->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;'),
							array('data' => apbd_fn($datauk->jumlah), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
							array('data' => apbd_fn($data->juml), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
							array('data' => apbd_fn2(apbd_hitungpersen($datauk->jumlah,$data->juml)), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
						);
					}
				}
			}
		}
		else{
			$results=db_query("SELECT  jumlah from anggperuk where kodero=:kodero and kodeuk=:kodeuk",array(':kodero'=>$data->kodero,':kodeuk'=>$kodeuk));
			foreach($results as $datauk){
				$no++;
				$rows[]=array(
					array('data' => $no.' ', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => $data->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($datauk->jumlah), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($data->juml), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn2(apbd_hitungpersen($datauk->jumlah,$data->juml)), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
				);
			}
		}
		
	}
	if($no==0){
		$rows[]=array(
				array('data' => '', 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => 'Data Masih Kosong', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;'),
			);
	}
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border-top:1px solid black;'),
	);
	$rows[] = array(
					array('data' => 'Sebagai mana data terlampir,','width' => '670px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Demikian Berita Acara ini dibuat untuk dilaksanakan sebagaimana perbaikan data-data.','width' => '510px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Kepala Sub Bidang Pelaporan Retribusi','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Bendahara Penerima','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'dan Dana Transfer','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => strtoupper($namas).' KAB. JEPARA','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Kabupaten Jepara','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
				array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'S.KENDAR PRAPTOMO,SH.MM.','width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
	);
	$rows[] = array(
					array('data' => 'NIP. 19711216 199703 1 005','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP.'.$bendaharanip,'width' => '250px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
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
