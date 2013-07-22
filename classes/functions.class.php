<?php
	
	class Functions {
	
		public function curl($url, $method = 'get', $header = null, $postdata = null, $timeout = 60) {
			$s = curl_init();

			curl_setopt($s,CURLOPT_URL, $url);
			if ($header) 
				curl_setopt($s,CURLOPT_HTTPHEADER, $header);

			if ($this->debug)
				curl_setopt($s,CURLOPT_VERBOSE, TRUE);

			curl_setopt($s,CURLOPT_TIMEOUT, $timeout);
			curl_setopt($s,CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($s,CURLOPT_MAXREDIRS, 3);
			curl_setopt($s,CURLOPT_RETURNTRANSFER, true);
			curl_setopt($s,CURLOPT_FOLLOWLOCATION, 1);
		 	switch(strtolower($method)) {
		 		case "post": 
		 			curl_setopt($s,CURLOPT_POST, true);
					curl_setopt($s,CURLOPT_POSTFIELDS, $postdata);
		 		break;
		 		case "delete":
		 			curl_setopt($s,CURLOPT_CUSTOMREQUEST, 'DELETE');
		 		break;
		 		case "put":
		 			curl_setopt($s,CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($s,CURLOPT_POSTFIELDS, $postdata);
		 		break;	
		 	}
			curl_setopt($s,CURLOPT_HEADER, $this->includeheader);			 
			curl_setopt($s,CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
			curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false);
		
			$html = curl_exec($s);
			$status = curl_getinfo($s, CURLINFO_HTTP_CODE);
		
			curl_close($s);
			return $html;
		}
	
	}	
	
?>