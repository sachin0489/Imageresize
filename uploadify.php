<?php
	ini_set('memory_limit', '1024M');
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
                   $size = getimagesize($src); 
             $this->ext = $size['mime'];
             $this->fileext = ltrim($size['mime'],"image/");
                    switch ($size['mime']) { 
                        case "image/gif": 
                            "Image is a gif"; 
                            $this->img_r = ImageCreateFromGIF($src);
                            break; 
                        case "image/jpeg": 
                          "Image is a JPEG"; 
                          $this->img_r = ImageCreateFromJPEG($src);
                            break; 
                        case "image/png": 
                            "Image is a png"; 
                       $this->img_r = ImageCreateFromPNG($src); 
                            break;  
                    }
                     $this->img_w = $size[0];
                     $this->img_h = $size[1];
                }
                
                function resize($w = 298) {
                    $h =  170;
                    $this->dst_r = ImageCreateTrueColor($w, $h);
                    imagecopyresampled($this->dst_r, $this->img_r, 0, 0, 0, 0, $w, $h, $this->img_w, $this->img_h);
                    $this->img_r = $this->dst_r;
                    $this->img_h = $h;
                    $this->img_w = $w;
                }
                
                function createFile($output_filename = null , $st) {
              $fileName= pathinfo(base64_decode($output_filename), PATHINFO_FILENAME );
          
                    if($this->ext == "image/jpeg" OR $this->ext == "image/jpg") {
                        imageJPEG($this->dst_r, $this->uploaddir.$output_filename.'.jpeg', $this->quality);
                    } elseif($this->ext == "image/png") {
                        imagePNG($this->dst_r, $this->uploaddir.$output_filename.'.png');
                    } elseif($this->ext == "image/gif") {
                        imageGIF($this->dst_r, $this->uploaddir.$output_filename.'.gif');
                    }
                      $this->output = $this->uploaddir.$output_filename.'.'.$this->fileext;
                if(isset($this->output)) {
						if($_SERVER['HTTP_HOST'] == 'staging.servint.locoolly.com' OR $_SERVER['HTTP_HOST'] == 'www.staging.servint.locoolly.com')
						{
							$targetPath = '/home/livelocoolly/public_html/Carduploadify/uploads/';
							copy($this->output, $targetPath.$output_filename.'.'.$this->fileext);
						}
						$datain = R::getAll( 'SELECT * FROM businesscardimg where checktabel=0 and clientid = '.$_SESSION["UserDetails"]['id']);
						$arry =  $datain;

		       $datain1 = R::getAll( 'SELECT * FROM businesscardimg where checktabel=1 and clientid = '.$_SESSION["UserDetails"]['id']);

            $arry1 =  $datain1;

						if(count($arry) > 0)
						{
							$status      =       0;
						}

             if(count($arry1) > 0)
            {
              $status      =       0;
            }

						$businesscard = R::dispense( 'businesscardimg' );
						$businesscard->clientid     =    $_SESSION["UserDetails"]['id'];
						$businesscard->imagepath     =    $this->output;
						$businesscard->status      =       $status;
                        $gettabelname = $_SESSION["tablename"];
                        if($gettabelname =="claimed"){
                           $businesscard->checktabel = 1;
                           if(count($arry1) == 0)
                               { 
                           $businesscard->status      =       1;
                          }
                        }
                        if($gettabelname =="businesscardmanagement"){
                           $businesscard->checktabel = 0;
                           if(count($arry) == 0)
                             {
                           $businesscard->status      =       1;
                         }
                        }
						if($st == 1)
						{
							$id = R::store( $businesscard );
						}
						
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
            $image->resize(298);
            $image->createFile(md5($tempFile) , 1);
            $image->flush();
           
        
        }

?>
