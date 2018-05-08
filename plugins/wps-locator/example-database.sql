CREATE TABLE IF NOT EXISTS `locations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `LocationNumber` varchar(32) NOT NULL,
    `LocationName` varchar(255) NOT NULL,
    `LocationAddress` varchar(255) NOT NULL,
    `LocationAddress2` varchar(128) NOT NULL,
    `LocationCity` varchar(128) NOT NULL,
    `LocationState` varchar(128) NOT NULL,
    `LocationZip` varchar(16) NOT NULL,
    `LocationPhone` varchar(32) NOT NULL,
    `LocationCountry` varchar(128) NOT NULL,
    `LocationLatitude` decimal(10, 8) NOT NULL,
    `LocationLongitude` decimal(11, 8) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`LocationNumber`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `locations` (`id`, `LocationName`, `LocationAddress`, `LocationAddress2`, `LocationCity`,
    `LocationState`, `LocationZip`, `LocationPhone`, `LocationCountry`, `LocationLatitude`, `LocationLongitude`) VALUES
(1, 'WP01', 'COLORADOLAND TIRE', '6600 S Quebec St e', '', 'Englewood', 'CO', '80111', '(303) 770-1006', '39.597204', '-104.903533'),
(2, 'WP02', 'COMMUNITY AUTO REPAIR', '661 E Kentucky Ave', '', 'Denver', 'CO', '80209', '(303) 691-0717', '39.700539', '-104.979727');
