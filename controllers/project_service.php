<?php
declare(strict_types=1);

final class ProjectService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getAllProjects(?int $user_id = 0, ?bool $is_admin = false, ?bool $is_active = true): array
    {
        $sql = 'SELECT DISTINCT p.project_id, p.project_name, p.project_start_year, p.project_end_year, p.is_active, p.project_tags
            FROM projects AS p
            LEFT JOIN project_users AS pu ON pu.project_id = p.project_id
            WHERE 1 = 1';

        $types = '';
        $params = [];

        if ($is_active == true) {
            $sql .= ' AND ( is_active = ? OR project_end_year >= YEAR(CURDATE()) )';
            $types = 'i';
            $params[] = (int) $is_active;
        }
        
        if ($is_admin == false && $user_id > 0) {
            $sql .= ' AND ( pu.user_id = ? OR p.created_by = ? )';
            $types .= 'ii';
            $params[] = (int) $user_id;
            $params[] = (int) $user_id;
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

    public function getAllProjectsWithTags(?int $user_id = 0, ?bool $is_admin = false): array
    {
        $projects = $this->getAllProjects($user_id, $is_admin, true);
        $tempProjects = [];

        foreach ($projects as $project) {
            $tempProject = $project;
            if (!empty($project['project_tags'])) {
                $tempProject['recent_tags'] = $this->getRecentTags($project['project_tags'], (int) ($project['project_id'] ?? 0));

                if (empty($tempProject['recent_tags'])) {
                    $tempProject['other_tags'] = $project['project_tags'];
                } else {
                    $existingTagNames = array_column($tempProject['recent_tags'], 'tag_name');
                    $otherTags = array_diff(array_map('trim', explode(',', $project['project_tags'])), $existingTagNames);
                    $tempProject['other_tags'] = implode(', ', $otherTags);
                }
            } else {
                $tempProject['recent_tags'] = [];
            }

            $tempProjects[] = $tempProject;
        }

        return $tempProjects ?: [];
    }

    private function getRecentTags(string $tags, int $project_id): ?array
    {
        $tagArray = array_map('trim', explode(',', $tags));
        if (empty($tagArray)) {
            return [];
        }
        // Escape tags for SQL (since these come from DB, not user input, this is safe)
        $tagList = implode(",", array_map(fn($t) => "'" . $this->connection->real_escape_string($t) . "'", $tagArray));
        $sql = "SELECT tag_name, COUNT(*) as tag_count 
        FROM projects_tag_logs 
        WHERE project_id = $project_id AND tag_name IN ($tagList) 
        GROUP BY tag_name
        ORDER BY COUNT(1) DESC";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) {
            return [];
        }
        $tags = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        
        return $tags ?: [];
    }

    public function getProjectAccess(int $project_id): ?array
    {
        $sql = "SELECT u.user_id, u.user_name, u.user_email,
                    p.project_id, p.project_name, pu.project_users_id,
                    CASE 
                        WHEN pu.project_id IS NOT NULL THEN 1
                        ELSE 0
                    END AS is_assigned
                FROM users AS u
                LEFT JOIN project_users AS pu 
                    ON pu.user_id = u.user_id 
                    AND pu.project_id = ?
                LEFT JOIN projects AS p 
                    ON p.project_id = ?
                    AND (p.is_active = 1 OR p.project_end_year >= YEAR(CURDATE()))
                WHERE u.is_active = 1 
                AND u.is_admin = 0 
                ORDER BY u.user_name ASC";

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [];
        }

        mysqli_stmt_bind_param($statement, 
            'ii', 
            $project_id,
            $project_id
        );

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $projects = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($statement);
        
        return $projects ?: [];
    }

    public function updateProjectAccess(array $payload, int $updated_by): array
    {
        $project_id = $payload['project_id'] ?? 0;
        $usersAccess = $payload['users'] ?? [];
        $oldAccess = $this->getProjectAccess((int) $project_id);

        foreach ($usersAccess as $userAccess) {
            $parts = explode('|', $userAccess);
            if (count($parts) !== 3) {
                continue; // skip invalid entries
            }

            [$user_id, $project_users_id, $project_id] = $parts;
            $user_id = (int) $user_id;
            $project_users_id = (int) $project_users_id;
            $project_id = (int) $project_id;

            $assignedUsers = array_filter($oldAccess, function($user) use ($user_id, $project_users_id, $project_id) {
                return ($user['user_id'] == $user_id && $user['project_users_id'] == $project_users_id && $user['project_id'] == $project_id) ? true : false;
            });

            if ($project_users_id > 0 && count($assignedUsers) > 0) {
                // User is already assigned, no action needed
                $oldAccess = array_diff_key($oldAccess, $assignedUsers);
                continue;
            } else {
                // Assign user to project
                $sql = 'INSERT INTO project_users (project_id, user_id, created_by)
                        VALUES (?, ?, ?)';
                $statement = mysqli_prepare($this->connection, $sql);
                if ($statement) {
                    mysqli_stmt_bind_param($statement, 'iii', $project_id, $user_id, $updated_by);
                    mysqli_stmt_execute($statement);
                    mysqli_stmt_close($statement);
                }
            }
        }

        foreach ($oldAccess as $user) {
            $project_users_id = (int) ($user['project_users_id'] ?? 0);
            if ($project_users_id <= 0) {
                continue;
            }

            $sql = 'DELETE FROM project_users WHERE project_users_id = ? AND project_id = ?';
            $statement = mysqli_prepare($this->connection, $sql);
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'ii', $project_users_id, $project_id);
                mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);
            }
        }
        
        return ['success' => true, 'message' => 'Project access updated successfully.'];
    }

    public function getProjectById(?int $project_id = 0, ?int $user_id = 0, ?bool $is_admin = false): ?array
    {
        $sql_join = '';
        $sql_and_condition = '';
        $types = 'i';
        $params = [];
        $params[] = (int) $project_id;

        if ($project_id <= 0) {
            return null;
        }

        if ($is_admin == false && $user_id > 0) {
            $sql_join = 'LEFT JOIN project_users ON p.project_id = project_users.project_id';
            $sql_and_condition = ' AND ( project_users.user_id = ? OR p.created_by = ? )';
            $types .= 'ii';
            $params[] = (int) $user_id;
            $params[] = (int) $user_id;
        }

        $sql = 'SELECT DISTINCT p.project_id, p.project_name, p.project_start_year, p.project_end_year, p.is_active
                FROM projects AS p
                ' . $sql_join . '
                WHERE p.project_id = ?
                ' . $sql_and_condition . '
                LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, $types, ...$params);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $project = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        return $project ?: null;
    }

    public function getProjectDetailById(?int $project_id = null): ?array
    {
        if ($project_id <= 0) {
            return null;
        }

        $project_detail = [];

        // get latest cost update or renewal for the project to show in project detail page
        $sql = 'SELECT project_id, new_cost
            FROM project_timeline
            WHERE project_id = ?
            AND change_type IN ("renewal", "cost_update")
            ORDER BY project_timeline_id DESC
            LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $project_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $project_detail['latest_cost'] = $result ? mysqli_fetch_assoc($result) : null;

        // get latest date change or renewal for the project to show in project detail page
        $sql = 'SELECT project_id, start_date, end_date
            FROM project_timeline
            WHERE project_id = ?
            AND change_type IN ("date_change", "renewal")
            ORDER BY project_timeline_id DESC
            LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $project_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $project_detail['latest_dates'] = $result ? mysqli_fetch_assoc($result) : null;


        mysqli_stmt_close($statement);

        return $project_detail ?: null;
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

        if ($normalized['project_status_type'] !== 'status_change') {
            return [
                'success' => false,
                'message' => 'Project status is required.',
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

        $sql = 'INSERT INTO projects (project_name, is_active, created_by, modified_by)
                VALUES (?, ?, ?, ?)';

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
            'siii',
            $normalized['project_name'],
            $normalized['project_status_value'],
            $created_by,
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

        return $this->updateProjectTimeline($normalized, $newId, $created_by);
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

        $sql_fields = '';
        $types = 'sii';
        $params = [
            $normalized['project_name'],
            $modified_by,
            $project_id,
        ];

        if ($normalized['project_status_type'] === 'status_change') {
            $sql_fields = ', is_active = ?';
            $types = 'siii';
            array_splice($params, 2, 0, [(int) $normalized['project_status_value']]);
        } elseif ($normalized['project_status_type'] === 'date_change' || $normalized['project_status_type'] === 'renewal') {
            $sql_fields = ', project_start_year = ?, project_end_year = ?';
            $types = 'siiii';
            array_splice($params, 2, 0, [
                $normalized['project_start_year'],
                $normalized['project_end_year']
            ]);
        }

        $sql = 'UPDATE projects
                SET project_name = ?, modified_by = ? ' . $sql_fields . '
                WHERE project_id = ?';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare update statement.',
                'project_id' => null,
            ];
        }

        mysqli_stmt_bind_param($statement, $types, ...$params);

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

        return $this->updateProjectTimeline($normalized, $project_id, $modified_by);
        return [
            'success' => true,
            'message' => 'Project updated successfully.',
            'project_id' => $project_id,
        ];
    }

    private function updateProjectTimeline(array $normalized, int $project_id, int $created_by): array
    {
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'project_id' => null,
            ];
        }

        if ($normalized['project_status_type'] === 'status_change'){
            $types = 'isisi';
            $params = [
                $project_id,
                $normalized['project_status_type'],
                $normalized['project_status_value'],
                $normalized['project_detail'] ?? '',
                $created_by
            ];
            $sql = 'INSERT INTO project_timeline (project_id, change_type, project_status, description, created_by)
                    VALUES (?, ?, ?, ?, ?)';
        } elseif ($normalized['project_status_type'] === 'date_change') {
            $types = 'issssi';
            $params = [
                $project_id,
                $normalized['project_status_type'],
                $normalized['project_start_date'],
                $normalized['project_end_date'],
                $normalized['project_detail'] ?? '',
                $created_by
            ];
            $sql = 'INSERT INTO project_timeline (project_id, change_type, start_date, end_date, description, created_by)
                    VALUES (?, ?, ?, ?, ?, ?)';
        } elseif ($normalized['project_status_type'] === 'renewal') {
            $types = 'isssisi';
            $params = [
                $project_id,
                $normalized['project_status_type'],
                $normalized['project_start_date'],
                $normalized['project_end_date'],
                $normalized['project_cost'],
                $normalized['project_detail'] ?? '',
                $created_by
            ];
            $sql = 'INSERT INTO project_timeline (project_id, change_type, start_date, end_date, new_cost, description, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?)';
        } elseif ($normalized['project_status_type'] === 'cost_update') {
            $types = 'isisi';
            $params = [
                $project_id,
                $normalized['project_status_type'],
                $normalized['project_cost'],
                $normalized['project_detail'] ?? '',
                $created_by
            ];
            $sql = 'INSERT INTO project_timeline (project_id, change_type, new_cost, description, created_by)
                    VALUES (?, ?, ?, ?, ?)';
        } else {
            return [
                'success' => false,
                'message' => 'Project status is required.',
                'project_id' => null,
            ];
        }


        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare update project timeline statement.',
                'project_id' => null,
            ];
        }
        
        mysqli_stmt_bind_param($statement, $types, ...$params);
        /* if ($normalized['project_status_type'] === 'status_change'){
            mysqli_stmt_bind_param(
                $statement,
                'isisi',
                $project_id,
                $normalized['project_status_type'],
                $normalized['project_status_value'],
                $normalized['project_detail'] ?? '',
                $created_by
            );
        } */

            try {
                $ok = mysqli_stmt_execute($statement);
            } catch (mysqli_sql_exception $exception) {
                mysqli_stmt_close($statement);
                return [
                    'success' => false,
                    'message' => 'Unable to update project timeline. Please check the fields and try again.',
                    'project_id' => null,
                ];
            }

        /* if ($normalized['project_status_type'] === 'status_change') {
            $sql = 'INSERT INTO project_timeline (project_id, change_type,created_by)
                    VALUES (?, ?, ?)';

        } else ($normalized['project_status_type'] !== 'status_change') {
            return [
                'success' => false,
                'message' => 'Project status is required.',
                'project_id' => null,
            ];
        } */



        $newId = $ok ? (int) mysqli_insert_id($this->connection) : null;
        mysqli_stmt_close($statement);

        if (!$ok) {
            return [
                'success' => false,
                'message' => 'Unable to update project timeline.',
                'project_id' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'Project updated successfully.',
            'project_id' => $newId,
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

    public function updateProjectTags(int $project_id, string $project_tags, int $modified_by): array
    {
        if ($project_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid project id.',
            ];
        }

        $project_tags = trim($project_tags);
        $sql = 'UPDATE projects SET project_tags = ?, modified_by = ? WHERE project_id = ?';
        $statement = mysqli_prepare($this->connection, $sql);

        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare update tags statement.',
            ];
        }

        mysqli_stmt_bind_param($statement, 'sii', $project_tags, $modified_by, $project_id);
        mysqli_stmt_execute($statement);
        $affectedRows = mysqli_stmt_affected_rows($statement);
        mysqli_stmt_close($statement);

        if ($affectedRows < 1) {
            return [
                'success' => false,
                'message' => 'Project not found or tags unchanged.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Project tags updated successfully.',
        ];
    }

    // Private helper methods
    private function normalizePayload(array $payload, bool $isUpdate): array
    {
        $project_name = trim((string) ($payload['project_name'] ?? ''));
        $project_detail = trim((string) ($payload['project_detail'] ?? ''));
        $project_status = trim((string) ($payload['project_status'] ?? ''));
        $project_start_date = trim((string) ($payload['project_start_date'] ?? ''));
        $project_end_date = trim((string) ($payload['project_end_date'] ?? ''));
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

        if (strlen($project_detail) > 255) {
            return ['valid' => false, 'message' => 'Project detail cannot exceed 255 characters.'];
        }
    
        $temp_status_array = explode('|', $project_status);

        if (is_array($temp_status_array) === false || count($temp_status_array) !== 2) {
            return ['valid' => false, 'message' => 'Invalid project status format.'];
        }

        if ($temp_status_array[0] === 'status_change') {
            return [
                'valid' => true,
                'project_name' => $project_name,
                'project_detail' => $project_detail === '' ? null : $project_detail,
                'project_status_type' => $temp_status_array[0],
                'project_status_value' => $temp_status_array[1],
                'message' => '',
            ];
        } elseif ($temp_status_array[0] === 'date_change' || $temp_status_array[0] === 'renewal') {
            // Validate dates for date_change
            if ($project_start_date === '') {
                return ['valid' => false, 'message' => 'Project start date is required for date change.'];
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $project_start_date)) {
                return ['valid' => false, 'message' => 'Project start date must be in YYYY-MM-DD format.'];
            }

            if ($project_end_date === '') {
                return ['valid' => false, 'message' => 'Project end date is required for date change.'];
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $project_end_date)) {
                return ['valid' => false, 'message' => 'Project end date must be in YYYY-MM-DD format.'];
            }

            // Validate date formats
            $startDateTime = DateTime::createFromFormat('Y-m-d', $project_start_date);
            $endDateTime = DateTime::createFromFormat('Y-m-d', $project_end_date);

            if (!$startDateTime || $startDateTime->format('Y-m-d') !== $project_start_date) {
                return ['valid' => false, 'message' => 'Invalid project start date.'];
            }

            if (!$endDateTime || $endDateTime->format('Y-m-d') !== $project_end_date) {
                return ['valid' => false, 'message' => 'Invalid project end date.'];
            }

            // Check start date is before end date
            if ($startDateTime >= $endDateTime) {
                return ['valid' => false, 'message' => 'Project start date must be before the end date.'];
            }

            // Extract years from dates
            $project_start_year_int = (int) $startDateTime->format('Y');
            $project_end_year_int = (int) $endDateTime->format('Y');

            $cost = null;
            if($temp_status_array[0] === 'renewal') {
                if(isset($payload['project_cost']) && is_numeric($payload['project_cost']) && (float) $payload['project_cost'] >= 0) {
                    $cost = (float) $payload['project_cost'];
                } else {
                    return ['valid' => false, 'message' => 'Project cost is required for renewal and must be a non-negative number.'];

                }
            }

            return [
                'valid' => true,
                'project_name' => $project_name,
                'project_detail' => $project_detail === '' ? null : $project_detail,
                'project_status_type' => $temp_status_array[0],
                'project_start_date' => $project_start_date,
                'project_end_date' => $project_end_date,
                'project_start_year' => $project_start_year_int,
                'project_end_year' => $project_end_year_int,
                'project_cost' => $cost,
                'message' => '',
            ];
        } elseif ($temp_status_array[0] === 'cost_update') {
            $cost = null;
            if(isset($payload['project_cost']) && is_numeric($payload['project_cost']) && (float) $payload['project_cost'] >= 0) {
                $cost = (float) $payload['project_cost'];
            } else {
                return ['valid' => false, 'message' => 'Project cost is required for cost update and must be a non-negative number.'];

            }
            
            return [
                'valid' => true,
                'project_name' => $project_name,
                'project_detail' => $project_detail === '' ? null : $project_detail,
                'project_status_type' => $temp_status_array[0],
                'project_cost' => $cost,
                'message' => '',
            ];
        } else {
            return ['valid' => false, 'message' => 'Invalid project status value.'];

        }
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
