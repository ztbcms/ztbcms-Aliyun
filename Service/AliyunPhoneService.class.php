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
        require APP_PATH. '/Aliyun/Lib/AliyunPhone/client/vendor/autoload.php';

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
     * 获取隐私保护的号码
     * @param string $PhoneNoA
     * @param string $PhoneNoB
     * @return array
     * @throws ClientException
     */
    public function getBindAxb($PhoneNoA = '', $PhoneNoB = '')
    {

        if (!$this->accessKeyId) return createReturn(false, '', 'accessKeyId不能为空');
        if (!$this->accessSecret) return createReturn(false, '', 'accessSecret不能为空');
        if (!$this->PoolKey) return createReturn(false, '', 'poolKey不能为空');

        $AliyunPhoneBindModel = new AliyunPhoneBindModel();
        //获取成功的查询记录
        $bindList = $AliyunPhoneBindModel->where([
            'phone_no_a' => $PhoneNoA,
            'phone_no_b' => $PhoneNoB,
            'bind_status' => '1'
        ])->select();
        if (!$bindList) {
            $bindList = $AliyunPhoneBindModel->where([
                'phone_no_a' => $PhoneNoB,
                'phone_no_b' => $PhoneNoA,
                'bind_status' => '1'
            ])->select();
        }
        if ($bindList) {
            // 存在旧的绑定关系
            $is_exist = false;
            foreach ($bindList as $k => $v) {
                $getQueryRes = $this->getQueryCallStatus($v['subs_id']);
                if ($getQueryRes['data']['SecretCallStatusDTO']['Status'] == '1') {
                    //该电话还能正常使用
                    $is_exist = true;
                    $res['secret_no'] = $v['secret_no'];
                    break;
                }
            }

            if ($is_exist) {
                //旧的绑定关系还能使用
                return createReturn(true, $res, '获取成功');
            }
        }

        // 不存在旧的绑定关系
        $sendBindRes = $this->sendBindAxb($PhoneNoA, $PhoneNoB);

        $res['secret_no'] = $sendBindRes['data']['SecretBindDTO']['SecretNo'];
        return createReturn(true, $res, '获取成功');
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
            $bindData['phone_no_a'] = $PhoneNoA;
            $bindData['phone_no_b'] = $PhoneNoB;
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
    public function unbindSubscription($SubsId, $SecretNo)
    {
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        $AliyunPhoneBindModel = new AliyunPhoneBindModel();

        //先查询当前的绑定状态
        $QueryCallRes = $this->getQueryCallStatus($SubsId);
        if ($QueryCallRes['data']['SecretCallStatusDTO']['Status'] == '4') {
            //该号码已过期
            return createReturn(true, $QueryCallRes['data'], '解绑成功');
        }

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

                //该号码已过期
                $save['bind_status'] = $AliyunPhoneBindModel::UN_BIND;
                $save['un_bind_time'] = time();
                $AliyunPhoneBindModel->where(['subs_id' => $SubsId])->save($save);

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

    /**
     * 获取当前的使用状态
     * @param string $SubsId
     * @param string $PoolKey
     * @return array
     * @throws ClientException
     */
    public function getQueryCallStatus($SubsId = '')
    {
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dyplsapi')
                ->version('2017-05-25')
                ->action('QueryCallStatus')
                ->method('POST')
                ->host('dyplsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'SubsId' => $SubsId,
                        'PoolKey' => $this->PoolKey,
                    ],
                ])
                ->request();
            // Status
            //1：呼叫正常。
            //2：当前绑定关系呼叫状态异常，平台重新分配了一个临时可用的X号码用于呼叫。
            //3：当前绑定呼叫异常，且分配临时号码失败。建议用户降级为透传真实的被叫号码来继续呼叫。
            //4：绑定关系已经过期。

            if ($result->toArray()['SecretCallStatusDTO']['Status'] == '4') {
                //过期的情况下取消
                $AliyunPhoneBindModel = new AliyunPhoneBindModel();
                $save['bind_status'] = $AliyunPhoneBindModel::UN_BIND;
                $save['un_bind_time'] = time();
                $AliyunPhoneBindModel->where(['subs_id' => $SubsId])->save($save);
            }

            return createReturn(true, $result->toArray(), '获取成功');
        } catch (ClientException $e) {
            return createReturn(false, '', $e->getErrorMessage() . PHP_EOL);
        } catch (ServerException $e) {
            return createReturn(false, '', $e->getErrorMessage() . PHP_EOL);
        }
    }

    /**
     * 定时关闭五分钟前的绑定
     * @return array
     */
    public function cronUnbindSubscription()
    {
        $AliyunPhoneBindModel = new AliyunPhoneBindModel();
        $bindList = $AliyunPhoneBindModel->where([
            'bind_status' => $AliyunPhoneBindModel::YES_BIND,
            'aliyun_id' => $this->aliyun_id,
            'add_time' => ['lt', time() - $this->Expiration]
        ])->select();
        foreach ($bindList as $k => $v) {
            $this->unbindSubscription($v['subs_id'], $v['secret_no']);
        }
        return createReturn(true, '', '自动取消成功');
    }

    /**
     * 添加或者编辑阿里云配置
     * @param array $post
     * @return array
     */
    static function addEditConfig($post = [])
    {
        $id = $post['id'];
        if ($id) {
            $post['edit_time'] = time();
            return self::update('aliyun_phone_confing', $id, $post);
        } else {
            return self::create('aliyun_phone_confing', $post);
        }
    }

    /**
     * 获取绑定列表
     * @param array $where
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     */
    static function getBindList($where = [], $order = '', $page = 1, $limit = 20)
    {
        $res = self::select('aliyun_phone_bind', $where, $order, $page, $limit);
        $items = $res['data']['items'];
        foreach ($items as $k => $v) {

        }
        $res['data']['items'] = $items;
        return $res;
    }

}