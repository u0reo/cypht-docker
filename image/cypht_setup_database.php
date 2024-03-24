<?php
$connected = false;
$session_type = $_ENV['CYPHT_SESSION_TYPE'];
$auth_type = $_ENV['CYPHT_AUTH_TYPE'];
$user_config_type = $_ENV['CYPHT_USER_CONFIG_TYPE'];
$db_conn_type = $_ENV['CYPHT_DB_CONNECTION_TYPE'];
$db_host = $_ENV['CYPHT_DB_HOST'];
$db_socket = $_ENV['CYPHT_DB_SOCKET'];
$db_name = $_ENV['CYPHT_DB_NAME'];
$db_user = $_ENV['CYPHT_DB_USER'];
$db_pass = $_ENV['CYPHT_DB_PASS'];
$db_driver = $_ENV['CYPHT_DB_DRIVER'];

while (!$connected) {
    $dsn = '';
    if ($db_driver == 'sqlite') {
        $dsn = sprintf('%s:%s', $db_driver, $db_socket);
    }
    if ($db_conn_type == 'socket') {
        $dsn = sprintf('%s:unix_socket=%s;dbname=%s', $db_driver, $db_socket, $db_name);
    }
    else {
        if ($db_port) {
            $dsn = sprintf('%s:host=%s;port=%s;dbname=%s', $db_driver, $db_host, $db_port, $db_name);
        }
        else {
            $dsn = sprintf('%s:host=%s;dbname=%s', $db_driver, $db_host, $db_name);
        }
    }
    try {
        $conn = new PDO($dsn, $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        printf("Database connection successful ...\n");
        $connected = true;
    } catch (PDOException $e) {
        error_log('Waiting for database connection ... (' . $e->getMessage() . ')');
        sleep(1);
    }
}
if ($session_type == 'DB')  {
    if ($db_driver == 'mysql') {
        $stmt = "CREATE TABLE IF NOT EXISTS hm_user_session (hm_id varchar(250), data longblob, date timestamp, primary key (hm_id));";
    } elseif ($db_driver == 'pgsql') {
        $stmt = "CREATE TABLE IF NOT EXISTS hm_user_session (hm_id varchar(250) primary key not null, data text, date timestamp);";
    }
    printf("Creating database table hm_user_session ...\n");
    $conn->exec($stmt);
}
if ($auth_type == 'DB')  {
    if ($db_driver == 'mysql') {
        $stmt = "CREATE TABLE IF NOT EXISTS hm_user (username varchar(250), hash varchar(250), primary key (username));";
    } elseif ($db_driver == 'pgsql') {
        $stmt = "CREATE TABLE IF NOT EXISTS hm_user (username varchar(255) primary key not null, hash varchar(255));";
    }
    printf("Creating database table hm_user ...\n");
    $conn->exec($stmt);
}
if ($user_config_type == 'DB')  {
    if ($db_driver == 'mysql') {
        $stmt = "CREATE TABLE IF NOT EXISTS hm_user_settings(username varchar(250), settings longblob, primary key (username));";
    } elseif ($db_driver == 'pgsql') {
        $stmt = "CREATE TABLE IF NOT EXISTS hm_user_settings (username varchar(250) primary key not null, settings text);";
    }
    printf("Creating database table hm_user_settings ...\n");
    $conn->exec($stmt);
}
