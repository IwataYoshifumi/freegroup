/*
 *
 * admins
 *
 */
LOCK TABLES `admins` WRITE;
ALTER TABLE `admins` DISABLE KEYS;
DELETE FROM admins;
INSERT INTO `admins` VALUES 
(1,'iwata','iwata@network-tokai.jp',NULL,'$2y$10$YOAzicP9380Vgyq9g5WlUenJ81pxWV6YIUkEJti.kxmU/l9i9j2U6',0,NULL,NULL,NULL,NULL),
(2,'admin1','admin1@your.domain',NULL,'$2y$10$YOAzicP9380Vgyq9g5WlUenJ83pxWV6YIUkEJti.kxmU/l9i9j2U6',0,NULL,NULL,NULL,NULL),
(3,'admin2','admin2@your.domain',NULL,'$2y$10$YOAzicP9380Vgyq9g5WlUenJ83pxWV6YIUkEJti.kxmU/l9i9j2U6',0,NULL,NULL,NULL,NULL);
ALTER TABLE `admins` ENABLE KEYS;
UNLOCK TABLES;

/*
 *
 * depts
 *
 */
LOCK TABLES `depts` WRITE;
ALTER TABLE `depts` DISABLE KEYS;
DELETE FROM depts;
INSERT INTO `depts` VALUES 
/*
 * 部署ID、部署名、 created_at, updated_at )
 */
(1,'総務部',NULL,NULL),
(2,'企画部',NULL,NULL),
(3,'営業部',NULL,NULL),
(4,'東京支店',NULL,NULL);
ALTER TABLE `depts` ENABLE KEYS;
UNLOCK TABLES;

/*
 *
 * users
 *
 */
LOCK TABLES `users` WRITE;
ALTER TABLE `users` DISABLE KEYS;
DELETE FROM users;
INSERT INTO `users` VALUES 
/*
 * ユーザID、名前、メールアドレス、email_valified must be null, hash password, 部署ID, 役職, 退社フラグ(0：在籍、1：退社、退社日、rememober_token must be null, created_at, updated_at )
 */
(1,'iwata','iwata@network-tokai.jp',NULL,'$2y$10$LR/jC2l4EcPlOldDFgZPIuGSLfYkegg1L99k1vPFtEHrPLvSKRY.6',1,'社員',0,NULL,NULL,NULL,NULL),
(2,'user1','user1@your.domain',NULL,'$2y$10$LR/jC2l4EcPlOldDFgZPIuGSLfYkegg1L99k1vPFtEHrPLvSKRY.6',1,'社員',0,NULL,NULL,NULL,NULL),
(3,'user2','user2@your.domain',NULL,'$2y$10$LR/jC2l4EcPlOldDFgZPIuGSLfYkegg1L99k1vPFtEHrPLvSKRY.6',2,'社員',0,NULL,NULL,NULL,NULL),
(4,'user3','user3@your.domain',NULL,'$2y$10$LR/jC2l4EcPlOldDFgZPIuGSLfYkegg1L99k1vPFtEHrPLvSKRY.6',3,'社員',0,NULL,NULL,NULL,NULL);
ALTER TABLE `users` ENABLE KEYS;
UNLOCK TABLES;

/*
 *
 * customers
 *
 */
LOCK TABLES `customers` WRITE;
ALTER TABLE `customers` DISABLE KEYS;
DELETE FROM customers;
INSERT INTO `customers` VALUES 
/*
 * 顧客ID,名前,メールアドレス,フリカナ,郵便番号','都道府県','市区町村','町名','建物名・部屋番号',TEL,FAX',携帯,誕生日,性別,備考,salseforce_id must be null,deleted_at,created_at,updated_at);
 */
(1,'株式会社ネットワーク東海','iwata@network-tokai.jp','ネットワークトウカイ','4710075','愛知県','豊田市','日之出町1-14-22','ビル名','0565356826','0565356827','',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(2,'FreeGroup開発','freegroup@freegroup.work','フリーグループカイハツ','4710075','愛知県','豊田市','日之出町1-14-22','ビル名','0565356826','0565356827','',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
(3,'名前','email@your.domain','フリカナ','471-0071','都道府県','市区町村','町名','建物名・部屋番号','0565356826','0565356827','08012345647','2021-03-11','男',NULL,NULL,NULL,NULL,NULL);
ALTER TABLE `customers` ENABLE KEYS;
UNLOCK TABLES;

/*
 *
 * role_groups
 *
 */
LOCK TABLES `role_groups` WRITE;
ALTER TABLE `role_groups` DISABLE KEYS;
DELETE FROM role_groups;
INSERT INTO `role_groups` VALUES 
(1,'一般社員用','初期設定',1,NULL,NULL),
(2,'管理者用','全許可',0,NULL,NULL),
(3,'部署内管理者用','閲覧制限カレンダー・日報 リストの作成可能',0,NULL,NULL);
ALTER TABLE `role_groups` ENABLE KEYS;
UNLOCK TABLES;

/*
 *
 * role_lists
 *
 */
LOCK TABLES `role_lists` WRITE;
ALTER TABLE `role_lists` DISABLE KEYS;
DELETE FROM role_lists;
INSERT INTO `role_lists` VALUES 
('CanCreatePrivateCalendars',NULL,2,'2021-02-23 00:32:44','2021-02-23 00:32:44'),
('CanCreateCompanyWideCalendars',NULL,2,'2021-02-23 00:32:44','2021-02-23 00:32:44'),
('CanCreatePrivateReportLists',NULL,2,'2021-02-23 00:32:44','2021-02-23 00:32:44'),
('CanCreatePrivateTaskLists',NULL,2,'2021-02-23 00:32:44','2021-02-23 00:32:44'),
('CanCreatePublicAccessLists',NULL,2,'2021-02-23 00:32:44','2021-02-23 00:32:44'),
('CanCreatePublicFacilities',NULL,2,'2021-02-23 00:32:44','2021-02-23 00:32:44'),
('CanCreatePrivateCalendars',NULL,3,'2021-02-23 00:33:22','2021-02-23 00:33:22'),
('CanCreatePrivateReportLists',NULL,3,'2021-02-23 00:33:22','2021-02-23 00:33:22'),
('CanCreatePrivateTaskLists',NULL,3,'2021-02-23 00:33:22','2021-02-23 00:33:22');
ALTER TABLE `role_lists` ENABLE KEYS;
UNLOCK TABLES;
