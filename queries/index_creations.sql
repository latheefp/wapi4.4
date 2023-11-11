CREATE INDEX idx_created ON streams (created);

CREATE INDEX idx_status ON rcv_queues(status);
CREATE INDEX idx_status ON send_queues(status);