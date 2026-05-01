<?php
    require_once 'controllers/login_service.php';
    require_once 'controllers/access_service.php';
    require_once 'controllers/user_service.php';
    require_once 'controllers/project_service.php';
    require_once 'controllers/report_service.php';

    $timeout = (int) (getenv('TIMEOUT') ?: 1000); // 5 minutes
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $brand_name = isset($_GET['BRAND_NAME']) ? $_GET['BRAND_NAME'] : 'Digital Home';
    $user_is_admin = isset($_SESSION['user_admin']) ? (int) $_SESSION['user_admin'] : 0;




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

                $user_is_admin = isset($_SESSION['user_admin']) ? (int) $_SESSION['user_admin'] : 0;

                $accessService = new AccessService($conn);
                $allPermissions = $accessService->getUserAllPermissions($result['user']['user_id']);
                $_SESSION['permissions'] = array_column($allPermissions, 'meta_key');

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
        case 'add_edit_user_submit':
            $userService = new UserService($conn);
            $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

            if( $user_id > 0){
                $result = $userService->updateUser($user_id, $_POST);
            } else {
                $result = $userService->createUser($_POST);
            }

            if ($result['success']) {
                $userSuccess = $result['message'];
            } else {
                $userError = $result['message'];
            }
            
            $action = 'users';
            $result['users'] = $userService->getAllUsers();
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

        case 'projects':
            $projectService = new ProjectService($conn);
            $result['projects'] = $projectService->getAllProjects($_SESSION['user_id'] ?? 0, $user_is_admin);

            break;

        case 'add_edit_project':
            $projectService = new ProjectService($conn);
            $temp_project_id = isset($_GET['project_id']) ? (int) $_GET['project_id'] : 0;

            $result['project'] = $projectService->getProjectById($temp_project_id, $_SESSION['user_id'] ?? 0, $user_is_admin);

            if (is_array($result['project']) === false || count($result['project']) == 0) {
                $result['projects'] = $projectService->getAllProjects($_SESSION['user_id'] ?? 0, $user_is_admin);
                $projectError = 'Project not found.';
                $action = 'projects';
            } else {
                //$result['detail'] = $projectService->getProjectDetailById($temp_project_id);
            }

            break;

        case 'add_edit_project_submit':
            $projectService = new ProjectService($conn);
            $projectId = isset($_POST['project_id']) ? (int) $_POST['project_id'] : 0;

            if ($projectId > 0) {
                $result = $projectService->updateProject($projectId, $_POST, $_SESSION['user_id'] ?? 0);
                $action = 'add_edit_project';
                $result['project'] = array_merge($_POST, ['project_id' => $projectId]);
            } else {
                $result = $projectService->createProject($_POST, $_SESSION['user_id'] ?? 0);
                $action = 'add_edit_project';
                $result['project'] = $_POST;
            }

            if ($result['success']) {
                $projectSuccess = $result['message'];
                $action = 'projects';
                $result['projects'] = $projectService->getAllProjects($_SESSION['user_id'] ?? 0, $user_is_admin);
            } else {
                $projectError = $result['message'];
            }

            break;

        case 'update_project_tags':
            $projectService = new ProjectService($conn);
            $projectId = isset($_POST['project_id']) ? (int) $_POST['project_id'] : 0;

            if ($projectId > 0) {
                $result = $projectService->updateProjectTags($projectId, $_POST['project_tags'] ?? '', $_SESSION['user_id'] ?? 0);

                if ($result['success']) {
                    $projectSuccess = $result['message'];
                } else {
                    $projectError = $result['message'];
                }
            } else {
                $projectError = 'Invalid project ID.';
            }

            $action = 'projects';
            $result['projects'] = $projectService->getAllProjects($_SESSION['user_id'] ?? 0, $user_is_admin);

            break;

        case 'project_access':
            $projectService = new ProjectService($conn);
            $result['project_access'] = $projectService->getProjectAccess($_GET['project_id'] ?? null);
            break;

        case 'project_access_submit':
            $projectService = new ProjectService($conn);
            $result = $projectService->updateProjectAccess($_POST, $_SESSION['user_id'] ?? 0);

            if ($result['success']) {
                $projectSuccess = $result['message'];
            } else {
                $projectError = $result['message'];
            }

            $action = 'projects';
            $result['projects'] = $projectService->getAllProjects($_SESSION['user_id'] ?? 0, $user_is_admin);

            break;

        case 'project_delete':
            $projectService = new ProjectService($conn);
            $projectId = isset($_GET['project_id']) ? (int) $_GET['project_id'] : 0;

            if ($projectId > 0) {
                $result = $projectService->deleteProject($projectId);

                if ($result['success']) {
                    $projectSuccess = $result['message'];
                } else {
                    $projectError = $result['message'];
                }
            } else {
                $projectError = 'Invalid project ID.';
            }

            $action = 'projects';
            $result['projects'] = $projectService->getAllProjects($_SESSION['user_id'] ?? 0, $user_is_admin);

            break;

        case 'add_edit_report':
            $reportService = new ReportService($conn);
            $projectService = new ProjectService($conn);
            $userService = new UserService($conn);

            $result['users'] = $userService->getAllUsers( null, true);
            $result['projects'] = $projectService->getAllProjectsWithTags($_SESSION['user_id'] ?? 0, $user_is_admin);
            $result['customers'] = $reportService->getAllCustomers();
            $result['activities'] = $reportService->getRecentReports();

            break;

        case 'add_edit_report_submit':
            $reportService = new ReportService($conn);
            $projectService = new ProjectService($conn);
            $userService = new UserService($conn);
            
            $result = $reportService->saveDailyReport($_POST, $_SESSION['user_id'] ?? 0);

             if ($result['success']) {
                $reportSuccess = $result['message'];

            } else {
                $reportError = $result['message'];
            }

            $result['users'] = $userService->getAllUsers( null, true);
            $result['projects'] = $projectService->getAllProjectsWithTags($_SESSION['user_id'] ?? 0, $user_is_admin);
            $result['customers'] = $reportService->getAllCustomers();
            $result['activities'] = $reportService->getRecentReports();

            $action = 'add_edit_report';
            break;

        default:
            $action = 'dashboard';
    }

    
    //print_r($result);

    $_SESSION['action'] = $action;
    //print_r($_SESSION);
?>
