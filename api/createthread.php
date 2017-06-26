<?php
/**
 * 创建 Thread
 *
 * @param url      页面完整链接
 * @param title    标题
 * @param sulg     slug
 * @param message  message
 *
 * @author   fooleap <fooleap@gmail.com>
 * @version  2017-06-27 09:07:07
 * @link     https://github.com/fooleap/disqus-php-api
 *
 */
namespace Emojione;
require_once('init.php');
$curl_url = '/api/3.0/threads/create.json';
$post_data = array(
    'api_key' => DISQUS_PUBKEY,
    'forum' => DISQUS_SHORTNAME,
    'message' => $_POST['message'],
    'slug' => $_POST['slug'],
    'title' => $_POST['title'],
    'url' => $_POST['url']
);
$data = curl_post($curl_url, $post_data);
print_r(json_encode($data)); 
