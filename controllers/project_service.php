<?php
declare(strict_types=1);

final class ProjectService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getAllProjects(?bool $is_active = true): array
    {
        $sql = 'SELECT project_id, project_name, project_detail, project_start_year, project_end_year, is_active, created_by, created, modified_by, modified
                FROM projects';

        $types = '';
        $params = [];

        if ($is_active == true) {
            $sql .= ' WHERE is_active = ? OR project_end_year >= YEAR(CURDATE())';
            $types = 'i';
            $params = [(int) $is_active];
        }

        $sql .= ' ORDER BY project_name ASC';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [];
        }

        if ($types !== '') {
            mysqli_stmt_bind_param($statement, $types, ...$params);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $projects = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($statement);

        return $projects ?: [];
    }

    public function getProjectById(?int $project_id = null): ?array
    {
        if ($project_id <= 0) {
            return null;
        }

        $sql = 'SELECT project_id, project_name, project_detail, project_start_year, project_end_year, is_active, created_by, created, modified_by, modified
                FROM projects
                WHERE project_id = ?
                LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $project_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $project = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        return $project ?: null;
    }

    public function createProject(array $payload, int $created_by): array
    {
        $normalized = $this->normalizePayload($payload, false);
        
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'project_id' => null,
            ];
        }

        if ($this->projectNameExists($normalized['project_name'])) {
            return [
                'success' => false,
                'message' => 'Project name already exists.',
                'project_id' => null,
            ];
        }

        $sql = 'INSERT INTO projects (project_name, project_detail, project_start_year, project_end_year, is_active, created_by)
                VALUES (?, ?, ?, ?, ?, ?)';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare create project statement.',
                'project_id' => null,
            ];
        }

        mysqli_stmt_bind_param(
            $statement,
            'ssiiii',
            $normalized['project_name'],
            $normalized['project_detail'],
            $normalized['project_start_year'],
            $normalized['project_end_year'],
            $normalized['is_active'],
            $created_by
        );

        try {
            $ok = mysqli_stmt_execute($statement);
        } catch (mysqli_sql_exception $exception) {
            mysqli_stmt_close($statement);
            return [
                'success' => false,
                'message' => 'Unable to create project. Please check the year fields and try again.',
                'project_id' => null,
            ];
        }

        $newId = $ok ? (int) mysqli_insert_id($this->connection) : null;
        mysqli_stmt_close($statement);

        if (!$ok) {
            return [
                'success' => false,
                'message' => 'Unable to create project.',
                'project_id' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'Project created successfully.',
            'project_id' => $newId,
        ];
    }

    public function updateProject(int $project_id, array $payload, int $modified_by): array
    {
        if ($project_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid project id.',
                'project_id' => null,
            ];
        }

        $existing = $this->getProjectById($project_id);
        if ($existing === null) {
            return [
                'success' => false,
                'message' => 'Project not found.',
                'project_id' => null,
            ];
        }

        $normalized = $this->normalizePayload($payload, true);
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'project_id' => null,
            ];
        }

        if ($this->projectNameExists($normalized['project_name'], $project_id)) {
            return [
                'success' => false,
                'message' => 'Project name already exists.',
                'project_id' => null,
            ];
        }

        $sql = 'UPDATE projects
                SET project_name = ?, project_detail = ?, project_start_year = ?, project_end_year = ?, is_active = ?, modified_by = ?
                WHERE project_id = ?';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare update statement.',
                'project_id' => null,
            ];
        }

        mysqli_stmt_bind_param(
            $statement,
            'ssiiiii',
            $normalized['project_name'],
            $normalized['project_detail'],
            $normalized['project_start_year'],
            $normalized['project_end_year'],
            $normalized['is_active'],
            $modified_by,
            $project_id
        );

        try {
            $ok = mysqli_stmt_execute($statement);
        } catch (mysqli_sql_exception $exception) {
            mysqli_stmt_close($statement);
            return [
                'success' => false,
                'message' => 'Unable to update project. Please check the year fields and try again.',
                'project_id' => null,
            ];
        }

        mysqli_stmt_close($statement);

        if (!$ok) {
            return [
                'success' => false,
                'message' => 'Unable to update project.',
                'project_id' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'Project updated successfully.',
            'project_id' => $project_id,
        ];
    }

    public function deleteProject(int $project_id): array
    {
        if ($project_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid project id.',
            ];
        }

        $sql = 'DELETE FROM projects WHERE project_id = ?';
        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare delete statement.',
            ];
        }

        mysqli_stmt_bind_param($statement, 'i', $project_id);
        mysqli_stmt_execute($statement);
        $affectedRows = mysqli_stmt_affected_rows($statement);
        mysqli_stmt_close($statement);

        if ($affectedRows < 1) {
            return [
                'success' => false,
                'message' => 'Project not found or already deleted.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Project deleted successfully.',
        ];
    }

    public function setProjectStatus(int $project_id, bool $is_active): array
    {
        if ($project_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid project id.',
            ];
        }

        $activeValue = $is_active ? 1 : 0;
        $sql = 'UPDATE projects SET is_active = ? WHERE project_id = ?';
        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare status update statement.',
            ];
        }

        mysqli_stmt_bind_param($statement, 'ii', $activeValue, $project_id);
        mysqli_stmt_execute($statement);
        $affectedRows = mysqli_stmt_affected_rows($statement);
        mysqli_stmt_close($statement);

        if ($affectedRows < 1) {
            return [
                'success' => false,
                'message' => 'Project not found or status unchanged.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Project status updated successfully.',
        ];
    }

    // Private helper methods
    private function normalizePayload(array $payload, bool $isUpdate): array
    {
        $project_name = trim((string) ($payload['project_name'] ?? ''));
        $project_detail = trim((string) ($payload['project_detail'] ?? ''));
        $project_start_year = trim((string) ($payload['project_start_year'] ?? ''));
        $project_end_year = trim((string) ($payload['project_end_year'] ?? ''));
        $is_active = isset($payload['is_active']) ? (int) ((bool) $payload['is_active']) : 0;

        if ($project_name === '') {
            return ['valid' => false, 'message' => 'Project name is required.'];
        }

        if (strlen($project_name) < 3) {
            return ['valid' => false, 'message' => 'Project name must be at least 3 characters.'];
        }

        if (strlen($project_name) > 255) {
            return ['valid' => false, 'message' => 'Project name cannot exceed 255 characters.'];
        }

        if ($project_start_year === '') {
            return ['valid' => false, 'message' => 'Project start year is required.'];
        }

        if (!preg_match('/^[0-9]{4}$/', $project_start_year)) {
            return ['valid' => false, 'message' => 'Project start year must be a 4-digit year.'];
        }

        if ($project_end_year === '') {
            return ['valid' => false, 'message' => 'Project end year is required.'];
        }

        if (!preg_match('/^[0-9]{4}$/', $project_end_year)) {
            return ['valid' => false, 'message' => 'Project end year must be a 4-digit year.'];
        }

        $project_start_year_int = (int) $project_start_year;
        $project_end_year_int = (int) $project_end_year;

        if ($project_start_year_int < 1901 || $project_start_year_int > 2155) {
            return ['valid' => false, 'message' => 'Project start year must be between 1901 and 2155.'];
        }

        if ($project_end_year_int < 1901 || $project_end_year_int > 2155) {
            return ['valid' => false, 'message' => 'Project end year must be between 1901 and 2155.'];
        }

        if ($project_end_year_int < $project_start_year_int) {
            return ['valid' => false, 'message' => 'Project end year cannot be earlier than the start year.'];
        }

        return [
            'valid' => true,
            'project_name' => $project_name,
            'project_detail' => $project_detail === '' ? null : $project_detail,
            'project_start_year' => $project_start_year_int,
            'project_end_year' => $project_end_year_int,
            'is_active' => $is_active,
            'message' => '',
        ];
    }

    private function projectNameExists(string $project_name, ?int $exclude_project_id = null): bool
    {
        $sql = 'SELECT project_id FROM projects WHERE project_name = ?';

        if ($exclude_project_id !== null) {
            $sql .= ' AND project_id <> ?';
        }

        $sql .= ' LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return false;
        }

        if ($exclude_project_id !== null) {
            mysqli_stmt_bind_param($statement, 'si', $project_name, $exclude_project_id);
        } else {
            mysqli_stmt_bind_param($statement, 's', $project_name);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $exists = $result ? mysqli_num_rows($result) > 0 : false;
        mysqli_stmt_close($statement);

        return $exists;
    }
}
