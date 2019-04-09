<?php
function importexcel_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('importexcel_edit_main_form');
	return drupal_render($output_form);//.$output;
	
	
}





function importexcel_edit_main_form($form, &$form_state) {
	$form['notes'] = array(
		'#type' => 'markup',
		'#markup' => '<div class="import-notes">Perhatian!<ul><li>Pastikan bahwa file yang di-upload adalah file csv.</li><li>Pastikan untuk menekan tombol Upload sebelum meng-import data</li><li>Beri centang pada Langsung Jurnalkan bila ingin langsung menjurnalkan data dari bank</li></ul></div>',
	);
	$form['autojurnal'] = array(
		'#type' => 'checkbox', 
		'#title' => t('Langsung jurnalkan'),
		'#default_value' => true,
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


function importexcel_edit_main_form_submit($form, &$form_state) {

	// Check to make sure that the file was uploaded to the server properly
	$uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(
					':fid' => $form_state['input']['import']['fid'],
					)
				)->fetchField();
	
	$autojurnal = $form_state['values']['autojurnal'];

	$i = 0;
	if(!empty($uri)) {
		if(file_exists(drupal_realpath($uri))) { 
			// Open the csv
			
			
			
			$handle = fopen(drupal_realpath($uri), "r");
			// Go through each row in the csv and run a function on it. In this case we are parsing by '|' (pipe) characters.
			// If you want commas are any other character, replace the pipe with it.
			while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
				
				//drupal_set_message($data[3]);
				//drupal_set_message(str_replace($data[3], '/', ''));
				
				//read data
				$tanggal = substr($data[7],0,4) . '-' . substr($data[7],4,2) . '-' . substr($data[7],-2);
				$refno = $data[3];
				$reftgl = date('Y') . '-' . date('m') . '-' . date('d');;
				$subtotal = $data[11]; $potongan = 0; $total = $subtotal;
				$nobukti = $data[8];
				
				if (strlen($data[10])==8)
					$kodero = $data[10];
				else
					$kodero = substr($data[10],0,5) . '0' . substr($data[10], -2);
				$keterangan = $data[9];
				
				$key = $data[3];
				if ($autojurnal) {		
					
					//drupal_set_message($kodero);
					if (substr($kodero,0,1) == '9') {
							
						//drupal_set_message('a');
						
						//kode 9 -> simpan antrian
						$kodero = '90090900';
						save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '0');
						
					} else {
						
						//drupal_set_message('j');
						
						//jurnalkan
						$i++;
						$transid = str_replace($data[3], '/', '');
						save2jurnal($transid, $tanggal, $i, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero);
						
						//tandai sudah jurnal
						save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '1');
					}
					
				} else {					//end if auto jurnal	
					
					//simpan antrian
					save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '0');

				}
				
				
				
				$operations[] = array(
									'pendapatan_import_batch_processing',  // The function to run on each row
									array($data),  // The row in the csv
								);
			}	//end while read data
 
			// Once everything is gathered and ready to be processed... well... process it!
			$batch = array(
				'title' => t('Importing data CSV dari Bank...'),
				'operations' => $operations,  // Runs all of the queued processes from the while loop above.
				'finished' => 'pendapatan_import_finished', // Function to run when the import is successful
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

?>
