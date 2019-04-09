<?php

function importexcel_import_form($form, $form_state) {
	
	drupal_set_title('Import Data Pendapatan');
	
	$kodero = $_SESSION['kodero'];
	
	$form['notes'] = array(
		'#type' => 'markup',
		'#markup' => '<div class="import-notes">Perhatian!<ul><li>Pastikan bahwa file yang di-upload adalah file csv.</li><li>Pastikan untuk menekan tombol Upload sebelum meng-import data</li></ul></div>',
	);

	$kodeuk=apbd_getuseruk();
	if($kodeuk==null){
		$kodeuk='81';
	}
	$form['kodeuk'] = array(
		'#type' => 'hidden',
		'#default_value' => $kodeuk,
	);	

	$query = db_select('anggperuk', 'a');
	$query->fields('a', array('uraian','kodero','anggaran'));
	$query->condition('a.kodeuk', $kodeuk, '=');
	//drupal_set_message($kodeuk);
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$opt = array();
	$opt['00'] = '- Pilih Rekening -';
	$no=0;
	foreach ($results as $data) {
		$opt[$data->kodero]=$data->uraian;
		
	}
	$form['kodero'] = array(
		'#type' => 'select',
		'#title' => 'Rekening',
		'#options' => $opt,
		'#default_value' => $kodero,
		'#validated'=>true,
	);	
	
	$form['hapus'] = array(
		'#type' => 'checkbox', 
		'#title' => t('Hapus'),
		'#default_value' => false,
	);	
	
	$form['import'] = array(
		'#title' => t('Import'),
		'#type' => 'managed_file',
		'#description' => t('The uploaded csv will be imported and temporarily saved.'),
		'#upload_location' => 'public://tmp/',
		'#upload_validators' => array(
		'file_validate_extensions' => array('csv', 'txt'),
		),
	);
	$form['submit'] = array (
		'#type' => 'submit',
		'#value' => t('Import'),
	);
  return $form;
}

function importexcel_import_form_validate($form, $form_state) {
	if ($form_state['input']['import']['fid']=='0') {
		form_set_error('import', 'File CSV belum di-upload. Upload terlebih dahulu file CSV baru lakukan Import.', NULL);
	}
	if ($form_state['values']['kodero']=='00') {
		form_set_error('kodero', 'Rekening belum dipilih.', NULL);
	}
}
function importexcel_import_form_submit($form, $form_state) {
 
	drupal_set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$kodero= $form_state['values']['kodero'];
	$kodeuk= $form_state['values']['kodeuk'];
	$hapus= $form_state['values']['hapus'];
	$fid = $form_state['input']['import']['fid'];
	
	$_SESSION['kodero'] = $kodero;
	
	//drupal_set_message($fid);
	// Check to make sure that the file was uploaded to the server properly
	/*
	$uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(
					':fid' => $fid,
					)
				)->fetchField();
	*/
	
	$results = db_query("SELECT uri,filename FROM {file_managed} WHERE fid = :fid", array(
					':fid' => $fid));
	foreach ($results as $data) {
		$uri = $data->uri;
		$filename = $data->filename;
		
	}	

	$i = 0;
	if(!empty($uri)) {
		if(file_exists(drupal_realpath($uri))) { 
			// Open the csv
			
			$handle = fopen(drupal_realpath($uri), "r");
			// Go through each row in the csv and run a function on it. In this case we are parsing by '|' (pipe) characters.
			// If you want commas are any other character, replace the pipe with it.
			while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
				 
				  
				//$kodeuk, $tanggal, $kodero, $uraian, $jumlahmasuk
				/*
				drupal_set_message('0 : ' . $data[0]);
				drupal_set_message('1 : ' . $data[1]);
				drupal_set_message('2 : ' . $data[2]);
				drupal_set_message('3 : ' . $data[3]);
				drupal_set_message('4 : ' . $data[4]);
				drupal_set_message('5 : ' . $data[5]);
				drupal_set_message('6 : ' . $data[6]);
				drupal_set_message('7 : ' . $data[7]);
				drupal_set_message('8 : ' . $data[8]);
				drupal_set_message('9 : ' . $data[9]);
				drupal_set_message('10 : ' . $data[10]);
				drupal_set_message('11 : ' . $data[11]);
				drupal_set_message($kodero);
				*/
				
				//$X = '41202001';
				if ($kodero=='41111001') {
					
					$arr_tgl = explode("/", $data[7]);
					
					$tanggal = $arr_tgl[2] . '-' . $arr_tgl[1] . '-' . $arr_tgl[0];
					$tgl_keluar = $tanggal;
					$uraian = $data[2];
					$keterangan = $data[1] . '; ' . $data[3] . '; ' . $data[6];
					$jumlahmasuk = str_replace('.', '', $data[5]) ;
					//drupal_set_message($tanggal."A");
				
				} else if ($kodero=='41110001') {
					$tanggal = '20' . substr($data[1],-2) . '-' . substr($data[1],3,3) . '-' . substr($data[1],0,2);
					$tanggal=apbd_pbb_tgl(strtolower($tanggal));
					$tgl_keluar = $tanggal;
					$uraian = 'PBB-' . $data[0];
					$keterangan = $data[0] . ' - ' . $data[2];
					$jumlahmasuk = $data[3] ;
					//drupal_set_message($tanggal."B");
				
				} else if ($kodero=='41202001') {

					$date = explode("/", $data[10]);
					$tanggal = $date[2].'-'.$date[1].'-'.$date[0];
					$tgl_keluar = $tanggal;
					
					$uraian = $data[5] . '; ' . $data[4] . '; ';
					$keterangan = $data[1] . '; ' . $data[3] . '; ' . $data[6] . '; ' . $data[7] . '; ' . $data[9];
					$jumlahmasuk = str_replace('.', '', $data[8]) ;
					//drupal_set_message($tanggal."C");
				
				} else if ($kodero=='41101006') {
					$date = explode("/", $data[11]);
					$tanggal = $date[2].'-'.$date[1].'-'.$date[0];
					
					$date = explode("/", $data[10]);
					$tgl_keluar = $date[2].'-'.$date[1].'-'.$date[0];
					
					$uraian = $data[4];
					$keterangan = $data[3] . '; ' . $data[7] . '; ' . $data[9] . '; ' . $data[1] . '; ' . $data[11];;
					$jumlahmasuk = str_replace('.', '', $data[8]) ;
					
				} else {
					//$tanggal = substr($data[10],-4) . '-' . substr($data[10],4,2) . '-' . substr($data[10],0,2);

					$date = explode("/", $data[10]);
					$tanggal = $date[2].'-'.$date[1].'-'.$date[0];
					$tgl_keluar = $tanggal;
					
					$uraian = $data[4];
					$keterangan = $data[3] . '; ' . $data[7] . '; ' . $data[9] . '; ' . $data[1];
					$jumlahmasuk = str_replace('.', '', $data[8]) ;
					//drupal_set_message($tanggal."C");
				}
 
				/*
				drupal_set_message($kodero);
				drupal_set_message('tgl : ' . $tanggal);
				drupal_set_message('ura : ' . $uraian);
				drupal_set_message('ket : ' . $keterangan);
				drupal_set_message('jml : ' . $jumlahmasuk);
				*/
				
				
				if ($hapus) {
					
					
					deletejurnal($kodeuk, $tanggal, $kodero, $uraian, $jumlahmasuk, $keterangan);
					$operations[] = array(
										'importexcel_import_batch_processing',  // The function to run on each row
										array($data),  // The row in the csv
									);
									
				} else {
					save2jurnal($kodeuk, $tanggal, $kodero, $uraian, $jumlahmasuk, $keterangan, $tgl_keluar);
					
					//drupal_set_message($kodeuk . ' - ' . $tanggal . ' - ' . $kodero . ' - ' . $uraian . ' - ' .  $jumlahmasuk . ' - ' . $keterangan);
					
					
					$operations[] = array(
										'importexcel_import_batch_processing',  // The function to run on each row
										array($data),  // The row in the csv
									);
					
				}
				
				
				$i++;		
			
			}	//end while read data
			
			
			// Once everything is gathered and ready to be processed... well... process it!
			
			$batch = array(
				'title' => t('Import data [' . $filename . '] dari Pajak...'),
				'operations' => $operations,  // Runs all of the queued processes from the while loop above.
				'finished' => 'importexcel_import_finished', // Function to run when the import is successful
				'error_message' => t('The installation has encountered an error.'),
				'progress_message' => t('Record ke @current dari @total transaksi.'),
			);
			batch_set($batch);
			
			
			//batch_process('user');
			fclose($handle);    
			
			
			
		}	//end exist 
	
	} else {	//end empty
		
		drupal_set_message(t('There was an error uploading your file. Please contact a System administator.'), 'error');
	}
	
}

function save2jurnal($kodeuk, $tanggal, $kodero, $uraian, $jumlahmasuk, $keterangan, $tgl_keluar) {
	
	$jumlah = (double) $jumlahmasuk;
	
	/*
	drupal_set_message($jumlahmasuk . '/' . $jumlah);
	drupal_set_message($tanggal);
	drupal_set_message($tgl_keluar);
	*/
	
	
	if ($jumlah>0) {
		
		$idkeluar = apbd_setorkeluarid($uraian, $kodeuk), $tgl_keluar;;
		
		//Terima
		
		$setorid_masuk = db_insert('setor')
		->fields(array('kodeuk', 'kodero', 'uraian', 'jumlahmasuk', 'tanggal', 'koderod', 'keterangan', 'tag'))
		->values(array(
				'kodeuk' => $kodeuk,
				'kodero' => $kodero,
				'uraian' => $uraian,
				'jumlahmasuk' => $jumlah,
				'jumlahkeluar' => $jumlah,
				'tanggal' => $tanggal,
				'tgl_keluar' => $tgl_keluar,
				'koderod' => '00',
				'idkeluar' => $idkeluar,
				'keterangan' => $keterangan,
				'tag' => 'import',
				))
		->execute();
		
	}
	
	
}

function deletejurnal($kodeuk, $tanggal, $kodero, $uraian, $jumlahmasuk, $keterangan) {
	
	
	
	
	$jumlah = (double) $jumlahmasuk;
	
	
	if ($jumlah>0) {
		
		//Terima
		$num = db_delete('setor')
			->condition('kodeuk', $kodeuk)
			->condition('tanggal', $tanggal)
			->condition('kodero', $kodero)
			->condition('uraian', $uraian)
			->condition('jumlahmasuk', $jumlah)
			->condition('keterangan', $keterangan)
			->execute();
		
		//Setor
		$num = db_delete('setor')
			->condition('kodeuk', $kodeuk)
			->condition('tanggal', $tanggal)
			->condition('kodero', $kodero)
			->condition('uraian', $uraian)
			->condition('jumlahkeluar', $jumlah)
			->condition('keterangan', $keterangan)
			->execute();
		
		//drupal_set_message($setorid);
	}
	
}


function importexcel_import_batch_processing($data) {
	drupal_set_message('Hai');
	
/*	
  // Lets make the variables more readable.
  $title = $data[0];
  $body = $data[1];
  $serial_num = $data[2];
  // Find out if the node already exists by looking up its serial number. Each serial number should be unique. You can use whatever you want.
  $nid = db_query("SELECT DISTINCT n.nid FROM {node} n " . 
    "INNER JOIN {field_data_field_serial_number} s ON s.revision_id = n.vid AND s.entity_id = n.nid " .
    "WHERE field_serial_number_value = :serial", array(
      ':serial' => $serial_num,
    ))->fetchField();
  if(!empty($nid)) {
    // The node exists! Load it.
    $node = node_load($nid);
 
    // Change the values. No need to update the serial number though.
    $node->title = $title;
    $node->body['und'][0]['value'] = $body;
    $node->body['und'][0]['safe_value'] = check_plain($body);
    node_save($node);
  }
  else {
    // The node does not exist! Create it.
    global $user;
    $node = new StdClass();
    $node->type = 'page'; // Choose your type
    $node->status = 1; // Sets to published automatically, 0 will be unpublished
    $node->title = $title;
    $node->uid = $user->uid;		
    $node->body['und'][0]['value'] = $body;
    $node->body['und'][0]['safe_value'] = check_plain($body);
    $node->language = 'und';
 
    $node->field_serial_number['und'][0]['value'] = $serial_num;
    $node->field_serial_number['und'][0]['safe_value'] = check_plain($serial_num);
    node_save($node);
  }
 */ 
}

function importexcel_import_finished() {
  drupal_set_message(t('Import Completed Successfully'));
}

?>