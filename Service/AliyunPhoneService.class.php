<?php

namespace Aliyun\Service;

use Aliyun\Model\AliyunConfigModel;
use Aliyun\Model\AliyunPhoneBindModel;
use Aliyun\Model\AliyunPhoneConfigModel;
use System\Service\BaseService;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AliyunPhoneService extends BaseService
{

    public $accessKeyId = '';
    public $accessSecret = '';
    public $PoolKey = '';
    public $Expiration = '';
    public $aliyun_id = '';

    function __construct($id = 1)
    {
        //加载sdk
        require dirname(dirname(__DIR__)) . '/Aliyun/Lib/AliyunPhone/client/vendor/autoload.php';

        $AliyunConfigModel = new AliyunConfigModel();
        $AliyunConfigRes = $AliyunConfigModel->where(['id' => $id])->find();

        $this->aliyun_id = $id;
        $this->accessKeyId = $AliyunConfigRes['access_key_id'];
        $this->accessSecret = $AliyunConfigRes['access_secret'];

        $AliyunPhoneConfigModel = new AliyunPhoneConfigModel();
        $AliyunPhoneConfigRes = $AliyunPhoneConfigModel->where(['aliyun_id' => $id])->find();

        //未设置的情况下5分钟后自动断开联系
        if (!$AliyunPhoneConfigRes['expiration']) $AliyunPhoneConfigRes['expiration'] = 300;

        $this->PoolKey = $AliyunPhoneConfigRes['pool_key'];
        $this->Expiration = $AliyunPhoneConfigRes['expiration'];
    }


    /**
     * 用接口BindAxb添加AXB号码的绑定关系。
     * @param string $PhoneNoA
     * @param string $PhoneNoB
     * @param string $Expiration
     * @return array
     * @throws ClientException
     */
    public function sendBindAxb(
        $PhoneNoA = '', $PhoneNoB = '', $Expiration = ''
    )
    {
        if (!$this->accessKeyId) return createReturn(false, '', 'accessKeyId不能为空');
        if (!$this->accessSecret) return createReturn(false, '', 'accessSecret不能为空');

        if (!$Expiration) {
            //未传断开连接的时间的情况下使用默认的断开连接时间
            $Expiration = date("Y-m-d H:i:s", time() + $this->Expiration);
        }

        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dyplsapi')
                ->version('2017-05-25')
                ->action('BindAxb')
                ->method('POST')
                ->host('dyplsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNoA' => $PhoneNoA,
                        'Expiration' => $Expiration,
                        'PoolKey' => $this->PoolKey,
                        'PhoneNoB' => $PhoneNoB,
                    ],
                ])
                ->request();

            //记录绑定记录
            $AliyunPhoneBindModel = new AliyunPhoneBindModel();
            $bindData['code'] = $result->toArray()['Code'];
            $bindData['message'] = $result->toArray()['Message'];
            $bindData['request_id'] = $result->toArray()['RequestId'];
            $bindData['secret_bind_dto'] = serialize($result->toArray()['SecretBindDTO']);
            $bindData['extension'] = $result->toArray()['SecretBindDTO']['Extension'];
            $bindData['secret_no'] = $result->toArray()['SecretBindDTO']['SecretNo'];
            $bindData['subs_id'] = $result->toArray()['SecretBindDTO']['SubsId'];
            $bindData['aliyun_id'] = $this->aliyun_id;
            $bindData['add_time'] = time();
            $bindData['edit_time'] = time();

            if ($result->toArray()['Code'] == 'OK') {

                $bindData['bind_status'] = $AliyunPhoneBindModel::YES_BIND;
                $AliyunPhoneBindModel->add($bindData);

                return createReturn(true, $result->toArray(), '绑定成功');
            } else {

                $bindData['bind_status'] = $AliyunPhoneBindModel::FAIL_BIND;
                $AliyunPhoneBindModel->add($bindData);

                return createReturn(false, $result->toArray(), $result->toArray()['Message']);
            }
        } catch (ClientException $e) {
            return createReturn(false, '', $e->getErrorMessage() . PHP_EOL);
        } catch (ServerException $e) {
            return createReturn(false, '', $e->getErrorMessage() . PHP_EOL);
        }
    }

    /**
     * 用接口BindAxb添加AXB号码的解绑。
     * @param $SubsId
     * @param $SecretNo
     * @throws ClientException
     */
    public function unbindSubscription($SubsId,$SecretNo){
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->product('Dyplsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('UnbindSubscription')
                ->method('POST')
                ->host('dyplsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'SubsId' => $SubsId,
                        'SecretNo' => $SecretNo,
                        'PoolKey' => $this->PoolKey,
                    ],
                ])
                ->request();

            if ($result->toArray()['Code'] == 'OK') {
                return createReturn(true, $result->toArray(), '解绑成功');
            } else {
                return createReturn(false, $result->toArray(), $result->toArray()['Message']);
            }
        } catch (ClientException $e) {
            return createReturn(false, '', $e->getErrorMessage() . PHP_EOL);
        } catch (ServerException $e) {
            return createReturn(false, '', $e->getErrorMessage() . PHP_EOL);
        }
    }

}