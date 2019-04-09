<?php
function laporanjenis3_main($arg=NULL, $nama=NULL) {
   $kodero=arg(1);
   $bulan=arg(2);
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
	
	
	$output = getlaporanjenis($kodero,$bulan);
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

function getlaporanjenis($kodero,$bulan){
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
		array('data' => '2017', 'width' => '140px','align'=>'left','style'=>'border:none;font-weight:bold;font-size:90%;'),
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
	$totalmasuk=0;
	$totalkeluar=0;
	$totsetor=0;
	//ITEM................
	$result=db_query('select s.tanggal,s.kodero,s.uraian,s.setorid,s.idmasuk,s.jumlahkeluar from  setor as s  where s.kodero= :kodero and s.kodeuk=:kodeuk and month(s.tanggal)= :bulan and s.jumlahmasuk=0 order by s.tanggal', array(':kodero'=>$kodero,':kodeuk'=>$kodeuk,':bulan'=>$bulan));
	
	foreach($result as $data){
		$no++;
		$rows[]=array(
				array('data' =>$data->setorid.$no, 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;font-weight:bold'),
				//array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
				array('data' => $data->uraian, 'width' => '240px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold'),
				array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($data->jumlahkeluar), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold'),
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
					$sss="#".$datas->setorid;
				}
				//sizeof($datasetorid).''.$double
				$rows[]=array(
				array('data' =>sizeof($datasetorid).''.$double, 'width' => '50px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%'),
				array('data' => $datas->setorid, 'width' => '40px','align'=>'left','style'=>'font-size:80%'),
				array('data' => $sss.'-'.$datas->uraian, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($datas->jumlahmasuk), 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
				array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%'),
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
				array('data' =>sizeof($dataid).'%'.sizeof($datasetorid), 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
				array('data' => $hilang.'', 'width' => '40px','align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;'),
				array('data' => 'Disetor', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
				array('data' => '('.apbd_fn($totmasuk).')', 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
				array('data' => '('.apbd_fn($totsetor).')', 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
			);
		}
		else{
			$rows[]=array(
				array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
				array('data' => '', 'width' => '40px','align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;'),
				array('data' => 'Disetor', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($totmasuk), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
				array('data' => apbd_fn($totsetor), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
			);
		}
		
		
		//$totalkeluar+=$data->jumlahkeluar;
		$totalkeluar+=$totsetor;
	}
	$rows[]=array(
			array('data' =>'', 'width' => '50px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%'),
			array('data' => 'TOTAL', 'width' => '40px','align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;font-weight:bold'),
			array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%'),
			array('data' => apbd_fn($totalmasuk), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold'),
			array('data' => apbd_fn($totalkeluar), 'width' => '110px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-size:80%;font-weight:bold'),
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
	$results=db_query('select  sum(s.jumlahmasuk) as masuk,sum(s.jumlahkeluar) as keluar from setor as s where s.kodero= '.$kodero.' and s.kodeuk= :kodeuk and month(s.tanggal) < '.$bulan.' and (jumlahkeluar<>0 or idkeluar=1)',array(':kodeuk'=>$kodeuk));
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
			array('data' => 'Jumlah s/d bulan ini '.$bulantext[$bulan-1][1].' '.$bulantext[$bulan-1][0].' 2017', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalmasuk+$totmasuksebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn($totalkeluar+$totkeluarsebelum), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'right','style'=>'font-size:80%'),
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => 'Sisa', 'width' => '200px','align'=>'cen','style'=>'font-size:80%;font-weight:bold;'),
			array('data' => '', 'width' => '110px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => ' Rp.', 'width' => '20px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
			array('data' => apbd_fn(($totalmasuk+$totmasuksebelum)-($totalkeluar+$totkeluarsebelum)), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:80%;font-weight:bold;'),
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

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */



?>
