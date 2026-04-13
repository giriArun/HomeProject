<?php
    require_once 'controllers/login_service.php';
    require_once 'controllers/access_service.php';
    require_once 'controllers/user_service.php';

    $timeout = (int) (getenv('TIMEOUT') ?: 1000); // 5 minutes
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $brand_name = isset($_GET['BRAND_NAME']) ? $_GET['BRAND_NAME'] : 'Digital Home';




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
            $action = $accessService->verifyUserPermissions($_SESSION['user_id'], $action);
            print_r($action);
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
            session_unset();
            session_destroy();

            $action = 'login';
            $loginService = new LoginService($conn);
            $result['users'] = $loginService->getAllUsers();
            $loginError = '';
            
            break;

        case 'users':
            $userService = new UserService($conn);
            $result['users'] = $userService->getAllUsers();
            break;

        case 'add_edit_user':
            $userService = new UserService($conn);
            $result['user'] = $userService->getUserById($_GET['id'] ?? null);
            print_r($result);
            exit;
            break;

        case 'user_access':
            $userService = new UserService($conn);
            $result['user'] = $userService->getUserById($_GET['user_id'] ?? null);
            $result['permissions'] = $userService->getUserPermissions($_GET['user_id'] ?? null);

            break;
        
        case 'user_access_submit':
            $userService = new UserService($conn);
            $result = $userService->updateUserPermissions($_POST);

            if ($result['success']) {
                $userSuccess = $result['message'];
            } else {
                $userError = $result['message'];
            }

            $result['users'] = $userService->getAllUsers();
            $action = 'users';

            break;

        default:
            $action = 'dashboard';
    }

    
    //print_r($result);

    $_SESSION['action'] = $action;
    //print_r($_SESSION);
?>