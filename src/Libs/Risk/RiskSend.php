<?php
namespace JMD\Libs\Risk;

class RiskSend
{

    public $invoker;

    public $accessToken;


    public function __construct($accessToken)
    {
        $this->invoker = new Invoker();
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
        $url = 'send_data/user_bash_info';
        $this->addBaseRequest($url, $data);
    }

    public function setUserContact($data)
    {
        $url = 'send_data/user_contact';
        $this->addBaseRequest($url, $data);
    }

    public function setPhoneHardware($data)
    {
        $url = 'send_data/phone_hardware';
        $this->addBaseRequest($url, $data);
    }

    public function setBankCard($data)
    {
        $url = 'send_data/bank_card';
        $this->addBaseRequest($url, $data);
    }

    public function setOrder($data)
    {
        $url = 'send_data/order';
        $this->addBaseRequest($url, $data);
    }

    public function setRepaymentPlan($data)
    {
        $url = 'send_data/repayment_plan';
        $this->addBaseRequest($url, $data);
    }

    public function setBlackList($data)
    {
        $url = 'send_data/black_list';
        $this->addBaseRequest($url, $data);
    }

    public function setTestUser($data)
    {
        $url = 'send_data/test_user';
        $this->addBaseRequest($url, $data);
    }

    public function setUserRealInfo($data)
    {
        $url = 'send_data/user_real_info';
        $this->addBaseRequest($url, $data);
    }

    public function setUserInfo($data)
    {
        $url = 'send_data/user_info';
        $this->addBaseRequest($url, $data);
    }

    public function setUserContactsTelephone($data)
    {
        $url = 'send_data/user_contacts_telephone';
        $this->addBaseRequest($url, $data);
    }

    public function setTelephoneCall($data)
    {
        $url = 'send_data/user_contacts_telephone';
        $this->addBaseRequest($url, $data);
    }

    public function setUserSms($data)
    {
        $url = 'send_data/user_sms';
        $this->addBaseRequest($url, $data);
    }


    private function addBaseRequest($url, $data)
    {
        $request = new BaseRequest();
        $request->setUrl($url)->setData($data)->setAccessToken($this->accessToken);
        $this->invoker->setCommands($request);
    }


    public function execute()
    {
        return $this->invoker->execute();
    }

}
