<?php
class BasJan {
	private $_textImg;

	private $_face_pool = array();

	private $_google_url = "https://www.googleapis.com/customsearch/v1?key=#APIKEY#&imgType=face&imgSize=medium&fileType=jpg&q=#QUERY#&searchType=image&safe=medium&cx=001019564263977871109:d0hasykupmy&start=";

	private $_json_result;

	private $_img;

	private $i = 10;

	private $queries = array(
		'man crying',
		'crying dude',
		'male face crying'
	);

	public function __construct($key) {
		$this->key = $key;
		$googleUrl = str_replace('#APIKEY#', $this->key, $this->_google_url);
		$query = urlencode($this->queries[array_rand($this->queries)]);
		$googleUrl = str_replace('#QUERY#', $query, $googleUrl);
		$googleUrl = $googleUrl . rand(1, 40);

		$this->_textImg = file_get_contents("bas-jan-ader.png");
		$this->_json_result = @file_get_contents($googleUrl);
		$this->_prepare_faces();
	}

	public function build() {
		$oldface = @imagecreatefromstring(@file_get_contents($this->_face_pool[rand(0,count($this->_face_pool))]));
		if ($oldface === false) {
			if ($this->i < 10) {
				$this->i++;
				return $this->build();
			} else {
				throw new Exception('Too much recursion.');
			}
		}
		// Find base image size
		$ofwidth = imagesx($oldface);
		$ofheight = imagesy($oldface);
		$fwidth = 600;
		$fheight = ((600/$ofwidth)*$ofheight);
		$face = imagecreatetruecolor($fwidth,$fheight);
		imagecopyresized($face,$oldface,0,0,0,0,$fwidth,$fheight,$ofwidth,$ofheight);

		if (imagealphablending($face, true) !== true) {
			throw new Exception('No alphablending possible?!');
		}
		$moustache = @imagecreatefromstring($this->_textImg);
		$mwidth = imagesx($moustache);
		$mheight = imagesy($moustache);

		$t = imagecopy($face, $moustache,
			$fwidth - 333,$fheight - 288,
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

	private function _prepare_faces() {
		if ($this->_json_result === false) {
			throw new Exception('No result');
		}
		$results = json_decode($this->_json_result, true);

		if (count($results['items']) > 0) {
			foreach($results['items'] as $result) {
				$this->_face_pool[] = $result['link'];
			}
		}
		return $this;
	}

	public function show() {
		return $this->_img;
	}

	public function __toString() {
		return "Cannot convert image to string";
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
