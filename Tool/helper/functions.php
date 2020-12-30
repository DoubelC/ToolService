<?php


/**
 * todo: 输出变量数据
 *
 * @param      $val 需要打印的值
 * @param  bool $die 是否中断执行
 *
 * @return array salt: 盐值， pwd：加密后的密码
 */
function p($val, $die = true)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';

    $die && die();
}

function dd($val, $die = true)
{
    var_dump($val);

    $die && die();
}


/**
 * todo: MD5加密密码
 *
 * @param  string $_password 明文密码
 * @param  string $_salt 盐值
 *
 * @return array salt: 盐值， pwd：加密后的密码
 */
function PasswordMd5($_password, $_salt = '')
{
    if (empty($_salt)) {
        $nRand = rand(1, 9990);
    } else {
        $nRand = $_salt;
    }

    $sPassword = md5(md5($_password) . $nRand);

    return ['password' => $sPassword, 'salt' => $nRand];
}


/**
 * todo: 获取缩略图文件名
 *
 * @param  string $_imgfilename 原图文件名
 * @param  array $_arrwh 宽高数组
 *
 * @return bool true or false
 */
function ThumbImageName($_imgfilename, $_arrwh)
{
    if (!IsArray($_arrwh)) {
        return $_imgfilename;
    } else {
        if (!IsNum($_arrwh[0], false, false) || !IsNum($_arrwh[1], false, false)) {
            return $_imgfilename;
        }
    }

    return str_replace(".jpg", ".jpg!{$_arrwh[0]}_{$_arrwh[1]}.jpg", $_imgfilename);
}

/**
 * todo: 创建随机字符串
 *
 * @param  int $len 生成的字符串位数
 * @param  string $format 生成的字符串类型.ALL:数字和大小写字母，CHAR：大小写字母，NUMBER：数字
 *
 * @return string
 */
function CreateRandNumber($len = 6, $format = 'ALL')
{
    $is_abc = $is_numer = 0;
    $password = $tmp = '';

    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }

    mt_srand((double)microtime() * 1000000 * getmypid());

    while (strlen($password) < $len) {
        $tmp = substr($chars, (mt_rand() % strlen($chars)), 1);
        if (($is_numer <> 1 && is_numeric($tmp) && $tmp > 0) || $format == 'CHAR') {
            $is_numer = 1;
        }
        if (($is_abc <> 1 && preg_match('/[a-zA-Z]/', $tmp)) || $format == 'NUMBER') {
            $is_abc = 1;
        }
        $password .= $tmp;
    }

    if ($is_numer <> 1 || $is_abc <> 1 || empty($password)) {
        $password = CreateRandNumber($len, $format);
    }

    return $password;
}

/**
 * todo: 格式化时间戳
 *
 * @param  integer $_timestamp 时间戳
 * @param  string $_format 格式化样式
 *
 * @return false|string
 */
function FmtTimestamp($_timestamp, $_format = 'Y-m-d H:i:s')
{
    if (!IsNum($_timestamp, false, false)) {
        return '--';
    } else {
        return date($_format, $_timestamp);
    }
}

/**
 * todo: 获取当前时间,带毫秒
 *
 * @param  string $format
 *
 * @return string
 */
function getCurrDateTime($format = 'YmdHis')
{
    $nMilliSecond = substr(getMillisecond(), -3, 3);
    $sDateTime = date($format) . $nMilliSecond;

    return $sDateTime;
}

/**
 * todo: 获取毫秒时间戳
 * @return float
 */
function getMillisecond()
{

    list($s1, $s2) = explode(' ', microtime());

    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);

}

/**
 * todo: 格式化模型错误信息到字符串数组
 *
 * @param  array $error 模型错误数组
 *
 * @return array
 */
function FmtModelErrorToStringArray($error)
{
    $ErrorList = [];

    foreach ($error as $key => $val) {
        if (IsArray($val)) {
            foreach ($val as $k => $v) {
                array_push($ErrorList, $v);
            }
        } else {
            array_push($ErrorList, $val);
        }
    }

    return $ErrorList;
}

/**
 * todo: 格式化int成float
 *
 * @param  int $_nunber 需要格式化的数字
 * @param  int $_bits 小数位数
 *
 * @return string
 */
function FmtIntToFloat($_nunber, $_bits = 2)
{
    return sprintf("%.{$_bits}f", $_nunber);
}

/**
 * UTF8字符串截取函数
 *
 * @param  string $str
 * @param  int $len
 * @param  int $offset
 *
 * @return string
 */
function utf8sub($str, $len, $offset = 0)
{
    if ($len < 0) {
        return '';
    }
    $res = '';
    // $offset = 0;
    $chars = 0;
    $count = 0;
    $length = strlen($str);//待截取字符串的字节数

    while ($chars < $len && $offset < $length) {
        $high = decbin(ord(substr($str, $offset, 1)));//先截取客串的一个字节，substr按字节进行截取
        //重要突破，已经能够判断高位字节
        if (strlen($high) < 8) {//英文字符ascii编码长度为7，通过长度小于8来判断
            $count = 1;
            // echo 'hello,I am in','<br>';
        } elseif (substr($high, 0, 3) == '110') {
            $count = 2;    //取两个字节的长度
        } elseif (substr($high, 0, 4) == '1110') {
            $count = 3;    //取三个字节的长度
        } elseif (substr($high, 0, 5) == '11110') {
            $count = 4;

        } elseif (substr($high, 0, 6) == '111110') {
            $count = 5;
        } elseif (substr($high, 0, 7) == '1111110') {
            $count = 6;
        }
        $res .= substr($str, $offset, $count);
        $chars += 1;
        $offset += $count;
    }

    return $res;
}

/**
 * todo: 检查是否是身份证号
 *
 * @param $number
 *
 * @return bool
 */
function IsIdCard($number)
{
    if (empty($number)) {
        return false;
    }

    $idCardLength = (strlen($number) + 0);

    if ($idCardLength !== 15 && $idCardLength !== 18) {
        return false;
    }

    // 转化为大写，如出现x
    $number = strtoupper($number);

    //加权因子
    $wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];

    //校验码串
    $ai = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

    //按顺序循环处理前17位
    $sigma = 0;
    for ($i = 0; $i < 17; $i++) {
        if (!IsNum($number{$i}, true, false) && $number{$i} !== 'X') {
            return false;
        }

        //提取前17位的其中一位，并将变量类型转为实数
        $b = (int)$number{$i};      //提取相应的加权因子
        $w = $wi[$i];     //把从身份证号码中提取的一位数字和加权因子相乘，并累加
        $sigma += $b * $w;
    }

    //计算序号
    $snumber = $sigma % 11;

    //按照序号从校验码串中提取相应的字符。
    $check_number = $ai[$snumber];

    if ($number{17} == $check_number) {
        return true;
    } else {
        return false;
    }
}

/**
 * todo: 从身份证号中获取性别
 *
 * @param  string $number 身份证号
 *
 * @return int 0: 女；1: 男；
 */
function IdCardGender($number)
{
    //根据身份证号，自动返回性别
    if (!IsIdCard($number)) {
        return '';
    }

    $nGenderNumber = (int)substr($number, 16, 1);

    return (($nGenderNumber % 2) === 0) ? 0 : 1;
}

/**
 * todo: 从身份证号中获取年龄
 *
 * @param  string $number 身份证号
 *
 * @return int
 */
function IdCardAge($number)
{
    //根据身份证号，自动返回性别
    if (!IsIdCard($number)) {
        return 0;
    }

    $nYear = (int)substr($number, 6, 4);
    $nCurrYear = date("Y", time());

    return (($nCurrYear + 0) - ($nYear + 0));
}

/**
 * todo: 从身份证号中获取出生日期
 *
 * @param  string $number 身份证号
 *
 * @return array
 */
function IdCardBirthday($number)
{
    //根据身份证号，自动返回性别
    if (!IsIdCard($number)) {
        $nYear = '1970';
        $nMonth = '01';
        $nDay = '01';
    } else {
        $nYear = (string)substr($number, 6, 4);
        $nMonth = (string)substr($number, 10, 2);
        $nDay = (string)substr($number, 12, 2);
    }

    //$nYear = (string)substr($number, 6, 4);
    //$nMonth = (string)substr($number, 10, 2);
    //$nDay = (string)substr($number, 2, 2);

    return ['year' => $nYear, 'month' => $nMonth, 'day' => $nDay];
}

/**
 * todo: 手机号脱敏
 *
 * @param  string $mobile 手机号
 *
 * @return string
 */
function ConfoundingMobile($mobile)
{
    if (IsN($mobile)) {
        return $mobile;
    } else {
        return substr_replace($mobile, '****', 3, 4);
    }
}

/**
 * todo: 身份证号脱敏
 *
 * @param  string $idcard 身份证号
 *
 * @return string
 */
function ConfoundingIdcard($idcard, $len = 4)
{
    if (IsN($idcard)) {
        return $idcard;
    } else {
        return substr_replace($idcard, str_repeat('*', $len), 4, 10);
    }
}

/**
 * todo: 身份证号脱敏2(后4位打码)
 *
 * @param  string $idcard 身份证号
 *
 * @return string
 */
function ConfoundingIdcard2($idcard)
{
    if (IsN($idcard)) {
        return $idcard;
    } else {
        return substr_replace($idcard, '****', -4);
    }
}

/**
 * todo: 身份证号脱敏3(后8位打码)
 *
 * @param  string $idcard 身份证号
 *
 * @return string
 */
function ConfoundingIdcard3($idcard)
{
    if (IsN($idcard)) {
        return $idcard;
    } else {
        return substr_replace($idcard, '********', -8);
    }
}

/**
 * todo: 真实姓名脱敏
 *
 * @param  string $real_name 真实姓名
 *
 * @return string
 */
function ConfoundingRealName($real_name)
{
    if (IsN($real_name)) {
        return $real_name;
    } else {
        $len = mb_strlen($real_name, 'utf-8');

        return substr_replace($real_name, str_repeat('*', $len - 1), 3);
    }
}

/**
 * todo: 真实姓名脱敏2(屏蔽姓，保留名)
 *
 * @param  string $real_name 真实姓名
 *
 * @return string
 */
function ConfoundingRealName2($real_name)
{
    if (IsN($real_name)) {
        return $real_name;
    } else {
        return substr_replace($real_name, '*', 0, 3);
    }
}

/**
 * todo: 微信号脱敏
 *
 * @param  string $wechat 微信号号
 *
 * @return string
 */
function ConfoundingWechat($wechat)
{
    if (IsN($wechat)) {
        return $wechat;
    } else {
        $frist = substr($wechat, 0, 2);
        $delete_last = substr($wechat, -2);

        return $frist . '****' . $delete_last;
    }
}

/**
 * todo: 银行卡号脱敏
 *
 * @param  string $card_no 银行卡号
 *
 * @return string
 */
function ConfoundingBankCard($card_no)
{
    if (IsN($card_no)) {
        return $card_no;
    } else {
        $frist = substr($card_no, 0, 5);
        $delete_last = substr($card_no, -4);

        return $frist . ' **** **** ' . $delete_last;
    }
}


/**
 * todo: 银行卡号脱敏
 *
 * @param  string $card_no 银行卡号
 *
 * @return string
 */
function ConfoundingLastBankCard($card_no)
{
    if (IsN($card_no)) {
        return $card_no;
    } else {
        $delete_last = substr($card_no, -4);

        return ' **** **** ' . $delete_last;
    }
}

#region 网络请求

/**
 * todo: get方式请求获取数据
 *
 * @param  string $url
 * @param  array $data
 * @param  int $timeout
 *
 * @return bool|string
 */
function HttpGet($url, $param = [], $timeout = 30)
{
    if ($url == "" || $timeout <= 0) {
        return false;
    }

    $url = $url . '?' . http_build_query($param);

    $curl = curl_init((string)$url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, (int)$timeout);

    return curl_exec($curl);
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param       $url
 * @param  array $param
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost($url, $param)
{
    if (!is_array($param)) {
        throw new Exception("参数必须为array");
    }

    $httph = curl_init($url);

    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $param);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($httph, CURLOPT_HEADER,1);
    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url
 * @param  array $param
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_Xiao($url, $param)
{
    if (empty($param)) {
        throw new Exception("缺少请求参数");
    }

    $HeadData = [
        "cache-control: no-cache",
        //'Content-Type: application/x-www-form-urlencoded',
    ];

    $httph = curl_init();

    curl_setopt($httph, CURLOPT_URL, $url);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($httph, CURLOPT_ENCODING, '');
    curl_setopt($httph, CURLOPT_MAXREDIRS, 10);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    //curl_setopt($httph, CURLOPT_POST, 1);
    curl_setopt($httph, CURLOPT_POSTFIELDS, $param);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 运营商认证模拟post请求，带header头参数
 *
 * @param  string $url
 * @param  array $param
 * @param  array $head_data
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_Yous($url, $param, $HeadData = [])
{
    if (empty($param) || empty($HeadData)) {
        throw new Exception("参数必须为json");
    }

    $httph = curl_init($url);

    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $param);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url
 * @param  array $param
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_LieBao($url, $param)
{
    if (empty($param)) {
        throw new Exception("缺少请求参数");
    }

    $HeadData = [
        "cache-control: no-cache",
        'Content-Type: application/x-www-form-urlencoded',
    ];

    $sParam = 'source=' . $param['source'] . '&data=' . $param['data'];
    $httph = curl_init();

    curl_setopt($httph, CURLOPT_URL, $url);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($httph, CURLOPT_ENCODING, '');
    curl_setopt($httph, CURLOPT_MAXREDIRS, 10);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    //curl_setopt($httph, CURLOPT_POST, 1);
    curl_setopt($httph, CURLOPT_POSTFIELDS, $sParam);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url 接口地址
 * @param  array $param 请求参数
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_CsAiMall($url, $param)
{
    if (!is_array($param)) {
        throw new Exception("参数必须为array");
    }
    if ($param) {
        $sPostData = json_encode($param);
    } else {
        $sPostData = '';
    }

    $HeadData = [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($sPostData),
    ];

    $httph = curl_init($url);

    //curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $sPostData);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url 接口地址
 * @param  array $param 请求参数
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_RongZiWang($url, $param)
{
    if (!is_array($param)) {
        throw new Exception("参数必须为array");
    }
    if ($param) {
        $sPostData = json_encode($param);
    } else {
        $sPostData = '';
    }

    $HeadData = [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($sPostData),
    ];

    if (!empty($head)) {
        if (is_array($head)) {
            if (array_key_exists('token', $head)) {
                array_push($HeadData, 'token:' . $head['token']);
            }
        }
    }

    $httph = curl_init($url);

    //curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $sPostData);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url 接口地址
 * @param  array $param 请求参数
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_ZhiZi($url, $param)
{
    if (empty($param)) {
        throw new Exception("参数不能为空");
    }

    $sPostData = json_encode($param);

    $HeadData = [
        'Content-Type: application/json',
        //'Content-Length: ' . strlen($sPostData),
    ];

    $httph = curl_init($url);

    //curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $sPostData);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url 接口地址
 * @param  array $param 请求参数
 *
 * @return mixed
 * @throws Exception
 */
function HttpPost_CaiNiao($url, $param)
{
    $httph = curl_init();

    curl_setopt($httph, CURLOPT_URL, $url);
    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($httph, CURLOPT_MAXREDIRS, 10);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_POSTFIELDS, $param);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为数组
 *
 * @param  string $url 接口地址
 * @param  array $param 请求参数
 * @param  null|array $head head参数
 *
 * @return mixed
 * @throws Exception
 */
function HttpPostJson($url, $param, $head = null)
{
    if (!is_array($param)) {
        throw new Exception("参数必须为array");
    }
    if ($param) {
        $sPostData = json_encode($param);
    } else {
        $sPostData = '';
    }

    $HeadData = [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($sPostData),
    ];

    if (!empty($head)) {
        if (is_array($head)) {
            if (array_key_exists('token', $head)) {
                array_push($HeadData, 'token:' . $head['token']);
            }
        }
    }

    $httph = curl_init($url);

    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_TIMEOUT, 30);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $sPostData);
    curl_setopt($httph, CURLOPT_HTTPHEADER, $HeadData);

    $rst = curl_exec($httph);
    curl_close($httph);

    return $rst;
}

/**
 * todo: 模拟post表单提交数据，$param为xml格式
 */
function HttpPost_QuickPay($url, $post = '')
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    //         curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $res = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $res; // 返回数据，json格式
}

#endregion 网络请求

//region 返回结果数据结构


/**
 * todo: 返回正确提示信息
 *
 * @param  string $_str 返回状态提示内容
 * @param  array $_data
 * @param  int $code 返回的错误码
 *
 * @return array error：0正确，info：提示信息
 */
function ReturnCorrect($_str = '', $_data = [], $code = 200)
{
    return ["error" => false, "info" => $_str, "data" => $_data, "code" => $code];
}

/**
 * todo: 返回错误提示信息
 *
 * @param  string $_str 返回状态提示内容
 * @param  array $_data 返回数据内容
 * @param  int $code 返回的错误码
 *
 * @return array error：0正确，info：提示信息
 */
function ReturnError($_str = '', $_data = [], $code = 1)
{
    return ["error" => true, "code" => $code, "info" => $_str, "data" => $_data];
}

/**
 * todo: ajax返回数据
 *
 * @param  array $_data 返回数据内容
 */
function AjaxReturn($_data = [])
{
    if (!array_key_exists('code', $_data)) {
        if ($_data['error'] === false) {
            $_data['code'] = 0;
        } else {
            $_data['code'] = '';
        }
    }

    if (!array_key_exists('data', $_data)) {
        $_data['data'] = '';
    } else {
        if (empty($_data['data'])) {
            $_data['data'] = '';
        }
    }

    $ResultData = [
        'code' => $_data['code'],
        'msg' => $_data['info'],
        'data' => $_data['data'],
    ];

    exit(json_encode($ResultData));
}

/**
 * todo: ajax返回错误提示信息
 *
 * @param  string $_str 返回状态提示内容
 * @param  array|string $_data 返回数据内容
 * @param  int $_code 返回的错误码
 *
 * @return void error：true错误，info：提示信息
 */
function AjaxReturnError($_str = '', $_data = '', $_code = 1)
{
    $arrResult = ["code" => $_code, "msg" => $_str, "data" => $_data];
    exit(json_encode($arrResult));
}

/**
 * todo: ajax返回正确提示信息
 *
 * @param  string $_str 返回状态提示内容
 * @param  array|string $_data 返回数据内容
 *
 * @return void error：false正确，info：提示信息
 */
function AjaxReturnCorrect($_str = '', $_data = '')
{
    $arrResult = ["code" => 200, "msg" => $_str, "data" => $_data];

    exit(json_encode($arrResult));
}

/**
 * todo: ajax返回图片上传结果提示信息
 *
 * @param  array $_msg 返回状态提示内容
 * @param  array $data 返回的数据
 *
 * @return void error 为空则表示没有错误
 */
function AjaxUploadReturn($_msg = [], $data = [])
{
    $arrResult = ["error" => $_msg, 'data' => $data];

    exit(json_encode($arrResult));
}

//endregion 返回结果数据结构

//region 检查函数
/**
 * 检查变量是否为空
 *
 * @param  string $_str 字符串
 *
 * @return bool true or false
 */
function IsN($_str)
{
    $sType = gettype($_str);

    if ($sType == "string") {
        if ($_str === "0") {
            return false;
        }
    }

    if ($sType == "integer") {
        if ($_str === 0) {
            return false;
        }
    }

    return empty($_str);
}

/**
 * 检查是否是数字
 *
 * @param  string $_str 字符串
 * @param  bool $_allow_zero true：允许0值，false：不允许0值
 * @param  bool $_allow_minus true:允许负数，false不允许负数
 *
 * @return array
 */
function IsNum($_str, $_allow_zero = true, $_allow_minus = true)
{
    //检查0值和空值的情况
    if ($_allow_zero) {
        //允许0
        if (IsN($_str)) {
            return false;
        }
    } else {
        //不允许0
        if (empty($_str)) {
            return false;
        }
    }

    //检查是否数字
    if (!is_numeric($_str)) {
        return false;
    } else {
        //判断是否允许负数
        if (!$_allow_minus) {
            if ($_str < 0) {
                return false;
            }
        }

        return true;
    }
}

/**
 * todo:检查是否是合法的浮点数
 *
 * @param  string $_str 字符串
 * @param  int $_digits 比较的小数点位数
 * @param  bool $_allow_zero true：允许0值，false：不允许0值
 * @param  bool $_allow_minus true:允许负数，false不允许负数
 *
 * @return bool
 */
function IsFloat($_str, $_digits = 2, $_allow_zero = true, $_allow_minus = true)
{
    if (IsN($_str)) {
        return false;
    } else {
        if (!IsNum($_str)) {
            return false;
        }
    }

    $Result = bccomp((string)$_str, "0", $_digits);

    if ($_allow_zero) {
        if ($_allow_minus) {
            return true;
        } else {
            if ($Result < 0) {
                return false;
            } else {
                return true;
            }
        }
    } else {
        if ($Result <= 0) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * todo: 检查是否是数组
 *
 * @param      $_arr
 * @param  bool $_allow_null
 *
 * @return bool
 */
function IsArray($_arr, $_allow_null = false)
{
    if ($_allow_null === false) {
        if (!is_array($_arr) || empty($_arr)) {
            return false;
        }
    } else {
        if (!is_array($_arr)) {
            return false;
        }
    }

    return true;
}

/**
 * todo: 检查日期字符串
 *
 * @param  string $str 日期字符串
 * @param  string $format 日期格式
 *
 * @return bool
 */
function IsDatetime($str, $format = "Y-m-d H:i:s")
{
    $unixTime = strtotime($str);
    $checkDate = date($format, $unixTime);

    if ($checkDate == $str) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证手机号是否正确
 *
 * @param  string $mobile 手机号
 *
 * @return bool true or false
 */
function IsMobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }

    return preg_match('#^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57]|19[89]|16[6])[0-9]{8}$#', $mobile) ? true :
        false;
}

/**
 * todo: 判断是否json字符串
 *
 * @param $string
 *
 * @return bool
 */
function IsJson($string)
{
    json_decode($string);

    return (json_last_error() == JSON_ERROR_NONE);
}


/**
 * todo: 数据格式化成银行格式
 *
 * @param $int
 *
 * @return int
 */
function FeeHandle($int)
{
    if (is_numeric($int)) {
        $int = preg_replace('/(?<=[0-9])(?=(?:[0-9]{3})+(?![0-9]))/', ',', $int);
    }
    return $int;
}

//endregion 检查函数

/**
 * 根据图片路径转换成base64数据信息
 *
 * @param $image_file
 *
 * @return string
 */
function base64EncodeImage($image_file)
{
    $image_data = file_get_contents($image_file);
    $base64_image = chunk_split(base64_encode($image_data));

    return $base64_image;
}


/*
 * @todo 消息时间格式化
 * $time 时间戳
 * */
function timeToChzh($time)
{
    $t = time();
    $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));//当天的开始时间
    $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));//当天的结束时间
    $monthstart = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));//当月开始时间
    $monthend = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));//当月结束时间
    //昨天起至时间
    $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('y'));
    $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('y')) - 1;

    //今天时间
    if ($time >= $start && $time <= $end) {
        return '今天' . date('H:i', $time);
    }
    //昨天
    if ($time >= $beginYesterday && $time <= $endYesterday) {
        return '昨天' . date('H:i', $time);
    }
    //周几
    if ($time >= $monthstart && $time <= $monthend) {
        return "周" . mb_substr("日一二三四五六", date("w", $time), 1, "utf-8") . date('H:i', $time);
    }

    //大于1年
    if ((time() - $time) > 3600 * 24 * 365) {
        return date('Y年m月d日H:i', $time);
    }

    return date('m-d', $time);
}

/**
 * @param  string $birthday
 *
 * @return string|number
 * @uses 根据生日计算年龄，年龄的格式是：2016-09-23或者20160923
 */
function calcAge($birthday)
{
    $iage = 0;
    if (!empty($birthday)) {
        $year = date('Y', strtotime($birthday));
        $month = date('m', strtotime($birthday));
        $day = date('d', strtotime($birthday));

        $now_year = date('Y');
        $now_month = date('m');
        $now_day = date('d');

        if ($now_year > $year) {
            $iage = $now_year - $year - 1;
            if ($now_month > $month) {
                $iage++;
            } elseif ($now_month == $month) {
                if ($now_day >= $day) {
                    $iage++;
                }
            }
        }
    }

    return $iage;
}

/**
 *  计算两个时间相差的天数，不满1为1，注意ceil函数
 *
 * @param  int $ntime 当前时间
 * @param  int $ctime 减少的时间
 *
 * @return    int
 */
function subTime($ntime, $ctime)
{
    $dayst = 3600 * 24;
    $cday = ceil(($ntime - $ctime) / $dayst);

    return $cday;
}

/**
 * todo:获取阿里云oss缩略图
 * demo:http://image-demo.oss-cn-hangzhou.aliyuncs.com/example.jpg?x-oss-process=image/resize,m_lfit,h_200,w_200
 * @param $url
 * @param $height
 * @param $width
 * @return string
 */
function getOssThumbnail($url, $height, $width)
{
    if (IsN($url)) {
        return $url;
    }

    if (!IsNum($height, false, false)) {
        return $url;
    }

    if (!IsNum($width, false, false)) {
        return $url;
    }

    if (strpos($url, 'oss') === false) {
        return $url;
    }

    return $url . '?x-oss-process=image/resize,m_lfit,h_' . $height . ',w_' . $width;
}

/**
 * 获取当月天数
 * @param $date
 * @param string $rtype 1天数 2具体日期数组
 * @return array|string
 */

function get_days($date, $rtype = '1')
{
    $r = array();
    $tem = explode('-', $date);    //切割日期 得到年份和月份
    $year = $tem['0'];
    $month = $tem['1'];
    if (in_array($month, array('1', '3', '5', '7', '8', '01', '03', '05', '07', '08', '10', '12'))) {
        // $text = $year.'年的'.$month.'月有31天';
        $text = '31';
    } elseif ($month == 2) {
        if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0))    //判断是否是闰年
        {
            // $text = $year.'年的'.$month.'月有29天';
            $text = '29';
        } else {
            // $text = $year.'年的'.$month.'月有28天';
            $text = '28';
        }
    } else {
        // $text = $year.'年的'.$month.'月有30天';
        $text = '30';
    }
    if ($rtype == '2') {
        for ($i = 1; $i <= $text; $i++) {
            if ($i < 10) {
                $r[] = $year . "-" . $month . "-0" . $i;
            } else {
                $r[] = $year . "-" . $month . "-" . $i;
            }

        }
    } else {
        $r = $text;
    }
    return $r;
}

