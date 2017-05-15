<?php

	$cfgfile = $_SERVER["DOCUMENT_ROOT"] . '/php/rb.php'; 
  include($cfgfile);
  $cfgfile = $_SERVER["DOCUMENT_ROOT"] . '/php/database.php'; 
  include($cfgfile);
  session_start();
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/Carduploadify/uploads'; // Relative to the root
if (!empty($_FILES)) {

            class Image {
                
                var $uploaddir;
                var $quality = 80;
                var $ext;
                var $dst_r;
                var $img_r;
                var $img_w;
                var $img_h;
                var $output;
                var $data;
                var $datathumb;
                
                function setFile($src = null) {
                    $this->ext = strtoupper(pathinfo($src, PATHINFO_EXTENSION));
                    
                    if(is_file($src) && ($this->ext == "JPG" OR $this->ext == "JPEG")) {
                        $this->img_r = ImageCreateFromJPEG($src);
                    } elseif(is_file($src) && $this->ext == "PNG") {
                        $this->img_r = ImageCreateFromPNG($src);      
                    } elseif(is_file($src) && $this->ext == "GIF") {
                        $this->img_r = ImageCreateFromGIF($src);
                    }
                    $this->img_w = imagesx($this->img_r);
                    $this->img_h = imagesy($this->img_r);
                }
                
                function resize($w = 350) {
                    $h =  200;
                    $this->dst_r = ImageCreateTrueColor($w, $h);
                    imagecopyresampled($this->dst_r, $this->img_r, 0, 0, 0, 0, $w, $h, $this->img_w, $this->img_h);
                    $this->img_r = $this->dst_r;
                    $this->img_h = $h;
                    $this->img_w = $w;
                }
                
                function createFile($output_filename = null) {
                	$fileName= pathinfo(base64_decode($output_filename), PATHINFO_FILENAME );
                	echo $fileName;
                    if($this->ext == "JPG" OR $this->ext == "JPEG") {
                        imageJPEG($this->dst_r, $this->uploaddir.$output_filename.'.'.$this->ext, $this->quality);
                    } elseif($this->ext == "PNG") {
                        imagePNG($this->dst_r, $this->uploaddir.$output_filename.'.'.$this->ext);
                    } elseif($this->ext == "GIF") {
                        imageGIF($this->dst_r, $this->uploaddir.$output_filename.'.'.$this->ext);
                    }
                    $this->output = $this->uploaddir.$output_filename.'.'.$this->ext;
                if(isset($this->output)) {
                    $datain = R::getAll( 'SELECT * FROM businesscardimg where clientid = '.$_SESSION["UserDetails"]['id']);
		$arry =  $datain;
		if(count($arry) == 0)
		{
			$status      =       1;
		}
		else
		{
			$status      =       0;
		}
	 	$businesscard = R::dispense( 'businesscardimg' );
		$businesscard->clientid     =    $_SESSION["UserDetails"]['id'];
		$businesscard->imagepath     =    $this->output;
		$businesscard->status      =       $status;
		
		echo $id = R::store( $businesscard );
                }
            }
                
                function setUploadDir($dirname) {
                    $this->uploaddir = $dirname;
                }
                
                function flush() {
                    $targetFolder = '/Carduploadify/uploads/';
           $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
   $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
                    
                    imagedestroy($this->dst_r);
                    unlink($targetFile);
                    //imagedestroy($this->img_r);
                    
                }
                
            }

            $targetFolder = '/Carduploadify/uploads/';
             $tempFile = $_FILES['Filedata']['tmp_name'];
                $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
             $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
              $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	         $fileParts = pathinfo($_FILES['Filedata']['name']);
	         $files = pathinfo(base64_decode($_FILES['Filedata']['name']), PATHINFO_FILENAME );
	         $getext = strtoupper(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION));
           $targetFilenews = rtrim($targetPath,'/') . '/' .$files.'.'.$getext;

	
	if (in_array($fileParts['extension'],$fileTypes)) {
		$movefile = move_uploaded_file($tempFile,$targetFile);
		
	} else {
		echo 'Invalid file type.';
	}
            
            $image = new Image();
            $image->setFile($targetFile);
            $image->setUploadDir($targetPath);
            $image->resize(350);
            $image->createFile(md5($tempFile));
            $image->flush();
        
        }

?>
