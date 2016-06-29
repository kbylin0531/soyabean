<?php
/**
 * User: linzh
 * Date: 2016/3/9
 * Time: 16:02
 */
namespace Soya\Util;
use Soya\Core\Exception;

use Soya\Util\Helper\XMLHelper;

/**
 * Class Response 输出控制类
 * @package Kbylin\System\Core
 */
class Response {

    /**
     * 数据返回形式
     */
    const AJAX_JSON     = 0;
    const AJAX_XML      = 1;
    const AJAX_STRING   = 2;
    
    /**
     * 返回的消息类型
     */
    const MESSAGE_TYPE_SUCCESS = 1;
    const MESSAGE_TYPE_WARNING = -1;
    const MESSAGE_TYPE_FAILURE = 0;

    /**
     * get returned content type by file suffix
     * @param string $suffix
     * @return null|string
     */
    public static function getMimeBysuffix($suffix){
        static $mimes = null;
        if(!$mimes){
            $mimes = include dirname(__DIR__).'/Common/mime.php/';
        }
        return isset($mimes[$suffix])?$mimes[$suffix]:null;
    }

    /**
     * 清空输出缓存
     * @return void
     */
    public static function cleanOutput(){
        ob_get_level() > 0 and ob_end_clean();
    }

    public static function flushOutput(){
        ob_get_level() and ob_end_flush();
    }

    public static function success($message){
        self::ajaxBack([
            '_message' => $message,
            '_type' => self::MESSAGE_TYPE_SUCCESS,
        ]);
    }

    public static function failed($message){
        self::ajaxBack([
            '_message' => $message,
            '_type' => self::MESSAGE_TYPE_FAILURE,
        ]);
    }

    /**
     * return the request in ajax way
     * and call this method will exit the script
     * @access protected
     * @param mixed $data general type of data
     * @param int $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     * @throws Exception
     */
    public static function ajaxBack($data, $type = self::AJAX_JSON, $json_option = 0){
        self::cleanOutput();
        \Soya::closeTrace();
        switch (strtoupper($type)) {
            case self::AJAX_JSON :// 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data, $json_option));
            case self::AJAX_XML :// 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(XMLHelper::encodeHtml($data));
            case self::AJAX_STRING:
                header('Content-Type:text/plain; charset=utf-8');
                exit($data);
            default:
                throw new Exception('Invalid output!');
        }
    }


    /**
     * 向浏览器客户端发送不缓存命令
     * @param bool $clean 显示清空
     * @return void
     */
    public static function sendNocache($clean=true){
        $clean and Response::cleanOutput();
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
    }

    /**
     * Set HTTP Status Header
     *
     * @param	int	$code the status code
     * @param	string
     * @return	void
     */
    public static function setStatusHeader($code = 200, $text = ''){
        if (IS_CLIENT){
            return;
        }

        if (empty($code) OR ! is_numeric($code)){
            Exception::throwing('Status codes must be numeric!');
        }

        if (empty($text)) {
            is_int($code) OR $code = (int) $code;
            $stati = array(
                100	=> 'Continue',
                101	=> 'Switching Protocols',

                200	=> 'OK',
                201	=> 'Created',
                202	=> 'Accepted',
                203	=> 'Non-Authoritative Information',
                204	=> 'No Content',
                205	=> 'Reset Content',
                206	=> 'Partial Content',

                300	=> 'Multiple Choices',
                301	=> 'Moved Permanently',
                302	=> 'Found',
                303	=> 'See Other',
                304	=> 'Not Modified',
                305	=> 'Use Proxy',
                307	=> 'Temporary Redirect',

                400	=> 'Bad Request',
                401	=> 'Unauthorized',
                402	=> 'Payment Required',
                403	=> 'Forbidden',
                404	=> 'Not Found',
                405	=> 'Method Not Allowed',
                406	=> 'Not Acceptable',
                407	=> 'Proxy Authentication Required',
                408	=> 'Request Timeout',
                409	=> 'Conflict',
                410	=> 'Gone',
                411	=> 'Length Required',
                412	=> 'Precondition Failed',
                413	=> 'Request Entity Too Large',
                414	=> 'Request-URI Too Long',
                415	=> 'Unsupported Media Type',
                416	=> 'Requested Range Not Satisfiable',
                417	=> 'Expectation Failed',
                422	=> 'Unprocessable Entity',

                500	=> 'Internal Server Error',
                501	=> 'Not Implemented',
                502	=> 'Bad Gateway',
                503	=> 'Service Unavailable',
                504	=> 'Gateway Timeout',
                505	=> 'HTTP Version Not Supported'
            );

            if (isset($stati[$code]))
            {
                $text = $stati[$code];
            }
            else
            {
                Exception::throwing('No status text available. Please check your status code number or supply your own message text.');
            }
        }

        if (strpos(PHP_SAPI, 'cgi') === 0)
        {
            header('Status: '.$code.' '.$text, TRUE);
        }
        else
        {
            $server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($server_protocol.' '.$code.' '.$text, TRUE, $code);
        }
    }
}