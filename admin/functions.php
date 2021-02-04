<?php
if(!defined('AJAX') && !defined('VAR4')) {
    die('Security');
}

function sessionStart($app) {
    $session_name = $app['name'];
    $secure = false;
    $httponly = true;
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    session_name($session_name);
    session_start();
    session_regenerate_id();
}

function checkBruteForce($user_id, $DB_con) {
    $fiveMinutesAgo = date("Y-m-d H:i:s", strtotime(" -5 minutes"));
    if ($stmt = $DB_con->prepare("SELECT date FROM login_attempts WHERE userId=:userId AND status=:status AND verify=:verify AND date > :date"))
    {
        $stmt->execute(array(":userId"=>$user_id,":status"=>"0",":verify"=>"0",":date"=>$fiveMinutesAgo));
        if($stmt->rowCount() > 3) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function loginCheck($DB_con) {
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if ($stmt = $DB_con->prepare("SELECT password FROM users WHERE id = :userId LIMIT 1")) {
            $stmt->bindparam(":userId",$user_id);
            $stmt->execute();
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1) {
                $login_check = hash('sha512', $userRow['password'] . $user_browser.$ip_address);
                if (hash_equals($login_check, $login_string) ){
                    return $user_id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function result($code, $message = '') {
    switch ($code) {
        case 400:
            $response = [
                'success' => false,
                'message' => $message ? $message : 'Bad request',
                'code' => $code
            ];
            break;
        case 401:
            $response = [
                'success' => false,
                'message' => $message ? $message : 'Unauthorized',
                'code' => $code
            ];
            break;
        case 500:
            $response = [
                'success' => false,
                'message' => $message ? $message : 'Internal server error',
                'code' => $code
            ];
            break;
        case 200:
            $response = [
                'success' => true,
                'message' => $message ? $message : 'Success',
                'code' => $code
            ];
            break;
        default:
            $response = [
                'success' => false,
                'message' => $message ? $message : 'Unknown error',
                'code' => $code
            ];
            break;
    }
    return json_encode($response);
}
