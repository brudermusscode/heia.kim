--
-- Table structure for table `order_paypal`
--

CREATE TABLE `order_paypal` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `paypal_order_id` varchar(256) NOT NULL,
  `paypal_payer_id` varchar(256) DEFAULT NULL,
  `paypal_order_status` varchar(256) NOT NULL,
  `paypal_url_order` text,
  `paypal_url_approve` text,
  `paypal_url_update` text,
  `paypal_url_capture` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for table `order_paypal`
--
ALTER TABLE `order_paypal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `paypal_order_id` (`paypal_order_id`),
  ADD KEY `paypal_payer_id` (`paypal_payer_id`);

--
-- AUTO_INCREMENT for table `order_paypal`
--
ALTER TABLE `order_paypal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;
