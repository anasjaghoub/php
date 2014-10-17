<?php

/**
* Class WebService
* @author Anas Jaghoub
* @example
* $ws = new WebService("http://localhost"); $ws->params['id'] = 10; $ws->params['name'] = "test"; $ws->get()->getResponseAsJson()
*
*/
class WebService {
/** @var string $url */
public $url;

/** @var array $params */
public $params = array();

private $_ch;
/** @var string */
private $_response;

/** @var int request timeout in seconds */
public $timeout = 3;

public function __construct($url) {
if(empty($url)) {
throw new Exception("url is empty! cannot complete your request.");
}
$this->url = $url;
$this->_ch = curl_init();
}

/**
* @param $name string
* @param $value string
* @return $this
*/
public function addHeader($name, $value) {
curl_setopt($this->_ch, CURLOPT_HTTPHEADER, ["$name: $value"]);
return $this;
}

/**
* Do GET request and passes params array as part of url params
* @return $this
*/
public function get() {
curl_setopt($this->_ch,CURLOPT_RETURNTRANSFER,true);
if(!empty($this->params)) {
$getFields = "?";
foreach($this->params as $key => $value) {
$getFields .= "$key=$value&";
}
rtrim($getFields, "&");
$this->url .= $getFields;
}

rtrim($getFields, "&");

$this->request();
return $this;
}

/**
* Do POST request and passes params array as part of the post fields
* @return $this
*/
public function post() {
curl_setopt($this->_ch, CURLOPT_POST,1);
$postFields = "";
foreach($this->params as $key => $value) {
$postFields .= "$key=$value&";
}
rtrim($postFields, "&");
curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postFields);
$this->request();
return $this;
}

/**
* Do PUT request
* @return $this
*/
public function put() {
curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, "PUT");
$postFields = "";
foreach($this->params as $key => $value) {
$postFields .= "$key=$value&";
}
rtrim($postFields, "&");
curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postFields);
$this->request();
return $this;
}

/**
* Do delete request
* @return $this
*/
public function delete() {
curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($this->_ch,CURLOPT_RETURNTRANSFER,true);
$getFields = "?";
foreach($this->params as $key => $value) {
$getFields .= "$key=$value&";
}
rtrim($getFields, "&");
$this->url .= $getFields;
$this->request();
return $this;
}

/**
* Sends the actual request, this method will be called implicitly when calling get() post() delete() and put() methods
* @throws Exception
*/
private function request() {
curl_setopt($this->_ch, CURLOPT_URL , $this->url);
curl_setopt ($this->_ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->timeout );
curl_setopt($this->_ch , CURLOPT_TIMEOUT, $this->timeout );
//var_dump($this->url);die();
$this->_response = curl_exec($this->_ch);
if($this->_response === false) {
throw new Exception(curl_errno($this->_ch) . " " . curl_error($this->_ch));
}
curl_close($this->_ch);
}

/**
* Returns response as plain text
* @return string
*/
public function getResponse() {
return $this->_response;
}

/**
* Returns response as json object
* @return mixed
*/
public function getResponseAsJsonObject() {
return json_decode($this->_response);
}
}
