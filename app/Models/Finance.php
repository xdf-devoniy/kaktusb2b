<?php

class Finance
{
    public function __construct(private Database $db)
    {
    }

    public function addPayment(array $data): void
    {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO payments (dealer_id, order_id, amount, method, paid_at, user_id) VALUES (:dealer_id, :order_id, :amount, :method, :paid_at, :user_id)');
        $stmt->execute($data);
    }

    public function latestPayments(int $limit = 10): array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT p.*, d.name AS dealer_name, o.global_number FROM payments p LEFT JOIN dealers d ON d.id = p.dealer_id LEFT JOIN orders o ON o.id = p.order_id ORDER BY p.paid_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dealerBalances(): array
    {
        $sql = 'SELECT d.id, d.name,
                IFNULL(SUM(o.total_price),0) AS total_orders,
                IFNULL((SELECT SUM(amount) FROM payments WHERE dealer_id = d.id),0) AS total_paid,
                IFNULL(SUM(o.total_price),0) - IFNULL((SELECT SUM(amount) FROM payments WHERE dealer_id = d.id),0) AS balance
                FROM dealers d
                LEFT JOIN orders o ON o.dealer_id = d.id
                GROUP BY d.id
                ORDER BY balance DESC';
        $stmt = $this->db->getConnection()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
