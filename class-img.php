<?php
	class img {
		var $image = '';
		var $temp = '';
		
		function img($sourceFile){
			if(file_exists($sourceFile)){
				ini_set("gd.jpeg_ignore_warning", 1);
				$this->image = @ImageCreateFromJPEG($sourceFile);
			} else {
				$this->errorHandler();
			}
			return;
		}
		
		function resize($width = 80, $height = 80, $aspectradio = true){
			$o_wd = @imagesx($this->image);
			$o_ht = @imagesy($this->image);
			if($o_wd>$width || $o_ht>$height)
			{
				if(isset($aspectradio)&&$aspectradio) {
					$w = round($o_wd * $height / $o_ht);
					$h = round($o_ht * $width / $o_wd);
					if(($height-$h)<($width-$w)){
						$width =& $w;
					} else {
						$height =& $h;
					}
				}
				$this->temp = imageCreateTrueColor($width,$height);
				imageCopyResampled($this->temp, $this->image,
				0, 0, 0, 0, $width, $height, $o_wd, $o_ht);
				$this->sync();
			}
			return;
		}

		function sync(){
			$this->image =& $this->temp;
			unset($this->temp);
			$this->temp = '';
			return;
		}
		
		function show(){
			$this->_sendHeader();
			@ImageJPEG($this->image,'',85);
			return;
		}
		
		function _sendHeader(){
			header('Content-Type: image/jpeg');
		}
		
		function errorHandler(){
			echo "error";
			//exit();
		}
		
		function store($file){
			@ImageJPEG($this->image,$file,85);
			return;
		}
		
		function watermark($pngImage, $left = 0, $top = 0){
			ImageAlphaBlending($this->image, true);
			$layer = ImageCreateFromPNG($pngImage); 
			$logoW = @ImageSX($layer); 
			$logoH = @ImageSY($layer); 
			ImageCopy($this->image, $layer, $left, $top, 0, 0, $logoW, $logoH); 
		}
	}
?>