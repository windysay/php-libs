<?php
namespace JMD\Libs\Risk;

class Authentication
{

    public static function getAccessToken($appKey, $secretKey, $domain = null)
    {
        $request = new BaseRequest();
        if ($domain) {
            $request->setDomain($domain);
        }
        $request->setUrl('account/login');
        $request->setData([
            'app_key' => $appKey,
            'secret_key' => $secretKey
        ]);
        $data = $request->execute();
        $format = new DataFormat($data);
        return $format->isSuccess() ? $format->getDataField('access_token') : false;

    }

    public static function accessTokenIsValid($accessToken, $domain = null)
    {
        $request = new BaseRequest();
        if ($domain) {
            $request->setDomain($domain);
        }
        $request->setUrl('risk/operator');
        $request->setAccessToken($accessToken);
        $request->setMethod(2);
        $data = $request->execute();
        $format = new DataFormat($data);
        return $format->isSuccess() ? true : false;
    }

}