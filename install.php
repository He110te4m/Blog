<?php
if (!file_exists('./despote/conf/services.php')) {
    die ('配置文件不存在');
}
if (!file_exists('./db/sql.sql')) {
    die('数据库文件缺失');
}

@$conf = require('./despote/conf/services.php');
if(!isset($conf['sql'])) {
    die('未开启数据库配置');
}

$db_conf = &$conf['sql'];
$host = isset($db_conf['host']) ? $db_conf['host'] : 'localhost';
$port = isset($db_conf['port']) ? $db_conf['port'] : '3306';
$user = isset($db_conf['use']) ? $db_conf['use'] : 'root';
$pass = isset($db_conf['pwd']) ? $db_conf['pwd'] : 'root';
$name = isset($db_conf['name']) ? $db_conf['name'] : 'blog';

try {
    $db = new PDO('mysql:dbname=' . $name . ';host=' . $host . ';port=' . $port, $user, $pass, []);
} catch (Exception $e) {
    die($e->getMessage());
}

$sql = file('./db/sql.sql');

foreach($sql as $item) {
    $db->exec($item);
}

echo '<h1>安装完成</h1>';

@unlink('./install.php');
