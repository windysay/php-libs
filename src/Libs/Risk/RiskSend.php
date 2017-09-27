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
        $method = 'user_bash_info';
        $this->addBaseRequest($method, $data);
    }

    public function setUserContact($data)
    {
        $method = 'user_contact';
        $this->addBaseRequest($method, $data);
    }

    public function setPhoneHardware($data)
    {
        $method = 'user_phone_hardware';
        $this->addBaseRequest($method, $data);
    }

    public function setBankCard($data)
    {
        $method = 'bank_card';
        $this->addBaseRequest($method, $data);
    }

    public function setOrder($data)
    {
        $method = 'order';
        $this->addBaseRequest($method, $data);
    }

    public function setRepaymentPlan($data)
    {
        $method = 'repayment_plan';
        $this->addBaseRequest($method, $data);
    }

    public function setBlackList($data)
    {
        $method = 'black_list';
        $this->addBaseRequest($method, $data);
    }

    public function setTestUser($data)
    {
        $method = 'test_user';
        $this->addBaseRequest($method, $data);
    }

    public function setUserRealInfo($data)
    {
        $method = 'user_real_info';
        $this->addBaseRequest($method, $data);
    }

    public function setUserInfo($data)
    {
        $method = 'user_info';
        $this->addBaseRequest($method, $data);
    }

    public function setUserContactsTelephone($data)
    {
        $method = 'user_contacts_telephone';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneCall($data)
    {
        $method = 'telephone_call';
        $this->addBaseRequest($method, $data);
    }

    public function setUserSms($data)
    {
        $method = 'user_sms';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneSms($data)
    {
        $method = 'telephone_sms';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneBill($data)
    {
        $method = 'telephone_bill';
        $this->addBaseRequest($method, $data);
    }

    public function setTelephoneUser($data)
    {
        $method = 'telephone_user';
        $this->addBaseRequest($method, $data);
    }

    public function setRong360Report($data)
    {
        $method = 'rong360_report';
        $this->addBaseRequest($method, $data);
    }

    public function setUserApplication($data)
    {
        $method = 'user_application';
        $this->addBaseRequest($method, $data);
    }


    public function setUserPosition($data)
    {
        $method = 'user_position';
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
