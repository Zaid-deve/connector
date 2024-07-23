<?php

class User
{
    public $loginUri;

    function __construct($redirect = false)
    {
        $this->loginUri = "https://{$_SERVER['SERVER_NAME']}/connector/user/signin.php";
        $this->session_on();
        if (!$this->getUserId() && $redirect) {
            header("Location:{$this->loginUri}");
            die();
        }
    }

    function session_on()
    {
        if (!session_id()) {
            session_start();
        }
    }

    function getUserId()
    {
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
    }

    function setUserId($id)
    {
        $_SESSION['user_id'] = $id;
    }

    function getUser($conn, $target, $returns = [])
    {
        $sel = empty($returns) ? "*" : implode(",", $returns);
        $stmt = $conn->prepare("SELECT $sel FROM users WHERE user_id = ? || user_name = ?");
        $stmt->execute([$target, $target]);

        if ($stmt && $stmt->rowCount()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sel == '*') {
                return $user;
            } else {
                $rt = [];
                foreach ($returns as $r) {
                    $rt[$r] = $user[$r];
                }
                if (count($rt) == 1) {
                    return array_shift($rt);
                }
                return $rt;
            }
        }
        return false;
    }

    // function getProfileUri($profile)
    // {
    //     $rootUri = $_SERVER['DOCUMENT_ROOT'] . '/connector/profiles/' . basename($profile);
    //     $uri = "https://{$_SERVER['SERVER_NAME']}/connector/profiles/" . basename($profile);
    //     if (!$profile || !file_exists($rootUri)) {
    //         $uri = "https://{$_SERVER['SERVER_NAME']}/connector/images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif";
    //     }
    //     return $uri;
    // }

    function isUserLogedIn($redirect = false)
    {
        if (!$this->getUserId()) {
            if ($redirect) {
                header($this->loginUri);
                die();
            }
            return;
        }
        return true;
    }

    static function getProfileUri($profile)
    {
        $rootUri = $_SERVER['DOCUMENT_ROOT'] . '/connector/profiles/' . basename($profile);
        $uri = "https://{$_SERVER['SERVER_NAME']}/connector/profiles/" . basename($profile);
        if (!$profile || !file_exists($rootUri)) {
            $uri = "https://{$_SERVER['SERVER_NAME']}/connector/images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif";
        }
        return $uri;
    }
}
