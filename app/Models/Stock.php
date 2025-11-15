<?php

class Stock
{
    public function __construct(private Database $db)
    {
    }

    public function rolls(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT sr.*, m.code AS material_code FROM stock_rolls sr JOIN materials m ON m.id = sr.material_id ORDER BY sr.id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function items(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT * FROM stock_items ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
