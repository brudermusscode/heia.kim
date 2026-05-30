  ALTER TABLE orders
  ADD order_pieces INT DEFAULT 1 AFTER order_amount;