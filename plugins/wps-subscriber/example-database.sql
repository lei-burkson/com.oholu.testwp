CREATE TABLE IF NOT EXISTS `subscribers` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `SubscriberEmail` varchar(320) NOT NULL,
    `SubscriberName` varchar(255) NULL,
    `SubscriberAddress` varchar(255) NULL,
    `SubscriberAddress2` varchar(128) NULL,
    `SubscriberCity` varchar(128) NULL,
    `SubscriberState` varchar(128) NULL,
    `SubscriberZip` varchar(16) NULL,
    `SubscriberPhone` varchar(32) NULL,
    `SubscriberCountry` varchar(128) NULL,
    `SubscriberUnsubscribed` varchar(6) NOT NULL,
    `SubscriberToken` varchar(36) NOT NULL,
    `SubscriberCreated` timestamp NULL DEFAULT NULL,
    `SubscriberLastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`SubscriberEmail`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `subscribers` (`id`, `SubscriberEmail`, `SubscriberName`, `SubscriberAddress`, `SubscriberAddress2`,
    `SubscriberCity`, `SubscriberState`, `SubscriberZip`, `SubscriberPhone`, `SubscriberCountry`, `SubscriberUnsubscribed`,
    `SubscriberToken`, `SubscriberCreated`, `SubscriberLastUpdated`) VALUES
(1, 'a@b.com', 'User Name', '6600 S Quebec St e', '', 'Englewood', 'CO', '80111', '(303) 770-1006', 'USA', 'NO', '0bd059bd02e4446f9d943799443f2c6f', now(), ),
(2, 'b@c.com', 'Test User', '661 E Kentucky Ave', '', 'Denver', 'CO', '80209', '(303) 691-0717', 'USA', 'NO', 'e381f05bc68f4f419e53a27fb9aef42f', now(),);
