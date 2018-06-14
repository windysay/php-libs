<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/16
 * Time: 10:20
 */

namespace JMD\Libs\Oss;

use OSS\Core\OssUtil;
use OSS\OssClient;
use OSS\Core\OssException;

class OssHelper
{
    const endpoint = 'https://oss-cn-shenzhen.aliyuncs.com';//线上oss上传（外部）
    const endpointInternal = 'http://oss-cn-shenzhen-internal.aliyuncs.com';//线上oss上传（内部）
    const jmdOssLoanHost = 'https://loan.cdn.jiumiaodai.com';//所有图cdn访问地址
    const aliOssInternalHost = 'http://jqb-loan.oss-cn-shenzhen-internal.aliyuncs.com';//线上默认oss访问地址（内网）
    const aliOssHost = 'https://jqb-loan.oss-cn-shenzhen.aliyuncs.com';//线上默认oss访问地址（外网）
    const accessKeyId = 'LTAI9qPgFnb0z0lD';
    const accessKeySecret = 'SUcRqsjwBlChGj9dpeskZYvTsTflpn';
    const bucket = 'jqb-loan';

    /**
     * 获取存储空间
     * @return string
     */
    public static function getBucketName()
    {
        return self::bucket;
    }

    /**
     * 连接
     * @param $is_internal
     * is_internal 内部（需内网环境），默认外网
     * @return null|OssClient
     */
    public static function getOssClient($is_internal = false)
    {
        try {
            if ($is_internal) {
                $ossClient = new OssClient(self::accessKeyId, self::accessKeySecret, self::endpointInternal, false);
            } else {
                $ossClient = new OssClient(self::accessKeyId, self::accessKeySecret, self::endpoint, false);
            }
        } catch (OssException $e) {
            return null;
        }
        return $ossClient;
    }

    /**
     * 通过对象参数上传
     * $object 自定义oss上对应路径加文件名
     * $file 为base64字符串
     * @param $object
     * @param $file
     * @return array
     */
    public static function uploadObject($object, $file)
    {
        if (!OssUtil::validateObject($object)) return self::result(1, '路径不能以/开头');
        //判断base64，并转换
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $result)) {
            if (strstr($file, ",")) {
                $file = explode(',', $file);
                $file = $file[1];
            }
            $file = base64_decode($file);
        }

        $bucketName = self::getBucketName();
        $ossClient = self::getOssClient(true);
        try {
            $result = $ossClient->putObject($bucketName, $object, $file);
        } catch (OssException $e) {
            return self::result(1, '上传失败');
        }
        return self::result(0, '上传成功');
    }

    /**
     * 通过文件上传
     * $object 自定义oss上对应路径加文件名
     * $file 原文件静态地址
     * @param $object
     * @param $file
     * @return array
     */
    public static function uploadFile($object, $file)
    {
        if (!OssUtil::validateObject($object)) return self::result(1, '路径不能以/开头');
        if (!file_exists($file)) return self::result(1, '文件不存在');
        $bucketName = self::getBucketName();
        $ossClient = self::getOssClient(true);
        try {
            $result = $ossClient->uploadFile($bucketName, $object, $file);
        } catch (OssException $e) {
            return self::result(1, '上传失败');
        }
        return self::result(0, '上传成功');
    }

    /**
     * 获取图片临时链接
     * $height 获取大小默认高度400
     * $is_internal 默认外网获取
     * $use_cdn 默认不走cdn
     * @param $object
     * @param int $height
     * @param bool $is_internal
     * @param bool $use_cdn
     * @return mixed|string
     */
    public static function picTokenUrl($object, $height = 400, $is_internal = false, $use_cdn = false)
    {
        if (!OssUtil::validateObject($object)) return '';
        $ossClient = self::getOssClient($is_internal);
        $bucketName = self::getBucketName();

        $timeout = 3600;
        $options = [];
        if ($height != 0) {
            $options = array(
                //OssClient::OSS_PROCESS => "image/resize,m_lfit,h_100,w_100",
                OssClient::OSS_PROCESS => "image/resize,m_lfit,h_{$height}",
            );
        }
        $signedUrl = $ossClient->signUrl($bucketName, $object, $timeout, "GET", $options);

        if ($use_cdn) {
            return str_replace(self::aliOssHost, self::jmdOssLoanHost, $signedUrl);
        }
        return $signedUrl;
    }

    /**
     * 文件下载
     * $object oss上对应路径加文件名
     * $localfile 本地保存路径加文件名
     * $height 大小默认400
     * @param $object
     * @param $localfile
     * @param $height
     * @return array
     */
    public static function getFile($object, $localfile, $height = 400)
    {
        if (!OssUtil::validateObject($object)) return self::result(1, '路径不能以/开头');
        $bucketName = self::getBucketName();
        $ossClient = self::getOssClient();

        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile,
            OssClient::OSS_PROCESS => "image/resize,m_lfit,h_{$height}",
        );

        try {
            $ossClient->getObject($bucketName, $object, $options);
        } catch (OssException $e) {
            return self::result(1, '文件下载失败');
        }
        return self::result(0, '文件下载成功');
    }

    /**
     * 拷贝object
     * $from_object oss上原对应路径加文件名
     * $to_object oss上新对应路径加文件名
     * @param $from_object
     * @param $to_object
     * @return array
     */
    public static function copyObject($from_object, $to_object)
    {
        $bucket = self::getBucketName();
        $ossClient = self::getOssClient(true);
        $from_bucket = $bucket;
        $to_bucket = $bucket;
        try {
            $ossClient->copyObject($from_bucket, $from_object, $to_bucket, $to_object);
        } catch (OssException $e) {
            return self::result(1, '复制失败');
        }
        return self::result(0, '复制成功');
    }

    /**
     * 删除object
     *
     * @param OssClient $ossClient OSSClient实例
     * @param string $bucket bucket名字
     * @return null
     */
    public static function deleteObject($object)
    {
        $bucket = self::getBucketName();
        $ossClient = self::getOssClient(true);
        try {
            $ossClient->deleteObject($bucket, $object);
        } catch (OssException $e) {
            return self::result(1, '删除失败');
        }
        return self::result(0, '删除成功');
    }

    /**
     * 批量删除object
     *
     * @param OssClient $ossClient OSSClient实例
     * @param string $bucket bucket名字
     * @return null
     */
    public static function deleteObjects($objects)
    {
        $bucket = self::getBucketName();
        $ossClient = self::getOssClient(true);
        try {
            $ossClient->deleteObjects($bucket, $objects);
        } catch (OssException $e) {
            return self::result(1, '删除失败');
        }
        return self::result(0, '删除成功');
    }

    public static function result($ret = 0, $msg = '', $data = [])
    {
        return [
            'ret' => $ret,
            'msg' => $msg,
            'data' => $data
        ];
    }

}