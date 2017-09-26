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
        $accessToken = Authentication::getAccessToken($appKey,$secretKey);
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
        $model->execute();
    }

    public function setUser($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_bash_info')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setUserContact($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_contact')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setPhoneHardware($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/phone_hardware')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setBankCard($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/bank_card')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setOrder($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/order')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setRepaymentPlan($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/repayment_plan')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setBlackList($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/black_list')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setTestUser($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/test_user')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setUserRealInfo($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_real_info')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setUserInfo($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_info')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setUserContactsTelephone($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_contacts_telephone')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setTelephoneCall($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_contacts_telephone')->setData($data);
        $this->invoker->setCommands($request);
    }

    public function setUserSms($data)
    {
        $request = new BaseRequest();
        $request->setUrl('send_data/user_sms')->setData($data);
        $this->invoker->setCommands($request);
    }


    public function execute()
    {
        $this->invoker->execute();
    }

}
