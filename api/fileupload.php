<?php 

	header('Content-type', 'application/json');

	require_once './core/init.php';

	$response = array('fileUploaded'=>false);

	$user = new User();

	if($user->isLoggedIn()){
	 	if(Input::exists('post') and Input::exists('files')){
	 		$dataValidate = new Validate();
	 		$dataValidate->validate($_POST, array(
	 			'documentType'=>array('label'=>'Document Type', 'required'=>true),
	 			'documentDescription'=>array('label'=>'Document Description', 'required'=>true, 'maxlength'=>50)
	 			));
	 		$fileValidate = new Validate();
	 		$fileValidate->validate($_FILES, array(
	 			'documentFile'=>array(
	 				'label'=>'Document File', 
	 				'required'=>true, 
	 				'size'=> Config::get('file_max_size'), 
	 				'extension'=> array('pdf', 'docx')
	 				)
	 			));
	 		if($dataValidate->isValid() and $fileValidate->isValid()){
	 			
	 			$userData = $user->data();
	 			$fileData = $_FILES['documentFile'];

	 			$folderName = $userData['id'];
	 			$tmp_location = $fileData['tmp_name'];
	 			$fileName = $fileData['name'];
	 			$ext = pathinfo($fileName, PATHINFO_EXTENSION);
				$new_file_name = md5($fileName).substr(md5(microtime()), 10).'.'.$ext;
	 			
	 			// create new file object	
	 			$file = new File($folderName, $new_file_name);
	 			
	 			if($file->upload($tmp_location)){
	 				$fields = array(
	 					'userId'=>$userData['id'],
	 					'type'=>Input::get('documentType'),
	 					'name'=>$fileName,
	 					'tmpName'=>$new_file_name,
	 					'description'=>Input::get('documentDescription')
	 					);
	 				try {
	 					$user->addFile($fields);
	 					$response['fileUploaded'] = true;
	 				} catch (Exception $e) {
	 					$response['errors'] = array("server"=>"Problem while uploading file. Please try after some time.");
	 				}
				}else{
					echo "Error while uploading file";
				}
	 		}else{
	 			$errors = array();	
	 			if(!$dataValidate->isValid()){
	 				$tmpErrors = $dataValidate->errors();
	 				foreach ($tmpErrors as $error) {
	 					$errors[] = $error;
	 				}
	 			}
	 			if(!$fileValidate->isValid()){
	 				$tmpErrors = $fileValidate->errors();
	 				foreach ($tmpErrors as $error) {
	 					$errors[] = $error;
	 				}
	 			}
	 			$response['errors'] = $errors;
	 		}
	 	}
	}

	echo json_encode($response);
	
 ?>

