<?php

declare(strict_types=1);

class TableDAO
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy một bàn theo mã định danh.
     */
    public function getTableById($tableId)
    {
        $sql = 'SELECT id, table_code, capacity, status
                FROM restaurant_tables
                WHERE id = ?';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tableId]);

        return $stmt->fetch();
    }

    /**
     * Lấy toàn bộ danh sách bàn.
     */
    public function getAllTables()
    {
        $sql = 'SELECT id, table_code, capacity, status
                FROM restaurant_tables';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái bàn.
     */
    public function updateTableStatus($tableId, $status)
    {
        $sql = 'UPDATE restaurant_tables
                SET status = ?
                WHERE id = ?';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$status, $tableId]);
    }
}