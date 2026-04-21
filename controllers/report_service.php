<?php
declare(strict_types=1);

final class ReportService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getAllReports(?string $search = null): array
    {
        $search = trim((string) $search);
        $sql = 'SELECT report_id, costs, customer_id, project_id, user_id, tags, notes, type, created, modified
                FROM daily_reports';

        $types = '';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE notes LIKE ?';
            $keyword = '%' . $search . '%';
            $types = 's';
            $params = [$keyword];
        }

        $sql .= ' ORDER BY created DESC';

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
print_r($payload);
        if ($report_id > 0) {
            return $this->updateReport($normalized, $payload);
        } else {
            return $this->createReport($normalized);
        }
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

    private function createReport(array $normalized): array
    {
        $sql = 'INSERT INTO daily_reports (price, date, customer_id, project_id, user_id)
                VALUES (?, ?, ?, ?, ?)';

        $statement = mysqli_prepare($this->connection, $sql);
        if (!$statement) {
            return [
                'success' => false,
                'message' => 'Failed to prepare statement.',
                'report_id' => null,
            ];
        }

        $tagsJson = $normalized['tags'] ? json_encode($normalized['tags']) : null;

        mysqli_stmt_bind_param($statement, 'isiii',
            $normalized['price'],
            $normalized['date'],
            $normalized['customer_id'],
            $normalized['project_id'],
            $normalized['user_id']
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

        $tagsJson = $normalized['tags'] ? json_encode($normalized['tags']) : null;

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

        $tags = isset($payload['tags']) && is_array($payload['tags']) ? $payload['tags'] : [];
        $tags = array_filter($tags, fn($tag) => is_numeric($tag) && $tag > 0);

        $notes = isset($payload['notes']) ? trim((string) $payload['notes']) : '';

        $type = isset($payload['credit']) ? $payload['credit'] : '';
        if (!in_array($type, ['1', '0'])) {
            return ['valid' => false, 'message' => 'Invalid action.'];
        }
        $type = $type === 'receive' ? '1' : '0';

        /*
        
            $customer_name = isset($payload['customerName']) ? trim((string) $payload['customerName']) : '';
            $customer_address = isset($payload['customerAddress']) ? trim((string) $payload['customerAddress']) : '';
            $customer_phone = isset($payload['customerPhone']) ? trim((string) $payload['customerPhone']) : '';
        */
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
            'notes' => $notes,
            'type' => $type,
        ];
    }
}