<?php
global $conf;
include_once(App::getSysPath('classes/Util.php'));
include_once(App::getSysPath('classes/Core.php'));
include_once(App::getSysPath('classes/File.php'));
include_once(App::getSysPath('classes/Session.php'));
include_once(App::getSysPath('classes/Common.php' ));
include_once(App::getSysPath('classes/Token.php'));
include_once(App::getSysPath('classes/SimpleNavegation.php' ));
include_once(App::getSysPath('classes/Image.php' ));
include_once(App::getSysPath('classes/SimpleImage.php' ));
include_once(App::getSysPath('classes/BaseDAO.php' ));

class Api {
    const DEFAULT_RESPONSE_CODE = 200;
    const DEFAULT_RESPONSE_MESSAGE = "Invalid Response from API";
    const CODE_UNAUTHORIZED = 302;
    const MESSAGE_UNAUTHORIZED = "Invalid Response from API";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_DELETE = "DELETE";
    const METHOD_PUT = "PUT";
    private $method = "";
    private $request;
    private $response = array();
	private	$requestBody = "";

    function __construct() {
        $this->response ['code'] = Api::DEFAULT_RESPONSE_CODE;
        $this->response ['message'] = Api::DEFAULT_RESPONSE_MESSAGE;
        $this->response ['response'] = "";
        $this->response ['return'] = false;
    }

    function start(){
        $this->method = $_SERVER ['REQUEST_METHOD'];
        $this->request = explode ( '/', trim ( $_SERVER ['PATH_INFO'], '/' ) );
        switch (strtoupper($this->method)) {
            case Api::METHOD_GET :
                $this->requestBody = json_decode (urldecode($_REQUEST['jsonData']), true);
                if($this->requestBody == null){
                    $this->requestBody = json_decode ($_REQUEST['jsonData'], true);
                }
                break;
            default:
                $this->requestBody = json_decode ( file_get_contents ( 'php://input' ), true );
                break;
        }
    }

    function authToken($id, $token, $message){
        $auth = Token::isValid($id, $token);
        if (! $auth) {
            if(empty($message)){
                $message = Api::MESSAGE_UNAUTHORIZED;
            }
            $this->end(Api::CODE_UNAUTHORIZED, $message);
        }
    }

    
    function authSession($sessionName, $var, $expectedValue, $message){
        $auth = false;
        $session = new Session($sessionName);
        if($session->getVar($var)==$expectedValue){
            $auth = true;
        }

        if (! $auth) {
            if(empty($message)){
                $message = Api::MESSAGE_UNAUTHORIZED;
            }
            $this->end(Api::CODE_UNAUTHORIZED, $message);
        }
    }

    function end($code = null, $message = null){
        $this->setResponseCode($code);
        $this->setMessage($message);
        echo $this->getResponse();
        exit;
    }

    function getParam($param){
        return $_REQUEST[$param];
    }

    function getMethod(){
        return $this->method;
    }

    function getResponse(){
        return json_encode($this->response);
    }

    function setResponse($response){
        if($response != NULL) $this->response['response'] = $response;
    }

    function getMessage(){
        return $response['message'];
    }

    function setMessage($message){
        if($message != NULL) $this->response['message'] = $message;
    }

    function getBody(){
        return $this->requestBody;
    }

    function setResponseCode($code){
        if($code > 0) $this->response['code'] = $code;
    }

    function setResponseReturn($return){
       $this->response['return'] = $return;
    }

    function getResponseCode(){
        return $this->response['code'];
    }


}