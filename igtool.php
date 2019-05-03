<?php 
error_reporting(0);
class curl {
	var $ch, $agent, $error, $info, $cookiefile, $savecookie;	
	function curl() {
		$this->ch = curl_init();
		curl_setopt ($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/530.1 (KHTML, like Gecko) Chrome/2.0.164.0 Safari/530.1');
		curl_setopt ($this->ch, CURLOPT_HEADER, 1);
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($this->ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt ($this->ch, CURLOPT_TIMEOUT, 30);
		curl_setopt ($this->ch, CURLOPT_CONNECTTIMEOUT,30);
	}
	function header($header) {
		curl_setopt ($this->ch, CURLOPT_HTTPHEADER, $header);
	}
	function http_code() {
		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
	}
	function error() {
		return curl_error($this->ch);
	}
	function ssl($veryfyPeer, $verifyHost){
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $veryfyPeer);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verifyHost);
	}
	function cookies($cookie_file_path) {
		$this->cookiefile = $cookie_file_path;;
		$fp = fopen($this->cookiefile,'wb');fclose($fp);
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $this->cookiefile);
	}
	function proxy($sock) {
		curl_setopt ($this->ch, CURLOPT_HTTPPROXYTUNNEL, true); 
		curl_setopt ($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4); 
		curl_setopt ($this->ch, CURLOPT_PROXY, $sock);
	}
	function post($url, $data) {
		curl_setopt($this->ch, CURLOPT_POST, 1);	
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
		return $this->getPage($url);
	}
	function data($url, $data, $hasHeader=true, $hasBody=true) {
		curl_setopt ($this->ch, CURLOPT_POST, 1);
		curl_setopt ($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
		return $this->getPage($url, $hasHeader, $hasBody);
	}
	function get($url, $hasHeader=true, $hasBody=true) {
		curl_setopt ($this->ch, CURLOPT_POST, 0);
		return $this->getPage($url, $hasHeader, $hasBody);
	}	
	function getPage($url, $hasHeader=true, $hasBody=true) {
		curl_setopt($this->ch, CURLOPT_HEADER, $hasHeader ? 1 : 0);
		curl_setopt($this->ch, CURLOPT_NOBODY, $hasBody ? 0 : 1);
		curl_setopt ($this->ch, CURLOPT_URL, $url);
		$data = curl_exec ($this->ch);
		$this->error = curl_error ($this->ch);
		$this->info = curl_getinfo ($this->ch);
		return $data;
	}
}

function fetchCurlCookies($source) {
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $source, $matches);
	$cookies = array();
	foreach($matches[1] as $item) {
		parse_str($item, $cookie);
		$cookies = array_merge($cookies, $cookie);
	}
	return $cookies;
}

function string($length = 15)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function angka($length = 15)
{
	$characters = '0123456789';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function fetch_value($str,$find_start,$find_end) {
	$start = @strpos($str,$find_start);
	if ($start === false) {
		return "";
	}
	$length = strlen($find_start);
	$end    = strpos(substr($str,$start +$length),$find_end);
	return trim(substr($str,$start +$length,$end));
}
function instagram_account_creator($socks) {
	$curl = new curl();
	$curl->cookies('cookies/'.md5($_SERVER['REMOTE_ADDR']).'.txt');
	$curl->ssl(0, 2);
	$curl->proxy($socks);
	$register = $curl->get('https://www.instagram.com/accounts/emailsignup/');

	$cookies = fetchCurlCookies($register);
	$csrftoken = $cookies['csrftoken'];
	$mid = $cookies['mid'];

	if ($register) {

		$headers = array();
		$headers[] = "accept-language: en-US,en;q=0.9";
		$headers[] = "content-type: application/x-www-form-urlencoded";
		$headers[] = 'cookie: mid='.$mid.'; mcd=3; shbid=13734; rur=FTW; csrftoken='.$csrftoken.'; csrftoken='.$csrftoken.';';
		$headers[] = "referer: https://www.instagram.com/accounts/emailsignup/";
		$headers[] = "user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.54";
		$headers[] = "x-csrftoken: ".$csrftoken."";
		$curl->header($headers);

		$page_api = file_get_contents('https://randomuser.me/api/');
		$first = fetch_value($page_api, '"first":"','"');
		$last = fetch_value($page_api, '"last":"','"');
		$name = $first.' '.$last;
		$mail = fetch_value($page_api, '"email":"','@example.com"');
		$user = fetch_value($page_api, '"username":"','"');
		$domain = array ('@gmail.com','@yahoo.com','@mail.com','@yandex.com','@gmx.de','@t-online.de','@yahoo.co.id','@yahoo.co.uk');
		$random = rand(0,7);
		$email  = $mail.angka(5).$domain[$random];
		$username = $first.$last.angka(3);
		$password = string(8);


		$page_register = $curl->post('https://www.instagram.com/accounts/web_create_ajax/', 'email='.$email.'&password='.$password.'&username='.$username.'&first_name='.$name.'&seamless_login_enabled=1&tos_version=row&opt_into_one_tap=false');

		if (strpos($page_register, '"account_created": true')) {
			echo "SUCCESS CREATE | ".$socks." | ".$email." | ".$username." | ".$password."\n";
			$data =  "SUCCESS CREATE | ".$socks." | ".$email." | ".$username." | ".$password."\r\n";
			$fh = fopen("success.txt", "a");
			fwrite($fh, $data);
			fclose($fh);
			flush();
			ob_flush();
		} elseif(strpos($page_register, '"account_created": false')) {
			echo "FAILED | ".$socks." | ".$email." | ".$username." | ".$password."\n";
			flush();
			ob_flush();
		}

	} else {
		$data['httpcode'] = $curl->http_code();
		$error = $curl->error();
		echo "".$socks." | ".$error." | Http code : ".$data['httpcode']."\n";
		flush();
		ob_flush();
	}
}

function instagram_account_creator_like_follow($socks, $link) {

	$curl = new curl();
	$curl->cookies('cookies/'.md5($_SERVER['REMOTE_ADDR']).'.txt');
	$curl->ssl(0, 2);
	$curl->proxy($socks);
	$data['httpcode'] = $curl->http_code();
	$register = $curl->get('https://www.instagram.com/accounts/emailsignup/');

	$cookies = fetchCurlCookies($register);
	$csrftoken = $cookies['csrftoken'];
	$mid = $cookies['mid'];

	if ($register) {

		$headers = array();
		$headers[] = "accept-language: en-US,en;q=0.9";
		$headers[] = "content-type: application/x-www-form-urlencoded";
		$headers[] = 'cookie: mid='.$mid.'; mcd=3; shbid=13734; rur=FTW; csrftoken='.$csrftoken.'; csrftoken='.$csrftoken.';';
		$headers[] = "referer: https://www.instagram.com/accounts/emailsignup/";
		$headers[] = "user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.54";
		$headers[] = "x-csrftoken: ".$csrftoken."";
		$curl->header($headers);

		$page_api = file_get_contents('https://randomuser.me/api/');
		$first = fetch_value($page_api, '"first":"','"');
		$last = fetch_value($page_api, '"last":"','"');
		$name = $first.' '.$last;
		$mail = fetch_value($page_api, '"email":"','@example.com"');
		$user = fetch_value($page_api, '"username":"','"');
		$domain = array ('@gmail.com','@yahoo.com','@mail.com','@yandex.com','@gmx.de','@t-online.de','@yahoo.co.id','@yahoo.co.uk');
		$random = rand(0,7);
		$email  = $mail.angka(5).$domain[$random];
		$username = $first.$last.angka(3);
		$password = string(8);

		$page_register = $curl->post('https://www.instagram.com/accounts/web_create_ajax/', 'email='.$email.'&password='.$password.'&username='.$username.'&first_name='.$name.'&seamless_login_enabled=1&tos_version=row&opt_into_one_tap=false');

		if (strpos($page_register, '"account_created": true')) {
			
			echo "SUCCESS CREATE | ".$socks." | ".$email." | ".$username." | ".$password."\n";
			$data =  "SUCCESS CREATE | ".$socks." | ".$email." | ".$username." | ".$password."\r\n";
			$fh = fopen("success.txt", "a");
			fwrite($fh, $data);
			fclose($fh);

			$page_login = $curl->post('https://www.instagram.com/accounts/login/ajax/?hl=en', 'username='.$username.'&password='.$password.'&queryParams=%7B%22hl%22%3A%22en%22%7D');

			if (strpos($page_login, '"authenticated": false')) {
				echo "LOGIN FAILED | ".$socks." | ".$email." | ".$username." | ".$password."\n";
				flush();
				ob_flush();
			} elseif (strpos($page_login, '"authenticated": true')) {

				$page_profil = $curl->get($link);

				if ($page_profil) {

					preg_match_all('/{"__typename":"GraphImage","id":"(.*?)"/s', $page_profil, $mediaid);

					$id = fetch_value($page_profil, '{"logging_page_id":"profilePage_','"');
					$follow = $curl->post('https://www.instagram.com/web/friendships/'.$id.'/follow/?hl=id', null);

					if (strpos($follow, '{"result": "following", "status": "ok"}')) {
						echo "FOLLOW | ".$socks." | ".$email." | ".$username." | ".$password." | ID: ".$id."\n";
						flush();
						ob_flush();
					} else {
						echo "FOLLOW | ".$socks." | ".$email." | ".$username." | ".$password." | ID: ".$id."\n";
						flush();
						ob_flush();
					}


					foreach ($mediaid[1] as $value) {

						$like = $curl->post('https://www.instagram.com/web/likes/'.$value.'/like/', null);

						if (strpos($like, '"status": "ok"')) {
							echo "SUCCESS LIKE | ".$socks." | ".$email." | ".$username." | ".$password." | MediaID: ".$value."\n";
							flush();
							ob_flush();
						} else {
							echo "ACTION BLOCKED | ".$socks." | ".$email." | ".$username." | ".$password." | MediaID: ".$value."\n";
							flush();
							ob_flush();
						}

					}
				}


			}

			

		} elseif(strpos($page_register, 'The IP address you are using has been flagged as an open proxy')) {
			$ip = fetch_value($page_register, '{"ip": ["','"]}');
			echo "FAILED | ".$socks." | ".$email." | ".$username." | ".$password." | ".$ip."\n";
			flush();
			ob_flush();
		} elseif (strpos($page_register, 'Enter a valid email address')) {
			echo "EMAIL NOT VALID | ".$socks." | ".$email." | ".$username." | ".$password."\n";
			flush();
			ob_flush();
		} else {
			echo "UNKNOWN | ".$socks." | ".$email." | ".$username." | ".$password."\n";
			flush();
			ob_flush();
		}

	} else {
		$error = $curl->error();
		echo "".$socks." | ".$error." | Http code : ".$data['httpcode']."\n";
		flush();
		ob_flush();
	}
}

echo 
"
====================================================


	CREATED BY YUDHA TIRA PAMUNGKAS
   https://www.facebook.com/yudha.t.pamungkas.3


====================================================
\n";
echo "LIST TOOLS\n";
echo "[1] INSTAGRAM ACCOUNT CREATOR\n";
echo "[2] INSTAGRAM ACCOUNT CREATOR + LIKE & FOLLOW\n";
echo "Select tools: ";
$list = trim(fgets(STDIN));
if ($list == "") {
	die ("Cannot be blank!\n");
}

if ($list == 1) {
	echo "INSTAGRAM ACCOUNT CREATOR\n";
	sleep(1);
	echo "Name file socks (Ex: socks.txt): ";
	$namefile = trim(fgets(STDIN));
	if ($namefile == "") {
		die ("Socks cannot be blank!\n");
	}
	echo "Please wait";
	sleep(1);
	echo ".";
	sleep(1);
	echo ".";
	sleep(1);
	echo ".\n";
	$file = file_get_contents($namefile) or die ("File not found!\n");
	$socks = explode("\r\n",$file);
	$total = count($socks);
	echo "Total socks: ".$total."\n";

	foreach ($socks as $value) {
		instagram_account_creator($value);
	}

} elseif ($list == 2) {
	echo "INSTAGRAM ACCOUNT CREATOR + LIKE & FOLLOW\n";
	sleep(1);
	echo "Name file socks (Ex: socks.txt): ";
	$namefile = trim(fgets(STDIN));
	if ($namefile == "") {
		die ("Socks cannot be blank!\n");
	}
	echo "Link (Ex: https://www.instagram.com/yudhatira/): ";
	$link = trim(fgets(STDIN));
	if ($link == "") {
		die ("Link cannot be blank!\n");
	}

	sleep(1);
	echo "Please wait";
	sleep(1);
	echo ".";
	sleep(1);
	echo ".";
	sleep(1);
	echo ".\n";
	$file = file_get_contents($namefile) or die ("File not found!\n");
	$socks = explode("\r\n",$file);
	$total = count($socks);
	echo "Total socks: ".$total."\n";

	foreach ($socks as $value) {
		instagram_account_creator_like_follow($value, $link);
	}
} else {
	die ("Command not found!\n");
}

?>