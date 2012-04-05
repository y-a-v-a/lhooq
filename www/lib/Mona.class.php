<?php
class Mona {
	private $_moustache_pool = array();
	
	private $_face_pool = array();
		
	private $_google_url = "http://ajax.googleapis.com/ajax/services/search/images?v=1.0&key=ABQIAAAAq4AIuvYeP4AL8FNxqeSM6BS1_wxKxGdp2RzRaADWaiSLCsSD_BRB9crcfrmh0Bj8s6TsAcqGepcWcg&q=woman%20face&imgtype=face&rsz=8&safe=off&start=";
	
	private $_json_result;
	
	private $_referer = '&userip=94.124.94.40';
	//private $_referer = '&userip=213.93.210.241';
	
	private $_img;
	
	public function __construct() {
		$this->_moustache_pool = glob('moustache_*.png');
		$this->_json_result = @file_get_contents($this->_google_url . rand(1,24) . $this->_referer);
		$this->_prepare_faces();
	}
	
	public function build() {
		$oldface = @imagecreatefromstring(@file_get_contents($this->_face_pool[rand(0,7)]));
		// Find base image size 
		$ofwidth = imagesx($oldface); 
		$ofheight = imagesy($oldface);
		$fwidth = 500;
		$fheight = ((500/$ofwidth)*$ofheight);
		$face = imagecreatetruecolor($fwidth,$fheight);
		imagecopyresized($face,$oldface,0,0,0,0,$fwidth,$fheight,$ofwidth,$ofheight);
		
		if (imagealphablending($face, true) !== true) {
			throw new Exception('No alphablending possible?!');
		}
		$moustache = @imagecreatefrompng($this->_moustache_pool[rand(0,2)]);
		$mwidth = imagesx($moustache);
		$mheight = imagesy($moustache);
		
		$t = imagecopy($face, $moustache,
			rand(0,$fwidth-$mwidth), rand(0,$fheight-$mheight),
			0, 0, $mwidth,$mheight);

		ob_start();
		if (@imagejpeg($face,null,50) === false) {
			throw new Exception('Could not blend images...');
		}
		$this->_img = ob_get_clean();
		imagedestroy($face);
		imagedestroy($moustache);
		return $this;
	}
	
	public function show() {
		return $this->_img;
	}
	
	public function __toString() {
		return "Cannot convert image to string";
	}
	
	private function _prepare_faces() {
		if ($this->_json_result === false) {
			throw new Exception('No result');
		}
		$results = json_decode($this->_json_result);
		
		if ($results->responseStatus == 200 &&
			count($results->responseData->results) > 0) {
			foreach($results->responseData->results as $result) {
				$this->_face_pool[] = $result->unescapedUrl;
			}
		}
		return $this;
	}
	
	private function log($msg) {
		ob_start();
		var_dump($msg);
		$msg = ob_get_clean();
		@file_put_contents('../var/log/log.txt',$msg,FILE_APPEND);
	}
	
	public function __destroy() {
		imagedestroy($this->_img);
	}
}