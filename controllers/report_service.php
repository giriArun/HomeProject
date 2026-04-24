<?php
declare(strict_types=1);

final class ReportService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getRecentReports(?int $report_id = 0): array
    {
        $search = '';
        $search = trim((string) $search);
        $sql = '
            SELECT dr.daily_report_id, dr.price, dr.date, dr.tags, dr.is_credit,
                c.customer_name, 
                p.project_name,
                u.user_name,
                TIMESTAMPDIFF(MINUTE, dr.modified, now()) AS minute_diff
            FROM daily_reports AS dr
            INNER JOIN projects AS p ON dr.project_id = p.project_id
            LEFT JOIN customers AS c ON dr.customer_id = c.customer_id
            LEFT JOIN users AS u ON dr.user_id = u.user_id
            ';

        $types = '';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE dr.notes LIKE ?';
            $keyword = '%' . $search . '%';
            $types = 's';
            $params = [$keyword];
        }

        $sql .= ' ORDER BY dr.modified DESC, dr.created DESC LIMIT 10';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [];
        }

        if ($types !== '') {
            mysqli_stmt_bind_param($statement, $types, ...$params);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $reports = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($statement);

        return $reports ?: [];
    }

    public function getReportById(?int $report_id = null): ?array
    {
        if ($report_id <= 0) {
            return null;
        }

        $sql = 'SELECT report_id, costs, customer_id, project_id, user_id, tags, notes, type, created, modified
                FROM daily_reports
                WHERE report_id = ?
                LIMIT 1';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'i', $report_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $report = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        return $report ?: null;
    }

    public function getAllCustomers(): array
    {
        $sql = 'SELECT customer_id, customer_name, customer_address FROM customers WHERE is_active = 1 ORDER BY customer_name ASC';
        $result = mysqli_query($this->connection, $sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    public function saveDailyReport(array $payload, int $user_id): array
    {
        $normalized = $this->normalizePayload($payload);
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'report_id' => null,
            ];
        }

        $report_id = $normalized['report_id'];

        if ($normalized['customer_id'] < 0) {
             $customer_id = $this->createCustomer($normalized['customer_name'], $normalized['customer_address'], $normalized['customer_phone'], $user_id);
             if ($customer_id === null) {
                 return [
                     'success' => false,
                     'message' => 'Failed to create customer.',
                     'report_id' => null,
                 ];
             }
             $normalized['customer_id'] = $customer_id; // Update normalized payload with new customer ID
        }

        if ($report_id > 0) {
            print('Updating report ID: ' . $report_id);exit;
            return $this->updateReport($normalized, $payload);
        } else {
            return $this->createReport($normalized, $user_id);
        }
    }

    private function createReport(array $normalized, int $user_id): array
    {
        $this->setProjectTagLogs($normalized['project_id'], $user_id, $normalized['tags']);
        
        $sql = 'INSERT INTO daily_reports (price, date, customer_id, project_id, user_id, tags, notes, created_by, is_credit)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare statement.',
                'report_id' => null,
            ];
        }

        $unique_tags = array_unique(array_merge($normalized['tags'], $normalized['other_tags'] ? explode(',', $normalized['other_tags']) : []));
        $tags = implode(',', $unique_tags);

       // $tags = $normalized['tags'] ? implode(',', $normalized['tags']) : '';
        //$tags = $normalized['other_tags'] ? ($tags ? $tags . ',' : '') . $normalized['other_tags'] : $tags;

        mysqli_stmt_bind_param($statement, 'isiiissii',
            $normalized['price'],
            $normalized['date'],
            $normalized['customer_id'],
            $normalized['project_id'],
            $normalized['user_id'],
            $tags,
            $normalized['notes'],
            $user_id,
            $normalized['is_credit']
        );

        $success = mysqli_stmt_execute($statement);
        $report_id = $success ? mysqli_insert_id($this->connection) : null;
        mysqli_stmt_close($statement);

        return [
            'success' => $success,
            'message' => $success ? 'Report created successfully.' : 'Failed to create report.',
            'report_id' => $report_id,
        ];
    }

    private function updateReport(int $report_id, array $payload): array
    {
        if ($report_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid report ID.',
                'report_id' => null,
            ];
        }

        $normalized = $this->normalizePayload($payload);
        if ($normalized['valid'] === false) {
            return [
                'success' => false,
                'message' => $normalized['message'],
                'report_id' => null,
            ];
        }

        $sql = 'UPDATE daily_reports
                SET costs = ?, customer_id = ?, project_id = ?, user_id = ?, tags = ?, notes = ?, type = ?, modified = CURRENT_TIMESTAMP
                WHERE report_id = ?';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare statement.',
                'report_id' => null,
            ];
        }

        $tagsJson = $normalized['tags'] ? implode(',', $normalized['tags']) : null;

        mysqli_stmt_bind_param($statement, 'diiisssi',
            $normalized['costs'],
            $normalized['customer_id'],
            $normalized['project_id'],
            $normalized['user_id'],
            $tagsJson,
            $normalized['notes'],
            $normalized['type'],
            $report_id
        );

        $success = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return [
            'success' => $success,
            'message' => $success ? 'Report updated successfully.' : 'Failed to update report.',
            'report_id' => $report_id,
        ];
    }

    private function createCustomer(string $name, string $address, string $phone, int $user_id): ?int
    {
        $sql = 'INSERT INTO customers (customer_name, customer_address, customer_phone, created_by) VALUES (?, ?, ?, ?)';
        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'sssi', $name, $address, $phone, $user_id);
        $success = mysqli_stmt_execute($statement);
        $customer_id = $success ? mysqli_insert_id($this->connection) : null;
        mysqli_stmt_close($statement);

        return $customer_id;
    }

    private function setProjectTagLogs(int $project_id, int $user_id, array $tags): void
    {
        if (!empty($tags)) {
            $sqlInsert = 'INSERT INTO projects_tag_logs (tag_name, project_id, created_by) VALUES (?, ?, ?)';
            $stmtInsert = mysqli_prepare($this->connection, $sqlInsert);
            if ($stmtInsert) {
                foreach ($tags as $tag) {
                    mysqli_stmt_bind_param($stmtInsert, 'sii', $tag, $project_id, $user_id);
                    mysqli_stmt_execute($stmtInsert);
                }
                mysqli_stmt_close($stmtInsert);
            }
        }
    }

    private function normalizePayload(array $payload): array
    {
        $price = isset($payload['price']) ? (float) $payload['price'] : 0;
        if ($price <= 0) {
            return ['valid' => false, 'message' => 'Price must be greater than 0.'];
        }

        $date = isset($payload['date']) ? trim($payload['date']) : date('Y-m-d');
        if ($date !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                return ['valid' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD.'];
            }
        } else {
            $date = date('Y-m-d');
        }

        $customer_id = isset($payload['customer']) && $payload['customer'] !== '' ? (int) $payload['customer'] : 0;
        if ($customer_id !== null && $customer_id < 0) {
            if (!isset($payload['customer_name']) || trim($payload['customer_name']) === '') {
                return ['valid' => false, 'message' => 'Customer name is required for new customers.'];
            }
        }

        $project_id = isset($payload['project']) ? (int) $payload['project'] : 0;
        if ($project_id <= 0) {
            return ['valid' => false, 'message' => 'Please select a project.'];
        }

        $user_id = isset($payload['user']) && $payload['user'] !== '' ? (int) $payload['user'] : null;

        $tags_key = 'tags' . $project_id;
        $tags_array = isset($payload[$tags_key]) && is_array($payload[$tags_key]) ? $payload[$tags_key] : [];
        $tags = array_filter($tags_array, fn($tag) => strlen($tag) > 0 && $tag > 0);

        if( in_array("-1", $tags_array) > 0 ){
            $other_tags_key = 'tagName' . $project_id;
            $other_tags = isset($payload[$other_tags_key]) ? trim((string)$payload[$other_tags_key]) : '';
        } else {
            $other_tags = '';

        }

        $notes = isset($payload['notes']) ? trim((string) $payload['notes']) : '';

        $type = isset($payload['credit']) ? $payload['credit'] : '';
        if (!in_array($type, ['1', '0'])) {
            return ['valid' => false, 'message' => 'Invalid action.'];
        }

        return [
            'valid' => true,
            'report_id' => isset($payload['report_id']) ? (int) $payload['report_id'] : 0,
            'price' => $price,
            'date' => $date,
            'customer_id' => $customer_id,
            'customer_name' => isset($payload['customer_name']) ? trim((string) $payload['customer_name']) : '',
            'customer_address' => isset($payload['customer_address']) ? trim((string) $payload['customer_address']) : '',
            'customer_phone' => isset($payload['customer_phone']) ? trim((string) $payload['customer_phone']) : '',
            'project_id' => $project_id,
            'user_id' => $user_id,
            'tags' => $tags,
            'other_tags' => $other_tags,
            'notes' => $notes,
            'is_credit' => $type === '1' ? 1 : 0,  
        ];
    }
}