-- SQL Script untuk Reset PB Counter ke 4589
-- Jalankan di phpMyAdmin atau MySQL command line

USE sirpo;

-- Update counter untuk tanggal hari ini
UPDATE pb_counters
SET counter_value = 4589, updated_at = NOW()
WHERE counter_date = '2025-08-15';

-- Jika record belum ada, insert baru
INSERT IGNORE INTO pb_counters (counter_date, counter_value, created_at, updated_at)
VALUES ('2025-08-15', 4589, NOW(), NOW());

-- Verifikasi hasil
SELECT counter_date, counter_value
FROM pb_counters
WHERE counter_date = '2025-08-15';

-- Keterangan:
-- Counter value 4589 berarti PB selanjutnya akan bernomor 4590
