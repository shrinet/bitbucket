TRUNCATE TABLE `service_requests_providers`;
TRUNCATE TABLE `service_providers`;
INSERT INTO `service_providers` (`id`, `latitude`, `longitude`, `moderation_status`, `user_id`, `business_name`, `url_name`, `description`, `url`, `established`, `phone`, `mobile`, `street_addr`, `city`, `zip`, `country_id`, `state_id`, `rating`, `rating_all`, `rating_num`, `rating_all_num`, `stat_offers`, `stat_quotes`, `stat_quote_average`, `stat_wins`, `stat_earned`, `config_data`, `created_at`, `updated_at`, `created_user_id`, `updated_user_id`, `role_name`, `description_experience`, `description_speciality`, `description_why_us`, `service_codes`, `service_radius`) VALUES
(1, '-33.871066', '151.208055', NULL, 2, 'A1 Carpentry', 'a1-carpentry', 'We are carpenters who specialise in all sorts of carptenry.\n\nDecks, pergolas, kitchens, you name it!\n\nCustomer''s love us because we are always on time and show up to the job with a smile!', NULL, NULL, '02 6111 0000', '0404 0404 0404', 'Some place', 'Sydney', '2000', 3, 73, NULL, NULL, NULL, NULL, 0, 0, '0.00', 0, NULL, '<?xml version="1.0"?>\n<data><field><id>schedule_0_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_0_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_1_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_1_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_2_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_2_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_3_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_3_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_4_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_4_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_5_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_5_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_6_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_6_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field></data>\n', '2012-07-11 01:52:29', now(), 1, 1, 'Carpenter', NULL, NULL, NULL, '|2000|2011|2009|2004|1300|1335|1340|1350|1355|1360|1363|2007|2010|2060|2061|2001|2041|2037|2008|2027|2059|2021|2016|2006|2089|2039|2028|2065|2050|2025|2090|2017|2040|2023|2043|2110|2038|1585|2048|2042|2022|2088|2066|2062|2015|2029|1470|2047|2052|1515|1560|1565|1445|2018|1465|2033|2031|1570|2064|2049|2026|2063|2045|1466|2024|2030|1590|1595|1475|2204|2032|2046|2044|2130|2020|2068|1460|2203|2092|2093|2057|2034|1670|1675|2111|2067|2137|1800|2131|2132|2035|2019|2193|2069|2036|2139|2205|2206|2135|2136|2070|2094|1655|1805|2134|2133|2095|2216|2112|2194|2113|2087|1450|1455|2138|1680|1685|1435|2140|1640|2071|2100|2207|2217|2191|2192|2096|2195|2208|2086|2073|2114|2109|2122|2218|2141|2127|2190|2072|2219|1710|2121|2220|2085|2115|2221|2209|2099|2196|1480|1481|1485|2128|2222|2223|1825|1835|2144|2074|2116|2075|2231|2097|2143|2200|1885|2129|2210|2118|2117|2199|2142|2076|2119|2211|2077|2229|1811|1851|2120|2162|2224|2150|2101|2225|2226|2197|1715|2212|2123|2124|2151|2084|2125|2161|1630|1635|1639|1490|2228|2160|2230|2198|2102|2163|2145|1700|1701|1790|1495|2227|2126|2213|2079|2214|2012|1499|2152|1750|1755|2153|2172|1660|2103|1860|2165|2104|1765|2154|2106|2080|2146|', '20'),
(2, '-33.871066', '151.208055', NULL, 2, 'Awesome Electrics', 'awesome-electrics', 'We are electricians who specialise in all sorts of electrical installs', NULL, NULL, '02 6111 0000', '0404 0404 0404', 'Some place', 'Paddington', '2000', 3, 73, NULL, NULL, NULL, NULL, 0, 0, '0.00', 0, '0.00', '<?xml version="1.0"?>\n<data><field><id>schedule_0_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_0_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_1_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_1_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_2_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_2_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_3_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_3_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_4_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_4_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_5_start</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_5_end</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_6_start</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_6_end</id><value><![CDATA[s:0:"";]]></value></field></data>\n', now(), now(), 1, 1, 'Electrician', NULL, NULL, NULL, '|2000|2011|2009|2004|1300|1335|1340|1350|1355|1360|1363|2007|2010|2060|2061|2001|2041|2037|2008|2027|2059|2021|2016|2006|2089|2039|2028|2065|2050|2025|2090|2017|2040|2023|2043|2110|2038|1585|2048|2042|2022|2088|2066|2062|2015|2029|1470|2047|2052|1515|1560|1565|1445|2018|1465|2033|2031|1570|2064|2049|2026|2063|2045|1466|2024|2030|1590|1595|1475|2204|2032|2046|2044|2130|2020|2068|1460|2203|2092|2093|2057|2034|1670|1675|2111|2067|2137|1800|2131|2132|2035|2019|2193|2069|2036|2139|2205|2206|2135|2136|2070|2094|1655|1805|2134|2133|2095|2216|2112|2194|2113|2087|1450|1455|2138|1680|1685|1435|2140|1640|2071|2100|2207|2217|2191|2192|2096|2195|2208|2086|2073|2114|2109|2122|2218|2141|2127|2190|2072|2219|1710|2121|2220|2085|2115|2221|2209|2099|2196|1480|1481|1485|2128|2222|2223|1825|1835|2144|2074|2116|2075|2231|2097|2143|2200|1885|2129|2210|2118|2117|2199|2142|2076|2119|2211|2077|2229|1811|1851|2120|2162|2224|2150|2101|2225|2226|2197|1715|2212|2123|2124|2151|2084|2125|2161|1630|1635|1639|1490|2228|2160|2230|2198|2102|2163|2145|1700|1701|1790|1495|2227|2126|2213|2079|2214|2012|1499|2152|1750|1755|2153|2172|1660|2103|1860|2165|2104|1765|2154|2106|2080|2146|', '20'),
(3, '-33.871066', '151.208055', NULL, 6, 'Wiring Solutions', 'wiring-solutions', NULL, NULL, NULL, '0404 123 456', '0404 123 456', '415 Kent Street', 'New Farm', '4005', 3, 74, NULL, NULL, NULL, NULL, 0, 0, '0.00', 0, '0.00', '<?xml version="1.0"?>\n<data><field><id>schedule_0_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_0_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_1_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_1_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_2_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_2_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_3_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_3_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_4_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_4_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_5_start</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_5_end</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_6_start</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_6_end</id><value><![CDATA[s:0:"";]]></value></field></data>\n', now(), now(), 1, 1, 'Electrician', NULL, NULL, NULL, '|4005|4002|4003|4004|4169|4171|4170|4000|4006|4060|4102|4101|4151|4007|4010|4120|4059|4001|4072|4064|4009|4103|4051|4030|4067|4104|4011|4121|4172|4152|4065|4031|4105|4068|4153|4013|4122|4173|4012|4008|4066|4174|4111|4107|4075|4106|4053|4032|4014|4061|4108|4054|4034|4154|4178|4155|4073|4109|4179|4110|4076|4113|4123|4018|4055|4035|4157|4069|4156|4074|4017|4158|4077|4112|4119|4127|4159|4116|4037|4036|4115|4078|4114|4117|4161|4070|4118|4160|4501|4300|4500|4301|4163|4502|4164|4131|4132|4303|4128|', '20'),
(4, '-33.871066', '151.208055', NULL, 7, 'Another Electrician', 'another-electrician', 'We are sparkies who specialise in all sorts of electrical installs', NULL, NULL, '02 6111 0000', '0404 0404 0404', 'Some place', 'Lilyfield', '2000', 3, 73, NULL, NULL, NULL, NULL, 0, 0, '0.00', 0, '0.00', '<?xml version="1.0"?>\n<data><field><id>schedule_0_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_0_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_1_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_1_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_2_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_2_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_3_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_3_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_4_start</id><value><![CDATA[s:8:"08:00:00";]]></value></field><field><id>schedule_4_end</id><value><![CDATA[s:8:"18:00:00";]]></value></field><field><id>schedule_5_start</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_5_end</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_6_start</id><value><![CDATA[s:0:"";]]></value></field><field><id>schedule_6_end</id><value><![CDATA[s:0:"";]]></value></field></data>\n', now(), now(), 1, 1, 'Electrician', NULL, NULL, NULL, '|2000|2011|2009|2004|1300|1335|1340|1350|1355|1360|1363|2007|2010|2060|2061|2001|2041|2037|2008|2027|2059|2021|2016|2006|2089|2039|2028|2065|2050|2025|2090|2017|2040|2023|2043|2110|2038|1585|2048|2042|2022|2088|2066|2062|2015|2029|1470|2047|2052|1515|1560|1565|1445|2018|1465|2033|2031|1570|2064|2049|2026|2063|2045|1466|2024|2030|1590|1595|1475|2204|2032|2046|2044|2130|2020|2068|1460|2203|2092|2093|2057|2034|1670|1675|2111|2067|2137|1800|2131|2132|2035|2019|2193|2069|2036|2139|2205|2206|2135|2136|2070|2094|1655|1805|2134|2133|2095|2216|2112|2194|2113|2087|1450|1455|2138|1680|1685|1435|2140|1640|2071|2100|2207|2217|2191|2192|2096|2195|2208|2086|2073|2114|2109|2122|2218|2141|2127|2190|2072|2219|1710|2121|2220|2085|2115|2221|2209|2099|2196|1480|1481|1485|2128|2222|2223|1825|1835|2144|2074|2116|2075|2231|2097|2143|2200|1885|2129|2210|2118|2117|2199|2142|2076|2119|2211|2077|2229|1811|1851|2120|2162|2224|2150|2101|2225|2226|2197|1715|2212|2123|2124|2151|2084|2125|2161|1630|1635|1639|1490|2228|2160|2230|2198|2102|2163|2145|1700|1701|1790|1495|2227|2126|2213|2079|2214|2012|1499|2152|1750|1755|2153|2172|1660|2103|1860|2165|2104|1765|2154|2106|2080|2146|', '20');

TRUNCATE TABLE `service_categories_providers`;
INSERT INTO `service_categories_providers` (`category_id`, `provider_id`) VALUES
(31, 1),
(38, 2),
(38, 3),
(38, 4);

TRUNCATE TABLE `service_categories`;
-- Top level categories
INSERT INTO `service_categories` (`id`, `name`, `url_name`, `description`, `keywords`, `parent_id`, `created_at`, `created_user_id`) VALUES
(1, 'Art & Media', 'art-media', NULL, NULL, NULL, now(), 1),
(2, 'Auto & Boating', 'auto-boating', NULL, NULL, NULL, now(), 1),
(3, 'Baby & Children', 'baby-children', NULL, NULL, NULL, now(), 1),
(4, 'Building & Maintenance', 'building-maintenance', NULL, NULL, NULL, now(), 1),
(5, 'Business & Legal', 'business-legal', NULL, NULL, NULL, now(), 1),
(7, 'Computer & Internet', 'computer-internet', NULL, NULL, NULL, now(), 1),
(8, 'Construction & Engineering', 'construction-engineering', NULL, NULL, NULL, now(), 1),
(9, 'Delivery', 'delivery', NULL, NULL, NULL, now(), 1),
(10, 'Educational & Training', 'educational-training', NULL, NULL, NULL, now(), 1),
(11, 'Events', 'events', NULL, NULL, NULL, now(), 1),
(12, 'Health & Beauty', 'health-beauty', NULL, NULL, NULL, now(), 1),
(13, 'Home Improvement', 'home-improvement', NULL, NULL, NULL, now(), 1),
(14, 'Household', 'household', NULL, NULL, NULL, now(), 1),
(15, 'Office & Clerical', 'office-clerical', NULL, NULL, NULL, now(), 1),
(16, 'Personal Services', 'personal-services', NULL, NULL, NULL, now(), 1),
(17, 'Pet & Animal', 'pet-animal', NULL, NULL, NULL, now(), 1),
(19, 'Sports & Recreation', 'sports-recreation', NULL, NULL, NULL, now(), 1),
(20, 'Cars & Trucks', 'cars-trucks', NULL, NULL, NULL, now(), 1);

-- Child categories
INSERT INTO `service_categories` (`name`, `url_name`, `description`, `keywords`, `parent_id`, `created_at`, `created_user_id`) VALUES
('Accountant', 'accountant', NULL, NULL, 5, now(), 1),
('Actor/Actress', 'actor-actress', NULL, NULL, 1, now(), 1),
('Administrative Assistant', 'administrative-assistant', NULL, NULL, 15, now(), 1),
('Animal Sitter', 'animal-sitter', NULL, NULL, 17, now(), 1),
('Architect', 'architect', NULL, NULL, 8, now(), 1),
('Art Teacher/Tutor', 'art-teacher-tutor', NULL, NULL,  1, now(), 1),
('Attorney', 'attorney', NULL, NULL, 5, now(), 1),
('Babysitter', 'babysitter', NULL, NULL, 3, now(), 1),
('Bartender', 'bartender', NULL, NULL, 11, now(), 1),
('Bookkeeper', 'bookkeeper', NULL, NULL, 5, now(), 1),
('Carpenter', 'carpenter', NULL, NULL, 8, now(), 1),
('Carpet Cleaner', 'carpet-cleaner', NULL, NULL, 14, now(), 1),
('Chiropractor', 'chiropractor', NULL, NULL, 12, now(), 1),
('Computer Programmer', 'computer-programmer', NULL, NULL, 7, now(), 1),
('Dance Instructor', 'dance-instructor', NULL, NULL, 1, now(), 1),
('Disc Jockey', 'disc-jockey', NULL, NULL,  1, now(), 1),
('Dog Walker', 'dog-walker', NULL, NULL,  17, now(), 1),
('Electrician', 'electrician', NULL, NULL, 8, now(), 1),
('Florist', 'florist', NULL, NULL, 9, now(), 1),
('General Contractor', 'general-contractor', NULL, NULL, 8, now(), 1),
('Graphic Designer', 'graphic-designer', NULL, NULL, 1, now(), 1),
('Handyman', 'handyman', NULL, NULL, 13, now(), 1),
('Helper', 'helper', NULL, NULL, 16, now(), 1),
('Interior Decorator', 'interior-decorator', NULL, NULL, 13, now(), 1),
('Journalist', 'journalist', NULL, NULL, 5, now(), 1),
('Life Coach', 'life-coach', NULL, NULL,  10, now(), 1),
('Maid', 'maid', NULL, NULL, 4, now(), 1),
('Makeup Artist', 'makeup-artist', NULL, NULL, 12, now(), 1),
('Multimedia Designer', 'multimedia-designer', NULL, NULL, 1, now(), 1),
('Nanny', 'nanny', NULL, NULL, 3, now(), 1),
('Network Administrator', 'network-administrator', NULL, NULL, 7, now(), 1),
('Nutritionist', 'nutritionist', NULL, NULL, 12, now(), 1),
('Painter', 'painter', NULL, NULL, 13, now(), 1),
('Personal Assistant', 'personal-assistant', NULL, NULL, 16, now(), 1),
('Personal Trainer', 'personal-trainer', NULL, NULL, 12, now(), 1),
('Photographer', 'photographer', NULL, NULL, 1, now(), 1),
('Plasterer', 'plasterer', NULL, NULL, 14, now(), 1),
('Plumber', 'plumber', NULL, NULL, 13, now(), 1),
('Pool Cleaner', 'pool-cleaner', NULL, NULL,  4, now(), 1),
('Private Detective', 'private-detective', NULL, NULL, 5, now(), 1),
('Receptionist', 'receptionist', NULL, NULL, 15, now(), 1),
('Referee', 'referee', NULL, NULL, 19, now(), 1),
('Security Guard', 'security-guard', NULL, NULL,  11, now(), 1),
('Singer', 'singer', NULL, NULL, 1, now(), 1),
('Sports Coach', 'sports-coach', NULL, NULL,  10, now(), 1),
('Storage Provider', 'storage-provider', NULL, NULL, 9, now(), 1),
('Taxi Driver', 'taxi-driver', NULL, NULL,  20, now(), 1),
('Technical Support Specialist', 'technical-support-specialist', NULL, NULL,  7, now(), 1),
('Tour Guide', 'tour-guide', NULL, NULL,  20, now(), 1),
('Translator', 'translator', NULL, NULL, 5, now(), 1),
('Travel Agent', 'travel-agent', NULL, NULL,  20, now(), 1),
('Veterinarian', 'veterinarian', NULL, NULL, 17, now(), 1),
('Video Editor', 'video-editor', NULL, NULL, 1, now(), 1),
('Waiter/Waitress', 'waiter-waitress', NULL, NULL, 11, now(), 1),
('Web Site Designer', 'web-site-designer', NULL, NULL, 7, now(), 1),
('Wedding Planner', 'wedding-planner', NULL, NULL, 11, now(), 1),
('Window Cleaner', 'window-cleaner', NULL, NULL, 15, now(), 1),
('Writer', 'writer', NULL, NULL, 1, now(), 1),
('Yard Worker', 'yard-worker', NULL, NULL,  4, now(), 1),
('Web Developer', 'web-developer', NULL, NULL,  7, now(), 1);

TRUNCATE TABLE `service_requests`;
INSERT INTO `service_requests` (`id`, `type`, `user_id`, `status_id`, `url_name`, `title`, `description`, `description_extra`, `city`, `zip`, `country_id`, `state_id`, `config_data`, `expired_at`, `created_at`, `updated_at`, `created_user_id`, `updated_user_id`, `required_by`, `required_type`, `firm_start`, `firm_end`, `firm_alt_start`, `firm_alt_end`, `is_remote`) VALUES
(1, 'open', 1, 3, 'handyman', 'Handyman', 'We are looking for a lot of shed installers to install sheds sold at Bunnings, Mitre 10, Masters and other hardware stores.\n\nPlease submit a quote for installing a single 6x4m shed.', NULL, NULL, NULL, NULL, NULL, '<?xml version="1.0"?>\n<data/>\n', now() + interval 1 month, now(), now(), 1, 1, 'flexible', 'flexible', NULL, NULL, NULL, NULL, 1),
(2, 'open', 1, 3, 'carpenter', 'Carpenter', 'Looking to replace four existing wooden doors/windows with new aluminium sliding doors/windows.\n\nOn the ground floor we require two standard-size sliding doors and one awning window.\n\nAlso there is one awning window on first floor.\n\nPrefer to use Dowell (Bayswater) windows to match with existing windows and doors.', NULL, 'Sydney', '2000', 3, 73, '<?xml version="1.0"?>\n<data/>\n', now() + interval 1 month, now(), now(), 1, 1, 'flexible', 'flexible', NULL, NULL, NULL, NULL, 1),
(3, 'open', 3, 3, 'painter', 'Painter', 'A painter who likes fishing, 4kg Australian Salmon caught off the beach. The job is located on King Island, in between Melbourne and Tasmania. Travel and Accomodation are paid for by us.\n\nThe job is painting of King Island High School\n760sqm of walls\n604sqm of ceilings\n17 doors\n\nExterior\n70sqm of villaboard\n160sqm of cladding\n\nThere is a opportunity for future work on the island for the right person.', NULL, NULL, NULL, NULL, NULL, '<?xml version="1.0"?>\n<data/>\n', now() + interval 1 month, now(), now(), NULL, NULL, 'flexible', 'flexible_week', NULL, NULL, NULL, NULL, 1),
(4, 'open', 4, 3, 'electrician', 'Electrician', 'Need some help installing a new oven (old one died).  Spark been in and said needs recabling back to box and circuit breaker. About 30m cable to box and apparently cable is ''6.5'' or something. Oven says 240V, 5.3W rating (22.1A).\n\nOld oven already removed.  New oven sitting here waiting...', NULL, NULL, NULL, NULL, NULL, '<?xml version="1.0"?>\n<data/>\n', now() + interval 1 month, now(), now(), NULL, NULL, 'urgent', 'flexible', NULL, NULL, NULL, NULL, 1),
(5, 'open', 5, 3, 'handyman-1', 'Handyman', 'We have 13 x 1x1m concrete blocks ready to be taken to rubbish tip.\n\nIt is already cut up and ready to be taken away. \n\nYou will need a truck or heavy vehicle for this job.', NULL, NULL, NULL, NULL, NULL, '<?xml version="1.0"?>\n<data/>\n', now() + interval 1 month, now(), now(), NULL, NULL, 'flexible', 'flexible_month', NULL, NULL, NULL, NULL, 1);

TRUNCATE TABLE `service_categories_requests`;
INSERT INTO `service_categories_requests` (`category_id`, `request_id`) VALUES
(42, 1),
(31, 2),
(38, 4),
(42, 5),
(53, 3);

TRUNCATE TABLE `service_questions`;
INSERT INTO `service_questions` (`id`, `description`, `is_public`, `provider_id`, `answer_id`, `request_id`, `created_at`, `updated_at`, `created_user_id`, `updated_user_id`) VALUES
(1, 'Could you please provide the new oven dimensions?', 1, 1, 1, 4, now(), now(), 1, NULL);

TRUNCATE TABLE `service_answers`;
INSERT INTO `service_answers` (`id`, `description`, `is_public`, `user_id`, `request_id`, `created_at`, `updated_at`, `created_user_id`, `updated_user_id`) VALUES
(1, 'Sure thing, the oven is 144cm wide x 260cm height.', 1, 4, 4, now(), NULL, NULL, NULL);

TRUNCATE TABLE `service_quotes`;
INSERT INTO `service_quotes` (`id`, `moderation_status`, `status_id`, `request_id`, `provider_id`, `comment`, `price`, `start_at`, `duration`, `config_data`, `created_at`, `updated_at`, `created_user_id`, `updated_user_id`, `quote_type`, `flat_items`, `flat_labor_description`, `flat_labor_price`, `onsite_price_start`, `onsite_price_end`, `onsite_travel_required`, `onsite_travel_price`, `onsite_travel_waived`) VALUES
(1, 'new', 1, 4, 2, 'Hello Sample2,\n\nI will need to visit your location to see the size of the space in your kitchen and what tools will be required. I will waive the travel fee if you choose my quote.\n\nI could visit onsite as soon as today. If you''d like a price quote, click ''Set Appointment'' on Scripts Ahoy! so we can share contact details and schedule a time.\n\nSincerely, \nA1 Carpentry', 325, NULL, NULL, '<?xml version="1.0"?>\n<data/>\n', now(), NULL, 1, NULL, 'onsite', NULL, NULL, NULL, '250.00', '400.00', 1, '75.00', 1);
