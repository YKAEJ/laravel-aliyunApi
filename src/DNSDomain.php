<?php

namespace Ykaej\Aliyun;

/**
 * Class GetListDomain
 * @package App\Aliyun
 */
class DNSDomain extends AliyunApiPublic
{
    protected $pageNumber = 1; //当前页数
    protected $pageSize = 500;

    public function __construct()
    {
        //设置dns解析公用 api url
        $this->setApiUrl('alidns.aliyuncs.com');
    }

    /**
     * 获取阿里云全部分页信息
     * @param $domain string 域名
     * @return array|string
     */
    public function aliyunDnsList($domain)
    {
        $apiParams = [
            'Action' => 'DescribeDomainRecords',
            'DomainName' => $domain,
            'PageNumber' => $this->pageNumber,
            'PageSize' => $this->pageSize,
        ];
        $domains[] = $this->aliyunDealApi($apiParams);
        if (isset($domains[0]['Code'])){
            return $this->callbackMessage($domains[0]['Message']);
        }
        //页数
        $page_count = ceil($domains[0]['TotalCount'] / $domains[0]['PageSize']);
        if ($page_count >= 2){
            for ($i=2;$i<=$page_count;$i++){
                $apiParams = [
                    'Action' => 'DescribeDomainRecords',
                    'DomainName' => $domain,
                    'PageNumber' => $i,
                    'PageSize' => $this->pageSize,
                ];
                $domains[] = $this->aliyunDealApi($apiParams);
            }
        }
        $result = [];
        for ($i=0;$i<$page_count;$i++) {
            foreach ($domains[$i]['DomainRecords']['Record'] as $key => $domain) {
                $result[] = $domain;
            }
        }
        return $result;

    }

    /**
     * 修改状态
     * @param $record_id string 唯一id值
     * @param $status string 状态 Enable 开 Disable 关
     * @return array|string
     */
    public function aliyunDnsEditStatus($record_id, $status)
    {
        $status = strtoupper($status)==strtoupper('Enable')?'Disable':'Enable';
        $apiParams = [
            'Action' => 'SetDomainRecordStatus',
            'RecordId' => $record_id,
            'Status' => $status
        ];

        $domains = $this->aliyunDealApi($apiParams);
        if (isset($domains['Code'])){
            return $this->callbackMessage($domains[0]['Message']);
        }
        return $domains;
    }


    /**
     * 创建解析
     * @param string $domainName 主域名
     * @param string $rr 主机记录
     * @param string $value 记录值
     * @param string $type 解析记录类型
     * @param int $ttl 生存时间
     * @param string $line 解析线路
     * @return array|string
     */
    public function aliyunDnsCreate($domainName, $rr, $value, $type='A', $ttl=600, $line='default')
    {
        $apiParams = [
            'Action' => 'AddDomainRecord',
            'DomainName' => $domainName,
            'RR' => $rr,
            'Value' => $value,
            'Type' => $type,
            'TTL' => $ttl,
            'Line' => $line
        ];
        //api请求
        $domains = $this->aliyunDealApi($apiParams);
        if (isset($domains['Code'])){
            return $this->callbackMessage($domains[0]['Message']);
        }
        return $domains;

    }

    /**
     * 删除解析
     * @param $record_id
     * @return array|string
     */
    public function aliyunDnsDelete($record_id)
    {
        $apiParams = [
            'Action' => 'DeleteDomainRecord',
            'RecordId' => $record_id
        ];
        $domains = $this->aliyunDealApi($apiParams);
        if (isset($domains['Code'])){
            return $this->callbackMessage($domains[0]['Message']);
        }
        return $domains;

    }

    /**
     * 修改解析
     * @param string $recordId
     * @param string $rr 主机记录
     * @param string $value 记录值
     * @param string $type 解析记录类型
     * @param int $ttl 生存时间
     * @param string $line 解析线路
     * @return array|string
     */
    public function aliyunDnsUpdate($recordId, $rr, $value, $type='A', $ttl=600, $line='default')
    {
        $apiParams = [
            'Action' => 'UpdateDomainRecord',
            'RecordId' => $recordId,
            'RR' => $rr,
            'Value' => $value,
            'Type' => $type,
            'TTL' => $ttl,
            'Line' => $line
        ];
        $domains = $this->aliyunDealApi($apiParams);
        if (isset($domains['Code'])){
            return $this->callbackMessage($domains[0]['Message']);
        }
        return $domains;

    }

    /**
     * @param $message
     * @return string
     */
    protected function callbackMessage($message)
    {
        return isset($message)?$message:'信息填写错误';
    }

}