<?php
function laporanbp2d_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
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
	
	
	$output = getlaporanbp2d();
	print_pdf_p($output);
	
	
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
function getlaporanbp2d(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '150px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'BADAN PERENCANAAN PEMBANGUNAN DAERAH', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'JL. PATIMURA NO. 4 JEPARA', 'width' => '625px','align'=>'center','style'=>'border-bottom-style:double;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERNYATAAN PENGAJUAN SPP-LS GAJI TUNJANGAN', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ........', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:125%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Pembayaran Langsung Gaji dan Tunjangan(LS GAJI TUNJANGAN) yang kami ajukan sebesar Rp. 193.021.457 (terbilang Seratus Sembilah Puluh Tiga Juta Dua Puluh Satu Ribu Empat Ratus Lima Puluh Tujuh Rupiah) untuk keperluan SKPD BADAN PERENCANAAN PEMBANGUNAN DAERAH Tahun Anggaran 2016, dengan ini menyatakan dengan sebenarnya bahwa :', 'width' => '625px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '75px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Langsung Gaji dan Tunjangan (LS GAJI TUNJANGAN) tersebut diatas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami laksanakan sesuai dengan DPA-SKPD.', 'width' => '550px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '75px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Langsung Gaji dan Tunjangan (LS GAJI TUNJANGAN) tersebut tidak akan digunakan untuk membiayai pengeluaran- pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung.', 'width' => '550px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan Pembayaran Langsung Gaji dan Tunjangan(LS GAJI TUNJANGAN) SKPD kami', 'width' => '625px','align'=>'left','style'=>'border:none;'),
	);
$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, 14 Desember 2016','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Pengguna Anggaran/Kuasa Pengguna Anggaran','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;font-weight:bold;'),
					array('data' => 'Ir.SUJAROT','width' => '300px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. 196104131986031007','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}


function getlaporanbp2d2(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Lembar Asli', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Salinan 1', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Salinan 2', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Salinan 3', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PENELITIAN KELENGKAPAN DOKUMEN SPP', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : 001/TL/0401/2016', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '625px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMBAYARAN LANGSUNG GAJI [SPP-LS GAJI]', 'width' => '625px','align'=>'left','style'=>'border:none;'),
	);
	$checkbox=array(
		'Surat Pengantar SPP-LS',
		'Ringkasan SPP-LS',
		'Rincian SPP-LS',
		'Gaji Susulan',
		'Pembayaran Gaji Induk',
		'Kekurangan Gaji',
		'Gaji Terusan',
		'Uang duka wafat / tewas yang dilengkapi dengan daftar gaji induk /gaji susulan/kekurangan gaji/uang duka wafat/tewas',
		'SK CPNS',
		'SK PNS',
		'SK Kenaikan Pangkat',
		'SK Jabatan',
		'Kenaikan Gaji Berkala',
		'Surat Pernyataan Pelantikan',
		'Surat Pernyataan Masih Menduduki Jabatan',
		'Surat Pernyataan melaksanakan tugas',
		'Daftar Keluarga(KP4)',
		'Fotokopi surat nikah',
		'Fotokopi akte kelahiran',
		'SKPP',
		'Daftar potongan sewa rumah dinas',
		'Surat keterangan masih sekolah/kuliah',
		'Surat pindah',
		'Surat kematian',
		'SSP PPh Pasal 21',
		'Peraturan perundang-undangan mengenai penghasilan pimpinan dan anggota DPRD serta gaji dan tunjangan kepala daerah/wakil kepala daerah',
	);
	for($n=0;$n<sizeof($checkbox);$n++){
		$rows[]=array(
			array('data' => '<div style="width:5px;height:5px;background:red"></div>', 'width' => '25px','align'=>'left','style'=>'border:0.1px solid black;'),
			array('data' => '', 'width' => '5px','align'=>'left','style'=>'border:none'),
			array('data' => $checkbox[$n], 'width' => '595px','align'=>'left','style'=>'border:none;'),
		);
	}
	$rows[]=array(
		array('data' => 'PENELITI KELENGKAPAN DOKUMEN SPP', 'width' => '625px','align'=>'left','style'=>'border:none;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Nama', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NIP', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanda Tangan', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$attributes=array('style'=>'cellspacing="10";');
	$output = theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => $attributes));
	
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
