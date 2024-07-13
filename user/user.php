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

    function getUser($conn, $uid, $returns = [])
    {
        $sel = empty($returns) ? "*" : implode(",", $returns);
        $stmt = $conn->prepare("SELECT $sel FROM users WHERE user_id = ?");
        $stmt->execute([$uid]);
        if ($stmt && $stmt->rowCount()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sel != '*') {
                $rd = [];
                foreach ($returns as $r) {
                    $rd[$r] = $user[$r];
                }
                if (count($rd) == 1) {
                    return $rd[0];
                }
                return $rd;
            }
            return $user;
        }
        return false;
    }

    function getProfileUri($profile)
    {
        $uri = "https://{$_SERVER['SERVER_NAME']}/profiles/" . basename($profile);
        if (!$profile || !file_exists($profile)) {
            $uri = "https://{$_SERVER['SERVER_NAME']}/connector/images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif";
        }
        return $uri;
    }

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
}
