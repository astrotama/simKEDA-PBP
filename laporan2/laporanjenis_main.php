<?php
function laporanjenis_main($arg=NULL, $nama=NULL) {
   $kodero=arg(1);
   $bulan=arg(2);
   $kodeuk=apbd_getuseruk();
	if(isSuperuser()){
		$kodeuk=arg(4);
	}
   if(arg(3)!=null)
	   $margin=arg(3);  
   else
	  $margin=10; 
   
   
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
	
	
	$output = getlaporanjenis($kodero,$bulan,$kodeuk);
	//$output2= footer();
	apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
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

function getlaporanjenis($kodero,$bulan,$kodeuk){
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
		array('data' => 'Nomor urut','rowspan'=>'2', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Diterima dari / uraian', 'rowspan'=>'2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah','rowspan'=>'1', 'width' => '220px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => 'Penerimaan','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Penyetoran', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '1', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '2','width' => '240px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => '3','width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => '4', 'width' => '110px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		
	);
	
	$no=0;
	$totmasuk=0;
	$totkeluar=0;
	//ITEM................
	$result=db_query('select distinct rod.uraian, s.koderod from rincianobyekdetil as rod inner join setor as s on s.koderod=rod.koderod where s.kodero= :kodero and s.kodeuk=:kodeuk and month(s.tanggal)= :bulan', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':bulan'=>$bulan));
	foreach($result as $data){
		$no++;
		$rows[]=array(
			array('data' => $no.' ', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => ' Terima '.$data->uraian. ' dari :', 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
		$results=db_query('select s.setorid , s.uraian, s.jumlahmasuk ,s.jumlahkeluar from setor as s where s.koderod= :koderod and s.jumlahmasuk!=0  and month(s.tanggal)= :bulan and s.kodeuk=:kodeuk and s.kodero=:kodero order by s.uraian', array(':koderod'=>$data->koderod,':bulan'=>$bulan,':kodeuk'=>$kodeuk,':kodero'=>$kodero));
		foreach($results as $datas){
				$totmasuk+=$datas->jumlahmasuk;
				$totalcount=0;
				$re=db_query("select count(setorid) as ct from setor where idmasuk like '%".$datas->setorid."%' and month(tanggal)=:bulan", array(':bulan'=>$bulan));
				foreach($re as $da){
					$totalcount=$da->ct;
				}
				$rows[]=array(
				array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
				array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
				array('data' => '-'.$datas->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($datas->jumlahmasuk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			);
		}
		$totsetor=0;
		$results=db_query('select s.uraian, s.jumlahmasuk ,s.jumlahkeluar from setor as s where s.koderod= :koderod and s.jumlahmasuk=0 and month(s.tanggal)=:bulan and kodeuk=:kodeuk and kodero=:kodero', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':koderod'=>$data->koderod,':bulan'=>$bulan));
		foreach($results as $datas){
				$totsetor+=$datas->jumlahkeluar;
				
		}
		$rows[]=array(
			array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
			array('data' => 'Disetor', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($totsetor), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
		$totkeluar+=$totsetor;
	}
	//................................
	
		$rows[]=array(
			array('data' => $no.' ', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => ' Terima No Detil dari :', 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
		$results=db_query('select s.uraian, s.jumlahmasuk ,s.jumlahkeluar from setor as s where s.jumlahmasuk!=0  and month(s.tanggal)= :bulan and s.koderod=0 and kodeuk=:kodeuk and kodero=:kodero order by s.setorid', array(':bulan'=>$bulan,':kodeuk'=>$kodeuk,':kodero'=>$kodero));
		foreach($results as $datas){
				$totmasuk+=$datas->jumlahmasuk;
				$rows[]=array(
				array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
				array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
				array('data' => '-'.$datas->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($datas->jumlahmasuk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			);
		}
		$totsetor=0;
		$results=db_query('select s.uraian, s.jumlahmasuk ,s.jumlahkeluar from setor as s where  s.koderod=0 and s.jumlahmasuk=0 and month(s.tanggal)=:bulan and kodeuk=:kodeuk and kodero=:kodero', array(':kodeuk'=>$kodeuk,':bulan'=>$bulan,':kodero'=>$kodero));
		foreach($results as $datas){
				$totsetor+=$datas->jumlahkeluar;
				
		}
		$rows[]=array(
			array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
			array('data' => 'Disetor', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($totsetor), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
		);
		$totkeluar+=$totsetor;
	
	//...................END
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
	$results=db_query('select  sum(s.jumlahmasuk) as masuk,sum(s.jumlahkeluar) as keluar from setor as s where s.kodero= '.$kodero.' and s.kodeuk= :kodeuk and month(s.tanggal) < '.$bulan,array(':kodeuk'=>$kodeuk));
		foreach($results as $datar){
			$totmasuksebelum=$datar->masuk;
			$totkeluarsebelum=$datar->keluar;
			
		}
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => 'Jumlah bulan ini', 'width' => '200px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totmasuk), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totkeluar), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
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
			array('data' => apbd_fn($totmasuk+$totmasuksebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totkeluar+$totkeluarsebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => 'Sisa', 'width' => '200px','align'=>'cen','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn(($totmasuk+$totmasuksebelum)-($totkeluar+$totkeluarsebelum)), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
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

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */



?>
