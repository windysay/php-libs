<?php
namespace JMD\Libs\Risk;

use JMD\Libs\Risk\Interfaces\Request;

class RiskSend
{

    public $accessToken;

    /** @var  Request */
    public $request;


    public function __construct($accessToken)
    {
        $this->request = new BaseRequest();
        $this->request->setUrl('send_data/all');
        $this->request->setAccessToken($accessToken);
        $this->accessToken = $accessToken;
    }


    public static function send(
        $appKey,
        $secretKey,
        $user,
        $userInfo,
        $userContact,
        $phoneHardware,
        $bankCard,
        $order,
        $repaymentPlan,
        $blackList,
        $testUser,
        $userRealInfo,
        $userContactsTelephone,
        $telephoneCall,
        $userSms
    ) {
        $accessToken = Authentication::getAccessToken($appKey, $secretKey);
        $model = new self($accessToken);
        $model->setUser($user);
        $model->setUserInfo($userInfo);
        $model->setUserContact($userContact);
        $model->setPhoneHardware($phoneHardware);
        $model->setBankCard($bankCard);
        $model->setOrder($order);
        $model->setRepaymentPlan($repaymentPlan);
        $model->setBlackList($blackList);
        $model->setTestUser($testUser);
        $model->setUserRealInfo($userRealInfo);
        $model->setUserContactsTelephone($userContactsTelephone);
        $model->setTelephoneCall($telephoneCall);
        $model->setUserSms($userSms);
        return $model->execute();
    }

    public function setUser($data)
    {
        $method = 'userBaseInfo';
        $this->addBaseRequest($method, $data);
    }

    public function setUserContact($data)
    {
        $method = 'userContact';
        $this->addBaseRequest($method, $data);
    }

    public function setPhoneHardware($data)
    {
        $method = 'userPhoneHardware';
        $this->addBaseRequest($method, $data);
    }

    public function setBankCard($data)
    {
        $method = 'bankCard';
        $this->addBaseRequest($method, $data);
    }

    public function setOrder($data)
    {
        $method = 'order';
        $this->addBaseRequest($method, $data);
    }

    public function setRepaymentPlan($data)
    {
        $method = 'repaymentPlan';
        $this->addBaseRequest($method, $data);
    }

    public function setBlackList($data)
    {
        $method = 'blackList';
        $this->addBaseRequest($method, $data);
    }

    public function setTestUser($data)
    {
        $method = 'testUser';
        $this->addBaseRequest($method, $data);
    }

    public function setUserRealInfo($data)
    {
        $method = 'userRealInfo';
        $this->addBaseRequest($method, $data);
    }

    public function setUserInfo($data)
    {
        $method = 'userInfo';
        $this->addBaseRequest($method, $data);
    }

    public function setUserContactsTelephone($data)
    {
        $method = 'userContactsTelephone';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneCall($data)
    {
        $method = 'telephoneCall';
        $this->addBaseRequest($method, $data);
    }

    public function setUserSms($data)
    {
        $method = 'userSms';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneSms($data)
    {
        $method = 'telephoneSms';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneBill($data)
    {
        $method = 'telephoneBill';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneUser($data)
    {
        $method = 'telephoneUser';
        $this->addBaseRequest($method, $data);
    }

    public function setRong360Report($data)
    {
        $method = 'rong360Report';
        $this->addBaseRequest($method, $data);
    }

    public function setUserApplication($data)
    {
        $method = 'userApplication';
        $this->addBaseRequest($method, $data);
    }


    public function setUserPosition($data)
    {
        $method = 'userPosition';
        $this->addBaseRequest($method, $data);
    }


    private function addBaseRequest($field, $val)
    {
        $data = $this->request->data;
        $data[$field] = $val;
        $this->request->setData($data);
    }


    public function execute()
    {
        return $this->request->execute();
    }

}
