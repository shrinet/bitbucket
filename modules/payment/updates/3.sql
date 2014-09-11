INSERT INTO `payment_invoice_statuses` (`id`, `code`, `name`, `notify_user`, `enabled`, `notify_recipient`, `user_message_template_id`) 
VALUES
  (1, 'new', 'Unpaid', NULL, NULL, NULL, 3),
  (2, 'paid', 'Paid', 1, NULL, 1, 3);