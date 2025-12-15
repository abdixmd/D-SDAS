-- D-SDAS STANDALONE SCHEMA
-- ------------------------

-- 1. USERS (Just Identity & Rank)
CREATE TYPE user_role AS ENUM ('student', 'admin');

CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role user_role DEFAULT 'student',
    
    -- THE CRITICAL METRIC
    admission_rank INT, -- Example: Rank #450
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. DEPARTMENTS (The Options)
CREATE TABLE departments (
    dept_id SERIAL PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL UNIQUE, -- e.g., "Computer Science"
    base_capacity INT NOT NULL, -- e.g., 200 seats
    current_locked INT DEFAULT 0 -- Live counter for speed
);

-- 3. ADMISSION ROUNDS (The Time Windows)
CREATE TYPE round_status AS ENUM ('scheduled', 'active', 'sealed', 'completed');

CREATE TABLE admission_rounds (
    round_id SERIAL PRIMARY KEY,
    round_name VARCHAR(50) NOT NULL, -- e.g., "Round 1 - Merit"
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    status round_status DEFAULT 'scheduled'
);

-- 4. ALLOCATIONS (The Student's Choice)
CREATE TABLE allocations (
    allocation_id SERIAL PRIMARY KEY,
    user_id INT UNIQUE REFERENCES users(user_id),
    dept_id INT REFERENCES departments(dept_id),
    round_id INT REFERENCES admission_rounds(round_id),
    
    -- COOLDOWN TRACKING
    locked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_finalized BOOLEAN DEFAULT FALSE -- True when round closes
);

-- 5. PREDICTION STATS (Data for the Algorithm)
CREATE TABLE prediction_stats (
    stat_id SERIAL PRIMARY KEY,
    dept_id INT REFERENCES departments(dept_id),
    historical_cutoff_rank INT, -- Last year's cutoff
    implicit_demand_factor DECIMAL(5, 2) DEFAULT 1.0 -- e.g., 1.2 = 20% higher interest
);