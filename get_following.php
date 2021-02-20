<?php

use Abraham\TwitterOAuth\TwitterOAuth;

require_once "vendor/autoload.php";

$options = getopt("u:dh");

if (isset($options['h'])) {
    echo "Get following(friends) screen_name list" . PHP_EOL;
    echo "usage: this.php -u {screen_name} (-d) (-h)" . PHP_EOL;
    echo " -u: required. twitter screen_name" . PHP_EOL;
    echo " -d: optional. output debug(progress) to stderr" . PHP_EOL;
    echo " -h: optional. show this message" . PHP_EOL;
    exit;
}

$target_screen_name = $options['u'] ?? die("require u option. `-u {screen_name}` ");
$GLOBALS['debug'] = isset($options['d']) ? true : false;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$content = createTwitterConnect()->get("account/verify_credentials");
echo "read from `{$target_screen_name}`. API call user as {$content->screen_name}" . PHP_EOL;

$list = readAllFollowersList($target_screen_name);

foreach ($list as $id) {
    echo $id . PHP_EOL;
}

if ($GLOBALS['debug']) error_log("done!!");

// =================

function readAllFollowersList(string $screen_name): array
{
    $all_id_list = [];
    foreach (readFollowersList($screen_name) as $id_list) {
        $all_id_list = array_merge($all_id_list, $id_list);
        if ($GLOBALS['debug'])  error_log(".");
        // sleep(1); // wait, if you want.
    }
    return $all_id_list;
}

function readFollowersList(string $screen_name): iterable
{
    $cursor = -1; // read from head.
    $connection = createTwitterConnect();
    do {
        if ($GLOBALS['debug']) error_log($cursor);
        $res = $connection->get("friends/list", [ // following.
            "cursor" => $cursor,
            "screen_name" => $screen_name,
            "skip_status" => "true",
            "include_user_entities" => "false",
            "count" => 200
        ]);
        yield array_map(function ($data) {
            return $data->screen_name;
        }, $res->users);
    } while ($cursor = $res->next_cursor_str);
}

function createTwitterConnect(): TwitterOAuth
{
    $TWITTER_CONSUMER_KEY = $_ENV['TWITTER_CONSUMER_KEY'] ?? die("missing TWITTER_CONSUMER_KEY in ENV");
    $TWITTER_CONSUMER_SECRET = $_ENV['TWITTER_CONSUMER_SECRET'] ?? die("missing TWITTER_CONSUMER_SECRET in ENV");
    $TWITTER_ACCESS_TOKEN = $_ENV['TWITTER_ACCESS_TOKEN'] ?? die("missing TWITTER_ACCESS_TOKEN in ENV");
    $TWITTER_ACCESS_TOKEN_SECRET = $_ENV['TWITTER_ACCESS_TOKEN_SECRET'] ?? die("missing TWITTER_ACCESS_TOKEN_SECRET in ENV");
    return new TwitterOAuth($TWITTER_CONSUMER_KEY, $TWITTER_CONSUMER_SECRET, $TWITTER_ACCESS_TOKEN, $TWITTER_ACCESS_TOKEN_SECRET);
}
