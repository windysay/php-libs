<?php

namespace JMD\Libs\Risk;

use JMD\Libs\Risk\Interfaces\Request;

class RiskSend
{

    public $accessToken;

    /** @var  Request */
    public $request;


    public function __construct($appKey, $secretKey, $telephone, $domain, $userId = null)
    {
        $this->request = new BaseRequest($appKey, $secretKey);
        $this->request->setUrl('send_data/all');
        $this->request->setDomain($domain);
        $this->addBaseRequest('telephone', $telephone);
        if (!empty($userId)) {
            $this->addBaseRequest('user_id', $userId);
        }
//        $this->request->setAccessToken($accessToken);
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
        $userSms,
        $telephoneSms,
        $telephoneBill,
        $telephoneUser,
        $rong360Report,
        $userApplication,
        $userPosition,
        $telephone,
        $domain = null
    ) {
        $model = new self($appKey, $secretKey, $telephone, $domain);

        if ($domain) {
            $model->request->setDomain($domain);
        }
        $model->addBaseRequest('telephone', $telephone);
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

        $model->setTelephoneSms($telephoneSms);
        $model->setTelephoneBill($telephoneBill);
        $model->setTelephoneUser($telephoneUser);
        $model->setRong360Report($rong360Report);
        $model->setUserApplication($userApplication);
        $model->setUserPosition($userPosition);
        $result = $model->execute();
        return $result;
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

    public function setTestAccount($data)
    {
        $method = 'test_account';
        $this->addBaseRequest($method, $data);
    }

    public function setUserWork($data)
    {
        $method = 'user_work';
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

    public function setContract($data)
    {
        $method = 'contract';
        $this->addBaseRequest($method, $data);
    }

    public function setPrimaryInfo($data)
    {
        $method = 'primary_info';
        $this->addBaseRequest($method, $data);

    }

    public function setOrderRecordNew($data)
    {
        $method = 'order_record_new';
        $this->addBaseRequest($method, $data);
    }


    public function setYzOrder($data)
    {
        $method = 'yz_order';
        $this->addBaseRequest($method, $data);
    }


    public function setVipOrder($data)
    {
        $method = 'vip_order';
        $this->addBaseRequest($method, $data);
    }

    public function setRiskSystem($data)
    {
        $method = 'risk_system';
        $this->addBaseRequest($method, $data);
    }

    public function setUserFace($data)
    {
        $method = 'user_face';
        $this->addBaseRequest($method, $data);
    }

    public function setUserLoanProductInfo($data)
    {
        $method = 'user_loan_product_info';
        $this->addBaseRequest($method, $data);
    }

    public function setUserFaceResult($data)
    {
        $method = 'user_face_result';
        $this->addBaseRequest($method, $data);
    }

    public function setUserFaceVerify($data)
    {
        $method = 'user_face_verify';
        $this->addBaseRequest($method, $data);
    }

    public function setUserFacebook($data)
    {
        $method = 'user_facebook';
        $this->addBaseRequest($method,$data);
    }


    private function addBaseRequest($field, $val)
    {
        $data = $this->request->data;
        $data[$field] = $val;
        $this->request->setData($data);
    }

    public function setUserCallRecords($data)
    {
        $method = 'user_call_records';
        $this->addBaseRequest($method, $data);
    }

    public function pushQueue($queueName, $passWord = null, $host = null, $port = null, $dataBase = 0)
    {
        $str = serialize($this);
        $redis = new \Redis();
        $redis->connect($host, $port);
        $redis->auth($passWord);
        $redis->select($dataBase);
        $redis->lPush($queueName, $str);
    }


    public function execute()
    {
        return new DataFormat($this->request->execute());
    }

}
