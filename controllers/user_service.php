<?php
declare(strict_types=1);

final class UserService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getAllUsers(?string $search = null): array
    {
        $search = trim((string) $search);
        $sql = 'SELECT user_id, user_name, user_email, is_active, is_admin, created, modified
                FROM users';

        $types = '';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE user_name LIKE ? OR user_email LIKE ?';
            $keyword = '%' . $search . '%';
            $types = 'ss';
            $params = [$keyword, $keyword];
        }

        $sql .= ' ORDER BY user_name ASC';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [];
        }

        if ($types !== '') {
            mysqli_stmt_bind_param($statement, $types, ...$params);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $users = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($statement);

        return $users ?: [];
    }

    public function getUserById(?int $user_id = null): ?array
    {
        if ($user_id <= 0) {
            return null;
        }

        $sql = 'SELECT user_id, user_name, user_email, is_active, is_admin, created, modified
                FROM users
                WHERE user_id = ?
                LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $user_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $user = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        return $user ?: null;
    }

    public function createUser(array $payload): array
    {
        $normalized = $this->normalizePayload($payload, false);
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'user_id' => null,
            ];
        }

        if ($this->userNameExists($normalized['user_name'])) {
            return [
                'success' => false,
                'message' => 'User name already exists.',
                'user_id' => null,
            ];
        }

        if ($this->userEmailExists($normalized['user_email'])) {
            return [
                'success' => false,
                'message' => 'Email already exists.',
                'user_id' => null,
            ];
        }

        $sql = 'INSERT INTO users (user_name, user_email, password, is_active, is_admin)
                VALUES (?, ?, ?, ?, ?)';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare create user statement.',
                'user_id' => null,
            ];
        }

        mysqli_stmt_bind_param(
            $statement,
            'sssii',
            $normalized['user_name'],
            $normalized['user_email'],
            $normalized['password_hash'],
            $normalized['is_active'],
            $normalized['is_admin']
        );

        $ok = mysqli_stmt_execute($statement);
        $newId = $ok ? (int) mysqli_insert_id($this->connection) : null;
        mysqli_stmt_close($statement);

        if (!$ok) {
            return [
                'success' => false,
                'message' => 'Unable to create user.',
                'user_id' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'User created successfully.',
            'user_id' => $newId,
        ];
    }

    public function updateUser(int $user_id, array $payload): array
    {
        if ($user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user id.',
                'user_id' => null,
            ];
        }

        $existing = $this->getUserById($user_id);
        if ($existing === null) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'user_id' => null,
            ];
        }

        $normalized = $this->normalizePayload($payload, true);
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'user_id' => null,
            ];
        }

        if ($this->userNameExists($normalized['user_name'], $user_id)) {
            return [
                'success' => false,
                'message' => 'User name already exists.',
                'user_id' => null,
            ];
        }

        if ($this->userEmailExists($normalized['user_email'], $user_id)) {
            return [
                'success' => false,
                'message' => 'Email already exists.',
                'user_id' => null,
            ];
        }

        if ($normalized['password_hash'] !== null) {
            $sql = 'UPDATE users
                    SET user_name = ?, user_email = ?, password = ?, is_active = ?, is_admin = ?
                    WHERE user_id = ?';

            $statement = mysqli_prepare($this->connection, $sql);
            if (!$statement) {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare update statement.',
                    'user_id' => null,
                ];
            }

            mysqli_stmt_bind_param(
                $statement,
                'sssiii',
                $normalized['user_name'],
                $normalized['user_email'],
                $normalized['password_hash'],
                $normalized['is_active'],
                $normalized['is_admin'],
                $user_id
            );
        } else {
            $sql = 'UPDATE users
                    SET user_name = ?, user_email = ?, is_active = ?, is_admin = ?
                    WHERE user_id = ?';

            $statement = mysqli_prepare($this->connection, $sql);
            if (!$statement) {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare update statement.',
                    'user_id' => null,
                ];
            }

            mysqli_stmt_bind_param(
                $statement,
                'ssiii',
                $normalized['user_name'],
                $normalized['user_email'],
                $normalized['is_active'],
                $normalized['is_admin'],
                $user_id
            );
        }

        $ok = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        if (!$ok) {
            return [
                'success' => false,
                'message' => 'Unable to update user.',
                'user_id' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'User updated successfully.',
            'user_id' => $user_id,
        ];
    }

    public function deleteUser(int $user_id): array
    {
        if ($user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user id.',
            ];
        }

        $sql = 'DELETE FROM users WHERE user_id = ?';
        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare delete statement.',
            ];
        }

        mysqli_stmt_bind_param($statement, 'i', $user_id);
        mysqli_stmt_execute($statement);
        $affectedRows = mysqli_stmt_affected_rows($statement);
        mysqli_stmt_close($statement);

        if ($affectedRows < 1) {
            return [
                'success' => false,
                'message' => 'User not found or already deleted.',
            ];
        }

        return [
            'success' => true,
            'message' => 'User deleted successfully.',
        ];
    }

    public function setUserStatus(int $user_id, bool $is_active): array
    {
        if ($user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user id.',
            ];
        }

        $activeValue = $is_active ? 1 : 0;
        $sql = 'UPDATE users SET is_active = ? WHERE user_id = ?';
        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare status update statement.',
            ];
        }

        mysqli_stmt_bind_param($statement, 'ii', $activeValue, $user_id);
        mysqli_stmt_execute($statement);
        $affectedRows = mysqli_stmt_affected_rows($statement);
        mysqli_stmt_close($statement);

        if ($affectedRows < 1) {
            return [
                'success' => false,
                'message' => 'User not found or status unchanged.',
            ];
        }

        return [
            'success' => true,
            'message' => 'User status updated successfully.',
        ];
    }

    public function getUserPermissions(int $user_id): array
    {
        if ($user_id <= 0) {
            return [];
        }

        $sql = 'SELECT meta_key, meta_value FROM user_meta WHERE user_id = ?';
        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return [];
        }

        mysqli_stmt_bind_param($statement, 'i', $user_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $permissions = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $permissions[$row['meta_key']] = $row['meta_value'];
            }
        }

        return $permissions;
    }

    public function updateUserPermissions(array $formData): array
    {
        if (!isset($formData['user_id']) || (int) $formData['user_id'] <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user id.',
            ];
        }

        $user_id = (int) $formData['user_id'];

        // Validate user exists
        $user = $this->getUserById($user_id);
        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        /* Process user permissions */
        if (isset($formData['user_permission']) && is_string($formData['user_permission'])) {

            $user_permissions = json_decode($formData['user_permission'], true);

            // Validate that the decoded permissions is an array
            if (!is_array($user_permissions)) {
                return [
                    'success' => false,
                    'message' => 'Invalid permissions data.',
                ];
            }

            // Process permissions as needed
            foreach ($user_permissions as $permission) {
                $permission_key = $permission['key'] ?? null;
                $permission_values = $permission['value'] ?? [];

                if ($permission_key === null || !is_array($permission_values)) {
                    continue;
                }

                $is_access_granted = (isset($formData[$permission_key]) && $formData[$permission_key] === 'on') ? 1 : 0;

                foreach ($permission_values as $single_permission_key) {
                    // Process the single permission
                    $result = $this->processSinglePermission($user_id, $single_permission_key, $is_access_granted);
                    if (!$result['success']) {
                        return $result;
                    }
                }
            }
        }

        /* Process project permissions */
        if (isset($formData['project_permission']) && is_string($formData['project_permission'])) {

            $project_permissions = json_decode($formData['project_permission'], true);

            // Validate that the decoded permissions is an array
            if (!is_array($project_permissions)) {
                return [
                    'success' => false,
                    'message' => 'Invalid permissions data.',
                ];
            }

            // Process permissions as needed
            foreach ($project_permissions as $permission) {
                $permission_key = $permission['key'] ?? null;
                $permission_values = $permission['value'] ?? [];

                if ($permission_key === null || !is_array($permission_values)) {
                    continue;
                }

                $is_access_granted = (isset($formData[$permission_key]) && $formData[$permission_key] === 'on') ? 1 : 0;

                foreach ($permission_values as $single_permission_key) {
                    // Process the single permission
                    $result = $this->processSinglePermission($user_id, $single_permission_key, $is_access_granted);
                    if (!$result['success']) {
                        return $result;
                    }
                }
            }
        }

        return [
            'success' => true,
            'message' => 'User permissions updated successfully.',
        ];
    }

    // Private helper methods
    private function normalizePayload(array $payload, bool $isUpdate): array
    {
        $user_name = trim((string) ($payload['user_name'] ?? ''));
        $user_email = trim((string) ($payload['user_email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $is_active = isset($payload['is_active']) ? (int) ((bool) $payload['is_active']) : 1;
        $is_admin = isset($payload['is_admin']) ? (int) ((bool) $payload['is_admin']) : 0;

        if ($user_name === '') {
            return ['valid' => false, 'message' => 'User name is required.'];
        }

        if ($user_email === '' || filter_var($user_email, FILTER_VALIDATE_EMAIL) === false) {
            return ['valid' => false, 'message' => 'A valid email is required.'];
        }

        $passwordHash = null;

        if ($isUpdate) {
            if (trim($password) !== '') {
                if (strlen($password) < 6) {
                    return ['valid' => false, 'message' => 'Password must be at least 6 characters.'];
                }

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }
        } else {
            if (trim($password) === '') {
                return ['valid' => false, 'message' => 'Password is required.'];
            }

            if (strlen($password) < 6) {
                return ['valid' => false, 'message' => 'Password must be at least 6 characters.'];
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        return [
            'valid' => true,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'password_hash' => $passwordHash,
            'is_active' => $is_active,
            'is_admin' => $is_admin,
            'message' => '',
        ];
    }

    private function userNameExists(string $user_name, ?int $exclude_user_id = null): bool
    {
        $sql = 'SELECT user_id FROM users WHERE user_name = ?';

        if ($exclude_user_id !== null) {
            $sql .= ' AND user_id <> ?';
        }

        $sql .= ' LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return false;
        }

        if ($exclude_user_id !== null) {
            mysqli_stmt_bind_param($statement, 'si', $user_name, $exclude_user_id);
        } else {
            mysqli_stmt_bind_param($statement, 's', $user_name);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $exists = $result ? mysqli_num_rows($result) > 0 : false;
        mysqli_stmt_close($statement);

        return $exists;
    }

    private function userEmailExists(string $user_email, ?int $exclude_user_id = null): bool
    {
        $sql = 'SELECT user_id FROM users WHERE user_email = ?';

        if ($exclude_user_id !== null) {
            $sql .= ' AND user_id <> ?';
        }

        $sql .= ' LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return false;
        }

        if ($exclude_user_id !== null) {
            mysqli_stmt_bind_param($statement, 'si', $user_email, $exclude_user_id);
        } else {
            mysqli_stmt_bind_param($statement, 's', $user_email);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $exists = $result ? mysqli_num_rows($result) > 0 : false;
        mysqli_stmt_close($statement);

        return $exists;
    }

    private function processSinglePermission(int $user_id, string $permission_key, int $is_access_granted): array
    {
        // Check if permission record exists
        $checkSql = 'SELECT * FROM user_meta WHERE user_id = ? AND meta_key = ? LIMIT 1';
        $checkStatement = mysqli_prepare($this->connection, $checkSql);

        if (!$checkStatement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare check statement.',
            ];
        }

        mysqli_stmt_bind_param($checkStatement, 'is', $user_id, $permission_key);
        mysqli_stmt_execute($checkStatement);
        $result = mysqli_stmt_get_result($checkStatement);
        $exists = $result ? mysqli_num_rows($result) > 0 : false;
        mysqli_stmt_close($checkStatement);

        if ($exists) {
            // UPDATE
            $updateSql = 'UPDATE user_meta SET meta_value = ? WHERE user_id = ? AND meta_key = ?';
            $updateStatement = mysqli_prepare($this->connection, $updateSql);

            if (!$updateStatement) {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare update statement.',
                ];
            }

            mysqli_stmt_bind_param($updateStatement, 'iis', $is_access_granted, $user_id, $permission_key);
            $ok = mysqli_stmt_execute($updateStatement);
            mysqli_stmt_close($updateStatement);

            if (!$ok) {
                return [
                    'success' => false,
                    'message' => 'Failed to update permission.',
                ];
            }
        } else {
            // INSERT
            $insertSql = 'INSERT INTO user_meta (user_id, meta_key, meta_value) VALUES (?, ?, ?)';
            $insertStatement = mysqli_prepare($this->connection, $insertSql);

            if (!$insertStatement) {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare insert statement.',
                ];
            }

            mysqli_stmt_bind_param($insertStatement, 'isi', $user_id, $permission_key, $is_access_granted);
            $ok = mysqli_stmt_execute($insertStatement);
            mysqli_stmt_close($insertStatement);

            if (!$ok) {
                return [
                    'success' => false,
                    'message' => 'Failed to insert permission.',
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Permission processed successfully.',
        ];
    }
}
