<?php
    require_once 'controllers/login_service.php';
    require_once 'controllers/access_service.php';

    $timeout = (int) (getenv('TIMEOUT') ?: 1000); // 5 minutes
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $_SESSION['brand_name'] = isset($_GET['BRAND_NAME']) ? $_GET['BRAND_NAME'] : 'Digital Home';




    // check if session exists
    if (!isset($_SESSION['user_id'])) {
        $action = $action == 'login_submit' ? $action : 'login';
    } else {
        // check for session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $timeout)) {
            // session timed out, destroy session and redirect to login
            session_unset();
            session_destroy();
            
            $action = 'login';
        } else {
            $_SESSION['login_time'] = time();

            if( array_search( $action ,['login', 'login_submit'] ) ){
                $action = 'dashboard';
            }

            // dashboard and logout actions are allowed for all logged in users.


            //check is user is valid and has admin access for dashboard
            $accessService = new AccessService($conn);
            $userPermissions = $accessService->verifyUserPermissions($_SESSION['user_id'], $action);
            print_r($userPermissions);
        }
    }

    switch ($action) {
        case 'login':
            $loginService = new LoginService($conn);
            $result['users'] = $loginService->getAllUsers();
            $loginError = '';
            break;

        case "login_submit":
            $loginService = new LoginService($conn);
            $result = $loginService->validateLogin($_POST['user_name'] ?? '', $_POST['password'] ?? '');

            if ($result['success']) {
                $_SESSION['user_id'] = $result['user']['user_id'];
                $_SESSION['user_name'] = $result['user']['user_name'];
                $_SESSION['user_admin'] = $result['user']['is_admin'];
                $_SESSION['login_time'] = time();

                $action = 'dashboard';
            } else {
                $loginError = $result['message'];
                $result['users'] = $loginService->getAllUsers();
                $action = 'login';
            }

            break;

        case 'logout':
            // clear session and redirect to login
            //session_unset();
            session_destroy();

            $action = 'login';
            break;

        case 'users':
            break;

        default:
            $action = 'dashboard';
    }

    
    //print_r($result);

    $_SESSION['action'] = $action;
    //print_r($_SESSION);
?>