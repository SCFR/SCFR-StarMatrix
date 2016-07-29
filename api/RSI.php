<?php namespace SCFR\StarMatrix\api;

class RSI {
  private $pass;
  private $user;
  private $ch;
  private $RSI_Token = '';
  private $Cookie_Token = '';
  private $cookie_file_path;
  private $token;
  private $logged;
  private static $_instance;

  private function __construct() {
    $this->reset();
    $ch = curl_init();

    $agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";
    curl_setopt($ch, CURLOPT_HEADER,  true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, false);

    $settings = \SCFR\StarMatrix\controller\Settings::get_settings();
    $this->user = $settings["RSI"]["username"];
    $this->pass = $settings["RSI"]["password"];

    $this->ch = $ch;
  }

  public static function get_api() {
    if(is_null(self::$_instance))
    self::$_instance = new RSI();

    return self::$_instance;
  }

  private function reset()  {
    $this->cookies = array();
    $this->token = null;
    $this->cookie_file_path = "cookies.txt";
    //	=unlink($this->cookie_file_path);
  }

  private function handle_token($content, $login = false) {
    preg_match_all('/Set-Cookie[ ]?:[ ]?Rsi-Token=([0-9a-zA-Z]*);/', $content, $tokens);
    if(isset($tokens[1][0])) {
      if($login) $this->login_token = $tokens[1][0];
      else $this->token = $tokens[1][0];
    }
  }

  public function LoginRSI()  {
    if(!$this->logged) {
    $this->reset();

    // options
    $HANDLE           = $this->user;
    $PASSWORD         = $this->pass;
    $LOGINURL         = "https://robertsspaceindustries.com/connect";

    $ch = $this->ch;
    // extra headers
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    $headers[] = "Referer: https://robertsspaceindustries.com/";

    // basic curl options for all requests
    curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($ch, CURLOPT_HEADER,  true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, false);

    // set first URL
    curl_setopt($ch, CURLOPT_URL, $LOGINURL);

    // execute session to get cookies and required form inputs
    $content = curl_exec($ch);

    $this->handle_token($content, true);

    // get cookies
    $cookies = array();

    $headers = array();
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    $headers[] = "X-Requested-With: XMLHttpRequest";
    $headers[] = "Referer: ".$LOGINURL;
    $headers[] = "X-Rsi-Token: ".$this->login_token;

    $fields['username'] = $HANDLE;
    $fields['password'] = $PASSWORD;
    $fields['remember'] = 1;

    // set postfields using what we extracted from the form
    $POSTFIELDS = http_build_query($fields);

    // change URL to login URL
    $LOGINURL   = "https://robertsspaceindustries.com/api/account/signin";
    curl_setopt($ch, CURLOPT_URL, $LOGINURL);

    // set post options
    curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);

    // perform login
    $this->ch = $ch;
    $result = curl_exec($ch);

    $this->handle_token($result);
    $this->logged = true;
    return $result;
    }
    else return $this->logged;
  }

  public function post($url, $query) {
    $headers = array();
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    $headers[] = "X-Requested-With: XMLHttpRequest";
    if(isset($this->token)) $headers[] = "X-Rsi-Token: ".$this->token;


    $POSTFIELDS = http_build_query($query);
    $ch = $this->ch;

    curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
    curl_setopt($ch, CURLOPT_URL, $url);

    return curl_exec($ch);
  }

  public function go_to($url, $custom_headers = array()) {
    $agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";

    // begin script
    $ch = $this->ch;

    $headers = $custom_headers;
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    if(isset($this->token)) $headers[] = "X-Rsi-Token: ".$this->token;

    // basic curl options for all requests
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($ch, CURLOPT_HEADER,  0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);

    curl_setopt($ch, CURLOPT_URL, $url);

    return curl_exec($ch);
  }

  public function logout() {
    curl_close($this->ch);
  }

  public function get_api_data($url, $custom_headers = array()) {
    $curl = json_decode($this->go_to($url, $custom_headers));
    if($curl->success != true) return false;
    else return $curl->data;
  }
}
?>
