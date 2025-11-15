<?php

class Order
{
    public function __construct(private Database $db)
    {
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT o.*, d.name AS dealer_name FROM orders o JOIN dealers d ON d.id = o.dealer_id WHERE 1=1';
        $params = [];

        if (!empty($filters['dealer_id'])) {
            $sql .= ' AND o.dealer_id = :dealer_id';
            $params['dealer_id'] = $filters['dealer_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND o.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= ' AND date(o.created_at) >= :from_date';
            $params['from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= ' AND date(o.created_at) <= :to_date';
            $params['to_date'] = $filters['to_date'];
        }

        $sql .= ' ORDER BY o.created_at DESC';
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT o.*, d.name AS dealer_name, d.phone AS dealer_phone FROM orders o JOIN dealers d ON d.id = o.dealer_id WHERE o.id = :id');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    public function items(int $orderId): array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT oi.*, m.code AS material_code FROM order_items oi JOIN materials m ON m.id = oi.material_id WHERE order_id = :id');
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $orderData, array $items): int
    {
        $conn = $this->db->getConnection();
        $conn->beginTransaction();

        $stmt = $conn->prepare('INSERT INTO orders (dealer_id, global_number, daily_sequence, required_date, status, comment, created_by, total_m2, total_price) VALUES (:dealer_id, :global_number, :daily_sequence, :required_date, :status, :comment, :created_by, :total_m2, :total_price)');
        $stmt->execute($orderData);
        $orderId = (int)$conn->lastInsertId();

        $itemStmt = $conn->prepare('INSERT INTO order_items (order_id, name, material_id, width_m, height_m, area_m2, perimeter_m, welding_required, price_per_m2, total_price, sketch_path, notes) VALUES (:order_id, :name, :material_id, :width_m, :height_m, :area_m2, :perimeter_m, :welding_required, :price_per_m2, :total_price, :sketch_path, :notes)');

        foreach ($items as $item) {
            $item['order_id'] = $orderId;
            $itemStmt->execute($item);
        }

        $conn->commit();
        return $orderId;
    }
}
