<?php

namespace Ykaej\Aliyun;


class AliyunApiPublic
{
    protected $method;//提交方法
    protected $format = 'JSON';  //返回值的类型,默认为XML
    protected $version = '2015-01-09'; //API版本号 形式：YYYY-MM-DD
    protected $accessKeyId; //密钥ID
    protected $accessSecret;
    protected $signatureMethod = 'HMAC-SHA1';//签名方式
    protected $dateTimesTamp = 'Y-m-d\TH:i:s\Z';//请求的时间戳
    protected $signatureVersion = '1.0';
    protected $signatureNonce; //唯一随机数
    protected $protocolType = 'http';
    protected $apiUrl;
//    protected $actionName;  //操作接口名

    /**
     * 处理api接口
     * @param $config array 参数
     * @param string $method
     * @return array
     */
    protected function aliyunDealApi($config, $method = 'GET')
    {
        $this->setMethod(strtoupper($method));
        $url = $this->composeUrl($this->getApiUrl(),$config);
        $domain_list = $this->http_curl($url);
        return json_decode($domain_list,true);
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @return mixed|string
     */
    protected function http_curl($url, $method='get', $params=[])
    {
        //初始化
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        //判断是否是post请求
        if ( strtolower($method) == 'post' && !empty($params) ){
            curl_setopt($curl,CURLOPT_POST,true);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$params);
        }
        //
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);

        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

        $data = curl_exec($curl);

        if (curl_errno($curl)){
            return curl_error($curl);
        }
        //关闭
        curl_close($curl);
        return $data;
    }
    /**
     * 拼接url
     * @param $AliyunDomain
     * @param $config
     * @return bool|string
     */
    public function composeUrl($AliyunDomain, $config)
    {
        //UTC时间
        date_default_timezone_set("GMT");
        //公共参数
        $apiParams = [
            'Format' => $this->getFormat(),
            'Version' => $this->getVersion(),
            'AccessKeyId' => $this->getAccessKeyId(),
            'SignatureMethod' => $this->signatureMethod,
            'SignatureNonce' => $this->getSignatureNonce(),
            'SignatureVersion' => $this->signatureVersion,
            'Timestamp' => date($this->dateTimesTamp),
        ];

        //获取列表参数
        $apiParams = array_merge($apiParams,$config);

        //signature
        $accessSecret = $this->getAccessSecret();
        $apiParams['Signature'] = $this->computeSignature($apiParams,$accessSecret);
        if (strtoupper($this->getMethod()) == 'POST'){
            #Todo Somthing
        }else{
            $requestUrl = $this->getProtocolType() . '://' . $AliyunDomain .'/?';
            foreach ($apiParams as $key => $val){
                $requestUrl .= "{$key}=" . urlencode($val) . "&";
            }
            return substr($requestUrl,0,-1);
        }
    }

    /**
     * 计算 signature
     * @param $apiParams
     * @param $accessSecret
     * @return string
     */
    private function computeSignature($apiParams, $accessSecret)
    {
        //字典排序
        ksort($apiParams);
        $urlParams = '';
        //拼接
        foreach ($apiParams as $key=>$val){
            $urlParams .= '&' .  $this->percentEncode($key) . '=' . $this->percentEncode($val);
        }
        $stringToSign = $this->getMethod() . '&%2F&' .$this->percentEncode(substr($urlParams,1));
        $signature = $this->signString($stringToSign,$accessSecret.'&');
        return $signature;
    }

    /**
     * 计算逻辑
     * @param $str
     * @return mixed|string
     */
    private function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    /**
     * @param $stringToSign
     * @param $accessSecret
     * @return string
     */
    public function signString($stringToSign, $accessSecret){
        return base64_encode(hash_hmac('sha1',$stringToSign,$accessSecret,true));
    }
    /**
     * @return mixed
     */
    public function getSignatureNonce()
    {
        return uniqid(str_random(10));
    }
    /**
     * @return mixed
     */
    public function getAccessKeyId()
    {
        return env('ALIYUN_ACCESS_KEYID');
    }

    /**
     * @return mixed
     */
    public function getAccessSecret()
    {
        return env('ALIYUN_ACCESS_SECRET');
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param mixed $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }



    /**
     * @return string
     */
    public function getProtocolType()
    {
        return $this->protocolType;
    }

    /**
     * @param string $protocolType
     */
    public function setProtocolType($protocolType)
    {
        $this->protocolType = $protocolType;
    }
    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }


}