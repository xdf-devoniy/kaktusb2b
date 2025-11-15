<?php

class Dealer
{
    public function __construct(private Database $db)
    {
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM dealers WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND name LIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM dealers WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $dealer = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dealer ?: null;
    }

    public function save(array $data): void
    {
        if (!empty($data['id'])) {
            $stmt = $this->db->getConnection()->prepare('UPDATE dealers SET name=:name, phone=:phone, region=:region, telegram_name=:telegram_name, discount_percent=:discount_percent, credit_limit=:credit_limit, status=:status, notes=:notes WHERE id=:id');
            $stmt->execute($data);
        } else {
            unset($data['id']);
            $stmt = $this->db->getConnection()->prepare('INSERT INTO dealers (name, phone, region, telegram_name, discount_percent, credit_limit, status, notes) VALUES (:name, :phone, :region, :telegram_name, :discount_percent, :credit_limit, :status, :notes)');
            $stmt->execute($data);
        }
    }
}
