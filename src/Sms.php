<?php
/**
 * Author: Shen.L
 * Email: shen@shenl.com
 * Date: 2018/7/4
 * Time: 23:02
 */

namespace hustshenl\aliyun\sms;

use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\Regions\EndpointConfig;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use yii\base\BaseObject;


class Sms extends BaseObject
{
    public $access_key;
    public $access_secret;
    public $sign_name;


    static $acsClient = null;

    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public static function getAcsClient($accessKeyId,$accessKeySecret) {

        if(static::$acsClient == null) {
            //产品名称:云通信流量服务API产品,开发者无需替换
            $product = "Dysmsapi";
            //产品域名,开发者无需替换
            $domain = "dysmsapi.aliyuncs.com";
            // 暂时不支持多Region
            $region = "cn-hangzhou";
            // 服务结点
            $endPointName = "cn-hangzhou";
            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
            // 增加服务结点
            // 手动加载endpoint
            //EndpointConfig::load();
            Config::load();
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }


    public function sendSms($to, $template_code, $data, Array $config = null, $outId = '')
    {
        $signName = $this->sign_name;
        $request = new SendSmsRequest();
        //必填-短信接收号码
        $request->setPhoneNumbers($to);
        //必填-短信签名
        $request->setSignName($signName);
        //必填-短信模板Code
        $request->setTemplateCode($template_code);
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        if ($data) {
            $request->setTemplateParam(json_encode($data));
        }
        //选填-发送短信流水号
        if ($outId) {
            $request->setOutId($outId);
        }
        $acsClient = static::getAcsClient($this->access_key,$this->access_secret);
        //发起访问请求
        return $acsClient->getAcsResponse($request);
    }
}