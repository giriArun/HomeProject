<?php
declare(strict_types=1);

final class AccessService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getUserAllPermissions(int $user_id): ?array
    {
        $sql = 'SELECT meta_key, meta_value
                FROM user_meta
                WHERE user_id = ?
                AND meta_value = 1';

        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $user_id);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $permissions = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;

        mysqli_stmt_close($statement);

        return $permissions ?: null;
    }

    public function verifyUserPermissions(int $user_id, string $action): ?string
    {
        $sql = 'SELECT user_id, is_admin, is_active
                FROM users
                WHERE user_id = ?
                ORDER BY user_name ASC';

        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return 'logout';
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
            $user_id = (int) $user[0]['user_id'];

            if( !$isActive ){
                return 'logout';
            }

            if( $isAdmin ){
                return $action;
            } else {

               $sql = 'SELECT *
                FROM user_meta
                WHERE user_id = ?
                AND meta_key = ?';

                $statement = mysqli_prepare($this->connection, $sql);

                if (!$statement) {
                    return 'logout';
                }

                mysqli_stmt_bind_param($statement, 'is', $user_id, $action);
                mysqli_stmt_execute($statement);

                $result = mysqli_stmt_get_result($statement);
                $user_permission = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;

                mysqli_stmt_close($statement);
                if( count($user_permission) > 0 && $user_permission[0]['meta_value'] === '1' ){
                    return $action;
                } else {
                    return 'logout';
                }
            }
        } else {
            return 'logout';
        }

    }
}
