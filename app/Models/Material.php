<?php

class Material
{
    public function __construct(private Database $db)
    {
    }

    public function all(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT * FROM materials ORDER BY code');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM materials WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);
        return $material ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO materials (code, color, texture, roll_width_m, default_price_small_m2, default_price_large_m2, purchase_price_per_m2, notes) ' .
            'VALUES (:code, :color, :texture, :roll_width_m, :default_price_small_m2, :default_price_large_m2, :purchase_price_per_m2, :notes)';
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($data);
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $sql = 'UPDATE materials SET code = :code, color = :color, texture = :texture, roll_width_m = :roll_width_m, ' .
            'default_price_small_m2 = :default_price_small_m2, default_price_large_m2 = :default_price_large_m2, purchase_price_per_m2 = :purchase_price_per_m2, notes = :notes ' .
            'WHERE id = :id';
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->getConnection()->prepare('DELETE FROM materials WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
