<?php
/**
 * 发表评论
 *
 * @param thread  thread ID
 * @param parent  父评论 ID，可为空
 * @param message 评论内容
 * @param name    访客名字
 * @param email   访客邮箱
 * @param url     访客网址，可为空
 *
 * @author   fooleap <fooleap@gmail.com>
 * @version  2017-06-27 09:07:07
 * @link     https://github.com/fooleap/disqus-php-api
 *
 */
namespace Emojione;
require_once('init.php');

$curl_url = '/api/3.0/posts/create.json';
$author_name = $_POST['name'];
$author_email = $_POST['email'];
$author_url = $_POST['url'] == '' || $_POST['url'] == 'null' ? null : $_POST['url'];

if( $author_name == DISQUS_USERNAME && $author_email == DISQUS_EMAIL && strpos($session, 'session') !== false ){
    $author_name = null;
    $author_email = null;
    $author_url = null;
}

$post_message = $client->shortnameToUnicode($_POST['message']);

$post_data = array(
    'api_key' => DISQUS_PUBKEY,
    'thread' => $_POST['thread'],
    'parent' => $_POST['parent'],
    'message' => $post_message,
    'author_name' => $author_name,
    'author_email' => $author_email,
    'author_url' => $author_url
    //'ip_address' => $_SERVER["REMOTE_ADDR"]
);
$data = curl_post($curl_url, $post_data);

$output = $data -> code == 0 ? array(
    'code' => $data -> code,
    'thread' => $_POST['thread'],
    'response' => post_format($data -> response)
) : $data;

if ( $_POST['parent'] != '' && $data -> code == 0 ){
    $mail_query = array(
        'parent'=> $_POST['parent'],
        'id'=> $data -> response -> id,
        'link'=> $_POST['link'],
        'title'=> $_POST['title']
    );
    $mail = curl_init();
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $curl_opt = array(
        CURLOPT_URL => $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/sendemail.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $mail_query,
        CURLOPT_TIMEOUT => 1
    );
    curl_setopt_array($mail, $curl_opt);
    curl_exec($mail);
    curl_close($mail);
}
print_r(json_encode($output));
