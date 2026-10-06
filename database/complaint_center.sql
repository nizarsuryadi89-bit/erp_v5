-- Additive migration. Back up the ERP database and import this file once.
-- All new tables use InnoDB; no existing table or production data is changed.
CREATE TABLE IF NOT EXISTS tbl_wa_complaint_messages (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  perusahaan_id INT NOT NULL,
  source VARCHAR(20) NOT NULL,
  external_key CHAR(64) NOT NULL,
  conversation VARCHAR(191) NOT NULL,
  sender VARCHAR(191) NOT NULL,
  sent_at DATETIME NOT NULL,
  body TEXT NOT NULL,
  state VARCHAR(20) NOT NULL DEFAULT 'pending',
  classification VARCHAR(20) DEFAULT NULL,
  category VARCHAR(40) DEFAULT NULL,
  priority VARCHAR(10) DEFAULT NULL,
  summary TEXT DEFAULT NULL,
  analysis_method VARCHAR(20) DEFAULT NULL,
  analysis_error VARCHAR(191) DEFAULT NULL,
  ticket_id BIGINT UNSIGNED DEFAULT NULL,
  attempts INT NOT NULL DEFAULT 0,
  locked_at DATETIME DEFAULT NULL,
  lock_token CHAR(32) DEFAULT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_wa_source (perusahaan_id, external_key),
  KEY idx_wa_queue (state, locked_at, id),
  KEY idx_wa_branch (perusahaan_id, sent_at),
  KEY idx_wa_ticket (perusahaan_id, ticket_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_complaint (
  complaint_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_no VARCHAR(50) NOT NULL,
  perusahaan_id INT NOT NULL,
  wa_message_id BIGINT UNSIGNED DEFAULT NULL,
  reported_at DATETIME NOT NULL,
  pelapor VARCHAR(191) NOT NULL,
  judul VARCHAR(191) NOT NULL,
  deskripsi TEXT NOT NULL,
  ai_summary TEXT DEFAULT NULL,
  analysis_method VARCHAR(20) NOT NULL DEFAULT 'manual',
  needs_review TINYINT NOT NULL DEFAULT 1,
  kategori VARCHAR(40) NOT NULL,
  priority VARCHAR(10) NOT NULL DEFAULT 'MEDIUM',
  status VARCHAR(20) NOT NULL DEFAULT 'OPEN',
  mesin_id INT DEFAULT NULL,
  pic_id INT DEFAULT NULL,
  assigned_at DATETIME DEFAULT NULL,
  resolved_at DATETIME DEFAULT NULL,
  closed_at DATETIME DEFAULT NULL,
  resolution TEXT DEFAULT NULL,
  root_cause TEXT DEFAULT NULL,
  preventive_action TEXT DEFAULT NULL,
  created_by INT DEFAULT NULL,
  version INT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (complaint_id),
  UNIQUE KEY uq_ticket_no (ticket_no),
  UNIQUE KEY uq_ticket_message (wa_message_id),
  KEY idx_complaint_branch (perusahaan_id, reported_at, status),
  KEY idx_complaint_pic (perusahaan_id, pic_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_complaint_spk (
  complaint_id BIGINT UNSIGNED NOT NULL,
  perusahaan_id INT NOT NULL,
  nospk VARCHAR(50) NOT NULL,
  PRIMARY KEY (complaint_id, nospk),
  KEY idx_complaint_spk_branch (perusahaan_id, nospk),
  CONSTRAINT fk_complaint_spk FOREIGN KEY (complaint_id)
    REFERENCES tbl_complaint (complaint_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_complaint_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  complaint_id BIGINT UNSIGNED NOT NULL,
  perusahaan_id INT NOT NULL,
  actor_id INT DEFAULT NULL,
  event VARCHAR(30) NOT NULL,
  old_status VARCHAR(20) DEFAULT NULL,
  new_status VARCHAR(20) DEFAULT NULL,
  note TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_history_ticket (perusahaan_id, complaint_id, id),
  CONSTRAINT fk_complaint_history FOREIGN KEY (complaint_id)
    REFERENCES tbl_complaint (complaint_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
