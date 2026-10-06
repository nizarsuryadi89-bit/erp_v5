-- Additive only: no UPDATE/ALTER/DELETE on existing ERP tables.
CREATE TABLE IF NOT EXISTS tbl_wip_qc (
    qc_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    produksi_id BIGINT UNSIGNED NOT NULL,
    target_qty DECIMAL(18,4) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'pending',
    version INT UNSIGNED NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (qc_id),
    UNIQUE KEY uq_wip_qc_production (produksi_id),
    KEY ix_wip_qc_queue (status, qc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tbl_wip_qc_inspection (
    inspection_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    qc_id BIGINT UNSIGNED NOT NULL,
    revision INT UNSIGNED NOT NULL,
    accepted_qty DECIMAL(18,4) NOT NULL,
    rejected_qty DECIMAL(18,4) NOT NULL,
    decision VARCHAR(16) NOT NULL,
    note TEXT NOT NULL,
    request_reprint TINYINT UNSIGNED NOT NULL DEFAULT 0,
    inspector_user_id BIGINT UNSIGNED NOT NULL,
    request_key VARCHAR(64) NOT NULL,
    payload_hash CHAR(64) NOT NULL,
    inspected_at DATETIME NOT NULL,
    PRIMARY KEY (inspection_id),
    UNIQUE KEY uq_wip_qc_revision (qc_id, revision),
    UNIQUE KEY uq_wip_qc_request (request_key),
    KEY ix_wip_qc_reprint (request_reprint, inspection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
