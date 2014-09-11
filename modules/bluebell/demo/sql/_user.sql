TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `phone`, `mobile`, `company`, `position`, `street_addr`, `city`, `state_id`, `zip`, `country_id`, `created_user_id`, `updated_user_id`, `created_at`, `deleted_at`, `guest`, `enabled`) VALUES
(1, 'Customer', 'Customer', 'Person', 'client@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, now(), NULL, NULL, NULL),
(2, 'Provider', 'Carpenter', 'Man', 'provider@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2012-07-11 01:50:38', NULL, NULL, NULL),
(3, 'Sample1', 'Joe', 'Bloggs', 'sample1@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, now(), NULL, NULL, NULL),
(4, 'Sample2', 'Hardy', 'Swift', 'sample2@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, now(), NULL, NULL, NULL),
(5, 'Sample3', 'Fred', 'Savage', 'sample3@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, now(), NULL, NULL, NULL),
(6, 'Electrician', 'Guy', 'Man', 'provider2@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2012-02-16 01:55:38', NULL, NULL, NULL),
(7, 'Spark', 'Boy', 'Man', 'provider3@demo.scriptsahoy.com', 'd5ad876d23d267156032d9f46e7cb84c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2012-05-11 01:50:38', NULL, NULL, NULL);

TRUNCATE TABLE `user_messages`;
INSERT INTO `user_messages` (`id`, `from_user_id`, `message`, `subject`, `master_object_class`, `master_object_id`, `sent_at`, `thread_id`, `is_latest`, `deleted_at`) VALUES
(1, 4, 'Thanks for your quote. I also have a light fitting that needs fixing, can you help me with this?', NULL, 'Service_Quote', 1, now(), NULL, 1, NULL);

TRUNCATE TABLE `user_message_recipients`;
INSERT INTO `user_message_recipients` (`message_id`, `user_id`, `is_new`, `deleted_at`) VALUES
(1, 2, NULL, NULL),
(1, 4, 1, NULL);
