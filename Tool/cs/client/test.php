<?php
use cs\client\ApiClientFile;

include_once 'CurlClient.php';
define('LARAVEL_START', 'adsd');


$api_host = 'http://ip:port';//接口地址

$access_key = '';//ak
$secret_key = '';//sk
if ($_GET['fun'] == "blacklist") {//cs 黑名单查询接口
    $black_list = '/credit-scoring/users/id-check/v6/blacklist-check';
    $blackList = array(
        'idNumber' => '1271100410830002',
        'name' => 'SA\'DIYEH',
        'phoneNumber' => "6281357723387"
    );
} elseif ($_GET['fun'] == "simple") {//cs KTP身份验证
    $black_list = '/credit-scoring/users/id-check/v1/simple-identity-check';
//    $blackList = array(
//        'idNumber' => '3172062312790002',
//        'name' => 'FERIAL THUFLI CHANDRA',
//    );
    $blackList = array(
        'idNumber' => '5202040307970002',
        'name' => 'SUDJONO',
    );
} elseif ($_GET['fun'] == "dk_check") {//cs 近期收入推断
    $black_list = '/credit-scoring/users/v1/dk-check';
    $blackList = array(
        'phoneNumber' => '6281316130001',
        'bankAccountNumber' => '042201005925532',
    );
} elseif ($_GET['fun'] == "get_recharge_data") {//cs 充值信息汇总
    $black_list = '/credit-scoring/users/v1/get-recharge-data';
    $blackList = array(
        'phoneNumber' => '62895386139500',
    );

} elseif ($_GET['fun'] == "kk_identity_check") {//cs NIK和KK匹配性验证
    $black_list = '/credit-scoring/users/id-check/v2/kk-identity-check';
    $blackList = array(
        'idNumber' => '1101011202000003',
        'noKk' => "1101010110060115"
    );
} elseif ($_GET['fun'] == "phone_nik_check") {//cs 电话实名验证
    $black_list = '/credit-scoring/users/v1/phone-nik-check';
    $blackList = array(
        'phoneNumber' => '6289501881500',
        'idNumber' => "1101011202000003"
    );
} elseif ($_GET['fun'] == "get_phone_call_info") {//cs 电话可联性查询
    $black_list = '/credit-scoring/users/v1/get-phone-call-info';
    $blackList = array(
        'phoneNumber' => '62895386139500',
    );
} elseif ($_GET['fun'] == "ocr_ktp_check") {//cs 简版OCR(KTP)

    $black_list = '/credit-scoring/users/v1/ocr-ktp-check';
    $filePath = '1112010209920001.jpg';//文件路径
    $blackList = array(
        "ocrKtpImage" => new ApiClientFile($filePath, file_get_contents($filePath))
    );
} elseif ($_GET['fun'] == "tele_score") {//cs 电信分查询
    $black_list = '/credit-scoring/users/v1/tele-score';
    $blackList = array(
        'phoneNumber' => '62895386139500',
        'date' => '1588932432021'
    );
} elseif ($_GET['fun'] == "get_phone_online_days") {//cs 在网时长
    $black_list = '/credit-scoring/users/v1/get-phone-online-days';
    $blackList = array(
        'phoneNumber' => '62895386139500',
    );
}

/**
 * NOTE 请求如果没有任何响应，请查看client->requestError，如果提示SSL certificate : unable to get local issuer certificate,
 * 是本地https证书安装的有问题，请参考如下网站解决：
 * https://stackoverflow.com/questions/24611640/curl-60-ssl-certificate-unable-to-get-local-issuer-certificate
 */

$client = new cs\client\CurlClient($api_host, $access_key, $secret_key);

//json形式
$result = $client->request($black_list, $blackList, null);
//图片
$result = $client->request($black_list, null, $blackList);
echo $result;



