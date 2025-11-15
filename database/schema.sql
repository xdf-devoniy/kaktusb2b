PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL,
    phone TEXT,
    aklad_monthly INTEGER DEFAULT 0,
    rate_per_m2 INTEGER DEFAULT 0,
    rate_per_meter INTEGER DEFAULT 0,
    sales_percent REAL DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS dealers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    region TEXT,
    telegram_name TEXT,
    discount_percent REAL DEFAULT 0,
    credit_limit INTEGER DEFAULT 0,
    status TEXT DEFAULT 'active',
    notes TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS materials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL,
    color TEXT,
    texture TEXT,
    roll_width_m REAL NOT NULL,
    default_price_small_m2 INTEGER DEFAULT 15000,
    default_price_large_m2 INTEGER DEFAULT 25000,
    purchase_price_per_m2 INTEGER DEFAULT 0,
    notes TEXT
);

CREATE TABLE IF NOT EXISTS price_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dealer_id INTEGER,
    material_id INTEGER,
    from_width_m REAL NOT NULL,
    to_width_m REAL NOT NULL,
    price_per_m2 INTEGER NOT NULL,
    is_active INTEGER DEFAULT 1,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id),
    FOREIGN KEY (material_id) REFERENCES materials(id)
);

CREATE TABLE IF NOT EXISTS stock_rolls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    material_id INTEGER NOT NULL,
    roll_width_m REAL NOT NULL,
    length_total_m REAL NOT NULL,
    length_remaining_m REAL NOT NULL,
    purchase_price_per_m INTEGER DEFAULT 0,
    batch_no TEXT,
    received_date TEXT,
    location TEXT,
    status TEXT DEFAULT 'active',
    FOREIGN KEY (material_id) REFERENCES materials(id)
);

CREATE TABLE IF NOT EXISTS stock_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    type TEXT NOT NULL,
    unit TEXT NOT NULL,
    qty_total REAL NOT NULL,
    qty_remaining REAL NOT NULL,
    purchase_price INTEGER DEFAULT 0,
    location TEXT,
    notes TEXT
);

CREATE TABLE IF NOT EXISTS stock_movements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL,
    roll_id INTEGER,
    item_id INTEGER,
    order_id INTEGER,
    order_item_id INTEGER,
    qty REAL NOT NULL,
    reason TEXT,
    user_id INTEGER,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (roll_id) REFERENCES stock_rolls(id),
    FOREIGN KEY (item_id) REFERENCES stock_items(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dealer_id INTEGER NOT NULL,
    global_number TEXT NOT NULL,
    daily_sequence TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    required_date TEXT,
    status TEXT DEFAULT 'draft',
    total_m2 REAL DEFAULT 0,
    total_price INTEGER DEFAULT 0,
    comment TEXT,
    invoice_path TEXT,
    created_by INTEGER,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    name TEXT,
    material_id INTEGER NOT NULL,
    width_m REAL NOT NULL,
    height_m REAL NOT NULL,
    area_m2 REAL NOT NULL,
    perimeter_m REAL DEFAULT 0,
    welding_required INTEGER DEFAULT 0,
    price_per_m2 INTEGER NOT NULL,
    total_price INTEGER NOT NULL,
    sketch_path TEXT,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (material_id) REFERENCES materials(id)
);

CREATE TABLE IF NOT EXISTS production_tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_item_id INTEGER NOT NULL,
    roll_id INTEGER NOT NULL,
    cutting_length_m REAL NOT NULL,
    welding_needed INTEGER DEFAULT 0,
    status TEXT DEFAULT 'pending',
    assigned_to INTEGER,
    started_at TEXT,
    finished_at TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    FOREIGN KEY (roll_id) REFERENCES stock_rolls(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS packing_tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    accessories TEXT,
    status TEXT DEFAULT 'pending',
    packer_id INTEGER,
    packed_at TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (packer_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS shipments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    shipped_at TEXT,
    delivery_type TEXT,
    courier_name TEXT,
    tracking_no TEXT,
    boxes_count INTEGER,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dealer_id INTEGER NOT NULL,
    order_id INTEGER,
    amount INTEGER NOT NULL,
    method TEXT,
    paid_at TEXT DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS work_log_pechat (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    order_id INTEGER,
    order_item_id INTEGER,
    date TEXT NOT NULL,
    quantity_m2 REAL NOT NULL,
    rate_per_m2 INTEGER NOT NULL,
    sum_earned INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id)
);

CREATE TABLE IF NOT EXISTS work_log_garfun (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    order_id INTEGER,
    order_item_id INTEGER,
    date TEXT NOT NULL,
    meters_used REAL NOT NULL,
    rate_per_meter INTEGER NOT NULL,
    sum_earned INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id)
);

CREATE TABLE IF NOT EXISTS work_log_sales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    order_id INTEGER NOT NULL,
    date TEXT NOT NULL,
    order_amount INTEGER NOT NULL,
    percent REAL NOT NULL,
    sum_earned INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
);

INSERT INTO settings(key, value) VALUES ('company_name', 'Kaktus B2B') ON CONFLICT(key) DO NOTHING;
INSERT INTO settings(key, value) VALUES ('working_days_per_month', '26') ON CONFLICT(key) DO NOTHING;

INSERT INTO users (name, username, password_hash, role)
VALUES ('Admin', 'admin', '$2y$12$t9HpCFR2JDB4zBjKDzQI2uT7o3v4MV7mTMBGkzXcKdQs8vlB59RqK', 'admin')
ON CONFLICT(username) DO NOTHING;
