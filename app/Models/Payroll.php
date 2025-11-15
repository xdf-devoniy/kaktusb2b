<?php

class Payroll
{
    public function __construct(private Database $db)
    {
    }

    public function addPechat(array $data): void
    {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO work_log_pechat (user_id, order_id, order_item_id, date, quantity_m2, rate_per_m2, sum_earned) VALUES (:user_id, :order_id, :order_item_id, :date, :quantity_m2, :rate_per_m2, :sum_earned)');
        $stmt->execute($data);
    }

    public function addGarfun(array $data): void
    {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO work_log_garfun (user_id, order_id, order_item_id, date, meters_used, rate_per_meter, sum_earned) VALUES (:user_id, :order_id, :order_item_id, :date, :meters_used, :rate_per_meter, :sum_earned)');
        $stmt->execute($data);
    }

    public function pechatLogs(string $date): array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT w.*, u.name AS worker_name FROM work_log_pechat w JOIN users u ON u.id = w.user_id WHERE date = :date ORDER BY w.id DESC');
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function garfunLogs(string $date): array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT w.*, u.name AS worker_name FROM work_log_garfun w JOIN users u ON u.id = w.user_id WHERE date = :date ORDER BY w.id DESC');
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
