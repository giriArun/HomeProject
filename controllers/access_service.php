<?php
declare(strict_types=1);

final class AccessService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function verifyUserPermissions(int $user_id, string $action): ?string
    {
        $sql = 'SELECT user_id, is_admin, is_active
                FROM users
                WHERE user_id = ?
                ORDER BY user_name ASC';

        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $user_id);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $user = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;

        mysqli_stmt_close($statement);

        return $this->validateUserPermissions($user, $action);
    }

    private function validateUserPermissions( array $user, string $action ): ?string
    {   
        if( count($user) > 0){
            $isAdmin = (int) $user[0]['is_admin'] === 1;
            $isActive = (int) $user[0]['is_active'] === 1;

            if( !$isActive ){
                return 'logout';
            }

            if( $isAdmin ){
                return $action;
            } else {

               // print_r( in_array($action, ['users', 'user_form'], true) );
                //exit;

                return 'admin';
            }
        } else {
            return 'dashboard';
        }

    }
}
