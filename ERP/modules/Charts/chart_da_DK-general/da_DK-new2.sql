# MySQL dump of database 'faupgrade' on host 'localhost'
# Backup Date and Time: 2010-08-06 13:55
# Built by FrontAccounting 2.3RC1
# http://frontaccounting.com
# Company: Tr�ningskompagniet ApS
# User: 



### Structure of table `0_areas` ###

DROP TABLE IF EXISTS `0_areas`;

CREATE TABLE `0_areas` (
  `area_code` int(11) NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`area_code`),
  UNIQUE KEY `description` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=2  ;


### Data of table `0_areas` ###



### Structure of table `0_attachments` ###

DROP TABLE IF EXISTS `0_attachments`;

CREATE TABLE `0_attachments` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `type_no` int(11) NOT NULL default '0',
  `trans_no` int(11) NOT NULL default '0',
  `unique_name` varchar(60) NOT NULL default '',
  `tran_date` date NOT NULL default '0000-00-00',
  `filename` varchar(60) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `filetype` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type_no` (`type_no`,`trans_no`)
) ENGINE=MyISAM  ;


### Data of table `0_attachments` ###



### Structure of table `0_audit_trail` ###

DROP TABLE IF EXISTS `0_audit_trail`;

CREATE TABLE `0_audit_trail` (
  `id` int(11) NOT NULL auto_increment,
  `type` smallint(6) unsigned NOT NULL default '0',
  `trans_no` int(11) unsigned NOT NULL default '0',
  `user` smallint(6) unsigned NOT NULL default '0',
  `stamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `description` varchar(60) default NULL,
  `fiscal_year` int(11) NOT NULL,
  `gl_date` date NOT NULL default '0000-00-00',
  `gl_seq` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `Seq` (`fiscal_year`,`gl_date`,`gl_seq`),
  KEY `Type_and_Number` (`type`,`trans_no`)
) ENGINE=InnoDB  ;


### Data of table `0_audit_trail` ###



### Structure of table `0_bank_accounts` ###

DROP TABLE IF EXISTS `0_bank_accounts`;

CREATE TABLE `0_bank_accounts` (
  `account_code` varchar(15) NOT NULL default '',
  `account_type` smallint(6) NOT NULL default '0',
  `bank_account_name` varchar(60) NOT NULL default '',
  `bank_account_number` varchar(100) NOT NULL default '',
  `bank_name` varchar(60) NOT NULL default '',
  `bank_address` tinytext,
  `bank_curr_code` char(3) NOT NULL default '',
  `dflt_curr_act` tinyint(1) NOT NULL default '0',
  `id` smallint(6) NOT NULL auto_increment,
  `last_reconciled_date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `ending_reconcile_balance` double NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `bank_account_name` (`bank_account_name`),
  KEY `bank_account_number` (`bank_account_number`),
  KEY `account_code` (`account_code`)
) ENGINE=MyISAM AUTO_INCREMENT=4  ;


### Data of table `0_bank_accounts` ###

INSERT INTO `0_bank_accounts` VALUES ('7920', '0', 'Bankkonto', 'N/A', 'N/A', NULL, 'DKK', '0', '1', '0000-00-00 00:00:00', '0', '0');
INSERT INTO `0_bank_accounts` VALUES ('7930', '0', 'Girokonto', 'N/A', 'N/A', NULL, 'DKK', '0', '2', '0000-00-00 00:00:00', '0', '0');
INSERT INTO `0_bank_accounts` VALUES ('7910', '0', 'Kasse', 'N/A', 'N/A', NULL, 'DKK', '0', '3', '0000-00-00 00:00:00', '0', '0');


### Structure of table `0_bank_trans` ###

DROP TABLE IF EXISTS `0_bank_trans`;

CREATE TABLE `0_bank_trans` (
  `id` int(11) NOT NULL auto_increment,
  `type` smallint(6) default NULL,
  `trans_no` int(11) default NULL,
  `bank_act` varchar(15) NOT NULL default '',
  `ref` varchar(40) default NULL,
  `trans_date` date NOT NULL default '0000-00-00',
  `bank_trans_type_id` int(10) unsigned default NULL,
  `amount` double default NULL,
  `dimension_id` int(11) NOT NULL default '0',
  `dimension2_id` int(11) NOT NULL default '0',
  `person_type_id` int(11) NOT NULL default '0',
  `person_id` tinyblob,
  `reconciled` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `bank_act` (`bank_act`,`ref`),
  KEY `type` (`type`,`trans_no`),
  KEY `bank_act_2` (`bank_act`,`reconciled`),
  KEY `bank_act_3` (`bank_act`,`trans_date`)
) ENGINE=InnoDB  ;


### Data of table `0_bank_trans` ###



### Structure of table `0_bom` ###

DROP TABLE IF EXISTS `0_bom`;

CREATE TABLE `0_bom` (
  `id` int(11) NOT NULL auto_increment,
  `parent` char(20) NOT NULL default '',
  `component` char(20) NOT NULL default '',
  `workcentre_added` int(11) NOT NULL default '0',
  `loc_code` char(5) NOT NULL default '',
  `quantity` double NOT NULL default '1',
  PRIMARY KEY  (`parent`,`component`,`workcentre_added`,`loc_code`),
  KEY `component` (`component`),
  KEY `id` (`id`),
  KEY `loc_code` (`loc_code`),
  KEY `parent` (`parent`,`loc_code`),
  KEY `workcentre_added` (`workcentre_added`)
) ENGINE=MyISAM  ;


### Data of table `0_bom` ###



### Structure of table `0_budget_trans` ###

DROP TABLE IF EXISTS `0_budget_trans`;

CREATE TABLE `0_budget_trans` (
  `counter` int(11) NOT NULL auto_increment,
  `type` smallint(6) NOT NULL default '0',
  `type_no` bigint(16) NOT NULL default '1',
  `tran_date` date NOT NULL default '0000-00-00',
  `account` varchar(15) NOT NULL default '',
  `memo_` tinytext NOT NULL,
  `amount` double NOT NULL default '0',
  `dimension_id` int(11) default '0',
  `dimension2_id` int(11) default '0',
  `person_type_id` int(11) default NULL,
  `person_id` tinyblob,
  PRIMARY KEY  (`counter`),
  KEY `Type_and_Number` (`type`,`type_no`),
  KEY `Account` (`account`,`tran_date`,`dimension_id`,`dimension2_id`)
) ENGINE=InnoDB  ;


### Data of table `0_budget_trans` ###



### Structure of table `0_chart_class` ###

DROP TABLE IF EXISTS `0_chart_class`;

CREATE TABLE `0_chart_class` (
  `cid` varchar(3) NOT NULL,
  `class_name` varchar(60) NOT NULL default '',
  `ctype` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  ;


### Data of table `0_chart_class` ###

INSERT INTO `0_chart_class` VALUES ('1', 'Aktiver', '1', '0');
INSERT INTO `0_chart_class` VALUES ('2', 'Passiver', '2', '0');
INSERT INTO `0_chart_class` VALUES ('3', 'Indt�gter', '4', '0');
INSERT INTO `0_chart_class` VALUES ('4', 'Omkostninger', '6', '0');


### Structure of table `0_chart_master` ###

DROP TABLE IF EXISTS `0_chart_master`;

CREATE TABLE `0_chart_master` (
  `account_code` varchar(15) NOT NULL default '',
  `account_code2` varchar(15) NOT NULL default '',
  `account_name` varchar(60) NOT NULL default '',
  `account_type` varchar(10) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`account_code`),
  KEY `account_name` (`account_name`),
  KEY `accounts_by_type` (`account_type`,`account_code`)
) ENGINE=MyISAM  ;


### Data of table `0_chart_master` ###

INSERT INTO `0_chart_master` VALUES ('1100', '', 'Varesalg', '10', '0');
INSERT INTO `0_chart_master` VALUES ('1110', '', 'Varesalg 2', '10', '0');
INSERT INTO `0_chart_master` VALUES ('1120', '', 'Salg EU', '10', '0');
INSERT INTO `0_chart_master` VALUES ('1200', '', 'Udf�rt arbejde', '10', '0');
INSERT INTO `0_chart_master` VALUES ('1210', '', 'Andre indt�gter m/moms', '10', '0');
INSERT INTO `0_chart_master` VALUES ('1790', '', 'Reg. igangv�rende arbejder', '10', '0');
INSERT INTO `0_chart_master` VALUES ('1810', '', 'Debitorrabatter', '10', '0');
INSERT INTO `0_chart_master` VALUES ('2100', '', 'Varek�b m/moms', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2110', '', 'Varek�b 2 m/moms', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2150', '', 'Varek�b u/moms', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2200', '', 'Fremmed assistance', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2400', '', 'K�bsomkostninger', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2790', '', 'Regulering af varelager', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2810', '', 'Kreditorrabatter', '20', '0');
INSERT INTO `0_chart_master` VALUES ('2820', '', 'Kursreguleringer', '20', '0');
INSERT INTO `0_chart_master` VALUES ('3100', '', 'Annoncer og reklame', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3110', '', 'Rejseudgifter', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3120', '', 'Blomster og gaver m.v.', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3130', '', 'Repr�sentation 75%', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3140', '', 'Repr�sentation 25%', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3210', '', 'Husleje og varme m/moms', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3220', '', 'Elforbrug', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3240', '', 'Reng�ring m.v.', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3250', '', 'Vedligeholdelse lokaler', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3310', '', 'Telefon og mobiltelefon', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3315', '', 'Fragt', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3320', '', 'Portoudgifter', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3330', '', 'Kontorartikler', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3340', '', 'Fagliteratur', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3345', '', 'Aviser og tidsskrifter', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3350', '', 'Sm�anskaffelser', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3360', '', 'Vedligeholdelse inventar', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3370', '', 'EDB-udgifter m.v.', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3380', '', 'Regnskabsm�ssig assistance', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3385', '', 'Anden ekstern assistance', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3390', '', 'Advokatudgifter', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3410', '', 'Kontigenter m/moms', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3420', '', 'Forsikringer', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3430', '', 'Gebyr m/moms', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3435', '', 'Gebyr u/moms', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3440', '', 'P-afgifter', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3450', '', 'Taxa', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3610', '', 'Autodrift m/moms', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3620', '', 'V�gtafgift og forsikringer', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3630', '', 'Service og reparationer', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3640', '', 'K�rselsudgifter u/moms', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3710', '', 'Tab p� dibitorer', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3740', '', 'Tab/Vind', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3800', '', 'Ikke fradragsberet. udgifter', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3900', '', 'Diverse', '30', '0');
INSERT INTO `0_chart_master` VALUES ('3905', '', 'Fejlkonto', '30', '0');
INSERT INTO `0_chart_master` VALUES ('4110', '', 'Udbetalte l�nninger', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4140', '', 'K�rselsgodtg�relse', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4150', '', 'AM-bidrag m.v.', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4160', '', 'Arb. giverbidrag', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4180', '', 'Pensioner m.v.', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4210', '', 'Feriepenge', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4250', '', 'Reg. af feriepengetilsvar', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4310', '', 'ATP-bidrag', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4320', '', 'Andre sociale bidrag', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4330', '', 'L�nsumsafgift', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4410', '', 'Personaleforsikringer', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4420', '', 'Skole og kurser', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4510', '', 'Modtaget l�nrefusioner', '40', '0');
INSERT INTO `0_chart_master` VALUES ('4600', '', 'Personaleudgifter', '40', '0');
INSERT INTO `0_chart_master` VALUES ('5110', '', 'Afskr. good-will', '50', '0');
INSERT INTO `0_chart_master` VALUES ('5120', '', 'Afskr. lokaleindretning', '50', '0');
INSERT INTO `0_chart_master` VALUES ('5210', '', 'Afskr. inventar', '50', '0');
INSERT INTO `0_chart_master` VALUES ('5220', '', 'Afskr. driftsmidler', '50', '0');
INSERT INTO `0_chart_master` VALUES ('5230', '', 'Afskr. grunde og bygninger', '50', '0');
INSERT INTO `0_chart_master` VALUES ('5250', '', 'Afskr. tekniske anl�g og maskin', '50', '0');
INSERT INTO `0_chart_master` VALUES ('5500', '', 'Nedskr. oms�t. aktiver', '50', '0');
INSERT INTO `0_chart_master` VALUES ('6020', '', 'Indt. kapitaland. tilknyt. vir', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6030', '', 'Indt. kapitaland. associerede', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6050', '', 'Indt. andre kapitaland. v�rdip', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6110', '', 'Renteindt�gt, banker', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6130', '', 'Renteindt�gt, debitorer', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6140', '', 'Renteindt�gt, v�rdipapirer', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6211', '', 'Renteudgift, banker', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6230', '', 'Renteudgift, kreditorer', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6240', '', 'Renteudgift, l�n m.v.', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6250', '', 'Renteudgift, i �vrigt', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6280', '', 'Renteudgift, offentlige', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6410', '', 'Ekstraordin�re indt�gter', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6420', '', 'Indt�gter vedr. tidl. �r', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6550', '', 'Ekstraordin�re udgifter', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6560', '', 'Udgifter vedr. tidl. �r', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6610', '', 'Skat af �rets resultat', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6620', '', 'Regulering af udskudt skat', '60', '0');
INSERT INTO `0_chart_master` VALUES ('6630', '', 'Skatter vedr. tidl. �r', '60', '0');
INSERT INTO `0_chart_master` VALUES ('7120', '', 'Goodwill Saldo primo', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7130', '', 'Goodwill Tilgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7135', '', 'Goodwill Afgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7150', '', 'Afskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7151', '', 'Akkumulerede afskrivninger, pr', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7185', '', 'Deposita, forudbetalt husleje', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7220', '', 'Forudb. Saldo primo', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7230', '', 'Forudb. Tilgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7235', '', 'Forudb. Afgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7250', '', '�rets afskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7251', '', 'Akkumulerede afskrivninger, pr', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7310', '', 'Bygn. Saldo primo', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7320', '', 'Bygn. Tilgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7330', '', 'Bygn. Afgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7340', '', 'Opskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7350', '', '�rets afskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7351', '', 'Akkumulerede afskrivninger, pr', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7410', '', 'Maskiner Saldo primo', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7420', '', 'Maskiner Tilgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7430', '', 'Maskiner Afgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7440', '', 'Opskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7450', '', '�rets afskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7451', '', 'Akkumulerede afskrivninger, pr', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7510', '', 'Inventar Saldo primo', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7520', '', 'Inventar Tilgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7530', '', 'Inventar Afgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7540', '', 'Inventar Opskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7550', '', '�rets afskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7551', '', 'Akkumulerede afskrivninger, pr', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7572', '', 'Driftsm. Saldo primo', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7577', '', 'Driftsm. Tilgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7578', '', 'Driftsm. Afgang', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7580', '', 'Driftsm. Opskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7586', '', '�rets afskrivninger', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7590', '', 'Akkumulerede afskrivninger, pr', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7597', '', 'Forudbet. for mat. anl�gsakriv', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7610', '', 'Kapitalandele i tilknyt. virks', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7620', '', 'Tilgodehav. hos tilknyt. virks', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7630', '', 'Kapitalandele i associerede vi', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7640', '', 'Tilgodehav. hos associerede vi', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7650', '', 'Andre v�rdipapirer og kapitala', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7660', '', 'Andre tilgodehaverner', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7680', '', 'Egne aktier eller anparter', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7690', '', 'Tilgodeh. hos virsomhedsdelta', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7710', '', 'Varelager', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7750', '', 'Igangv�rende arbejder', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7810', '', 'Debitorsamlekonto', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7820', '', 'Andre tilgodehavender', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7840', '', 'Periodeafgr�nsningsposter', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7860', '', 'Kapitalandele i tilknyt. virks', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7870', '', 'Egne aktier eller anparter', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7880', '', 'Andre v�rdipapirer og kapitala', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7910', '', 'Kassebeholdning', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7920', '', 'Pengeinstitut', '70', '0');
INSERT INTO `0_chart_master` VALUES ('7930', '', 'Girokonto', '70', '0');
INSERT INTO `0_chart_master` VALUES ('8110', '', 'Selskabskapital', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8120', '', 'Overkurs ved emission', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8130', '', 'Opskrivningshenl�ggelser', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8140', '', 'Reserver for nettoopskrivning', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8141', '', 'Reserve for egne aktier eller', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8142', '', '�vrige lovpligtige henl�ggelse', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8144', '', 'Vedt�gtsm�ssige henl�ggelser', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8190', '', 'Overf�rt af �rets resultat', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8195', '', 'Overf�rt fra tidligere �r', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8200', '', 'Privat indskud', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8210', '', 'Private h�vninger', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8610', '', 'Udskudt skat', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8650', '', 'Debitorreservationer', '80', '0');
INSERT INTO `0_chart_master` VALUES ('8670', '', 'Andre hens�ttelser', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9005', '', 'Langfr. Obligationsl�n', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9120', '', 'Konvertibel g�ldsbreve', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9130', '', 'Udbyttegivende g�ldsbreve', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9140', '', 'Kreditinstitutter', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9160', '', 'Leverand�rer af varer og tjenes', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9170', '', 'Vekselg�ld', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9180', '', 'G�ld til tilknyt. virksomheder', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9190', '', 'G�ld til associerede virksomhe', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9200', '', 'Selskabsskat', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9301', '', 'Kortfr. Obligationsl�n', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9320', '', 'Konvertible g�ldsbreve', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9330', '', 'Udbyttegivende g�ldsbreve', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9340', '', 'Kreditinstitutter', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9350', '', 'Modtagne forudbetalinger fra kr', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9360', '', 'Kreditorsamlekonto', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9370', '', 'Vekselg�ld', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9380', '', 'G�ld til tilknyt. virksomheder', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9390', '', 'G�ld til associerede virksomhe', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9395', '', 'Skyldig selskabsskat', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9401', '', 'Toldv�senet', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9410', '', 'Udg�ende afgift', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9415', '', 'Importmoms', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9420', '', 'Indg�ende afgift', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9430', '', 'Elafgift', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9440', '', 'CO2-aftift', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9470', '', 'Anden aftift', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9480', '', 'Skyldig moms', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9520', '', 'Tilbagehold A-skat', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9525', '', 'Betalt A-skat', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9530', '', 'Tilbageholdt AM-bidrag', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9535', '', 'Betalt AM-bidrag', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9540', '', 'Skyldig ATP-bidrag', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9545', '', 'Skyldig arb.giverbidrag', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9560', '', 'Skyldige feriepenge', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9570', '', 'Feriepengetilsvar', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9590', '', 'Mellemregn. selskabsdeltagere', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9800', '', 'Skyldige omkostninger', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9900', '', 'Udbytte for regnskabs�ret', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9920', '', 'Ikke h�vet udbytte for tidligere', '80', '0');
INSERT INTO `0_chart_master` VALUES ('9930', '', 'Skyldig udbytteskat', '80', '0');


### Structure of table `0_chart_types` ###

DROP TABLE IF EXISTS `0_chart_types`;

CREATE TABLE `0_chart_types` (
  `id` varchar(10) NOT NULL,
  `name` varchar(60) NOT NULL default '',
  `class_id` varchar(3) NOT NULL default '',
  `parent` varchar(10) NOT NULL default '-1',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=91  ;


### Data of table `0_chart_types` ###

INSERT INTO `0_chart_types` VALUES ('10', 'Salg', '3', '', '0');
INSERT INTO `0_chart_types` VALUES ('20', 'Varek�b', '4', '', '0');
INSERT INTO `0_chart_types` VALUES ('30', 'Kontoromk', '4', '', '0');
INSERT INTO `0_chart_types` VALUES ('40', 'L�nninger', '4', '', '0');
INSERT INTO `0_chart_types` VALUES ('50', 'Andre omk', '4', '', '0');
INSERT INTO `0_chart_types` VALUES ('60', 'Finansielle omk', '4', '', '0');
INSERT INTO `0_chart_types` VALUES ('70', 'Aktiver', '1', '', '0');
INSERT INTO `0_chart_types` VALUES ('80', 'Passiver', '2', '', '0');
INSERT INTO `0_chart_types` VALUES ('90', 'Fri', '4', '', '0');


### Structure of table `0_comments` ###

DROP TABLE IF EXISTS `0_comments`;

CREATE TABLE `0_comments` (
  `type` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL default '0',
  `date_` date default '0000-00-00',
  `memo_` tinytext,
  KEY `type_and_id` (`type`,`id`)
) ENGINE=InnoDB  ;


### Data of table `0_comments` ###



### Structure of table `0_credit_status` ###

DROP TABLE IF EXISTS `0_credit_status`;

CREATE TABLE `0_credit_status` (
  `id` int(11) NOT NULL auto_increment,
  `reason_description` char(100) NOT NULL default '',
  `dissallow_invoices` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `reason_description` (`reason_description`)
) ENGINE=MyISAM AUTO_INCREMENT=5  ;


### Data of table `0_credit_status` ###

INSERT INTO `0_credit_status` VALUES ('1', 'God historik', '0', '0');
INSERT INTO `0_credit_status` VALUES ('3', 'Inget arbejde f�rend betaling er modtaget', '1', '0');
INSERT INTO `0_credit_status` VALUES ('4', 'Under likvidation', '1', '0');


### Structure of table `0_crm_categories` ###

DROP TABLE IF EXISTS `0_crm_categories`;

CREATE TABLE `0_crm_categories` (
  `id` int(11) NOT NULL auto_increment COMMENT 'pure technical key',
  `type` varchar(20) NOT NULL COMMENT 'contact type e.g. customer',
  `action` varchar(20) NOT NULL COMMENT 'detailed usage e.g. department',
  `name` varchar(30) NOT NULL COMMENT 'for category selector',
  `description` tinytext NOT NULL COMMENT 'usage description',
  `system` tinyint(1) NOT NULL default '0' COMMENT 'nonzero for core system usage',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`,`action`),
  UNIQUE KEY `type_2` (`type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13  ;


### Data of table `0_crm_categories` ###

INSERT INTO `0_crm_categories` VALUES ('1', 'cust_branch', 'general', 'General', 'General contact data for customer branch (overrides company setting)', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('2', 'cust_branch', 'invoice', 'Invoices', 'Invoice posting (overrides company setting)', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('3', 'cust_branch', 'order', 'Orders', 'Order confirmation (overrides company setting)', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('4', 'cust_branch', 'delivery', 'Deliveries', 'Delivery coordination (overrides company setting)', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('5', 'customer', 'general', 'General', 'General contact data for customer', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('6', 'customer', 'order', 'Orders', 'Order confirmation', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('7', 'customer', 'delivery', 'Deliveries', 'Delivery coordination', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('8', 'customer', 'invoice', 'Invoices', 'Invoice posting', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('9', 'supplier', 'general', 'General', 'General contact data for supplier', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('10', 'supplier', 'order', 'Orders', 'Order confirmation', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('11', 'supplier', 'delivery', 'Deliveries', 'Delivery coordination', '1', '0');
INSERT INTO `0_crm_categories` VALUES ('12', 'supplier', 'invoice', 'Invoices', 'Invoice posting', '1', '0');


### Structure of table `0_crm_contacts` ###

DROP TABLE IF EXISTS `0_crm_contacts`;

CREATE TABLE `0_crm_contacts` (
  `id` int(11) NOT NULL auto_increment,
  `person_id` int(11) NOT NULL default '0' COMMENT 'foreign key to crm_contacts',
  `type` varchar(20) NOT NULL COMMENT 'foreign key to crm_categories',
  `action` varchar(20) NOT NULL COMMENT 'foreign key to crm_categories',
  `entity_id` varchar(11) default NULL COMMENT 'entity id in related class table',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`,`action`)
) ENGINE=InnoDB  ;


### Data of table `0_crm_contacts` ###



### Structure of table `0_crm_persons` ###

DROP TABLE IF EXISTS `0_crm_persons`;

CREATE TABLE `0_crm_persons` (
  `id` int(11) NOT NULL auto_increment,
  `ref` varchar(30) NOT NULL,
  `name` varchar(60) NOT NULL,
  `name2` varchar(60) default NULL,
  `address` tinytext,
  `phone` varchar(30) default NULL,
  `phone2` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `email` varchar(100) default NULL,
  `lang` char(5) default NULL,
  `notes` tinytext NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ref` (`ref`)
) ENGINE=InnoDB  ;


### Data of table `0_crm_persons` ###



### Structure of table `0_currencies` ###

DROP TABLE IF EXISTS `0_currencies`;

CREATE TABLE `0_currencies` (
  `currency` varchar(60) NOT NULL default '',
  `curr_abrev` char(3) NOT NULL default '',
  `curr_symbol` varchar(10) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  `hundreds_name` varchar(15) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  `auto_update` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`curr_abrev`)
) ENGINE=MyISAM  ;


### Data of table `0_currencies` ###

INSERT INTO `0_currencies` VALUES ('Kronor', 'SEK', 'kr', 'Sverige', '�ren', '0', '1');
INSERT INTO `0_currencies` VALUES ('Kroner', 'DKK', 'kr.', 'Danmark', '�re', '0', '1');
INSERT INTO `0_currencies` VALUES ('Euro', 'EUR', '�', 'Europa', 'Cents', '0', '1');
INSERT INTO `0_currencies` VALUES ('Pounds', 'GBP', '�', 'England', 'Pence', '0', '1');
INSERT INTO `0_currencies` VALUES ('US Dollars', 'USD', '$', 'USA', 'Cents', '0', '1');


### Structure of table `0_cust_allocations` ###

DROP TABLE IF EXISTS `0_cust_allocations`;

CREATE TABLE `0_cust_allocations` (
  `id` int(11) NOT NULL auto_increment,
  `amt` double unsigned default NULL,
  `date_alloc` date NOT NULL default '0000-00-00',
  `trans_no_from` int(11) default NULL,
  `trans_type_from` int(11) default NULL,
  `trans_no_to` int(11) default NULL,
  `trans_type_to` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `From` (`trans_type_from`,`trans_no_from`),
  KEY `To` (`trans_type_to`,`trans_no_to`)
) ENGINE=InnoDB  ;


### Data of table `0_cust_allocations` ###



### Structure of table `0_cust_branch` ###

DROP TABLE IF EXISTS `0_cust_branch`;

CREATE TABLE `0_cust_branch` (
  `branch_code` int(11) NOT NULL auto_increment,
  `debtor_no` int(11) NOT NULL default '0',
  `br_name` varchar(60) NOT NULL default '',
  `br_address` tinytext NOT NULL,
  `area` int(11) default NULL,
  `salesman` int(11) NOT NULL default '0',
  `contact_name` varchar(60) NOT NULL default '',
  `default_location` varchar(5) NOT NULL default '',
  `tax_group_id` int(11) default NULL,
  `sales_account` varchar(15) NOT NULL default '',
  `sales_discount_account` varchar(15) NOT NULL default '',
  `receivables_account` varchar(15) NOT NULL default '',
  `payment_discount_account` varchar(15) NOT NULL default '',
  `default_ship_via` int(11) NOT NULL default '1',
  `disable_trans` tinyint(4) NOT NULL default '0',
  `br_post_address` tinytext NOT NULL,
  `group_no` int(11) NOT NULL default '0',
  `notes` tinytext,
  `inactive` tinyint(1) NOT NULL default '0',
  `branch_ref` varchar(30) NOT NULL,
  PRIMARY KEY  (`branch_code`,`debtor_no`),
  KEY `branch_code` (`branch_code`),
  KEY `branch_ref` (`branch_ref`),
  KEY `group_no` (`group_no`)
) ENGINE=MyISAM  ;


### Data of table `0_cust_branch` ###



### Structure of table `0_debtor_trans` ###

DROP TABLE IF EXISTS `0_debtor_trans`;

CREATE TABLE `0_debtor_trans` (
  `trans_no` int(11) unsigned NOT NULL default '0',
  `type` smallint(6) unsigned NOT NULL default '0',
  `version` tinyint(1) unsigned NOT NULL default '0',
  `debtor_no` int(11) unsigned default NULL,
  `branch_code` int(11) NOT NULL default '-1',
  `tran_date` date NOT NULL default '0000-00-00',
  `due_date` date NOT NULL default '0000-00-00',
  `reference` varchar(60) NOT NULL default '',
  `tpe` int(11) NOT NULL default '0',
  `order_` int(11) NOT NULL default '0',
  `ov_amount` double NOT NULL default '0',
  `ov_gst` double NOT NULL default '0',
  `ov_freight` double NOT NULL default '0',
  `ov_freight_tax` double NOT NULL default '0',
  `ov_discount` double NOT NULL default '0',
  `alloc` double NOT NULL default '0',
  `rate` double NOT NULL default '1',
  `ship_via` int(11) default NULL,
  `dimension_id` int(11) NOT NULL default '0',
  `dimension2_id` int(11) NOT NULL default '0',
  `payment_terms` int(11) default NULL,
  PRIMARY KEY  (`type`,`trans_no`),
  KEY `debtor_no` (`debtor_no`,`branch_code`),
  KEY `tran_date` (`tran_date`)
) ENGINE=InnoDB  ;


### Data of table `0_debtor_trans` ###



### Structure of table `0_debtor_trans_details` ###

DROP TABLE IF EXISTS `0_debtor_trans_details`;

CREATE TABLE `0_debtor_trans_details` (
  `id` int(11) NOT NULL auto_increment,
  `debtor_trans_no` int(11) default NULL,
  `debtor_trans_type` int(11) default NULL,
  `stock_id` varchar(20) NOT NULL default '',
  `description` tinytext,
  `unit_price` double NOT NULL default '0',
  `unit_tax` double NOT NULL default '0',
  `quantity` double NOT NULL default '0',
  `discount_percent` double NOT NULL default '0',
  `standard_cost` double NOT NULL default '0',
  `qty_done` double NOT NULL default '0',
  `src_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Transaction` (`debtor_trans_type`,`debtor_trans_no`),
  KEY `src_id` (`src_id`)
) ENGINE=InnoDB  ;


### Data of table `0_debtor_trans_details` ###



### Structure of table `0_debtors_master` ###

DROP TABLE IF EXISTS `0_debtors_master`;

CREATE TABLE `0_debtors_master` (
  `debtor_no` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `address` tinytext,
  `tax_id` varchar(55) NOT NULL default '',
  `curr_code` char(3) NOT NULL default '',
  `sales_type` int(11) NOT NULL default '1',
  `dimension_id` int(11) NOT NULL default '0',
  `dimension2_id` int(11) NOT NULL default '0',
  `credit_status` int(11) NOT NULL default '0',
  `payment_terms` int(11) default NULL,
  `discount` double NOT NULL default '0',
  `pymt_discount` double NOT NULL default '0',
  `credit_limit` float NOT NULL default '1000',
  `notes` tinytext,
  `inactive` tinyint(1) NOT NULL default '0',
  `debtor_ref` varchar(30) NOT NULL,
  PRIMARY KEY  (`debtor_no`),
  KEY `name` (`name`),
  UNIQUE KEY `debtor_ref` (`debtor_ref`)
) ENGINE=MyISAM  ;


### Data of table `0_debtors_master` ###



### Structure of table `0_dimensions` ###

DROP TABLE IF EXISTS `0_dimensions`;

CREATE TABLE `0_dimensions` (
  `id` int(11) NOT NULL auto_increment,
  `reference` varchar(60) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `type_` tinyint(1) NOT NULL default '1',
  `closed` tinyint(1) NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  `due_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `date_` (`date_`),
  KEY `due_date` (`due_date`),
  KEY `type_` (`type_`)
) ENGINE=InnoDB  ;


### Data of table `0_dimensions` ###



### Structure of table `0_exchange_rates` ###

DROP TABLE IF EXISTS `0_exchange_rates`;

CREATE TABLE `0_exchange_rates` (
  `id` int(11) NOT NULL auto_increment,
  `curr_code` char(3) NOT NULL default '',
  `rate_buy` double NOT NULL default '0',
  `rate_sell` double NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `curr_code` (`curr_code`,`date_`)
) ENGINE=MyISAM  ;


### Data of table `0_exchange_rates` ###



### Structure of table `0_fiscal_year` ###

DROP TABLE IF EXISTS `0_fiscal_year`;

CREATE TABLE `0_fiscal_year` (
  `id` int(11) NOT NULL auto_increment,
  `begin` date default '0000-00-00',
  `end` date default '0000-00-00',
  `closed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `begin` (`begin`),
  UNIQUE KEY `end` (`end`)
) ENGINE=InnoDB AUTO_INCREMENT=2  ;


### Data of table `0_fiscal_year` ###

INSERT INTO `0_fiscal_year` VALUES ('1', '2008-01-01', '2008-12-31', '0');


### Structure of table `0_gl_trans` ###

DROP TABLE IF EXISTS `0_gl_trans`;

CREATE TABLE `0_gl_trans` (
  `counter` int(11) NOT NULL auto_increment,
  `type` smallint(6) NOT NULL default '0',
  `type_no` bigint(16) NOT NULL default '1',
  `tran_date` date NOT NULL default '0000-00-00',
  `account` varchar(15) NOT NULL default '',
  `memo_` tinytext NOT NULL,
  `amount` double NOT NULL default '0',
  `dimension_id` int(11) NOT NULL default '0',
  `dimension2_id` int(11) NOT NULL default '0',
  `person_type_id` int(11) default NULL,
  `person_id` tinyblob,
  PRIMARY KEY  (`counter`),
  KEY `Type_and_Number` (`type`,`type_no`),
  KEY `dimension_id` (`dimension_id`),
  KEY `dimension2_id` (`dimension2_id`),
  KEY `tran_date` (`tran_date`),
  KEY `account_and_tran_date` (`account`,`tran_date`)
) ENGINE=InnoDB  ;


### Data of table `0_gl_trans` ###



### Structure of table `0_grn_batch` ###

DROP TABLE IF EXISTS `0_grn_batch`;

CREATE TABLE `0_grn_batch` (
  `id` int(11) NOT NULL auto_increment,
  `supplier_id` int(11) NOT NULL default '0',
  `purch_order_no` int(11) default NULL,
  `reference` varchar(60) NOT NULL default '',
  `delivery_date` date NOT NULL default '0000-00-00',
  `loc_code` varchar(5) default NULL,
  PRIMARY KEY  (`id`),
  KEY `delivery_date` (`delivery_date`),
  KEY `purch_order_no` (`purch_order_no`)
) ENGINE=InnoDB  ;


### Data of table `0_grn_batch` ###



### Structure of table `0_grn_items` ###

DROP TABLE IF EXISTS `0_grn_items`;

CREATE TABLE `0_grn_items` (
  `id` int(11) NOT NULL auto_increment,
  `grn_batch_id` int(11) default NULL,
  `po_detail_item` int(11) NOT NULL default '0',
  `item_code` varchar(20) NOT NULL default '',
  `description` tinytext,
  `qty_recd` double NOT NULL default '0',
  `quantity_inv` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `grn_batch_id` (`grn_batch_id`)
) ENGINE=InnoDB  ;


### Data of table `0_grn_items` ###



### Structure of table `0_groups` ###

DROP TABLE IF EXISTS `0_groups`;

CREATE TABLE `0_groups` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=4  ;


### Data of table `0_groups` ###

INSERT INTO `0_groups` VALUES ('1', 'Small', '0');
INSERT INTO `0_groups` VALUES ('2', 'Medium', '0');
INSERT INTO `0_groups` VALUES ('3', 'Large', '0');


### Structure of table `0_item_codes` ###

DROP TABLE IF EXISTS `0_item_codes`;

CREATE TABLE `0_item_codes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_code` varchar(20) NOT NULL,
  `stock_id` varchar(20) NOT NULL,
  `description` varchar(200) NOT NULL default '',
  `category_id` smallint(6) unsigned NOT NULL,
  `quantity` double NOT NULL default '1',
  `is_foreign` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `stock_id` (`stock_id`,`item_code`),
  KEY `item_code` (`item_code`)
) ENGINE=MyISAM  ;


### Data of table `0_item_codes` ###



### Structure of table `0_item_tax_type_exemptions` ###

DROP TABLE IF EXISTS `0_item_tax_type_exemptions`;

CREATE TABLE `0_item_tax_type_exemptions` (
  `item_tax_type_id` int(11) NOT NULL default '0',
  `tax_type_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`item_tax_type_id`,`tax_type_id`)
) ENGINE=InnoDB  ;


### Data of table `0_item_tax_type_exemptions` ###



### Structure of table `0_item_tax_types` ###

DROP TABLE IF EXISTS `0_item_tax_types`;

CREATE TABLE `0_item_tax_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `exempt` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  ;


### Data of table `0_item_tax_types` ###



### Structure of table `0_item_units` ###

DROP TABLE IF EXISTS `0_item_units`;

CREATE TABLE `0_item_units` (
  `abbr` varchar(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `decimals` tinyint(2) NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`abbr`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  ;


### Data of table `0_item_units` ###



### Structure of table `0_loc_stock` ###

DROP TABLE IF EXISTS `0_loc_stock`;

CREATE TABLE `0_loc_stock` (
  `loc_code` char(5) NOT NULL default '',
  `stock_id` char(20) NOT NULL default '',
  `reorder_level` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`loc_code`,`stock_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=InnoDB  ;


### Data of table `0_loc_stock` ###



### Structure of table `0_locations` ###

DROP TABLE IF EXISTS `0_locations`;

CREATE TABLE `0_locations` (
  `loc_code` varchar(5) NOT NULL default '',
  `location_name` varchar(60) NOT NULL default '',
  `delivery_address` tinytext NOT NULL,
  `phone` varchar(30) NOT NULL default '',
  `phone2` varchar(30) NOT NULL default '',
  `fax` varchar(30) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `contact` varchar(30) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`loc_code`)
) ENGINE=MyISAM  ;


### Data of table `0_locations` ###

INSERT INTO `0_locations` VALUES ('STD', 'Standard', 'N/A', '', '', '', '', '', '0');


### Structure of table `0_movement_types` ###

DROP TABLE IF EXISTS `0_movement_types`;

CREATE TABLE `0_movement_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2  ;


### Data of table `0_movement_types` ###

INSERT INTO `0_movement_types` VALUES ('1', 'Justering', '0');


### Structure of table `0_payment_terms` ###

DROP TABLE IF EXISTS `0_payment_terms`;

CREATE TABLE `0_payment_terms` (
  `terms_indicator` int(11) NOT NULL auto_increment,
  `terms` char(80) NOT NULL default '',
  `days_before_due` smallint(6) NOT NULL default '0',
  `day_in_following_month` smallint(6) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`terms_indicator`),
  UNIQUE KEY `terms` (`terms`)
) ENGINE=MyISAM AUTO_INCREMENT=5  ;


### Data of table `0_payment_terms` ###

INSERT INTO `0_payment_terms` VALUES ('1', '10 dage netto', '10', '0', '0');
INSERT INTO `0_payment_terms` VALUES ('2', 'Netto kontant', '1', '0', '0');
INSERT INTO `0_payment_terms` VALUES ('3', 'L�bende m�ned + 15 dage', '0', '17', '0');
INSERT INTO `0_payment_terms` VALUES ('4', 'L�bende m�ned + 30 dage', '0', '30', '0');


### Structure of table `0_prices` ###

DROP TABLE IF EXISTS `0_prices`;

CREATE TABLE `0_prices` (
  `id` int(11) NOT NULL auto_increment,
  `stock_id` varchar(20) NOT NULL default '',
  `sales_type_id` int(11) NOT NULL default '0',
  `curr_abrev` char(3) NOT NULL default '',
  `price` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `price` (`stock_id`,`sales_type_id`,`curr_abrev`)
) ENGINE=MyISAM  ;


### Data of table `0_prices` ###



### Structure of table `0_print_profiles` ###

DROP TABLE IF EXISTS `0_print_profiles`;

CREATE TABLE `0_print_profiles` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `profile` varchar(30) NOT NULL,
  `report` varchar(5) default NULL,
  `printer` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `profile` (`profile`,`report`)
) ENGINE=MyISAM AUTO_INCREMENT=10  ;


### Data of table `0_print_profiles` ###

INSERT INTO `0_print_profiles` VALUES ('1', 'Out of office', NULL, '0');
INSERT INTO `0_print_profiles` VALUES ('2', 'Sales Department', NULL, '0');
INSERT INTO `0_print_profiles` VALUES ('3', 'Central', NULL, '2');
INSERT INTO `0_print_profiles` VALUES ('4', 'Sales Department', '104', '2');
INSERT INTO `0_print_profiles` VALUES ('5', 'Sales Department', '105', '2');
INSERT INTO `0_print_profiles` VALUES ('6', 'Sales Department', '107', '2');
INSERT INTO `0_print_profiles` VALUES ('7', 'Sales Department', '109', '2');
INSERT INTO `0_print_profiles` VALUES ('8', 'Sales Department', '110', '2');
INSERT INTO `0_print_profiles` VALUES ('9', 'Sales Department', '201', '2');


### Structure of table `0_printers` ###

DROP TABLE IF EXISTS `0_printers`;

CREATE TABLE `0_printers` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(60) NOT NULL,
  `queue` varchar(20) NOT NULL,
  `host` varchar(40) NOT NULL,
  `port` smallint(11) unsigned NOT NULL,
  `timeout` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4  ;


### Data of table `0_printers` ###

INSERT INTO `0_printers` VALUES ('1', 'QL500', 'Label printer', 'QL500', 'server', '127', '20');
INSERT INTO `0_printers` VALUES ('2', 'Samsung', 'Main network printer', 'scx4521F', 'server', '515', '5');
INSERT INTO `0_printers` VALUES ('3', 'Local', 'Local print server at user IP', 'lp', '', '515', '10');


### Structure of table `0_purch_data` ###

DROP TABLE IF EXISTS `0_purch_data`;

CREATE TABLE `0_purch_data` (
  `supplier_id` int(11) NOT NULL default '0',
  `stock_id` char(20) NOT NULL default '',
  `price` double NOT NULL default '0',
  `suppliers_uom` char(50) NOT NULL default '',
  `conversion_factor` double NOT NULL default '1',
  `supplier_description` char(50) NOT NULL default '',
  PRIMARY KEY  (`supplier_id`,`stock_id`)
) ENGINE=MyISAM  ;


### Data of table `0_purch_data` ###



### Structure of table `0_purch_order_details` ###

DROP TABLE IF EXISTS `0_purch_order_details`;

CREATE TABLE `0_purch_order_details` (
  `po_detail_item` int(11) NOT NULL auto_increment,
  `order_no` int(11) NOT NULL default '0',
  `item_code` varchar(20) NOT NULL default '',
  `description` tinytext,
  `delivery_date` date NOT NULL default '0000-00-00',
  `qty_invoiced` double NOT NULL default '0',
  `unit_price` double NOT NULL default '0',
  `act_price` double NOT NULL default '0',
  `std_cost_unit` double NOT NULL default '0',
  `quantity_ordered` double NOT NULL default '0',
  `quantity_received` double NOT NULL default '0',
  PRIMARY KEY  (`po_detail_item`),
  KEY `order` (`order_no`,`po_detail_item`)
) ENGINE=InnoDB  ;


### Data of table `0_purch_order_details` ###



### Structure of table `0_purch_orders` ###

DROP TABLE IF EXISTS `0_purch_orders`;

CREATE TABLE `0_purch_orders` (
  `order_no` int(11) NOT NULL auto_increment,
  `supplier_id` int(11) NOT NULL default '0',
  `comments` tinytext,
  `ord_date` date NOT NULL default '0000-00-00',
  `reference` tinytext NOT NULL,
  `requisition_no` tinytext,
  `into_stock_location` varchar(5) NOT NULL default '',
  `delivery_address` tinytext NOT NULL,
  `total` double NOT NULL default '0',
  `tax_included` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`order_no`),
  KEY `ord_date` (`ord_date`)
) ENGINE=InnoDB  ;


### Data of table `0_purch_orders` ###



### Structure of table `0_quick_entries` ###

DROP TABLE IF EXISTS `0_quick_entries`;

CREATE TABLE `0_quick_entries` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `type` tinyint(1) NOT NULL default '0',
  `description` varchar(60) NOT NULL,
  `base_amount` double NOT NULL default '0',
  `base_desc` varchar(60) default NULL,
  `bal_type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `description` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=4  ;


### Data of table `0_quick_entries` ###

INSERT INTO `0_quick_entries` VALUES ('1', '1', 'Maintenance', '0', 'Amount', '0');
INSERT INTO `0_quick_entries` VALUES ('2', '1', 'Phone', '0', 'Amount', '0');
INSERT INTO `0_quick_entries` VALUES ('3', '2', 'Cash Sales', '0', 'Amount', '0');


### Structure of table `0_quick_entry_lines` ###

DROP TABLE IF EXISTS `0_quick_entry_lines`;

CREATE TABLE `0_quick_entry_lines` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `qid` smallint(6) unsigned NOT NULL,
  `amount` double default '0',
  `action` varchar(2) NOT NULL,
  `dest_id` varchar(15) NOT NULL default '',
  `dimension_id` smallint(6) unsigned default NULL,
  `dimension2_id` smallint(6) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `qid` (`qid`)
) ENGINE=MyISAM AUTO_INCREMENT=4  ;


### Data of table `0_quick_entry_lines` ###

INSERT INTO `0_quick_entry_lines` VALUES ('1', '1', '0', '=', '6600', '0', '0');
INSERT INTO `0_quick_entry_lines` VALUES ('2', '2', '0', '=', '6730', '0', '0');
INSERT INTO `0_quick_entry_lines` VALUES ('3', '3', '0', '=', '3000', '0', '0');


### Structure of table `0_recurrent_invoices` ###

DROP TABLE IF EXISTS `0_recurrent_invoices`;

CREATE TABLE `0_recurrent_invoices` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `order_no` int(11) unsigned NOT NULL,
  `debtor_no` int(11) unsigned default NULL,
  `group_no` smallint(6) unsigned default NULL,
  `days` int(11) NOT NULL default '0',
  `monthly` int(11) NOT NULL default '0',
  `begin` date NOT NULL default '0000-00-00',
  `end` date NOT NULL default '0000-00-00',
  `last_sent` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB  ;


### Data of table `0_recurrent_invoices` ###



### Structure of table `0_refs` ###

DROP TABLE IF EXISTS `0_refs`;

CREATE TABLE `0_refs` (
  `id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `reference` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`,`type`),
  KEY `Type_and_Reference` (`type`,`reference`)
) ENGINE=InnoDB  ;


### Data of table `0_refs` ###



### Structure of table `0_sales_order_details` ###

DROP TABLE IF EXISTS `0_sales_order_details`;

CREATE TABLE `0_sales_order_details` (
  `id` int(11) NOT NULL auto_increment,
  `order_no` int(11) NOT NULL default '0',
  `trans_type` smallint(6) NOT NULL default '30',
  `stk_code` varchar(20) NOT NULL default '',
  `description` tinytext,
  `qty_sent` double NOT NULL default '0',
  `unit_price` double NOT NULL default '0',
  `quantity` double NOT NULL default '0',
  `discount_percent` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sorder` (`trans_type`,`order_no`)
) ENGINE=InnoDB  ;


### Data of table `0_sales_order_details` ###



### Structure of table `0_sales_orders` ###

DROP TABLE IF EXISTS `0_sales_orders`;

CREATE TABLE `0_sales_orders` (
  `order_no` int(11) NOT NULL,
  `trans_type` smallint(6) NOT NULL default '30',
  `version` tinyint(1) unsigned NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  `debtor_no` int(11) NOT NULL default '0',
  `branch_code` int(11) NOT NULL default '0',
  `reference` varchar(100) NOT NULL default '',
  `customer_ref` tinytext NOT NULL,
  `comments` tinytext,
  `ord_date` date NOT NULL default '0000-00-00',
  `order_type` int(11) NOT NULL default '0',
  `ship_via` int(11) NOT NULL default '0',
  `delivery_address` tinytext NOT NULL,
  `contact_phone` varchar(30) default NULL,
  `contact_email` varchar(100) default NULL,
  `deliver_to` tinytext NOT NULL,
  `freight_cost` double NOT NULL default '0',
  `from_stk_loc` varchar(5) NOT NULL default '',
  `delivery_date` date NOT NULL default '0000-00-00',
  `payment_terms` int(11) default NULL,
  `total` double NOT NULL default '0',
  PRIMARY KEY  (`trans_type`,`order_no`)
) ENGINE=InnoDB  ;


### Data of table `0_sales_orders` ###



### Structure of table `0_sales_pos` ###

DROP TABLE IF EXISTS `0_sales_pos`;

CREATE TABLE `0_sales_pos` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `pos_name` varchar(30) NOT NULL,
  `cash_sale` tinyint(1) NOT NULL,
  `credit_sale` tinyint(1) NOT NULL,
  `pos_location` varchar(5) NOT NULL,
  `pos_account` smallint(6) unsigned NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pos_name` (`pos_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2  ;


### Data of table `0_sales_pos` ###

INSERT INTO `0_sales_pos` VALUES ('1', 'Default', '1', '1', 'DEF', '1', '0');


### Structure of table `0_sales_types` ###

DROP TABLE IF EXISTS `0_sales_types`;

CREATE TABLE `0_sales_types` (
  `id` int(11) NOT NULL auto_increment,
  `sales_type` char(50) NOT NULL default '',
  `tax_included` int(1) NOT NULL default '0',
  `factor` double NOT NULL default '1',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sales_type` (`sales_type`)
) ENGINE=MyISAM AUTO_INCREMENT=3  ;


### Data of table `0_sales_types` ###

INSERT INTO `0_sales_types` VALUES ('1', 'Slutkunde', '1', '1', '0');
INSERT INTO `0_sales_types` VALUES ('2', 'Forhandler', '0', '1', '0');


### Structure of table `0_salesman` ###

DROP TABLE IF EXISTS `0_salesman`;

CREATE TABLE `0_salesman` (
  `salesman_code` int(11) NOT NULL auto_increment,
  `salesman_name` char(60) NOT NULL default '',
  `salesman_phone` char(30) NOT NULL default '',
  `salesman_fax` char(30) NOT NULL default '',
  `salesman_email` varchar(100) NOT NULL default '',
  `provision` double NOT NULL default '0',
  `break_pt` double NOT NULL default '0',
  `provision2` double NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`salesman_code`),
  UNIQUE KEY `salesman_name` (`salesman_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2  ;


### Data of table `0_salesman` ###

INSERT INTO `0_salesman` VALUES ('1', 'Supers�lgeren', '', '', '', '0', '0', '0', '0');


### Structure of table `0_security_roles` ###

DROP TABLE IF EXISTS `0_security_roles`;

CREATE TABLE `0_security_roles` (
  `id` int(11) NOT NULL auto_increment,
  `role` varchar(30) NOT NULL,
  `description` varchar(50) default NULL,
  `sections` text,
  `areas` text,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=MyISAM AUTO_INCREMENT=4  ;


### Data of table `0_security_roles` ###

INSERT INTO `0_security_roles` VALUES(1, 'Inquiries', 'Inquiries', '768;2816;3072;3328;5632;5888;8192;8448;10752;11008;13312;15872;16128', '257;258;259;260;513;514;515;516;517;518;519;520;521;522;523;524;525;773;774;2822;3073;3075;3076;3077;3329;3330;3331;3332;3333;3334;3335;5377;5633;5640;5889;5890;5891;7937;7938;7939;7940;8193;8194;8450;8451;10497;10753;11009;11010;11012;13313;13315;15617;15618;15619;15620;15621;15622;15623;15624;15625;15626;15873;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(2, 'System Administrator', 'System Administrator', '256;512;768;2816;3072;3328;5376;5632;5888;7936;8192;8448;10496;10752;11008;13056;13312;15616;15872;16128', '257;258;259;260;513;514;515;516;517;518;519;520;521;522;523;524;525;526;769;770;771;772;773;774;2817;2818;2819;2820;2821;2822;2823;3073;3074;3082;3075;3076;3077;3078;3079;3080;3081;3329;3330;3331;3332;3333;3334;3335;5377;5633;5634;5635;5636;5637;5641;5638;5639;5640;5889;5890;5891;7937;7938;7939;7940;8193;8194;8195;8196;8197;8449;8450;8451;10497;10753;10754;10755;10756;10757;11009;11010;11011;11012;13057;13313;13314;13315;15617;15618;15619;15620;15621;15622;15623;15624;15628;15625;15626;15627;15873;15874;15875;15876;15877;15878;15879;15880;15883;15881;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(3, 'Salesman', 'Salesman', '768;3072;5632;8192;15872', '773;774;3073;3075;3081;5633;8194;15873', 0);
INSERT INTO `0_security_roles` VALUES(4, 'Stock Manager', 'Stock Manager', '2816;3072;3328;5632;5888;8192;8448;10752;11008;13312;15872;16128', '2818;2822;3073;3076;3077;3329;3330;3330;3330;3331;3331;3332;3333;3334;3335;5633;5640;5889;5890;5891;8193;8194;8450;8451;10753;11009;11010;11012;13313;13315;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(5, 'Production Manager', 'Production Manager', '512;2816;3072;3328;5632;5888;8192;8448;10752;11008;13312;15616;15872;16128', '521;523;524;2818;2819;2820;2821;2822;2823;3073;3074;3076;3077;3078;3079;3080;3081;3329;3330;3330;3330;3331;3331;3332;3333;3334;3335;5633;5640;5640;5889;5890;5891;8193;8194;8196;8197;8450;8451;10753;10755;11009;11010;11012;13313;13315;15617;15619;15620;15621;15624;15624;15876;15877;15880;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(6, 'Purchase Officer', 'Purchase Officer', '512;2816;3072;3328;5376;5632;5888;8192;8448;10752;11008;13312;15616;15872;16128', '521;523;524;2818;2819;2820;2821;2822;2823;3073;3074;3076;3077;3078;3079;3080;3081;3329;3330;3330;3330;3331;3331;3332;3333;3334;3335;5377;5633;5635;5640;5640;5889;5890;5891;8193;8194;8196;8197;8449;8450;8451;10753;10755;11009;11010;11012;13313;13315;15617;15619;15620;15621;15624;15624;15876;15877;15880;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(7, 'AR Officer', 'AR Officer', '512;768;2816;3072;3328;5632;5888;8192;8448;10752;11008;13312;15616;15872;16128', '521;523;524;771;773;774;2818;2819;2820;2821;2822;2823;3073;3073;3074;3075;3076;3077;3078;3079;3080;3081;3081;3329;3330;3330;3330;3331;3331;3332;3333;3334;3335;5633;5633;5634;5637;5638;5639;5640;5640;5889;5890;5891;8193;8194;8194;8196;8197;8450;8451;10753;10755;11009;11010;11012;13313;13315;15617;15619;15620;15621;15624;15624;15873;15876;15877;15878;15880;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(8, 'AP Officer', 'AP Officer', '512;2816;3072;3328;5376;5632;5888;8192;8448;10752;11008;13312;15616;15872;16128', '257;258;259;260;521;523;524;769;770;771;772;773;774;2818;2819;2820;2821;2822;2823;3073;3074;3082;3076;3077;3078;3079;3080;3081;3329;3330;3331;3332;3333;3334;3335;5377;5633;5635;5640;5889;5890;5891;7937;7938;7939;7940;8193;8194;8196;8197;8449;8450;8451;10497;10753;10755;11009;11010;11012;13057;13313;13315;15617;15619;15620;15621;15624;15876;15877;15880;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(9, 'Accountant', 'New Accountant', '512;768;2816;3072;3328;5376;5632;5888;8192;8448;10752;11008;13312;15616;15872;16128', '257;258;259;260;521;523;524;771;772;773;774;2818;2819;2820;2821;2822;2823;3073;3074;3075;3076;3077;3078;3079;3080;3081;3329;3330;3331;3332;3333;3334;3335;5377;5633;5634;5635;5637;5638;5639;5640;5889;5890;5891;7937;7938;7939;7940;8193;8194;8196;8197;8449;8450;8451;10497;10753;10755;11009;11010;11012;13313;13315;15617;15618;15619;15620;15621;15624;15873;15876;15877;15878;15880;15882;16129;16130;16131;16132', 0);
INSERT INTO `0_security_roles` VALUES(10, 'Sub Admin', 'Sub Admin', '512;768;2816;3072;3328;5376;5632;5888;8192;8448;10752;11008;13312;15616;15872;16128', '257;258;259;260;521;523;524;771;772;773;774;2818;2819;2820;2821;2822;2823;3073;3074;3082;3075;3076;3077;3078;3079;3080;3081;3329;3330;3331;3332;3333;3334;3335;5377;5633;5634;5635;5637;5638;5639;5640;5889;5890;5891;7937;7938;7939;7940;8193;8194;8196;8197;8449;8450;8451;10497;10753;10755;11009;11010;11012;13057;13313;13315;15617;15619;15620;15621;15624;15873;15874;15876;15877;15878;15879;15880;15882;16129;16130;16131;16132', 0);


### Structure of table `0_shippers` ###

DROP TABLE IF EXISTS `0_shippers`;

CREATE TABLE `0_shippers` (
  `shipper_id` int(11) NOT NULL auto_increment,
  `shipper_name` varchar(60) NOT NULL default '',
  `phone` varchar(30) NOT NULL default '',
  `phone2` varchar(30) NOT NULL default '',
  `contact` tinytext NOT NULL,
  `address` tinytext NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`shipper_id`),
  UNIQUE KEY `name` (`shipper_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2  ;


### Data of table `0_shippers` ###

INSERT INTO `0_shippers` VALUES ('1', 'Standard', '', '', '', '', '0');


### Structure of table `0_sql_trail` ###

DROP TABLE IF EXISTS `0_sql_trail`;

CREATE TABLE `0_sql_trail` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `sql` text NOT NULL,
  `result` tinyint(1) NOT NULL,
  `msg` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  ;


### Data of table `0_sql_trail` ###



### Structure of table `0_stock_category` ###

DROP TABLE IF EXISTS `0_stock_category`;

CREATE TABLE `0_stock_category` (
  `category_id` int(11) NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  `dflt_tax_type` int(11) NOT NULL default '1',
  `dflt_units` varchar(20) NOT NULL default 'each',
  `dflt_mb_flag` char(1) NOT NULL default 'B',
  `dflt_sales_act` varchar(15) NOT NULL default '',
  `dflt_cogs_act` varchar(15) NOT NULL default '',
  `dflt_inventory_act` varchar(15) NOT NULL default '',
  `dflt_adjustment_act` varchar(15) NOT NULL default '',
  `dflt_assembly_act` varchar(15) NOT NULL default '',
  `dflt_dim1` int(11) default NULL,
  `dflt_dim2` int(11) default NULL,
  `dflt_no_sale` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=5  ;


### Data of table `0_stock_category` ###

INSERT INTO `0_stock_category` VALUES ('1', 'Komponent', '0', '1', 'each', 'B', '1100', '2100', '7710', '2790', '7750', '0', '0', '0');
INSERT INTO `0_stock_category` VALUES ('2', 'Afgift', '0', '1', 'each', 'B', '1100', '2100', '7710', '2790', '7750', '0', '0', '0');
INSERT INTO `0_stock_category` VALUES ('3', 'System', '0', '1', 'each', 'B', '1100', '2100', '7710', '2790', '7750', '0', '0', '0');
INSERT INTO `0_stock_category` VALUES ('4', 'Tjeneste', '0', '1', 'each', 'B', '1100', '2100', '7710', '2790', '7750', '0', '0', '0');


### Structure of table `0_stock_master` ###

DROP TABLE IF EXISTS `0_stock_master`;

CREATE TABLE `0_stock_master` (
  `stock_id` varchar(20) NOT NULL default '',
  `category_id` int(11) NOT NULL default '0',
  `tax_type_id` int(11) NOT NULL default '0',
  `description` varchar(200) NOT NULL default '',
  `long_description` tinytext NOT NULL,
  `units` varchar(20) NOT NULL default 'each',
  `mb_flag` char(1) NOT NULL default 'B',
  `sales_account` varchar(15) NOT NULL default '',
  `cogs_account` varchar(15) NOT NULL default '',
  `inventory_account` varchar(15) NOT NULL default '',
  `adjustment_account` varchar(15) NOT NULL default '',
  `assembly_account` varchar(15) NOT NULL default '',
  `dimension_id` int(11) default NULL,
  `dimension2_id` int(11) default NULL,
  `actual_cost` double NOT NULL default '0',
  `last_cost` double NOT NULL default '0',
  `material_cost` double NOT NULL default '0',
  `labour_cost` double NOT NULL default '0',
  `overhead_cost` double NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  `no_sale` tinyint(1) NOT NULL default '0',
  `editable` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`stock_id`)
) ENGINE=InnoDB  ;


### Data of table `0_stock_master` ###



### Structure of table `0_stock_moves` ###

DROP TABLE IF EXISTS `0_stock_moves`;

CREATE TABLE `0_stock_moves` (
  `trans_id` int(11) NOT NULL auto_increment,
  `trans_no` int(11) NOT NULL default '0',
  `stock_id` char(20) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  `loc_code` char(5) NOT NULL default '',
  `tran_date` date NOT NULL default '0000-00-00',
  `person_id` int(11) default NULL,
  `price` double NOT NULL default '0',
  `reference` char(40) NOT NULL default '',
  `qty` double NOT NULL default '1',
  `discount_percent` double NOT NULL default '0',
  `standard_cost` double NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`trans_id`),
  KEY `type` (`type`,`trans_no`),
  KEY `Move` (`stock_id`,`loc_code`,`tran_date`)
) ENGINE=InnoDB  ;


### Data of table `0_stock_moves` ###



### Structure of table `0_supp_allocations` ###

DROP TABLE IF EXISTS `0_supp_allocations`;

CREATE TABLE `0_supp_allocations` (
  `id` int(11) NOT NULL auto_increment,
  `amt` double unsigned default NULL,
  `date_alloc` date NOT NULL default '0000-00-00',
  `trans_no_from` int(11) default NULL,
  `trans_type_from` int(11) default NULL,
  `trans_no_to` int(11) default NULL,
  `trans_type_to` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `From` (`trans_type_from`,`trans_no_from`),
  KEY `To` (`trans_type_to`,`trans_no_to`)
) ENGINE=InnoDB  ;


### Data of table `0_supp_allocations` ###



### Structure of table `0_supp_invoice_items` ###

DROP TABLE IF EXISTS `0_supp_invoice_items`;

CREATE TABLE `0_supp_invoice_items` (
  `id` int(11) NOT NULL auto_increment,
  `supp_trans_no` int(11) default NULL,
  `supp_trans_type` int(11) default NULL,
  `gl_code` varchar(15) NOT NULL default '',
  `grn_item_id` int(11) default NULL,
  `po_detail_item_id` int(11) default NULL,
  `stock_id` varchar(20) NOT NULL default '',
  `description` tinytext,
  `quantity` double NOT NULL default '0',
  `unit_price` double NOT NULL default '0',
  `unit_tax` double NOT NULL default '0',
  `memo_` tinytext,
  PRIMARY KEY  (`id`),
  KEY `Transaction` (`supp_trans_type`,`supp_trans_no`,`stock_id`)
) ENGINE=InnoDB  ;


### Data of table `0_supp_invoice_items` ###



### Structure of table `0_supp_trans` ###

DROP TABLE IF EXISTS `0_supp_trans`;

CREATE TABLE `0_supp_trans` (
  `trans_no` int(11) unsigned NOT NULL default '0',
  `type` smallint(6) unsigned NOT NULL default '0',
  `supplier_id` int(11) unsigned default NULL,
  `reference` tinytext NOT NULL,
  `supp_reference` varchar(60) NOT NULL default '',
  `tran_date` date NOT NULL default '0000-00-00',
  `due_date` date NOT NULL default '0000-00-00',
  `ov_amount` double NOT NULL default '0',
  `ov_discount` double NOT NULL default '0',
  `ov_gst` double NOT NULL default '0',
  `rate` double NOT NULL default '1',
  `alloc` double NOT NULL default '0',
  `tax_included` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`type`,`trans_no`),
  KEY `supplier_id` (`supplier_id`),
  KEY `SupplierID_2` (`supplier_id`,`supp_reference`),
  KEY `type` (`type`),
  KEY `tran_date` (`tran_date`)
) ENGINE=InnoDB  ;


### Data of table `0_supp_trans` ###



### Structure of table `0_suppliers` ###

DROP TABLE IF EXISTS `0_suppliers`;

CREATE TABLE `0_suppliers` (
  `supplier_id` int(11) NOT NULL auto_increment,
  `supp_name` varchar(60) NOT NULL default '',
  `address` tinytext NOT NULL,
  `supp_address` tinytext NOT NULL,
  `gst_no` varchar(25) NOT NULL default '',
  `contact` varchar(60) NOT NULL default '',
  `supp_account_no` varchar(40) NOT NULL default '',
  `website` varchar(100) NOT NULL default '',
  `bank_account` varchar(60) NOT NULL default '',
  `curr_code` char(3) default NULL,
  `payment_terms` int(11) default NULL,
  `tax_included` tinyint(1) NOT NULL default '0',
  `dimension_id` int(11) default '0',
  `dimension2_id` int(11) default '0',
  `tax_group_id` int(11) default NULL,
  `credit_limit` double NOT NULL default '0',
  `purchase_account` varchar(15) NOT NULL default '',
  `payable_account` varchar(15) NOT NULL default '',
  `payment_discount_account` varchar(15) NOT NULL default '',
  `notes` tinytext NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  `supp_ref` varchar(30) NOT NULL,
  PRIMARY KEY  (`supplier_id`),
  KEY `supp_ref` (`supp_ref`)
) ENGINE=MyISAM  ;


### Data of table `0_suppliers` ###



### Structure of table `0_sys_prefs` ###

DROP TABLE IF EXISTS `0_sys_prefs`;

CREATE TABLE `0_sys_prefs` (
  `name` varchar(35) NOT NULL default '',
  `category` varchar(30) default NULL,
  `type` varchar(20) NOT NULL default '',
  `length` smallint(6) default NULL,
  `value` tinytext,
  PRIMARY KEY  (`name`),
  KEY `category` (`category`)
) ENGINE=MyISAM  ;


### Data of table `0_sys_prefs` ###

INSERT INTO `0_sys_prefs` VALUES ('coy_name', 'setup.company', 'varchar', '60', 'Tr�ningskompagniet ApS');
INSERT INTO `0_sys_prefs` VALUES ('gst_no', 'setup.company', 'varchar', '25', 'DK999999999901');
INSERT INTO `0_sys_prefs` VALUES ('coy_no', 'setup.company', 'varchar', '25', '999999-9999');
INSERT INTO `0_sys_prefs` VALUES ('tax_prd', 'setup.company', 'int', '11', '1');
INSERT INTO `0_sys_prefs` VALUES ('tax_last', 'setup.company', 'int', '11', '1');
INSERT INTO `0_sys_prefs` VALUES ('postal_address', 'setup.company', 'tinytext', '0', 'Tr�ningsgade 5\r\nDK-6000 Kolding');
INSERT INTO `0_sys_prefs` VALUES ('phone', 'setup.company', 'varchar', '30', '99-99 99 99');
INSERT INTO `0_sys_prefs` VALUES ('fax', 'setup.company', 'varchar', '30', '99-99 99 98');
INSERT INTO `0_sys_prefs` VALUES ('email', 'setup.company', 'varchar', '100', 'demo@demo.dm');
INSERT INTO `0_sys_prefs` VALUES ('coy_logo', 'setup.company', 'varchar', '100', NULL);
INSERT INTO `0_sys_prefs` VALUES ('domicile', 'setup.company', 'varchar', '55', NULL);
INSERT INTO `0_sys_prefs` VALUES ('curr_default', 'setup.company', 'char', '3', 'DKK');
INSERT INTO `0_sys_prefs` VALUES ('use_dimension', 'setup.company', 'tinyint', '1', '1');
INSERT INTO `0_sys_prefs` VALUES ('f_year', 'setup.company', 'int', '11', '1');
INSERT INTO `0_sys_prefs` VALUES ('no_item_list', 'setup.company', 'tinyint', '1', '0');
INSERT INTO `0_sys_prefs` VALUES ('no_customer_list', 'setup.company', 'tinyint', '1', '0');
INSERT INTO `0_sys_prefs` VALUES ('no_supplier_list', 'setup.company', 'tinyint', '1', '0');
INSERT INTO `0_sys_prefs` VALUES ('base_sales', 'setup.company', 'int', '11', '-1');
INSERT INTO `0_sys_prefs` VALUES ('time_zone', 'setup.company', 'tinyint', '1', '0');
INSERT INTO `0_sys_prefs` VALUES ('add_pct', 'setup.company', 'int', '5', '-1');
INSERT INTO `0_sys_prefs` VALUES ('round_to', 'setup.company', 'int', '5', '1');
INSERT INTO `0_sys_prefs` VALUES ('login_tout', 'setup.company', 'smallint', '6', '600');
INSERT INTO `0_sys_prefs` VALUES ('past_due_days', 'glsetup.general', 'int', '11', '30');
INSERT INTO `0_sys_prefs` VALUES ('profit_loss_year_act', 'glsetup.general', 'varchar', '15', '9990');
INSERT INTO `0_sys_prefs` VALUES ('retained_earnings_act', 'glsetup.general', 'varchar', '15', '1210');
INSERT INTO `0_sys_prefs` VALUES ('bank_charge_act', 'glsetup.general', 'varchar', '15', '2100');
INSERT INTO `0_sys_prefs` VALUES ('exchange_diff_act', 'glsetup.general', 'varchar', '15', '2820');
INSERT INTO `0_sys_prefs` VALUES ('default_credit_limit', 'glsetup.customer', 'int', '11', '1000');
INSERT INTO `0_sys_prefs` VALUES ('accumulate_shipping', 'glsetup.customer', 'tinyint', '1', '0');
INSERT INTO `0_sys_prefs` VALUES ('legal_text', 'glsetup.customer', 'tinytext', '0', NULL);
INSERT INTO `0_sys_prefs` VALUES ('freight_act', 'glsetup.customer', 'varchar', '15', '1210');
INSERT INTO `0_sys_prefs` VALUES ('debtors_act', 'glsetup.sales', 'varchar', '15', '7810');
INSERT INTO `0_sys_prefs` VALUES ('default_sales_act', 'glsetup.sales', 'varchar', '15', '1100');
INSERT INTO `0_sys_prefs` VALUES ('default_sales_discount_act', 'glsetup.sales', 'varchar', '15', '1810');
INSERT INTO `0_sys_prefs` VALUES ('default_prompt_payment_act', 'glsetup.sales', 'varchar', '15', '1810');
INSERT INTO `0_sys_prefs` VALUES ('default_delivery_required', 'glsetup.sales', 'smallint', '6', '1');
INSERT INTO `0_sys_prefs` VALUES ('default_dim_required', 'glsetup.dims', 'int', '11', '20');
INSERT INTO `0_sys_prefs` VALUES ('pyt_discount_act', 'glsetup.purchase', 'varchar', '15', '2810');
INSERT INTO `0_sys_prefs` VALUES ('creditors_act', 'glsetup.purchase', 'varchar', '15', '9360');
INSERT INTO `0_sys_prefs` VALUES ('po_over_receive', 'glsetup.purchase', 'int', '11', '10');
INSERT INTO `0_sys_prefs` VALUES ('po_over_charge', 'glsetup.purchase', 'int', '11', '10');
INSERT INTO `0_sys_prefs` VALUES ('allow_negative_stock', 'glsetup.inventory', 'tinyint', '1', '0');
INSERT INTO `0_sys_prefs` VALUES ('default_inventory_act', 'glsetup.items', 'varchar', '15', '7710');
INSERT INTO `0_sys_prefs` VALUES ('default_cogs_act', 'glsetup.items', 'varchar', '15', '2100');
INSERT INTO `0_sys_prefs` VALUES ('default_adj_act', 'glsetup.items', 'varchar', '15', '2790');
INSERT INTO `0_sys_prefs` VALUES ('default_inv_sales_act', 'glsetup.items', 'varchar', '15', '1100');
INSERT INTO `0_sys_prefs` VALUES ('default_assembly_act', 'glsetup.items', 'varchar', '15', '7750');
INSERT INTO `0_sys_prefs` VALUES ('default_workorder_required', 'glsetup.manuf', 'int', '11', '20');
INSERT INTO `0_sys_prefs` VALUES ('version_id', 'system', 'varchar', '11', '2.3rc');
INSERT INTO `0_sys_prefs` VALUES ('auto_curr_reval', 'setup.company', 'smallint', '6', '1');


### Structure of table `0_sys_types` ###

DROP TABLE IF EXISTS `0_sys_types`;

CREATE TABLE `0_sys_types` (
  `type_id` smallint(6) NOT NULL default '0',
  `type_no` int(11) NOT NULL default '1',
  `next_reference` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB  ;


### Data of table `0_sys_types` ###

INSERT INTO `0_sys_types` VALUES ('0', '0', '1');
INSERT INTO `0_sys_types` VALUES ('1', '0', '1');
INSERT INTO `0_sys_types` VALUES ('2', '0', '1');
INSERT INTO `0_sys_types` VALUES ('4', '0', '1');
INSERT INTO `0_sys_types` VALUES ('10', '1', '2');
INSERT INTO `0_sys_types` VALUES ('11', '0', '1');
INSERT INTO `0_sys_types` VALUES ('12', '0', '1');
INSERT INTO `0_sys_types` VALUES ('13', '0', '1');
INSERT INTO `0_sys_types` VALUES ('16', '0', '1');
INSERT INTO `0_sys_types` VALUES ('17', '1', '2');
INSERT INTO `0_sys_types` VALUES ('18', '0', '1');
INSERT INTO `0_sys_types` VALUES ('20', '0', '1');
INSERT INTO `0_sys_types` VALUES ('21', '0', '1');
INSERT INTO `0_sys_types` VALUES ('22', '0', '1');
INSERT INTO `0_sys_types` VALUES ('25', '0', '1');
INSERT INTO `0_sys_types` VALUES ('26', '0', '2');
INSERT INTO `0_sys_types` VALUES ('28', '0', '1');
INSERT INTO `0_sys_types` VALUES ('29', '0', '1');
INSERT INTO `0_sys_types` VALUES ('30', '0', '1');
INSERT INTO `0_sys_types` VALUES ('32', '0', '1');
INSERT INTO `0_sys_types` VALUES ('35', '0', '1');
INSERT INTO `0_sys_types` VALUES ('40', '0', '2');


### Structure of table `0_tag_associations` ###

DROP TABLE IF EXISTS `0_tag_associations`;

CREATE TABLE `0_tag_associations` (
  `record_id` varchar(15) NOT NULL,
  `tag_id` int(11) NOT NULL,
  UNIQUE KEY `record_id` (`record_id`,`tag_id`)
) ENGINE=MyISAM  ;


### Data of table `0_tag_associations` ###



### Structure of table `0_tags` ###

DROP TABLE IF EXISTS `0_tags`;

CREATE TABLE `0_tags` (
  `id` int(11) NOT NULL auto_increment,
  `type` smallint(6) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(60) default NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=MyISAM  ;


### Data of table `0_tags` ###



### Structure of table `0_tax_group_items` ###

DROP TABLE IF EXISTS `0_tax_group_items`;

CREATE TABLE `0_tax_group_items` (
  `tax_group_id` int(11) NOT NULL default '0',
  `tax_type_id` int(11) NOT NULL default '0',
  `rate` double NOT NULL default '0',
  PRIMARY KEY  (`tax_group_id`,`tax_type_id`)
) ENGINE=InnoDB  ;


### Data of table `0_tax_group_items` ###

INSERT INTO `0_tax_group_items` VALUES ('1', '1', '25');
INSERT INTO `0_tax_group_items` VALUES ('2', '2', '25');


### Structure of table `0_tax_groups` ###

DROP TABLE IF EXISTS `0_tax_groups`;

CREATE TABLE `0_tax_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `tax_shipping` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3  ;


### Data of table `0_tax_groups` ###

INSERT INTO `0_tax_groups` VALUES ('1', 'Normal moms', '0', '0');
INSERT INTO `0_tax_groups` VALUES ('2', 'Fragt', '1', '0');


### Structure of table `0_tax_types` ###

DROP TABLE IF EXISTS `0_tax_types`;

CREATE TABLE `0_tax_types` (
  `id` int(11) NOT NULL auto_increment,
  `rate` double NOT NULL default '0',
  `sales_gl_code` varchar(15) NOT NULL default '',
  `purchasing_gl_code` varchar(15) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`,`rate`)
) ENGINE=InnoDB AUTO_INCREMENT=2  ;


### Data of table `0_tax_types` ###

INSERT INTO `0_tax_types` VALUES ('1', '25', '2610', '2640', 'Moms', '0');


### Structure of table `0_trans_tax_details` ###

DROP TABLE IF EXISTS `0_trans_tax_details`;

CREATE TABLE `0_trans_tax_details` (
  `id` int(11) NOT NULL auto_increment,
  `trans_type` smallint(6) default NULL,
  `trans_no` int(11) default NULL,
  `tran_date` date NOT NULL,
  `tax_type_id` int(11) NOT NULL default '0',
  `rate` double NOT NULL default '0',
  `ex_rate` double NOT NULL default '1',
  `included_in_price` tinyint(1) NOT NULL default '0',
  `net_amount` double NOT NULL default '0',
  `amount` double NOT NULL default '0',
  `memo` tinytext,
  PRIMARY KEY  (`id`),
  KEY `Type_and_Number` (`trans_type`,`trans_no`),
  KEY `tran_date` (`tran_date`)
) ENGINE=InnoDB  ;


### Data of table `0_trans_tax_details` ###



### Structure of table `0_useronline` ###

DROP TABLE IF EXISTS `0_useronline`;

CREATE TABLE `0_useronline` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(15) NOT NULL default '0',
  `ip` varchar(40) NOT NULL default '',
  `file` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM  ;


### Data of table `0_useronline` ###



### Structure of table `0_users` ###

DROP TABLE IF EXISTS `0_users`;

CREATE TABLE `0_users` (
  `id` smallint(6) NOT NULL auto_increment,
  `user_id` varchar(60) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `real_name` varchar(100) NOT NULL default '',
  `role_id` int(11) NOT NULL default '1',
  `phone` varchar(30) NOT NULL default '',
  `email` varchar(100) default NULL,
  `language` varchar(20) default NULL,
  `date_format` tinyint(1) NOT NULL default '0',
  `date_sep` tinyint(1) NOT NULL default '0',
  `tho_sep` tinyint(1) NOT NULL default '0',
  `dec_sep` tinyint(1) NOT NULL default '0',
  `theme` varchar(20) NOT NULL default 'default',
  `page_size` varchar(20) NOT NULL default 'A4',
  `prices_dec` smallint(6) NOT NULL default '2',
  `qty_dec` smallint(6) NOT NULL default '2',
  `rates_dec` smallint(6) NOT NULL default '4',
  `percent_dec` smallint(6) NOT NULL default '1',
  `show_gl` tinyint(1) NOT NULL default '1',
  `show_codes` tinyint(1) NOT NULL default '0',
  `show_hints` tinyint(1) NOT NULL default '0',
  `last_visit_date` datetime default NULL,
  `query_size` tinyint(1) default '10',
  `graphic_links` tinyint(1) default '1',
  `pos` smallint(6) default '1',
  `print_profile` varchar(30) NOT NULL default '1',
  `rep_popup` tinyint(1) default '1',
  `sticky_doc_date` tinyint(1) default '0',
  `startup_tab` varchar(20) NOT NULL default 'orders',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3  ;


### Data of table `0_users` ###

INSERT INTO `0_users` VALUES ('1', 'demo', '5f4dcc3b5aa765d61d8327deb882cf99', 'Demobruger', '9', '999-999-999', 'demo@demo.nu', 'da_DK', '1', '1', '1', '1', 'default', 'A4', '2', '2', '3', '1', '1', '0', '0', '2007-02-06 19:02:35', '10', '1', '1', '1', '1', '0', 'orders', '0');
INSERT INTO `0_users` VALUES ('2', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'Administrator', '2', '', NULL, 'da_DK', '1', '1', '1', '1', 'default', 'A4', '2', '2', '4', '1', '1', '0', '0', '2007-10-12 11:33:25', '10', '1', '1', '1', '1', '0', 'orders', '0');


### Structure of table `0_voided` ###

DROP TABLE IF EXISTS `0_voided`;

CREATE TABLE `0_voided` (
  `type` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  `memo_` tinytext NOT NULL,
  UNIQUE KEY `id` (`type`,`id`)
) ENGINE=InnoDB  ;


### Data of table `0_voided` ###



### Structure of table `0_wo_issue_items` ###

DROP TABLE IF EXISTS `0_wo_issue_items`;

CREATE TABLE `0_wo_issue_items` (
  `id` int(11) NOT NULL auto_increment,
  `stock_id` varchar(40) default NULL,
  `issue_id` int(11) default NULL,
  `qty_issued` double default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  ;


### Data of table `0_wo_issue_items` ###



### Structure of table `0_wo_issues` ###

DROP TABLE IF EXISTS `0_wo_issues`;

CREATE TABLE `0_wo_issues` (
  `issue_no` int(11) NOT NULL auto_increment,
  `workorder_id` int(11) NOT NULL default '0',
  `reference` varchar(100) default NULL,
  `issue_date` date default NULL,
  `loc_code` varchar(5) default NULL,
  `workcentre_id` int(11) default NULL,
  PRIMARY KEY  (`issue_no`),
  KEY `workorder_id` (`workorder_id`)
) ENGINE=InnoDB  ;


### Data of table `0_wo_issues` ###



### Structure of table `0_wo_manufacture` ###

DROP TABLE IF EXISTS `0_wo_manufacture`;

CREATE TABLE `0_wo_manufacture` (
  `id` int(11) NOT NULL auto_increment,
  `reference` varchar(100) default NULL,
  `workorder_id` int(11) NOT NULL default '0',
  `quantity` double NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  KEY `workorder_id` (`workorder_id`)
) ENGINE=InnoDB  ;


### Data of table `0_wo_manufacture` ###



### Structure of table `0_wo_requirements` ###

DROP TABLE IF EXISTS `0_wo_requirements`;

CREATE TABLE `0_wo_requirements` (
  `id` int(11) NOT NULL auto_increment,
  `workorder_id` int(11) NOT NULL default '0',
  `stock_id` char(20) NOT NULL default '',
  `workcentre` int(11) NOT NULL default '0',
  `units_req` double NOT NULL default '1',
  `std_cost` double NOT NULL default '0',
  `loc_code` char(5) NOT NULL default '',
  `units_issued` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `workorder_id` (`workorder_id`)
) ENGINE=InnoDB  ;


### Data of table `0_wo_requirements` ###



### Structure of table `0_workcentres` ###

DROP TABLE IF EXISTS `0_workcentres`;

CREATE TABLE `0_workcentres` (
  `id` int(11) NOT NULL auto_increment,
  `name` char(40) NOT NULL default '',
  `description` char(50) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  ;


### Data of table `0_workcentres` ###



### Structure of table `0_workorders` ###

DROP TABLE IF EXISTS `0_workorders`;

CREATE TABLE `0_workorders` (
  `id` int(11) NOT NULL auto_increment,
  `wo_ref` varchar(60) NOT NULL default '',
  `loc_code` varchar(5) NOT NULL default '',
  `units_reqd` double NOT NULL default '1',
  `stock_id` varchar(20) NOT NULL default '',
  `date_` date NOT NULL default '0000-00-00',
  `type` tinyint(4) NOT NULL default '0',
  `required_by` date NOT NULL default '0000-00-00',
  `released_date` date NOT NULL default '0000-00-00',
  `units_issued` double NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  `released` tinyint(1) NOT NULL default '0',
  `additional_costs` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `wo_ref` (`wo_ref`)
) ENGINE=InnoDB  ;


### Data of table `0_workorders` ###

