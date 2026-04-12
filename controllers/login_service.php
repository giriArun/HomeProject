<?php
declare(strict_types=1);

final class LoginService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getAllUsers(): ?array
    {
        $sql = 'SELECT user_id, user_name
                FROM users
                WHERE is_active = 1
                ORDER BY user_name ASC';

        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return null;
        }

        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $users = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;

        mysqli_stmt_close($statement);

        return $users ?: null;
    }

    public function validateLogin(string $user_name, string $password): array
    {
        $user_name = $this->normalizeUserName($user_name);
        $password = trim($password);

        if ($user_name === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'User name and password are required.',
                'user' => null,
            ];
        }

        $user = $this->findUserByUserName($user_name);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Invalid user name or password.',
                'user' => null,
            ];
        }

        if ((int) $user['is_active'] !== 1) {
            return [
                'success' => false,
                'message' => 'Your account is inactive.',
                'user' => null,
            ];
        }

        if (!$this->passwordMatches($password, (string) $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid user name or password.',
                'user' => null,
            ];
        }

        unset($user['password']);

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
        ];
    }

    private function findUserByUserName(string $user_name): ?array
    {
        $sql = 'SELECT user_id, user_name, user_email, password, is_active, is_admin, created, modified
                FROM users
                WHERE user_name = ?
                LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 's', $user_name);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $user = $result ? mysqli_fetch_assoc($result) : null;

        mysqli_stmt_close($statement);

        return $user ?: null;
    }

    private function passwordMatches(string $plainPassword, string $storedPassword): bool
    {
        if ($storedPassword === '') {
            return false;
        }

        if (password_verify($plainPassword, $storedPassword)) {
            return true;
        }

        return hash_equals($storedPassword, $plainPassword);
    }

    private function normalizeUserName(string $user_name): string
    {
        return trim($user_name);
    }
}
