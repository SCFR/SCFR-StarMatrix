<?php namespace SCFR\StarMatrix\api;

class RSI_API {

  private $ch;
  private $RSI_Token = '';
  private $Cookie_Token = '';
  private $cookie_file_path;
  private $token;

  private function Reset()  {
    $this->cookies = array();
    $this->token = null;
    $this->cookie_file_path = __DIR__."/cookies.txt";
    //	=unlink($this->cookie_file_path);
  }


  public function LoginRSI()  {
    $this->reset();

    // options
    $HANDLE           = $this->sd_user;
    $PASSWORD         = $this->sd_pass;
    $LOGINURL         = "https://robertsspaceindustries.com/connect";
    $agent            = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";


    // begin script
    $ch = curl_init();

    // extra headers
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    $headers[] = "Referer: https://robertsspaceindustries.com/";

    // basic curl options for all requests
    curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($ch, CURLOPT_HEADER,  true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);

    // set first URL
    curl_setopt($ch, CURLOPT_URL, $LOGINURL);

    // execute session to get cookies and required form inputs
    $content = curl_exec($ch);

    $string = file_get_contents($this->cookie_file_path);

    // get cookies
    $cookies = array();
    preg_match_all('|\.robertsspaceindustries\.com(\s+\S+){4}\s+(?<name>\S+)\s+(?<value>\S+)\s*|', $string, $cookies, PREG_SET_ORDER);

    $headers = array();
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    $headers[] = "X-Requested-With: XMLHttpRequest";
    $headers[] = "Referer: ".$LOGINURL;
    foreach($cookies as $cookie)
    {
      if($cookie['name'] == 'Rsi-Token')
      {
        $headers[] = "X-Rsi-Token: ".$cookie['value'];
        $this->token = $cookie["value"];
      }

      $this->cookies[$cookie['name']] = $cookie['value'];
    }

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



    $test = preg_match("#Rsi-Token=([a-zA-Z0-9]*)#", $result,$match);
    $this->token = $match[1];

    $cut = preg_match('#({"success".*"})#',$result,$match);
    $result = $match[1];

    return $result;
  }

  public function GetPage($url, $custom_headers = array()) {
    $agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";

    // begin script
    $ch = $this->ch;

    $headers = $custom_headers;
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";

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
    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file_path);

    curl_setopt($ch, CURLOPT_URL, $url);

    return curl_exec($ch);
  }

  private function getToken() {
  }


  public function sendPost($url,$data,$custom_headers = array()) {
    $agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";
    $ch = $this->ch;

    $headers = $custom_headers;
    $headers[] = "Accept: */*";
    $headers[] = "Connection: Keep-Alive";
    $headers[] = "X-Rsi-Token: ".$this->token;

    curl_setopt($ch, CURLOPT_USERPWD, $this->sd_user.':'.$this->sd_pass);

    foreach($data as $key=>$token) {
      $fields[$key] = $token;
    }

    // set postfields using what we extracted from the form
    $POSTFIELDS = http_build_query($fields);

    // set post options
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);

    // perform post
    $result = curl_exec($ch);
    return $result;
  }


  private function GetFormFields($data) {
    if (preg_match('/(<form class="signin-form".*?<\/form>)/is', $data, $matches)) {
      $inputs = $this->GetInputs($matches[1]);

      return $inputs;
    } else {
      die('didnt find login form');
    }
  }

  private function GetInputs($form)  {
    $inputs = array();

    $elements = preg_match_all('/(<input[^>]+>)/is', $form, $matches);

    if ($elements > 0) {
      for($i = 0; $i < $elements; $i++) {
        $el = preg_replace('/\s{2,}/', ' ', $matches[1][$i]);

        if (preg_match('/name=(?:["\'])?([^"\'\s]*)/i', $el, $name)) {
          $name  = $name[1];
          $value = '';

          if (preg_match('/value=(?:["\'])?([^"\'\s]*)/i', $el, $value)) {
            $value = $value[1];
          }

          $inputs[$name] = $value;
        }
      }
    }

    return $inputs;
  }


  public function logout() {
    curl_close($this->ch);
  }
}
$api = new RSI_API();
$api->LoginRSI();
?>
