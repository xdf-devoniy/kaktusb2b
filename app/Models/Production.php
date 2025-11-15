<?php

class Production
{
    public function __construct(private Database $db)
    {
    }

    public function cuttingTasks(): array
    {
        $sql = 'SELECT pt.*, oi.name AS room_name, o.global_number, m.code AS material_code
                FROM production_tasks pt
                JOIN order_items oi ON oi.id = pt.order_item_id
                JOIN orders o ON o.id = oi.order_id
                JOIN materials m ON m.id = oi.material_id
                WHERE pt.status != "finished"
                ORDER BY pt.created_at DESC';
        $stmt = $this->db->getConnection()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function packingTasks(): array
    {
        $sql = 'SELECT pt.*, o.global_number, d.name AS dealer_name, o.total_m2
                FROM packing_tasks pt
                JOIN orders o ON o.id = pt.order_id
                JOIN dealers d ON d.id = o.dealer_id
                WHERE pt.status IN ("pending", "packed")
                ORDER BY pt.id DESC';
        $stmt = $this->db->getConnection()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
