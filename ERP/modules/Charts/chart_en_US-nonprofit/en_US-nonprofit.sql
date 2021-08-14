-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 11, 2010 at 11:37 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6-1+lenny2

--
-- Database: `fatest`
-- Coa: 'en_US-nonprofit.sql'

-- --------------------------------------------------------

--
-- Table structure for table `0_areas`
--

DROP TABLE IF EXISTS `0_areas`;
CREATE TABLE IF NOT EXISTS `0_areas` (
  `area_code` int(11) NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`area_code`),
  UNIQUE KEY `description` (`description`)
) ENGINE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_areas`
--

INSERT INTO `0_areas` VALUES(1, 'Global', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_attachments`
--

DROP TABLE IF EXISTS `0_attachments`;
CREATE TABLE IF NOT EXISTS `0_attachments` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_attachments`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_audit_trail`
--

DROP TABLE IF EXISTS `0_audit_trail`;
CREATE TABLE IF NOT EXISTS `0_audit_trail` (
  `id` int(11) NOT NULL auto_increment,
  `type` smallint(6) unsigned NOT NULL default '0',
  `trans_no` int(11) unsigned NOT NULL default '0',
  `user` smallint(6) unsigned NOT NULL default '0',
  `stamp` timestamp NOT NULL,
  `description` varchar(60) default NULL,
  `fiscal_year` int(11) NOT NULL,
  `gl_date` date NOT NULL default '0000-00-00',
  `gl_seq` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `Seq` (`fiscal_year`,`gl_date`,`gl_seq`),
  KEY `Type_and_Number` (`type`,`trans_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_audit_trail`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_bank_accounts`
--

DROP TABLE IF EXISTS `0_bank_accounts`;
CREATE TABLE IF NOT EXISTS `0_bank_accounts` (
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
) ENGINE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Dumping data for table `0_bank_accounts`
--

INSERT INTO `0_bank_accounts` VALUES('1010', 0, 'Cash in bank', 'N/A', 'N/A', '', 'USD', 1, 1, '0000-00-00 00:00:00', 0, 0);
INSERT INTO `0_bank_accounts` VALUES('1040', 3, 'Petty Cash', 'N/A', 'N/A', '', 'USD', 0, 2, '0000-00-00 00:00:00', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_bank_trans`
--

DROP TABLE IF EXISTS `0_bank_trans`;
CREATE TABLE IF NOT EXISTS `0_bank_trans` (
  `id` int(11) NOT NULL auto_increment,
  `type` smallint(6) default NULL,
  `trans_no` int(11) default NULL,
  `bank_act` varchar(15) NOT NULL default '',
  `ref` varchar(40) default NULL,
  `trans_date` date NOT NULL default '0000-00-00',
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_bank_trans`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_bom`
--

DROP TABLE IF EXISTS `0_bom`;
CREATE TABLE IF NOT EXISTS `0_bom` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_bom`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_budget_trans`
--

DROP TABLE IF EXISTS `0_budget_trans`;
CREATE TABLE IF NOT EXISTS `0_budget_trans` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_budget_trans`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_chart_class`
--

DROP TABLE IF EXISTS `0_chart_class`;
CREATE TABLE IF NOT EXISTS `0_chart_class` (
  `cid` varchar(3) NOT NULL,
  `class_name` varchar(60) NOT NULL default '',
  `ctype` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_chart_class`
--

INSERT INTO `0_chart_class` VALUES('1', 'Assets', 1, 0);
INSERT INTO `0_chart_class` VALUES('2', 'Liabilities', 2, 0);
INSERT INTO `0_chart_class` VALUES('3', 'Income', 4, 0);
INSERT INTO `0_chart_class` VALUES('4', 'Costs', 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_chart_master`
--

DROP TABLE IF EXISTS `0_chart_master`;
CREATE TABLE IF NOT EXISTS `0_chart_master` (
  `account_code` varchar(15) NOT NULL default '',
  `account_code2` varchar(15) NOT NULL default '',
  `account_name` varchar(60) NOT NULL default '',
  `account_type` varchar(10) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`account_code`),
  KEY `account_name` (`account_name`),
  KEY `accounts_by_type` (`account_type`,`account_code`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_chart_master`
--

INSERT INTO 0_chart_master VALUES ('1000', '', 'Cash','1',0);
INSERT INTO 0_chart_master VALUES ('1010', '', 'Cash in bank-operating','1',0);
INSERT INTO 0_chart_master VALUES ('1020', '', 'Cash in bank-payroll','1',0);
INSERT INTO 0_chart_master VALUES ('1040', '', 'Petty cash','1',0);
INSERT INTO 0_chart_master VALUES ('1070', '', 'Cash in bank-capital','1',0);
INSERT INTO 0_chart_master VALUES ('1100', '', 'Accounts receivable','1',0);
INSERT INTO 0_chart_master VALUES ('1110', '', 'Accounts receivable','1',0);
INSERT INTO 0_chart_master VALUES ('1115', '', 'Doubtful accounts allowance','1',0);
INSERT INTO 0_chart_master VALUES ('1200', '', 'Contributions receivable','1',0);
INSERT INTO 0_chart_master VALUES ('1210', '', 'Pledges receivable','1',0);
INSERT INTO 0_chart_master VALUES ('1215', '', 'Doubtful pledges allowance','1',0);
INSERT INTO 0_chart_master VALUES ('1225', '', 'Discounts - long-term pledges','1',0);
INSERT INTO 0_chart_master VALUES ('1240', '', 'Grants receivable','1',0);
INSERT INTO 0_chart_master VALUES ('1245', '', 'Discounts - long-term grants','1',0);
INSERT INTO 0_chart_master VALUES ('1300', '', 'Other receivables','1',0);
INSERT INTO 0_chart_master VALUES ('1310', '', 'Employee & trustee receivables','1',0);
INSERT INTO 0_chart_master VALUES ('1320', '', 'Notes/loans receivable','1',0);
INSERT INTO 0_chart_master VALUES ('1325', '', 'Doubtful notes/loans allowance','1',0);
INSERT INTO 0_chart_master VALUES ('1400', '', 'Other assets','1',0);
INSERT INTO 0_chart_master VALUES ('1410', '', 'Inventories for sale','1',0);
INSERT INTO 0_chart_master VALUES ('1420', '', 'Inventories for use','1',0);
INSERT INTO 0_chart_master VALUES ('1450', '', 'Prepaid expenses','1',0);
INSERT INTO 0_chart_master VALUES ('1460', '', 'Accrued revenues','1',0);
INSERT INTO 0_chart_master VALUES ('1500', '', 'Investments','1',0);
INSERT INTO 0_chart_master VALUES ('1510', '', 'Marketable securities ','1',0);
INSERT INTO 0_chart_master VALUES ('1530', '', 'Land held for investment','1',0);
INSERT INTO 0_chart_master VALUES ('1540', '', 'Buildings held for investment','1',0);
INSERT INTO 0_chart_master VALUES ('1545', '', 'Accum deprec - bldg investment','1',0);
INSERT INTO 0_chart_master VALUES ('1580', '', 'Investments - other','1',0);
INSERT INTO 0_chart_master VALUES ('1600', '', 'Fixed operating assets','1',0);
INSERT INTO 0_chart_master VALUES ('1610', '', 'Land - operating','1',0);
INSERT INTO 0_chart_master VALUES ('1620', '', 'Buildings - operating','1',0);
INSERT INTO 0_chart_master VALUES ('1630', '', 'Leasehold improvements','1',0);
INSERT INTO 0_chart_master VALUES ('1640', '', 'Furniture, fixtures, & equip','1',0);
INSERT INTO 0_chart_master VALUES ('1650', '', 'Vehicles','1',0);
INSERT INTO 0_chart_master VALUES ('1680', '', 'Construction in progress','1',0);
INSERT INTO 0_chart_master VALUES ('1700', '', 'Accumulated depreciation','1',0);
INSERT INTO 0_chart_master VALUES ('1725', '', 'Accum deprec - building','1',0);
INSERT INTO 0_chart_master VALUES ('1735', '', 'Accum amort - leasehold improvements','1',0);
INSERT INTO 0_chart_master VALUES ('1745', '', 'Accum deprec - furn,fix,equip','1',0);
INSERT INTO 0_chart_master VALUES ('1755', '', 'Accum deprec - vehicles','1',0);
INSERT INTO 0_chart_master VALUES ('1800', '', 'Other long-term assets','1',0);
INSERT INTO 0_chart_master VALUES ('1810', '', 'Other long-term assets','1',0);
INSERT INTO 0_chart_master VALUES ('1850', '', 'Split-interest agreements','1',0);
INSERT INTO 0_chart_master VALUES ('1910', '', 'Collections - art, etc','1',0);
INSERT INTO 0_chart_master VALUES ('1950', '', 'Funds held in trust by others','1',0);
INSERT INTO 0_chart_master VALUES ('2000', '', 'Payables','2',0);
INSERT INTO 0_chart_master VALUES ('2010', '', 'Accounts payable','2',0);
INSERT INTO 0_chart_master VALUES ('2020', '', 'Grants & allocations payable','2',0);
INSERT INTO 0_chart_master VALUES ('2100', '', 'Accrued liabilities','2',0);
INSERT INTO 0_chart_master VALUES ('2110', '', 'Accrued payroll','2',0);
INSERT INTO 0_chart_master VALUES ('2120', '', 'Accrued paid leave','2',0);
INSERT INTO 0_chart_master VALUES ('2130', '', 'Accrued payroll taxes','2',0);
INSERT INTO 0_chart_master VALUES ('2140', '', 'Accrued sales taxes','2',0);
INSERT INTO 0_chart_master VALUES ('2150', '', 'Accrued expenses - other','2',0);
INSERT INTO 0_chart_master VALUES ('2300', '', 'Deferred revenue','2',0);
INSERT INTO 0_chart_master VALUES ('2310', '', 'Deferred contract revenue','2',0);
INSERT INTO 0_chart_master VALUES ('2350', '', 'Unearned/deferred revenue - other','2',0);
INSERT INTO 0_chart_master VALUES ('2400', '', 'Advances','2',0);
INSERT INTO 0_chart_master VALUES ('2410', '', 'Refundable advances','2',0);
INSERT INTO 0_chart_master VALUES ('2500', '', 'Short-term notes','2',0);
INSERT INTO 0_chart_master VALUES ('2510', '', 'Trustee & employee loans payable','2',0);
INSERT INTO 0_chart_master VALUES ('2550', '', 'Line of Credit','2',0);
INSERT INTO 0_chart_master VALUES ('2560', '', 'Current portion - long-term loan','2',0);
INSERT INTO 0_chart_master VALUES ('2570', '', 'Short-term liabilities - other','2',0);
INSERT INTO 0_chart_master VALUES ('2610', '', 'Split-interest liabilities','2',0);
INSERT INTO 0_chart_master VALUES ('2700', '', 'Long-term notes','2',0);
INSERT INTO 0_chart_master VALUES ('2710', '', 'Bonds payable','2',0);
INSERT INTO 0_chart_master VALUES ('2730', '', 'Mortgages payable','2',0);
INSERT INTO 0_chart_master VALUES ('2750', '', 'Capital leases','2',0);
INSERT INTO 0_chart_master VALUES ('2770', '', 'Long-term liabilities - other','2',0);
INSERT INTO 0_chart_master VALUES ('2810', '', 'Gov&#039;t-owned fixed assets liability','2',0);
INSERT INTO 0_chart_master VALUES ('2910', '', 'Custodial funds','2',0);
INSERT INTO 0_chart_master VALUES ('3000', '', 'Unrestricted net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3010', '', 'Unrestricted net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3020', '', 'Board-designated net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3030', '', 'Board designated quasi-endowment','3',0);
INSERT INTO 0_chart_master VALUES ('3040', '', 'Fixed operating net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3100', '', 'Temporarily restricted net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3110', '', 'Use restricted net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3120', '', 'Use restricted net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3200', '', 'Permanently restricted net assets','3',0);
INSERT INTO 0_chart_master VALUES ('3210', '', 'Endowment net assets','3',0);
INSERT INTO 0_chart_master VALUES ('4000', '', 'Revenue from direct contributions','4',0);
INSERT INTO 0_chart_master VALUES ('4010', '', 'Individual/small business contributions','4',0);
INSERT INTO 0_chart_master VALUES ('4020', '', 'Corporate contributions','4',0);
INSERT INTO 0_chart_master VALUES ('4070', '', 'Legacies & bequests','4',0);
INSERT INTO 0_chart_master VALUES ('4075', '', 'Uncollectible pledges - estimated','4',0);
INSERT INTO 0_chart_master VALUES ('4085', '', 'Long-term pledges discount','4',0);
INSERT INTO 0_chart_master VALUES ('4100', '', 'Donated goods & services','4',0);
INSERT INTO 0_chart_master VALUES ('4110', '', 'Donated professional services-GAAP','4',0);
INSERT INTO 0_chart_master VALUES ('4120', '', 'Donated other services - non-GAAP','4',0);
INSERT INTO 0_chart_master VALUES ('4130', '', 'Donated use of facilities','4',0);
INSERT INTO 0_chart_master VALUES ('4140', '', 'Gifts in kind - goods','4',0);
INSERT INTO 0_chart_master VALUES ('4150', '', 'Donated art, etc','4',0);
INSERT INTO 0_chart_master VALUES ('4200', '', 'Revenue from non-government grants','4',0);
INSERT INTO 0_chart_master VALUES ('4210', '', 'Corporate/business grants','4',0);
INSERT INTO 0_chart_master VALUES ('4230', '', 'Foundation/trust grants','4',0);
INSERT INTO 0_chart_master VALUES ('4250', '', 'Nonprofit organization grants','4',0);
INSERT INTO 0_chart_master VALUES ('4255', '', 'Discounts - long-term grants','4',0);
INSERT INTO 0_chart_master VALUES ('4300', '', 'Revenue from split-interest agreements','4',0);
INSERT INTO 0_chart_master VALUES ('4310', '', 'Split-interest agreement contributions','4',0);
INSERT INTO 0_chart_master VALUES ('4350', '', 'Gain (loss) split-interest agreements','4',0);
INSERT INTO 0_chart_master VALUES ('4400', '', 'Revenue from indirect contributions','4',0);
INSERT INTO 0_chart_master VALUES ('4410', '', 'United Way or CFC contributions','4',0);
INSERT INTO 0_chart_master VALUES ('4420', '', 'Affiliated organizations revenue','4',0);
INSERT INTO 0_chart_master VALUES ('4430', '', 'Fundraising agencies revenue','4',0);
INSERT INTO 0_chart_master VALUES ('4500', '', 'Revenue from government grants','4',0);
INSERT INTO 0_chart_master VALUES ('4510', '', 'Agency (government) grants','4',0);
INSERT INTO 0_chart_master VALUES ('4520', '', 'Federal grants','4',0);
INSERT INTO 0_chart_master VALUES ('4530', '', 'State grants','4',0);
INSERT INTO 0_chart_master VALUES ('4540', '', 'Local government grants','4',0);
INSERT INTO 0_chart_master VALUES ('5000', '', 'Revenue from government agencies','5',0);
INSERT INTO 0_chart_master VALUES ('5010', '', 'Agency (government) contracts/fees','5',0);
INSERT INTO 0_chart_master VALUES ('5020', '', 'Federal contracts/fees','5',0);
INSERT INTO 0_chart_master VALUES ('5030', '', 'State contracts/fees','5',0);
INSERT INTO 0_chart_master VALUES ('5040', '', 'Local government contracts/fees','5',0);
INSERT INTO 0_chart_master VALUES ('5080', '', 'Medicare/Medicaid payments','5',0);
INSERT INTO 0_chart_master VALUES ('5100', '', 'Revenue from program-related sales & fees','5',0);
INSERT INTO 0_chart_master VALUES ('5180', '', 'Program service fees','5',0);
INSERT INTO 0_chart_master VALUES ('5185', '', 'Bad debts, est - program fees','5',0);
INSERT INTO 0_chart_master VALUES ('5200', '', 'Revenue from dues','5',0);
INSERT INTO 0_chart_master VALUES ('5210', '', 'Membership dues-individuals','5',0);
INSERT INTO 0_chart_master VALUES ('5220', '', 'Assessments and dues-organizations','5',0);
INSERT INTO 0_chart_master VALUES ('5300', '', 'Revenue from investments','5',0);
INSERT INTO 0_chart_master VALUES ('5310', '', 'Interest-savings/short-term investments','5',0);
INSERT INTO 0_chart_master VALUES ('5320', '', 'Dividends & interest - securities','5',0);
INSERT INTO 0_chart_master VALUES ('5330', '', 'Real estate rent - debt-financed','5',0);
INSERT INTO 0_chart_master VALUES ('5335', '', 'Real estate rental cost - debt-financed','5',0);
INSERT INTO 0_chart_master VALUES ('5340', '', 'Real estate rent - not debt-financed','5',0);
INSERT INTO 0_chart_master VALUES ('5345', '', 'Real estate rental cost - not debt-financed','5',0);
INSERT INTO 0_chart_master VALUES ('5350', '', 'Personal property rent ','5',0);
INSERT INTO 0_chart_master VALUES ('5355', '', 'Personal property rental cost ','5',0);
INSERT INTO 0_chart_master VALUES ('5360', '', 'Other investment income','5',0);
INSERT INTO 0_chart_master VALUES ('5370', '', 'Securities sales - gross','5',0);
INSERT INTO 0_chart_master VALUES ('5375', '', 'Securities sales cost','5',0);
INSERT INTO 0_chart_master VALUES ('5400', '', 'Revenue from other sources','5',0);
INSERT INTO 0_chart_master VALUES ('5410', '', 'Non-inventory sales - gross','5',0);
INSERT INTO 0_chart_master VALUES ('5415', '', 'Non-inventory sales cost','5',0);
INSERT INTO 0_chart_master VALUES ('5440', '', 'Gross sales - inventory','5',0);
INSERT INTO 0_chart_master VALUES ('5445', '', 'Cost of inventory sold','5',0);
INSERT INTO 0_chart_master VALUES ('5450', '', 'Advertising revenue','5',0);
INSERT INTO 0_chart_master VALUES ('5460', '', 'Affiliate revenues from other entities','5',0);
INSERT INTO 0_chart_master VALUES ('5490', '', 'Misc revenue','5',0);
INSERT INTO 0_chart_master VALUES ('5800', '', 'Special events','5',0);
INSERT INTO 0_chart_master VALUES ('5810', '', 'Special events - non-gift revenue','5',0);
INSERT INTO 0_chart_master VALUES ('5820', '', 'Special events - gift revenue','5',0);
INSERT INTO 0_chart_master VALUES ('6800', '', 'Unrealized gain (loss)','6',0);
INSERT INTO 0_chart_master VALUES ('6810', '', 'Unrealized gain (loss) - investments','6',0);
INSERT INTO 0_chart_master VALUES ('6820', '', 'Unrealized gain (loss) - other assets','6',0);
INSERT INTO 0_chart_master VALUES ('6900', '', 'Net assets released from restriction','6',0);
INSERT INTO 0_chart_master VALUES ('6910', '', 'Satisfaction of use restriction','6',0);
INSERT INTO 0_chart_master VALUES ('6920', '', 'LB&E acquisition satisfaction','6',0);
INSERT INTO 0_chart_master VALUES ('6930', '', 'Time restriction satisfaction','6',0);
INSERT INTO 0_chart_master VALUES ('7000', '', 'Grants, contracts & direct assistance','7',0);
INSERT INTO 0_chart_master VALUES ('7010', '', 'Contracts - program-related','7',0);
INSERT INTO 0_chart_master VALUES ('7020', '', 'Grants to other organizations','7',0);
INSERT INTO 0_chart_master VALUES ('7040', '', 'Awards & grants - individuals','7',0);
INSERT INTO 0_chart_master VALUES ('7050', '', 'Specific assistance - individuals','7',0);
INSERT INTO 0_chart_master VALUES ('7060', '', 'Benefits paid to or for members','7',0);
INSERT INTO 0_chart_master VALUES ('7200', '', 'Salaries & related expenses','7',0);
INSERT INTO 0_chart_master VALUES ('7210', '', 'Officers & directors salaries','7',0);
INSERT INTO 0_chart_master VALUES ('7230', '', 'Pension plan contributions','7',0);
INSERT INTO 0_chart_master VALUES ('7240', '', 'Employee benefits - not pension','7',0);
INSERT INTO 0_chart_master VALUES ('7250', '', 'Payroll taxes, etc.','7',0);
INSERT INTO 0_chart_master VALUES ('7500', '', 'Contract service expenses','7',0);
INSERT INTO 0_chart_master VALUES ('7510', '', 'Fundraising fees','7',0);
INSERT INTO 0_chart_master VALUES ('7520', '', 'Accounting fees','7',0);
INSERT INTO 0_chart_master VALUES ('7530', '', 'Legal fees','7',0);
INSERT INTO 0_chart_master VALUES ('7550', '', 'Temporary help - contract','7',0);
INSERT INTO 0_chart_master VALUES ('7580', '', 'Donated professional services - GAAP','7',0);
INSERT INTO 0_chart_master VALUES ('7590', '', 'Donated other services - non-GAAP','7',0);
INSERT INTO 0_chart_master VALUES ('8100', '', 'Non-personnel expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8110', '', 'Supplies','8',0);
INSERT INTO 0_chart_master VALUES ('8120', '', 'Donated materials & supplies','8',0);
INSERT INTO 0_chart_master VALUES ('8130', '', 'Telephone & telecommunications','8',0);
INSERT INTO 0_chart_master VALUES ('8140', '', 'Postage & shipping','8',0);
INSERT INTO 0_chart_master VALUES ('8150', '', 'Mailing services','8',0);
INSERT INTO 0_chart_master VALUES ('8170', '', 'Printing & copying','8',0);
INSERT INTO 0_chart_master VALUES ('8180', '', 'Books, subscriptions, references','8',0);
INSERT INTO 0_chart_master VALUES ('8190', '', 'In-house publications','8',0);
INSERT INTO 0_chart_master VALUES ('8200', '', 'Facility & equipment expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8210', '', 'Rent, parking, other occupancy','8',0);
INSERT INTO 0_chart_master VALUES ('8220', '', 'Utilities','8',0);
INSERT INTO 0_chart_master VALUES ('8230', '', 'Real estate taxes','8',0);
INSERT INTO 0_chart_master VALUES ('8240', '', 'Personal property taxes','8',0);
INSERT INTO 0_chart_master VALUES ('8250', '', 'Mortgage interest','8',0);
INSERT INTO 0_chart_master VALUES ('8260', '', 'Equipment rental & maintenance','8',0);
INSERT INTO 0_chart_master VALUES ('8270', '', 'Deprec & amort - allowable','8',0);
INSERT INTO 0_chart_master VALUES ('8280', '', 'Deprec & amort - not allowable','8',0);
INSERT INTO 0_chart_master VALUES ('8290', '', 'Donated facilities','8',0);
INSERT INTO 0_chart_master VALUES ('8300', '', 'Travel & meetings expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8310', '', 'Travel','8',0);
INSERT INTO 0_chart_master VALUES ('8320', '', 'Conferences, conventions, meetings','8',0);
INSERT INTO 0_chart_master VALUES ('8500', '', 'Other expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8510', '', 'Interest-general','8',0);
INSERT INTO 0_chart_master VALUES ('8520', '', 'Insurance - non-employee related','8',0);
INSERT INTO 0_chart_master VALUES ('8530', '', 'Membership dues - organization','8',0);
INSERT INTO 0_chart_master VALUES ('8540', '', 'Staff development','8',0);
INSERT INTO 0_chart_master VALUES ('8550', '', 'List rental','8',0);
INSERT INTO 0_chart_master VALUES ('8560', '', 'Outside computer services','8',0);
INSERT INTO 0_chart_master VALUES ('8570', '', 'Advertising expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8580', '', 'Contingency provisions','8',0);
INSERT INTO 0_chart_master VALUES ('8590', '', 'Other expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8600', '', 'Business expenses','8',0);
INSERT INTO 0_chart_master VALUES ('8610', '', 'Bad debt expense','8',0);
INSERT INTO 0_chart_master VALUES ('8620', '', 'Sales taxes','8',0);
INSERT INTO 0_chart_master VALUES ('8630', '', 'UBI Taxes','8',0);
INSERT INTO 0_chart_master VALUES ('8650', '', 'Taxes - other','8',0);
INSERT INTO 0_chart_master VALUES ('8660', '', 'Fines, penalties, judgments','8',0);
INSERT INTO 0_chart_master VALUES ('8670', '', 'Organizational (corp) expenses','8',0);
INSERT INTO 0_chart_master VALUES ('9800', '', 'Fixed asset purchases','9',0);
INSERT INTO 0_chart_master VALUES ('9810', '', 'Capital purchases - land','9',0);
INSERT INTO 0_chart_master VALUES ('9820', '', 'Capital purchases - building','9',0);
INSERT INTO 0_chart_master VALUES ('9830', '', 'Capital purchases - equipment','9',0);
INSERT INTO 0_chart_master VALUES ('9840', '', 'Capital purchases - vehicles','9',0);
INSERT INTO 0_chart_master VALUES ('9900', '', 'Other non-GAAP expenses','9',0);
INSERT INTO 0_chart_master VALUES ('9910', '', 'Payments to affiliates','9',0);
INSERT INTO 0_chart_master VALUES ('9920', '', 'Additions to reserves','9',0);
INSERT INTO 0_chart_master VALUES ('9930', '', 'Program administration allocations','9',0);
INSERT INTO 0_chart_master VALUES ('9990', '', 'Year Profit/Loss','9',0);

-- --------------------------------------------------------

--
-- Table structure for table `0_chart_types`
--

DROP TABLE IF EXISTS `0_chart_types`;
CREATE TABLE IF NOT EXISTS `0_chart_types` (
  `id` varchar(10) NOT NULL,
  `name` varchar(60) NOT NULL default '',
  `class_id` varchar(3) NOT NULL default '',
  `parent` varchar(10) NOT NULL default '-1',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `0_chart_types`
--

INSERT INTO 0_chart_types VALUES ('1', 'Assets','1','', 0);
INSERT INTO 0_chart_types VALUES ('2', 'Liabilities','2','', 0);
INSERT INTO 0_chart_types VALUES ('3', 'Equity','2','', 0);
INSERT INTO 0_chart_types VALUES ('4', 'Contributions','3','', 0);
INSERT INTO 0_chart_types VALUES ('5', 'Earned revenues','3','', 0);
INSERT INTO 0_chart_types VALUES ('6', 'Other revenue','3','', 0);
INSERT INTO 0_chart_types VALUES ('7', 'Expenses - personnel related','4','', 0);
INSERT INTO 0_chart_types VALUES ('8', 'Non-personnel related expenses','4','', 0);
INSERT INTO 0_chart_types VALUES ('9', 'Non-GAAP expenses','4','', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_comments`
--

DROP TABLE IF EXISTS `0_comments`;
CREATE TABLE IF NOT EXISTS `0_comments` (
  `type` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL default '0',
  `date_` date default '0000-00-00',
  `memo_` tinytext,
  KEY `type_and_id` (`type`,`id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_credit_status`
--

DROP TABLE IF EXISTS `0_credit_status`;
CREATE TABLE IF NOT EXISTS `0_credit_status` (
  `id` int(11) NOT NULL auto_increment,
  `reason_description` char(100) NOT NULL default '',
  `dissallow_invoices` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `reason_description` (`reason_description`)
) ENGINE=MyISAM  AUTO_INCREMENT=5 ;

--
-- Dumping data for table `0_credit_status`
--

INSERT INTO `0_credit_status` VALUES(1, 'Good History', 0, 0);
INSERT INTO `0_credit_status` VALUES(3, 'No more work until payment received', 1, 0);
INSERT INTO `0_credit_status` VALUES(4, 'In liquidation', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_crm_categories`
--

DROP TABLE IF EXISTS `0_crm_categories`;
CREATE TABLE IF NOT EXISTS `0_crm_categories` (
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
) ENGINE=InnoDB  AUTO_INCREMENT=13 ;

--
-- Dumping data for table `0_crm_categories`
--

INSERT INTO `0_crm_categories` VALUES(1, 'cust_branch', 'general', 'General', 'General contact data for customer branch (overrides company setting)', 1, 0);
INSERT INTO `0_crm_categories` VALUES(2, 'cust_branch', 'invoice', 'Invoices', 'Invoice posting (overrides company setting)', 1, 0);
INSERT INTO `0_crm_categories` VALUES(3, 'cust_branch', 'order', 'Orders', 'Order confirmation (overrides company setting)', 1, 0);
INSERT INTO `0_crm_categories` VALUES(4, 'cust_branch', 'delivery', 'Deliveries', 'Delivery coordination (overrides company setting)', 1, 0);
INSERT INTO `0_crm_categories` VALUES(5, 'customer', 'general', 'General', 'General contact data for customer', 1, 0);
INSERT INTO `0_crm_categories` VALUES(6, 'customer', 'order', 'Orders', 'Order confirmation', 1, 0);
INSERT INTO `0_crm_categories` VALUES(7, 'customer', 'delivery', 'Deliveries', 'Delivery coordination', 1, 0);
INSERT INTO `0_crm_categories` VALUES(8, 'customer', 'invoice', 'Invoices', 'Invoice posting', 1, 0);
INSERT INTO `0_crm_categories` VALUES(9, 'supplier', 'general', 'General', 'General contact data for supplier', 1, 0);
INSERT INTO `0_crm_categories` VALUES(10, 'supplier', 'order', 'Orders', 'Order confirmation', 1, 0);
INSERT INTO `0_crm_categories` VALUES(11, 'supplier', 'delivery', 'Deliveries', 'Delivery coordination', 1, 0);
INSERT INTO `0_crm_categories` VALUES(12, 'supplier', 'invoice', 'Invoices', 'Invoice posting', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_crm_contacts`
--

DROP TABLE IF EXISTS `0_crm_contacts`;
CREATE TABLE IF NOT EXISTS `0_crm_contacts` (
  `id` int(11) NOT NULL auto_increment,
  `person_id` int(11) NOT NULL default '0' COMMENT 'foreign key to crm_contacts',
  `type` varchar(20) NOT NULL COMMENT 'foreign key to crm_categories',
  `action` varchar(20) NOT NULL COMMENT 'foreign key to crm_categories',
  `entity_id` varchar(11) default NULL COMMENT 'entity id in related class table',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_crm_contacts`
--

-- --------------------------------------------------------

--
-- Table structure for table `0_crm_persons`
--

DROP TABLE IF EXISTS `0_crm_persons`;
CREATE TABLE IF NOT EXISTS `0_crm_persons` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_crm_persons`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_currencies`
--

DROP TABLE IF EXISTS `0_currencies`;
CREATE TABLE IF NOT EXISTS `0_currencies` (
  `currency` varchar(60) NOT NULL default '',
  `curr_abrev` char(3) NOT NULL default '',
  `curr_symbol` varchar(10) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  `hundreds_name` varchar(15) NOT NULL default '',
  `auto_update` tinyint(1) NOT NULL default '1',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`curr_abrev`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_currencies`
--

INSERT INTO `0_currencies` VALUES('US Dollars', 'USD', '$', 'United States', 'Cents', 1, 0);
INSERT INTO `0_currencies` VALUES('CA Dollars', 'CAD', '$', 'Canada', 'Cents', 1, 0);
INSERT INTO `0_currencies` VALUES('Euro', 'EUR', '?', 'Europe', 'Cents', 1, 0);
INSERT INTO `0_currencies` VALUES('Pounds', 'GBP', '?', 'England', 'Pence', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_cust_allocations`
--

DROP TABLE IF EXISTS `0_cust_allocations`;
CREATE TABLE IF NOT EXISTS `0_cust_allocations` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_cust_allocations`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_cust_branch`
--

DROP TABLE IF EXISTS `0_cust_branch`;
CREATE TABLE IF NOT EXISTS `0_cust_branch` (
  `branch_code` int(11) NOT NULL auto_increment,
  `debtor_no` int(11) NOT NULL default '0',
  `br_name` varchar(60) NOT NULL default '',
  `branch_ref` varchar(30) NOT NULL default '',
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
  `notes` tinytext NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`branch_code`,`debtor_no`),
  KEY `branch_code` (`branch_code`),
  KEY `branch_ref` (`branch_ref`),
  KEY `group_no` (`group_no`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_cust_branch`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_debtors_master`
--

DROP TABLE IF EXISTS `0_debtors_master`;
CREATE TABLE IF NOT EXISTS `0_debtors_master` (
  `debtor_no` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `debtor_ref` varchar(30) NOT NULL,
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
  `notes` tinytext NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`debtor_no`),
  KEY `name` (`name`),
  UNIQUE KEY `debtor_ref` (`debtor_ref`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_debtors_master`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_debtor_trans`
--

DROP TABLE IF EXISTS `0_debtor_trans`;
CREATE TABLE IF NOT EXISTS `0_debtor_trans` (
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
) ENGINE=InnoDB;

--
-- Dumping data for table `0_debtor_trans`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_debtor_trans_details`
--

DROP TABLE IF EXISTS `0_debtor_trans_details`;
CREATE TABLE IF NOT EXISTS `0_debtor_trans_details` (
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
  `src_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `Transaction` (`debtor_trans_type`,`debtor_trans_no`),
  KEY (`src_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_debtor_trans_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_dimensions`
--

DROP TABLE IF EXISTS `0_dimensions`;
CREATE TABLE IF NOT EXISTS `0_dimensions` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_dimensions`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_exchange_rates`
--

DROP TABLE IF EXISTS `0_exchange_rates`;
CREATE TABLE IF NOT EXISTS `0_exchange_rates` (
  `id` int(11) NOT NULL auto_increment,
  `curr_code` char(3) NOT NULL default '',
  `rate_buy` double NOT NULL default '0',
  `rate_sell` double NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `curr_code` (`curr_code`,`date_`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_exchange_rates`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_fiscal_year`
--

DROP TABLE IF EXISTS `0_fiscal_year`;
CREATE TABLE IF NOT EXISTS `0_fiscal_year` (
  `id` int(11) NOT NULL auto_increment,
  `begin` date default '0000-00-00',
  `end` date default '0000-00-00',
  `closed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `begin` (`begin`),
  UNIQUE KEY `end` (`end`)
) ENGINE=InnoDB  AUTO_INCREMENT=4 ;

--
-- Dumping data for table `0_fiscal_year`
--

INSERT INTO `0_fiscal_year` VALUES(1, '2008-01-01', '2008-12-31', 0);
INSERT INTO `0_fiscal_year` VALUES(2, '2009-01-01', '2009-12-31', 0);
INSERT INTO `0_fiscal_year` VALUES(3, '2010-01-01', '2010-12-31', 0);
INSERT INTO `0_fiscal_year` VALUES(4, '2011-01-01', '2011-12-31', 0);

--
-- Table structure for table `0_gl_trans`
--

DROP TABLE IF EXISTS `0_gl_trans`;
CREATE TABLE IF NOT EXISTS `0_gl_trans` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_gl_trans`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_grn_batch`
--

DROP TABLE IF EXISTS `0_grn_batch`;
CREATE TABLE IF NOT EXISTS `0_grn_batch` (
  `id` int(11) NOT NULL auto_increment,
  `supplier_id` int(11) NOT NULL default '0',
  `purch_order_no` int(11) default NULL,
  `reference` varchar(60) NOT NULL default '',
  `delivery_date` date NOT NULL default '0000-00-00',
  `loc_code` varchar(5) default NULL,
  PRIMARY KEY  (`id`),
  KEY `delivery_date` (`delivery_date`),
  KEY `purch_order_no` (`purch_order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_grn_batch`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_grn_items`
--

DROP TABLE IF EXISTS `0_grn_items`;
CREATE TABLE IF NOT EXISTS `0_grn_items` (
  `id` int(11) NOT NULL auto_increment,
  `grn_batch_id` int(11) default NULL,
  `po_detail_item` int(11) NOT NULL default '0',
  `item_code` varchar(20) NOT NULL default '',
  `description` tinytext,
  `qty_recd` double NOT NULL default '0',
  `quantity_inv` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `grn_batch_id` (`grn_batch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_grn_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_groups`
--

DROP TABLE IF EXISTS `0_groups`;
CREATE TABLE IF NOT EXISTS `0_groups` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=MyISAM  AUTO_INCREMENT=4 ;

--
-- Dumping data for table `0_groups`
--

INSERT INTO `0_groups` VALUES(1, 'Small', 0);
INSERT INTO `0_groups` VALUES(2, 'Medium', 0);
INSERT INTO `0_groups` VALUES(3, 'Large', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_item_codes`
--

DROP TABLE IF EXISTS `0_item_codes`;
CREATE TABLE IF NOT EXISTS `0_item_codes` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_item_codes`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_item_tax_types`
--

DROP TABLE IF EXISTS `0_item_tax_types`;
CREATE TABLE IF NOT EXISTS `0_item_tax_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `exempt` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_item_tax_types`
--

INSERT INTO `0_item_tax_types` VALUES(1, 'Regular', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_item_tax_type_exemptions`
--

DROP TABLE IF EXISTS `0_item_tax_type_exemptions`;
CREATE TABLE IF NOT EXISTS `0_item_tax_type_exemptions` (
  `item_tax_type_id` int(11) NOT NULL default '0',
  `tax_type_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`item_tax_type_id`,`tax_type_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_item_tax_type_exemptions`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_item_units`
--

DROP TABLE IF EXISTS `0_item_units`;
CREATE TABLE IF NOT EXISTS `0_item_units` (
  `abbr` varchar(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `decimals` tinyint(2) NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`abbr`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_item_units`
--

INSERT INTO `0_item_units` VALUES('ea.', 'Each', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_locations`
--

DROP TABLE IF EXISTS `0_locations`;
CREATE TABLE IF NOT EXISTS `0_locations` (
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
) ENGINE=MyISAM;

--
-- Dumping data for table `0_locations`
--

INSERT INTO `0_locations` VALUES('DEF', 'Default', 'N/A', '', '', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_loc_stock`
--

DROP TABLE IF EXISTS `0_loc_stock`;
CREATE TABLE IF NOT EXISTS `0_loc_stock` (
  `loc_code` char(5) NOT NULL default '',
  `stock_id` char(20) NOT NULL default '',
  `reorder_level` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`loc_code`,`stock_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_loc_stock`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_movement_types`
--

DROP TABLE IF EXISTS `0_movement_types`;
CREATE TABLE IF NOT EXISTS `0_movement_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_movement_types`
--

INSERT INTO `0_movement_types` VALUES(1, 'Adjustment', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_payment_terms`
--

DROP TABLE IF EXISTS `0_payment_terms`;
CREATE TABLE IF NOT EXISTS `0_payment_terms` (
  `terms_indicator` int(11) NOT NULL auto_increment,
  `terms` char(80) NOT NULL default '',
  `days_before_due` smallint(6) NOT NULL default '0',
  `day_in_following_month` smallint(6) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`terms_indicator`),
  UNIQUE KEY `terms` (`terms`)
) ENGINE=MyISAM  AUTO_INCREMENT=5 ;

--
-- Dumping data for table `0_payment_terms`
--

INSERT INTO `0_payment_terms` VALUES(1, 'Due 15th Of the Following Month', 0, 17, 0);
INSERT INTO `0_payment_terms` VALUES(2, 'Due By End Of The Following Month', 0, 30, 0);
INSERT INTO `0_payment_terms` VALUES(3, 'Payment due within 10 days', 10, 0, 0);
INSERT INTO `0_payment_terms` VALUES(4, 'Cash Only', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_prices`
--

DROP TABLE IF EXISTS `0_prices`;
CREATE TABLE IF NOT EXISTS `0_prices` (
  `id` int(11) NOT NULL auto_increment,
  `stock_id` varchar(20) NOT NULL default '',
  `sales_type_id` int(11) NOT NULL default '0',
  `curr_abrev` char(3) NOT NULL default '',
  `price` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `price` (`stock_id`,`sales_type_id`,`curr_abrev`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_prices`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_printers`
--

DROP TABLE IF EXISTS `0_printers`;
CREATE TABLE IF NOT EXISTS `0_printers` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(60) NOT NULL,
  `queue` varchar(20) NOT NULL,
  `host` varchar(40) NOT NULL,
  `port` smallint(11) unsigned NOT NULL,
  `timeout` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  AUTO_INCREMENT=4 ;

--
-- Dumping data for table `0_printers`
--

INSERT INTO `0_printers` VALUES(1, 'QL500', 'Label printer', 'QL500', 'server', 127, 20);
INSERT INTO `0_printers` VALUES(2, 'Samsung', 'Main network printer', 'scx4521F', 'server', 515, 5);
INSERT INTO `0_printers` VALUES(3, 'Local', 'Local print server at user IP', 'lp', '', 515, 10);

-- --------------------------------------------------------

--
-- Table structure for table `0_print_profiles`
--

DROP TABLE IF EXISTS `0_print_profiles`;
CREATE TABLE IF NOT EXISTS `0_print_profiles` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `profile` varchar(30) NOT NULL,
  `report` varchar(5) default NULL,
  `printer` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `profile` (`profile`,`report`)
) ENGINE=MyISAM  AUTO_INCREMENT=10 ;

--
-- Dumping data for table `0_print_profiles`
--

INSERT INTO `0_print_profiles` VALUES(1, 'Out of office', '', 0);
INSERT INTO `0_print_profiles` VALUES(2, 'Sales Department', '', 0);
INSERT INTO `0_print_profiles` VALUES(3, 'Central', '', 2);
INSERT INTO `0_print_profiles` VALUES(4, 'Sales Department', '104', 2);
INSERT INTO `0_print_profiles` VALUES(5, 'Sales Department', '105', 2);
INSERT INTO `0_print_profiles` VALUES(6, 'Sales Department', '107', 2);
INSERT INTO `0_print_profiles` VALUES(7, 'Sales Department', '109', 2);
INSERT INTO `0_print_profiles` VALUES(8, 'Sales Department', '110', 2);
INSERT INTO `0_print_profiles` VALUES(9, 'Sales Department', '201', 2);

-- --------------------------------------------------------

--
-- Table structure for table `0_purch_data`
--

DROP TABLE IF EXISTS `0_purch_data`;
CREATE TABLE IF NOT EXISTS `0_purch_data` (
  `supplier_id` int(11) NOT NULL default '0',
  `stock_id` char(20) NOT NULL default '',
  `price` double NOT NULL default '0',
  `suppliers_uom` char(50) NOT NULL default '',
  `conversion_factor` double NOT NULL default '1',
  `supplier_description` char(50) NOT NULL default '',
  PRIMARY KEY  (`supplier_id`,`stock_id`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_purch_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_purch_orders`
--

DROP TABLE IF EXISTS `0_purch_orders`;
CREATE TABLE IF NOT EXISTS `0_purch_orders` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_purch_orders`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_purch_order_details`
--

DROP TABLE IF EXISTS `0_purch_order_details`;
CREATE TABLE IF NOT EXISTS `0_purch_order_details` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_purch_order_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_quick_entries`
--

DROP TABLE IF EXISTS `0_quick_entries`;
CREATE TABLE IF NOT EXISTS `0_quick_entries` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `type` tinyint(1) NOT NULL default '0',
  `description` varchar(60) NOT NULL,
  `base_amount` double NOT NULL default '0',
  `base_desc` varchar(60) default NULL,
  `bal_type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `description` (`description`)
) ENGINE=MyISAM  AUTO_INCREMENT=4 ;

--
-- Dumping data for table `0_quick_entries`
--

INSERT INTO `0_quick_entries` VALUES(1, 1, 'Maintenance', 0, 'Amount', 0);
INSERT INTO `0_quick_entries` VALUES(2, 4, 'Phone', 0, 'Amount', 0);
INSERT INTO `0_quick_entries` VALUES(3, 2, 'Cash Sales', 0, 'Amount', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_quick_entry_lines`
--

DROP TABLE IF EXISTS `0_quick_entry_lines`;
CREATE TABLE IF NOT EXISTS `0_quick_entry_lines` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `qid` smallint(6) unsigned NOT NULL,
  `amount` double default '0',
  `action` varchar(2) NOT NULL,
  `dest_id` varchar(15) NOT NULL default '',
  `dimension_id` smallint(6) unsigned default NULL,
  `dimension2_id` smallint(6) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `qid` (`qid`)
) ENGINE=MyISAM  AUTO_INCREMENT=7 ;

--
-- Dumping data for table `0_quick_entry_lines`
--

INSERT INTO `0_quick_entry_lines` VALUES(1, 1, 0, 't-', '1', 0, 0);
INSERT INTO `0_quick_entry_lines` VALUES(2, 2, 0, 't-', '1', 0, 0);
INSERT INTO `0_quick_entry_lines` VALUES(3, 3, 0, 't-', '1', 0, 0);
INSERT INTO `0_quick_entry_lines` VALUES(4, 3, 0, '=', '4000', 0, 0);
INSERT INTO `0_quick_entry_lines` VALUES(5, 1, 0, '=', '8500', 0, 0);
INSERT INTO `0_quick_entry_lines` VALUES(6, 2, 0, '=', '8130', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_recurrent_invoices`
--

DROP TABLE IF EXISTS `0_recurrent_invoices`;
CREATE TABLE IF NOT EXISTS `0_recurrent_invoices` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_recurrent_invoices`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_refs`
--

DROP TABLE IF EXISTS `0_refs`;
CREATE TABLE IF NOT EXISTS `0_refs` (
  `id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `reference` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`,`type`),
  KEY `Type_and_Reference` (`type`,`reference`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_refs`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_salesman`
--

DROP TABLE IF EXISTS `0_salesman`;
CREATE TABLE IF NOT EXISTS `0_salesman` (
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
) ENGINE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_salesman`
--

INSERT INTO `0_salesman` VALUES(1, 'Sales Person', '', '', '', 5, 1000, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_sales_orders`
--

DROP TABLE IF EXISTS `0_sales_orders`;
CREATE TABLE IF NOT EXISTS `0_sales_orders` (
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
) ENGINE=InnoDB;

--
-- Dumping data for table `0_sales_orders`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_sales_order_details`
--

DROP TABLE IF EXISTS `0_sales_order_details`;
CREATE TABLE IF NOT EXISTS `0_sales_order_details` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_sales_order_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_sales_pos`
--

DROP TABLE IF EXISTS `0_sales_pos`;
CREATE TABLE IF NOT EXISTS `0_sales_pos` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `pos_name` varchar(30) NOT NULL,
  `cash_sale` tinyint(1) NOT NULL,
  `credit_sale` tinyint(1) NOT NULL,
  `pos_location` varchar(5) NOT NULL,
  `pos_account` smallint(6) unsigned NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pos_name` (`pos_name`)
) ENGINE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_sales_pos`
--

INSERT INTO `0_sales_pos` VALUES(1, 'Default', 1, 1, 'DEF', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_sales_types`
--

DROP TABLE IF EXISTS `0_sales_types`;
CREATE TABLE IF NOT EXISTS `0_sales_types` (
  `id` int(11) NOT NULL auto_increment,
  `sales_type` char(50) NOT NULL default '',
  `tax_included` int(1) NOT NULL default '0',
  `factor` double NOT NULL default '1',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sales_type` (`sales_type`)
) ENGINE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Dumping data for table `0_sales_types`
--

INSERT INTO `0_sales_types` VALUES(1, 'Retail', 1, 1, 0);
INSERT INTO `0_sales_types` VALUES(2, 'Wholesale', 0, 0.7, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_security_roles`
--

DROP TABLE IF EXISTS `0_security_roles`;
CREATE TABLE IF NOT EXISTS `0_security_roles` (
  `id` int(11) NOT NULL auto_increment,
  `role` varchar(30) NOT NULL,
  `description` varchar(50) default NULL,
  `sections` text,
  `areas` text,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=MyISAM  AUTO_INCREMENT=11 ;

--
-- Dumping data for table `0_security_roles`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `0_shippers`
--

DROP TABLE IF EXISTS `0_shippers`;
CREATE TABLE IF NOT EXISTS `0_shippers` (
  `shipper_id` int(11) NOT NULL auto_increment,
  `shipper_name` varchar(60) NOT NULL default '',
  `phone` varchar(30) NOT NULL default '',
  `phone2` varchar(30) NOT NULL default '',
  `contact` tinytext NOT NULL,
  `address` tinytext NOT NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`shipper_id`),
  UNIQUE KEY `name` (`shipper_name`)
) ENGINE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_shippers`
--

INSERT INTO `0_shippers` VALUES(1, 'Default', '', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_sql_trail`
--

DROP TABLE IF EXISTS `0_sql_trail`;
CREATE TABLE IF NOT EXISTS `0_sql_trail` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `sql` text NOT NULL,
  `result` tinyint(1) NOT NULL,
  `msg` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_sql_trail`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_stock_category`
--

DROP TABLE IF EXISTS `0_stock_category`;
CREATE TABLE IF NOT EXISTS `0_stock_category` (
  `category_id` int(11) NOT NULL auto_increment,
  `description` varchar(60) NOT NULL default '',
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
  `inactive` tinyint(1) NOT NULL default '0',
  `dflt_no_sale` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=MyISAM  AUTO_INCREMENT=5 ;

--
-- Dumping data for table `0_stock_category`
--

INSERT INTO `0_stock_category` VALUES(1, 'Components', 1, 'each', 'B', '4000', '7000', '1510', '8500', '1510', 0, 0, 0, 0);
INSERT INTO `0_stock_category` VALUES(2, 'Charges', 1, 'each', 'D', '4000', '7000', '1510', '8500', '1510', 0, 0, 0, 0);
INSERT INTO `0_stock_category` VALUES(3, 'Systems', 1, 'each', 'M', '4000', '7000', '1510', '8500', '1510', 0, 0, 0, 0);
INSERT INTO `0_stock_category` VALUES(4, 'Services', 1, 'hrs', 'D', '4000', '7000', '1510', '8500', '1510', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_stock_master`
--

DROP TABLE IF EXISTS `0_stock_master`;
CREATE TABLE IF NOT EXISTS `0_stock_master` (
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
) ENGINE=InnoDB;

--
-- Dumping data for table `0_stock_master`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_stock_moves`
--

DROP TABLE IF EXISTS `0_stock_moves`;
CREATE TABLE IF NOT EXISTS `0_stock_moves` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_stock_moves`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_suppliers`
--

DROP TABLE IF EXISTS `0_suppliers`;
CREATE TABLE IF NOT EXISTS `0_suppliers` (
  `supplier_id` int(11) NOT NULL auto_increment,
  `supp_name` varchar(60) NOT NULL default '',
  `supp_ref` varchar(30) NOT NULL default '',
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
  PRIMARY KEY  (`supplier_id`),
  KEY `supp_ref` (`supp_ref`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_suppliers`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_supp_allocations`
--

DROP TABLE IF EXISTS `0_supp_allocations`;
CREATE TABLE IF NOT EXISTS `0_supp_allocations` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_supp_allocations`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_supp_invoice_items`
--

DROP TABLE IF EXISTS `0_supp_invoice_items`;
CREATE TABLE IF NOT EXISTS `0_supp_invoice_items` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_supp_invoice_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_supp_trans`
--

DROP TABLE IF EXISTS `0_supp_trans`;
CREATE TABLE IF NOT EXISTS `0_supp_trans` (
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
) ENGINE=InnoDB;

--
-- Dumping data for table `0_supp_trans`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_sys_prefs`
--

DROP TABLE IF EXISTS `0_sys_prefs`;
CREATE TABLE IF NOT EXISTS `0_sys_prefs` (
  `name` varchar(35) NOT NULL default '',
  `category` varchar(30) default NULL,
  `type` varchar(20) NOT NULL default '',
  `length` smallint(6) default NULL,
  `value` tinytext,
  PRIMARY KEY  (`name`),
  KEY `category` (`category`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_sys_prefs`
--

INSERT INTO `0_sys_prefs` VALUES('coy_name', 'setup.company', 'varchar', 60, 'Company name');
INSERT INTO `0_sys_prefs` VALUES('gst_no', 'setup.company', 'varchar', 25, '');
INSERT INTO `0_sys_prefs` VALUES('coy_no', 'setup.company', 'varchar', 25, '');
INSERT INTO `0_sys_prefs` VALUES('tax_prd', 'setup.company', 'int', 11, '1');
INSERT INTO `0_sys_prefs` VALUES('tax_last', 'setup.company', 'int', 11, '1');
INSERT INTO `0_sys_prefs` VALUES('postal_address', 'setup.company', 'tinytext', 0, 'N/A');
INSERT INTO `0_sys_prefs` VALUES('phone', 'setup.company', 'varchar', 30, '');
INSERT INTO `0_sys_prefs` VALUES('fax', 'setup.company', 'varchar', 30, '');
INSERT INTO `0_sys_prefs` VALUES('email', 'setup.company', 'varchar', 100, '');
INSERT INTO `0_sys_prefs` VALUES('coy_logo', 'setup.company', 'varchar', 100, '');
INSERT INTO `0_sys_prefs` VALUES('domicile', 'setup.company', 'varchar', 55, '');
INSERT INTO `0_sys_prefs` VALUES('curr_default', 'setup.company', 'char', 3, 'USD');
INSERT INTO `0_sys_prefs` VALUES('use_dimension', 'setup.company', 'tinyint', 1, '1');
INSERT INTO `0_sys_prefs` VALUES('f_year', 'setup.company', 'int', 11, '4');
INSERT INTO `0_sys_prefs` VALUES('no_item_list', 'setup.company', 'tinyint', 1, '0');
INSERT INTO `0_sys_prefs` VALUES('no_customer_list', 'setup.company', 'tinyint', 1, '0');
INSERT INTO `0_sys_prefs` VALUES('no_supplier_list', 'setup.company', 'tinyint', 1, '0');
INSERT INTO `0_sys_prefs` VALUES('base_sales', 'setup.company', 'int', 11, '1');
INSERT INTO `0_sys_prefs` VALUES('time_zone', 'setup.company', 'tinyint', 1, '0');
INSERT INTO `0_sys_prefs` VALUES('add_pct', 'setup.company', 'int', 5, '-1');
INSERT INTO `0_sys_prefs` VALUES('round_to', 'setup.company', 'int', 5, '1');
INSERT INTO `0_sys_prefs` VALUES('login_tout', 'setup.company', 'smallint', 6, '600');
INSERT INTO `0_sys_prefs` VALUES('past_due_days', 'glsetup.general', 'int', 11, '30');
INSERT INTO `0_sys_prefs` VALUES('profit_loss_year_act', 'glsetup.general', 'varchar', 15, '9990');
INSERT INTO `0_sys_prefs` VALUES('retained_earnings_act', 'glsetup.general', 'varchar', 15, '3200');
INSERT INTO `0_sys_prefs` VALUES('bank_charge_act', 'glsetup.general', 'varchar', 15, '8250');
INSERT INTO `0_sys_prefs` VALUES('exchange_diff_act', 'glsetup.general', 'varchar', 15, '8500');
INSERT INTO `0_sys_prefs` VALUES('default_credit_limit', 'glsetup.customer', 'int', 11, '1000');
INSERT INTO `0_sys_prefs` VALUES('accumulate_shipping', 'glsetup.customer', 'tinyint', 1, '0');
INSERT INTO `0_sys_prefs` VALUES('legal_text', 'glsetup.customer', 'tinytext', 0, '');
INSERT INTO `0_sys_prefs` VALUES('freight_act', 'glsetup.customer', 'varchar', 15, '5490');
INSERT INTO `0_sys_prefs` VALUES('debtors_act', 'glsetup.sales', 'varchar', 15, '1100');
INSERT INTO `0_sys_prefs` VALUES('default_sales_act', 'glsetup.sales', 'varchar', 15, '4000');
INSERT INTO `0_sys_prefs` VALUES('default_sales_discount_act', 'glsetup.sales', 'varchar', 15, '4255');
INSERT INTO `0_sys_prefs` VALUES('default_prompt_payment_act', 'glsetup.sales', 'varchar', 15, '4255');
INSERT INTO `0_sys_prefs` VALUES('default_delivery_required', 'glsetup.sales', 'smallint', 6, '1');
INSERT INTO `0_sys_prefs` VALUES('default_dim_required', 'glsetup.dims', 'int', 11, '20');
INSERT INTO `0_sys_prefs` VALUES('pyt_discount_act', 'glsetup.purchase', 'varchar', 15, '7000');
INSERT INTO `0_sys_prefs` VALUES('creditors_act', 'glsetup.purchase', 'varchar', 15, '2010');
INSERT INTO `0_sys_prefs` VALUES('po_over_receive', 'glsetup.purchase', 'int', 11, '10');
INSERT INTO `0_sys_prefs` VALUES('po_over_charge', 'glsetup.purchase', 'int', 11, '10');
INSERT INTO `0_sys_prefs` VALUES('allow_negative_stock', 'glsetup.inventory', 'tinyint', 1, '0');
INSERT INTO `0_sys_prefs` VALUES('default_inventory_act', 'glsetup.items', 'varchar', 15, '1510');
INSERT INTO `0_sys_prefs` VALUES('default_cogs_act', 'glsetup.items', 'varchar', 15, '7000');
INSERT INTO `0_sys_prefs` VALUES('default_adj_act', 'glsetup.items', 'varchar', 15, '7000');
INSERT INTO `0_sys_prefs` VALUES('default_inv_sales_act', 'glsetup.items', 'varchar', 15, '4000');
INSERT INTO `0_sys_prefs` VALUES('default_assembly_act', 'glsetup.items', 'varchar', 15, '1510');
INSERT INTO `0_sys_prefs` VALUES('default_workorder_required', 'glsetup.manuf', 'int', 11, '20');
INSERT INTO `0_sys_prefs` VALUES('version_id', 'system', 'varchar', 11, '2.3rc');
INSERT INTO `0_sys_prefs` VALUES('auto_curr_reval', 'setup.company', 'smallint', 6, '1');
INSERT INTO `0_sys_prefs` VALUES('grn_clearing_act', 'glsetup.purchase', 'varchar', 15, '1510');

-- --------------------------------------------------------

--
-- Table structure for table `0_sys_types`
--

DROP TABLE IF EXISTS `0_sys_types`;
CREATE TABLE IF NOT EXISTS `0_sys_types` (
  `type_id` smallint(6) NOT NULL default '0',
  `type_no` int(11) NOT NULL default '1',
  `next_reference` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_sys_types`
--

INSERT INTO `0_sys_types` VALUES(0, 17, '1');
INSERT INTO `0_sys_types` VALUES(1, 7, '1');
INSERT INTO `0_sys_types` VALUES(2, 4, '1');
INSERT INTO `0_sys_types` VALUES(4, 3, '1');
INSERT INTO `0_sys_types` VALUES(10, 16, '1');
INSERT INTO `0_sys_types` VALUES(11, 2, '1');
INSERT INTO `0_sys_types` VALUES(12, 6, '1');
INSERT INTO `0_sys_types` VALUES(13, 1, '1');
INSERT INTO `0_sys_types` VALUES(16, 2, '1');
INSERT INTO `0_sys_types` VALUES(17, 2, '1');
INSERT INTO `0_sys_types` VALUES(18, 1, '1');
INSERT INTO `0_sys_types` VALUES(20, 6, '1');
INSERT INTO `0_sys_types` VALUES(21, 1, '1');
INSERT INTO `0_sys_types` VALUES(22, 3, '1');
INSERT INTO `0_sys_types` VALUES(25, 1, '1');
INSERT INTO `0_sys_types` VALUES(26, 1, '1');
INSERT INTO `0_sys_types` VALUES(28, 1, '1');
INSERT INTO `0_sys_types` VALUES(29, 1, '1');
INSERT INTO `0_sys_types` VALUES(30, 0, '1');
INSERT INTO `0_sys_types` VALUES(32, 0, '1');
INSERT INTO `0_sys_types` VALUES(35, 1, '1');
INSERT INTO `0_sys_types` VALUES(40, 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `0_tags`
--

DROP TABLE IF EXISTS `0_tags`;
CREATE TABLE IF NOT EXISTS `0_tags` (
  `id` int(11) NOT NULL auto_increment,
  `type` smallint(6) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(60) default NULL,
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_tag_associations`
--

DROP TABLE IF EXISTS `0_tag_associations`;
CREATE TABLE IF NOT EXISTS `0_tag_associations` (
  `record_id` varchar(15) NOT NULL,
  `tag_id` int(11) NOT NULL,
  UNIQUE KEY `record_id` (`record_id`,`tag_id`)
) ENGINE=MyISAM;

--
-- Dumping data for table `0_tag_associations`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_tax_groups`
--

DROP TABLE IF EXISTS `0_tax_groups`;
CREATE TABLE IF NOT EXISTS `0_tax_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `tax_shipping` tinyint(1) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  AUTO_INCREMENT=3 ;

--
-- Dumping data for table `0_tax_groups`
--

INSERT INTO `0_tax_groups` VALUES(1, 'Tax', 0, 0);
INSERT INTO `0_tax_groups` VALUES(2, 'Tax Exempt', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_tax_group_items`
--

DROP TABLE IF EXISTS `0_tax_group_items`;
CREATE TABLE IF NOT EXISTS `0_tax_group_items` (
  `tax_group_id` int(11) NOT NULL default '0',
  `tax_type_id` int(11) NOT NULL default '0',
  `rate` double NOT NULL default '0',
  PRIMARY KEY  (`tax_group_id`,`tax_type_id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_tax_group_items`
--

INSERT INTO `0_tax_group_items` VALUES(1, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `0_tax_types`
--

DROP TABLE IF EXISTS `0_tax_types`;
CREATE TABLE IF NOT EXISTS `0_tax_types` (
  `id` int(11) NOT NULL auto_increment,
  `rate` double NOT NULL default '0',
  `sales_gl_code` varchar(15) NOT NULL default '',
  `purchasing_gl_code` varchar(15) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_tax_types`
--

INSERT INTO `0_tax_types` VALUES(1, 5, '2140', '2140', 'Tax', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_trans_tax_details`
--

DROP TABLE IF EXISTS `0_trans_tax_details`;
CREATE TABLE IF NOT EXISTS `0_trans_tax_details` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_trans_tax_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_useronline`
--

DROP TABLE IF EXISTS `0_useronline`;
CREATE TABLE IF NOT EXISTS `0_useronline` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(15) NOT NULL default '0',
  `ip` varchar(40) NOT NULL default '',
  `file` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_useronline`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_users`
--

DROP TABLE IF EXISTS `0_users`;
CREATE TABLE IF NOT EXISTS `0_users` (
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
  `startup_tab` varchar(20) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `0_users`
--

INSERT INTO `0_users` VALUES(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'Administrator', 2, '', 'adm@adm.com', 'en_US', 0, 0, 0, 0, 'default', 'Letter', 2, 2, 4, 1, 1, 0, 0, '2008-04-04 12:34:29', 10, 1, 1, '1', 1, 0, 'orders', 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_voided`
--

DROP TABLE IF EXISTS `0_voided`;
CREATE TABLE IF NOT EXISTS `0_voided` (
  `type` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  `memo_` tinytext NOT NULL,
  UNIQUE KEY `id` (`type`,`id`)
) ENGINE=InnoDB;

--
-- Dumping data for table `0_voided`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_workcentres`
--

DROP TABLE IF EXISTS `0_workcentres`;
CREATE TABLE IF NOT EXISTS `0_workcentres` (
  `id` int(11) NOT NULL auto_increment,
  `name` char(40) NOT NULL default '',
  `description` char(50) NOT NULL default '',
  `inactive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_workcentres`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_workorders`
--

DROP TABLE IF EXISTS `0_workorders`;
CREATE TABLE IF NOT EXISTS `0_workorders` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_workorders`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_wo_issues`
--

DROP TABLE IF EXISTS `0_wo_issues`;
CREATE TABLE IF NOT EXISTS `0_wo_issues` (
  `issue_no` int(11) NOT NULL auto_increment,
  `workorder_id` int(11) NOT NULL default '0',
  `reference` varchar(100) default NULL,
  `issue_date` date default NULL,
  `loc_code` varchar(5) default NULL,
  `workcentre_id` int(11) default NULL,
  PRIMARY KEY  (`issue_no`),
  KEY `workorder_id` (`workorder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_wo_issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_wo_issue_items`
--

DROP TABLE IF EXISTS `0_wo_issue_items`;
CREATE TABLE IF NOT EXISTS `0_wo_issue_items` (
  `id` int(11) NOT NULL auto_increment,
  `stock_id` varchar(40) default NULL,
  `issue_id` int(11) default NULL,
  `qty_issued` double default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_wo_issue_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_wo_manufacture`
--

DROP TABLE IF EXISTS `0_wo_manufacture`;
CREATE TABLE IF NOT EXISTS `0_wo_manufacture` (
  `id` int(11) NOT NULL auto_increment,
  `reference` varchar(100) default NULL,
  `workorder_id` int(11) NOT NULL default '0',
  `quantity` double NOT NULL default '0',
  `date_` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  KEY `workorder_id` (`workorder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_wo_manufacture`
--


-- --------------------------------------------------------

--
-- Table structure for table `0_wo_requirements`
--

DROP TABLE IF EXISTS `0_wo_requirements`;
CREATE TABLE IF NOT EXISTS `0_wo_requirements` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `0_wo_requirements`
--
