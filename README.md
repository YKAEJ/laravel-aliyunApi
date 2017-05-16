# laravel-aliyunApi
>laravel aliyun dns api , just a demo

# 安装
添加到 `composer.json` 当中，执行 `composer update`
>"ykaej/aliyun": "dev-master"

# For Laravel
添加到 `config/app.php` 的 `providers` 中
````
'providers' => [
    ...
    Ykaej\Aliyun\AliyunDnsProvider::class,
    ...
],
````

# 配置
在 `.env` 文件中添加一下内容
````
ALIYUN_ACCESS_KEYID=your_key
ALIYUN_ACCESS_SECRET=you_secret
````
# 使用
````
    use Ykaej\Aliyun\DNSDomain;
    
    // 依赖注入 或 使用 app('aliyun_dns')
    public function index(DNSDomain $domain)
    {
        $dns = app('aliyun_dns');
        //获取所有子域名
        $dns->aliyunDnsList('domain.com');
        // or 
        $domain->aliyunDnsList('domain.com');
        
        //添加一个子域名解析
        $dns->aliyunDnsCreate($domainName, $rr, $value, $type='A', $ttl=600, $line='default');
        // or 
        $domain->aliyunDnsCreate($domainName, $rr, $value, $type='A', $ttl=600, $line='default');
       
        //修改一个子域名解析
        $domain->aliyunDnsUpdate($recordId, $rr, $value, $type='A', $ttl=600, $line='default');
        
        //修改一个子域名解析状态
        $domain->aliyunDnsEditStatus($record_id, $status); //当前状态 status : 'Disable' or 'Enable'
        
        //删除一个子域名
        $domain->aliyunDnsDelete($record_id);
    }
````



