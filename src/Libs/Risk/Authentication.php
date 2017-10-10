<?php
namespace JMD\Libs\Risk;

class Authentication
{

    public static function getAccessToken($appKey, $secretKey, $domain = null)
    {
        //TODO 第一版本，此处有些违反单一职责原则
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
}