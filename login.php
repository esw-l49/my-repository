<?php
header("Content-Type:application/json;CHARSET=utf8"); // 设置头字符为utf8解决中文编码问题
$post_data = json_decode(file_get_contents("php://input"), true);
// file_put_contents('postdata.text', $post_data);
if ($post_data) {
    // echo $post_data['data'];
}
$private_key = "-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDy7eSOrqqcwX0ZCdo7Qt8B5pLkyNR7stIt9Rjykc/HKOSWng2G
gr8KzX7wuPExYbkED33JbmurpTi6+sLyoWk2ao9ZnxppaTniObaH0vVir8ivxf4J
JsFGLVlDmZ8cDepWuWbAyLn+cehBggNh3uV2mBx2Sr2Ugex+aUVwI2KeVQIDAQAB
AoGBAJjVP9jgXKg4Nsrc3vYvkPuyIzJagwu7qe2N6H8baxwCRyXXE+1PLn/OXxF1
WRDXST4bvOhQVt7rGHDSOHnLQNPoj9B0GVJtCmKYxUQwftyPkTpGu1O+r77Al6WC
kEMT3Otht1Sa2Wunan9XqgLy2DObsYJhDGI6dO62bB28W59BAkEA/Mn1N7wEif/V
pBUti3MuvRrWXFzKFH0ECyTnev377AVF7NbuwEq2zFCYpmR+lMAwEiO57MQQmQ56
yENrflgM5QJBAPYD31p2NuLvRWuEGkkUQt8duPo7XHoKG+v8WtCBA+S5ejwpqCir
1quboOwA+Ppw1dHU3DcjrBBPv4Vl20pTpLECQHECEB/0a8sNlgKefRfkTDa58q6j
xKtYICCjROCU/rRKvzHb/Cv2urWoKjXoozX4nQTe99VC6XCjKnywtzNqRYkCQQCJ
ftiSIofChZ/y2z4loeFN+bqsgAjLXGMGnV/UMIcQimk6vr1xHbk46B4kSNbegbm/
MIxzdMhxxWBxGeBZxi3BAkAWpXHfsSJmaTa+xdQvrP/zs2UGb0MLEzSgLsMTYdfr
AITNss0qHejalrwlqKzyI8rdTY17bhTNu3xj8RR56DF4
-----END RSA PRIVATE KEY-----
";
$public_key = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDy7eSOrqqcwX0ZCdo7Qt8B5pLk
yNR7stIt9Rjykc/HKOSWng2Ggr8KzX7wuPExYbkED33JbmurpTi6+sLyoWk2ao9Z
nxppaTniObaH0vVir8ivxf4JJsFGLVlDmZ8cDepWuWbAyLn+cehBggNh3uV2mBx2
Sr2Ugex+aUVwI2KeVQIDAQAB
-----END PUBLIC KEY-----
";
if (isset($_POST['ask'])) {
    $key = [];
    // if (file_get_contents('./ssl/public.key')) {
    //     $key['publicKey'] = file_get_contents('./ssl/public.key');
    // } else {
    //     $configargs = array(
    //         "config" => "C:/wamp64/bin/php/php7.4.0/extras/ssl/openssl.cnf",
    //         'private_key_bits' => 2048,
    //         'default_md' => "sha256",
    //     );
    //     $key_pair = openssl_pkey_new($configargs);
    //     // 生成私钥
    //     openssl_pkey_export($key_pair, $privkey, null, $configargs);
    //     // 生产公钥
    //     $pubKey = openssl_pkey_get_details($key_pair)['key'];
    //     // $private_key = openssl_get_privatekey();
    //     file_put_contents('./ssl/private.key', $privkey);
    //     file_put_contents('./ssl/public.key', $pubKey);
    //     $key['publicKey'] = $pubKey;
    // }
    $key['publicKey'] = $public_key;
    echo json_encode($key);
    exit();
}
if (!isset($_POST['username']) && !isset($_POST['password'])) {
    $json['msg'] = "login first";
    echo $json;
    exit();
}
$conn = new mysqli("localhost:3308", "root", "", "mydb") or die("链接出错"); // 第一个参数是mysql服务器的端口地址，不是当前Apache的服务器端口
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}
// 前端将“+”号替换为%2B,现在将+号替换回来,确保密钥正确
$user = strtr(base64_decode($_REQUEST['username']), '%2B', '+');
$pwd = strtr(base64_decode($_REQUEST['password']), '%2B', '+');
// openssl_public_encrypt('testword', $msg, $public_key);
// $res = openssl_private_decrypt($msg, $username, $private_key, OPENSSL_PKCS1_PADDING);
$res = openssl_private_decrypt($user, $username, $private_key, OPENSSL_PKCS1_PADDING);
openssl_private_decrypt($pwd, $password, $private_key, OPENSSL_PKCS1_PADDING);

$sql = "SELECT ID FROM users WHERE username='$username' AND password='$password'";

// $sql = "INSERT INTO lessons values(3,'BJ','ME','BLOCK','QA','正式生产','2020-1-2','CNC','崩刃','duandao','原因','措施','经验教训','',2,'')";
$result = $conn->query($sql);
$rows = $result->num_rows;
if ($rows) {
    $json['msg'] = "登录成功";
    echo json_encode($json);
} else {
    $json['msg'] = $res ? "登录成功$username" : "$user";
    echo json_encode($json);
}

$conn->close();