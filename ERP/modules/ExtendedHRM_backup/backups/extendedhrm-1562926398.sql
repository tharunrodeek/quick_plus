# MySQL dump of database 'ALHAMER' on host 'localhost'
# Backup Date and Time: 2019-07-12 12:13 pm
# Module name : Extended HRM
# Module Author : Kvvaradha
# http://www.kvcodes.com
# Company: ALHAMER
# User : Administrator
# Compatibility: 2.4.1

SET SQL_MODE='';

# Table Backups hrm_tech_alhamer

#------------------------------------------------------------------------------------
    ### Structure of table `kv_empl_advance_payments` ### 

 DROP TABLE IF EXISTS 0_kv_empl_advance_payments;

CREATE TABLE `0_kv_empl_advance_payments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `month` int(2) NOT NULL,
  `date` date NOT NULL,
  `year` int(3) NOT NULL,
  `amount` double NOT NULL,
  `purpose` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_allowance_advanced` ### 

 DROP TABLE IF EXISTS 0_kv_empl_allowance_advanced;

CREATE TABLE `0_kv_empl_allowance_advanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `allowance_id` int(11) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `formula` text NOT NULL,
  `value` varchar(20) NOT NULL,
  `percentage` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_allowance_advanced VALUES("1","1","1","","Profile Input","0"),
 ("2","2","1","","Gross Percentage","40"),
 ("3","3","1","","Payroll Input","0"),
 ("4","4","1","","Profile Input","0"),
 ("5","5","1","","Profile Input","0"),
 ("6","6","1","","Profile Input","0"),
 ("7","7","1","","Calculation","0"),
 ("8","8","1","","Calculation","0"),
 ("9","9","1","","Calculation","0"),
 ("10","10","1","","Calculation","0"),
 ("11","11","1","","Calculation","0"),
 ("12","12","1","","Calculation","0"),
 ("13","13","1","","Calculation","0"),
 ("14","14","1","","Calculation","0"),
 ("15","15","1","","Profile Input","0"),
 ("16","16","1","","Percentage","5"),
 ("17","17","1","","Payroll Input","0"),
 ("18","2","3","","Profile Input","0"),
 ("19","6","3","","Profile Input","0"),
 ("20","16","3","","Profile Input","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_allowances` ### 

 DROP TABLE IF EXISTS 0_kv_empl_allowances;

CREATE TABLE `0_kv_empl_allowances` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `debit_code` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `credit_code` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `unique_name` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `basic` int(11) DEFAULT NULL,
  `Tax` int(2) NOT NULL,
  `loan` tinyint(1) NOT NULL,
  `esic` tinyint(1) NOT NULL,
  `pf` tinyint(1) NOT NULL,
  `al_type` int(3) NOT NULL DEFAULT '0',
  `gross` tinyint(1) NOT NULL,
  `sort_order` int(3) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_allowances VALUES("1","","","gros","Gross Pay","Earnings","0","0","0","0","0","0","1","1","0"),
 ("2","","","basi","Basic Pay","Earnings","1","0","0","0","0","0","0","2","0"),
 ("3","","","loan","Loan","Deductions","0","0","1","0","0","0","0","3","0"),
 ("4","","","trav","Travel/Transport Allowance","Earnings","0","0","0","0","0","0","0","4","0"),
 ("5","","","hous","Housing Allowance","Earnings","0","0","0","0","0","0","0","5","0"),
 ("6","","","educ","Education Allowance","Earnings","0","0","0","0","0","0","0","6","0"),
 ("7","","","lmra","LMRA Fees","Employer Contribution","0","0","0","0","0","1","0","7","0"),
 ("8","","","medi","Medical Allowance","Employer Contribution","0","0","0","0","0","3","0","8","0"),
 ("9","","","visa","Visa and Immigartion Exp","Employer Contribution","0","0","0","0","0","4","0","9","0"),
 ("10","","","leav","Leave Travel","Employer Contribution","0","0","0","0","0","5","0","10","0"),
 ("11","","","gose","Gosi - EE 1%","Deductions","0","0","0","0","0","2","0","11","0"),
 ("12","","","gosr","Gosi- ER 3%","Employer Contribution","0","0","0","0","0","2","0","12","0"),
 ("13","","","levp","Leave Pay","Employer Contribution","0","0","0","0","0","6","0","13","0"),
 ("14","","","inde","Indemnity ","Employer Contribution","0","0","0","0","0","7","0","14","0"),
 ("15","","","paci","Personal Accident Insurance","Employer Contribution","0","0","0","0","0","8","0","15","0"),
 ("16","","","food","Food Allowance","Earnings","0","0","0","0","0","0","0","16","0"),
 ("17","","","pena","Penalties","Deductions","0","0","0","0","0","0","0","17","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_allowances_coa` ### 

 DROP TABLE IF EXISTS 0_kv_empl_allowances_coa;

CREATE TABLE `0_kv_empl_allowances_coa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_id` int(11) NOT NULL,
  `accrual_salary_wage` varchar(15) NOT NULL,
  `bank_salary_wage` varchar(15) NOT NULL,
  `accrual_lmra_fees` varchar(15) NOT NULL,
  `bank_lmra_fees` varchar(15) NOT NULL,
  `accrual_social_security` varchar(15) NOT NULL,
  `bank_social_security` varchar(15) NOT NULL,
  `prepaid_medical_expenses` varchar(15) NOT NULL,
  `bank_medical_expenses` varchar(15) NOT NULL,
  `prepaid_visa_expenses` varchar(15) NOT NULL,
  `bank_visa_expenses` varchar(15) NOT NULL,
  `accrual_leave_travel` varchar(15) NOT NULL,
  `bank_leave_travel` varchar(15) NOT NULL,
  `accrual_leave_pay` varchar(15) NOT NULL,
  `bank_leave_pay` varchar(15) NOT NULL,
  `accrual_indemnity` varchar(15) NOT NULL,
  `bank_indemnity` varchar(15) NOT NULL,
  `prepaid_insurance` varchar(15) NOT NULL,
  `bank_insurance` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_allowances_coa VALUES("1","106","5501106","2","5502106","2","5503106","0","","2","","2","","2","5507106","2","5508106","2","5509106","2"),
 ("2","101","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("3","103","5501103","1","5503103","1","5503103","","","1","","1","","1","1060","1","1060","1","1060","1"),
 ("4","109","5501109","2","5502109","2","5503109","0","5504109","2","5505109","2","5506109","2","5507109","2","5508109","2","5509109","2"),
 ("5","104","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("6","110","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("7","111","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("8","102","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("9","112","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("10","113","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("11","114","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("12","115","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("13","117","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("14","118","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("15","119","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("16","0","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("17","0","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("18","0","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("19","0","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("20","125","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("21","120","9010003","0","9010002","0","4010005","0","4010005","0","4010005","0","4010003","0","4010002","0","4010001","0","4010005","0"),
 ("22","121","9010003","0","9010002","0","4010005","0","4010005","0","4010005","0","4010003","0","4010002","0","4010001","0","4010005","0"),
 ("23","123","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("24","126","","0","","0","","0","","0","","0","","0","","0","","0","","0"),
 ("25","127","","0","","0","","0","","0","","0","","0","","0","","0","","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_attendance` ### 

 DROP TABLE IF EXISTS 0_kv_empl_attendance;

CREATE TABLE `0_kv_empl_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_id` int(11) NOT NULL,
  `code` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `shift` int(11) NOT NULL,
  `a_date` date NOT NULL,
  `in_time` time NOT NULL,
  `out_time` time NOT NULL,
  `dimension` int(11) NOT NULL DEFAULT '0',
  `dimension2` int(11) NOT NULL DEFAULT '0',
  `duration` int(11) NOT NULL,
  `ot` int(11) NOT NULL,
  `sot` int(11) NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_attendance VALUES("1","129","P","0","2019-01-01","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("2","129","P","0","2019-01-02","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("3","129","P","0","2019-01-03","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("4","129","P","0","2019-01-04","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("5","129","P","0","2019-01-05","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("7","129","SL","0","2019-01-08","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("8","129","SL","0","2019-01-09","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("9","129","SL","0","2019-01-10","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("10","129","SL","0","2019-01-11","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("11","129","SL","0","2019-01-12","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("12","129","P","0","2019-01-14","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("13","129","P","0","2019-01-15","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("14","129","P","0","2019-01-16","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("15","129","P","0","2019-01-17","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("16","129","P","0","2019-01-18","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("17","129","P","0","2019-01-19","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("18","129","P","0","2019-01-21","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("19","129","P","0","2019-01-22","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("20","129","P","0","2019-01-23","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("21","129","P","0","2019-01-24","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("22","129","P","0","2019-01-25","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("23","129","P","0","2019-01-26","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("24","129","P","0","2019-01-28","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("25","129","P","0","2019-01-29","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("26","129","P","0","2019-01-30","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("27","129","P","0","2019-01-31","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("28","129","AL","0","2019-02-01","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("29","129","AL","0","2019-02-02","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("30","129","P","0","2019-02-04","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("31","129","P","0","2019-02-05","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("32","129","P","0","2019-02-06","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("33","129","P","0","2019-02-07","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("34","129","P","0","2019-02-08","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("35","129","P","0","2019-02-09","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("36","129","A","0","2019-02-11","00:00:00","00:00:00","0","0","0","0","0","","0"),
 ("37","129","HL","0","2019-02-12","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("38","129","HL","0","2019-02-13","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("39","129","HL","0","2019-02-14","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("40","129","HL","0","2019-02-15","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("41","129","HL","0","2019-02-16","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("42","129","A","0","2019-02-18","00:00:00","00:00:00","0","0","0","0","0","","0"),
 ("43","129","P","0","2019-02-19","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("44","129","P","0","2019-02-20","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("45","129","P","0","2019-02-21","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("46","129","P","0","2019-02-22","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("47","129","P","0","2019-02-23","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("48","129","P","0","2019-02-25","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("49","129","P","0","2019-02-26","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("50","129","P","0","2019-02-27","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("51","129","P","0","2019-02-28","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("52","129","AL","0","2019-03-01","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("53","129","AL","0","2019-03-02","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("54","129","AL","0","2019-03-04","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("55","129","AL","0","2019-03-05","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("56","129","AL","0","2019-03-06","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("57","129","AL","0","2019-03-07","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("58","129","AL","0","2019-03-08","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("59","129","AL","0","2019-03-09","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("60","129","A","0","2019-03-11","00:00:00","00:00:00","0","0","0","0","0","","0"),
 ("61","129","P","0","2019-03-12","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("62","129","P","0","2019-03-13","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("63","129","P","0","2019-03-14","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("64","129","P","0","2019-03-15","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("65","129","P","0","2019-03-16","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("66","129","P","0","2019-03-18","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("67","129","P","0","2019-03-19","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("68","129","P","0","2019-03-20","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("69","129","P","0","2019-03-21","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("70","129","P","0","2019-03-29","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("71","129","P","0","2019-03-30","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("72","129","SL","0","2019-04-01","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("73","129","SL","0","2019-04-02","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("74","129","SL","0","2019-04-03","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("75","129","SL","0","2019-04-04","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("76","129","SL","0","2019-04-05","00:00:00","00:00:00","0","0","36000","0","0","","0"),
 ("77","129","SL","0","2019-04-06","00:00:00","00:00:00","0","0","18000","0","0","Full day Sick leave exhausted. It will be considered  as Half day","0"),
 ("78","129","P","0","2019-04-08","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("79","129","P","0","2019-04-09","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("80","129","P","0","2019-04-10","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("81","129","P","0","2019-04-11","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("82","129","P","0","2019-04-12","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("83","129","P","0","2019-04-13","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("84","129","P","0","2019-04-15","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("85","129","P","0","2019-04-16","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("86","129","P","0","2019-04-17","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("87","129","P","0","2019-04-18","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("88","129","P","0","2019-04-19","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("89","129","P","0","2019-04-20","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("90","129","P","0","2019-04-22","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("91","129","P","0","2019-04-23","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("92","129","P","0","2019-04-24","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("93","129","P","0","2019-04-25","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("94","129","P","0","2019-04-26","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("95","129","P","0","2019-04-27","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("96","129","P","0","2019-04-29","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("97","129","P","0","2019-04-30","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("98","129","SL","0","2019-05-01","00:00:00","00:00:00","0","0","18000","0","0","Full day Sick leave exhausted. It will be considered  as Half day","0"),
 ("99","129","SL","0","2019-05-02","00:00:00","00:00:00","0","0","18000","0","0","Full day Sick leave exhausted. It will be considered  as Half day","0"),
 ("100","129","SL","0","2019-05-03","00:00:00","00:00:00","0","0","18000","0","0","Full day Sick leave exhausted. It will be considered  as Half day","0"),
 ("101","129","SL","0","2019-05-04","00:00:00","00:00:00","0","0","18000","0","0","Full day Sick leave exhausted. It will be considered  as Half day","0"),
 ("102","129","A","0","2019-05-06","00:00:00","00:00:00","0","0","0","0","0","sick leave Completely exhausted. It will be considered as Absent","0"); 
INSERT INTO 0_kv_empl_attendance VALUES("103","129","P","0","2019-05-07","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("104","129","P","0","2019-05-08","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("105","129","P","0","2019-05-09","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("106","129","P","0","2019-05-10","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("107","129","P","0","2019-05-11","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("108","129","P","0","2019-05-13","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("109","129","P","0","2019-05-14","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("110","129","P","0","2019-05-15","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("111","129","P","0","2019-05-16","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("112","129","P","0","2019-05-17","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("113","129","P","0","2019-05-18","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("114","129","P","0","2019-05-20","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("115","129","P","0","2019-05-21","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("116","129","P","0","2019-05-22","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("117","129","P","0","2019-05-23","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("118","129","P","0","2019-05-24","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("119","129","P","0","2019-05-25","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("120","129","P","0","2019-05-27","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("121","129","P","0","2019-05-28","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("122","129","P","0","2019-05-29","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("123","129","P","0","2019-05-30","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("124","129","P","0","2019-05-31","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("125","129","ML","0","2019-06-01","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("126","129","ML","0","2019-06-03","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("127","129","ML","0","2019-06-04","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("128","129","ML","0","2019-06-05","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("129","129","ML","0","2019-06-06","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("130","129","ML","0","2019-06-07","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("131","129","ML","0","2019-06-08","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("132","129","ML","0","2019-06-10","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("133","129","ML","0","2019-06-11","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("134","129","ML","0","2019-06-12","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("135","129","A","0","2019-06-13","00:00:00","00:00:00","0","0","0","0","0","","0"),
 ("136","129","A","0","2019-06-14","00:00:00","00:00:00","0","0","0","0","0","","0"),
 ("137","129","P","0","2019-06-15","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("138","129","P","0","2019-06-17","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("139","129","P","0","2019-06-18","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("140","129","P","0","2019-06-19","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("141","129","P","0","2019-06-20","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("142","129","P","0","2019-06-21","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("143","129","P","0","2019-06-22","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("144","129","P","0","2019-06-24","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("145","129","P","0","2019-06-25","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("146","129","P","0","2019-06-26","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("147","129","P","0","2019-06-27","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("148","129","P","0","2019-06-28","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("149","129","P","0","2019-06-29","08:00:00","18:00:00","0","0","36000","0","0","","0"),
 ("150","129","A","0","2019-01-07","00:00:00","00:00:00","0","0","0","0","0","","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_attendance_settings` ### 

 DROP TABLE IF EXISTS 0_kv_empl_attendance_settings;

CREATE TABLE `0_kv_empl_attendance_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dept_id` int(5) NOT NULL,
  `option_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `option_value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_attendance_settings VALUES("2","2","early_coming_punch",""),
 ("3","2","Halfday_workduration","1"),
 ("4","2","absent_workduration","1"),
 ("5","2","mark_half_day_late","1"),
 ("6","2","mark_half_day_early_go","1"),
 ("7","2","late_going_punch","1"),
 ("8","2","grace_in_time","00:20:00"),
 ("9","2","grace_out_time","00:10:00"),
 ("10","2","Halfday_workduration_min","05:00:00"),
 ("11","2","absent_workduration_min","02:00:00"),
 ("12","2","mark_half_day_late_min","01:00:00"),
 ("13","2","mark_half_day_early_go_min","01:00:00"),
 ("15","1","early_coming_punch",""),
 ("16","1","Halfday_workduration",""),
 ("17","1","absent_workduration",""),
 ("18","1","mark_half_day_late",""),
 ("19","1","mark_half_day_early_go",""),
 ("20","1","late_going_punch",""),
 ("21","1","grace_in_time","00:20:00"),
 ("22","1","grace_out_time","00:15:00"),
 ("23","1","Halfday_workduration_min","03:40:00"),
 ("24","1","absent_workduration_min","01:00:00"),
 ("25","1","mark_half_day_late_min","04:00:00"),
 ("26","1","mark_half_day_early_go_min","02:00:00"),
 ("27","3","early_coming_punch","1"),
 ("28","3","Halfday_workduration",""),
 ("29","3","absent_workduration",""),
 ("30","3","mark_half_day_late",""),
 ("31","3","mark_half_day_early_go",""),
 ("32","3","late_going_punch","1"),
 ("33","3","grace_in_time","00:30:00"),
 ("34","3","grace_out_time","00:30:00"),
 ("35","3","Halfday_workduration_min","07:48:00"),
 ("36","3","absent_workduration_min","07:48:00"),
 ("37","3","mark_half_day_late_min","07:48:00"),
 ("38","3","mark_half_day_early_go_min","07:48:00");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_attendancee` ### 

 DROP TABLE IF EXISTS 0_kv_empl_attendancee;

CREATE TABLE `0_kv_empl_attendancee` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `year` int(2) DEFAULT NULL,
  `dept_id` int(10) NOT NULL,
  `empl_id` int(10) DEFAULT NULL,
  `1` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `2` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `3` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `4` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `5` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `6` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `7` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `8` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `9` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `10` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `11` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `12` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `13` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `14` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `15` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `16` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `17` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `18` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `19` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `20` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `21` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `22` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `23` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `24` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `25` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `26` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `27` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `28` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `29` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `30` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `31` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_country` ### 

 DROP TABLE IF EXISTS 0_kv_empl_country;

CREATE TABLE `0_kv_empl_country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iso` varchar(50) DEFAULT NULL,
  `local_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;

INSERT INTO 0_kv_empl_country VALUES("1","AD","Andorra"),
 ("2","AE","United Arab Emirates"),
 ("3","AF","Afghanistan"),
 ("4","AG","Antigua and Barbuda"),
 ("5","AI","Anguilla"),
 ("6","AL","Albania"),
 ("7","AM","Armenia"),
 ("8","AN","Netherlands Antilles"),
 ("9","AO","Angola"),
 ("10","AQ","Antarctica"),
 ("11","AR","Argentina"),
 ("12","AS","American Samoa"),
 ("13","AT","Austria"),
 ("14","AU","Australia"),
 ("15","AW","Aruba"),
 ("16","AX","Aland Islands"),
 ("17","AZ","Azerbaijan"),
 ("18","BA","Bosnia and Herzegovina"),
 ("19","BB","Barbados"),
 ("20","BD","Bangladesh"),
 ("21","BE","Belgium"),
 ("22","BF","Burkina Faso"),
 ("23","BG","Bulgaria"),
 ("24","BH","Bahrain"),
 ("25","BI","Burundi"),
 ("26","BJ","Benin"),
 ("27","BL","Saint Barthlemy"),
 ("28","BM","Bermuda"),
 ("29","BN","Brunei Darussalam"),
 ("30","BO","BoliviaBolivia, Plurinational state of"),
 ("31","BR","Brazil"),
 ("32","BS","Bahamas"),
 ("33","BT","Bhutan"),
 ("34","BV","Bouvet Island"),
 ("35","BW","Botswana"),
 ("36","BY","Belarus"),
 ("37","BZ","Belize"),
 ("38","CA","Canada"),
 ("39","CC","Cocos (Keeling) Islands"),
 ("40","CD","Congo, The Democratic Republic of the"),
 ("41","CF","Central African Republic"),
 ("42","CG","Congo"),
 ("43","CH","Switzerland"),
 ("45","CK","Cook Islands"),
 ("46","CL","Chile"),
 ("47","CM","Cameroon"),
 ("48","CN","China"),
 ("49","CO","Colombia"),
 ("50","CR","Costa Rica"),
 ("51","CU","Cuba"),
 ("52","CV","Cape Verde"),
 ("53","CX","Christmas Island"),
 ("54","CY","Cyprus"),
 ("55","CZ","Czech Republic"),
 ("56","DE","Germany"),
 ("57","DJ","Djibouti"),
 ("58","DK","Denmark"),
 ("59","DM","Dominica"),
 ("60","DO","Dominican Republic"),
 ("61","DZ","Algeria"),
 ("62","EC","Ecuador"),
 ("63","EE","Estonia"),
 ("64","EG","Egypt"),
 ("65","EH","Western Sahara"),
 ("66","ER","Eritrea"),
 ("67","ES","Spain"),
 ("68","ET","Ethiopia"),
 ("69","FI","Finland"),
 ("70","FJ","Fiji"),
 ("71","FK","Falkland Islands (Malvinas)"),
 ("72","FM","Micronesia, Federated States of"),
 ("73","FO","Faroe Islands"),
 ("74","FR","France"),
 ("75","GA","Gabon"),
 ("76","GB","United Kingdom"),
 ("77","GD","Grenada"),
 ("78","GE","Georgia"),
 ("79","GF","French Guiana"),
 ("80","GG","Guernsey"),
 ("81","GH","Ghana"),
 ("82","GI","Gibraltar"),
 ("83","GL","Greenland"),
 ("84","GM","Gambia"),
 ("85","GN","Guinea"),
 ("86","GP","Guadeloupe"),
 ("87","GQ","Equatorial Guinea"),
 ("88","GR","Greece"),
 ("89","GS","South Georgia and the South Sandwich Islands"),
 ("90","GT","Guatemala"),
 ("91","GU","Guam"),
 ("92","GW","Guinea-Bissau"),
 ("93","GY","Guyana"),
 ("94","HK","Hong Kong"),
 ("95","HM","Heard Island and McDonald Islands"),
 ("96","HN","Honduras"),
 ("97","HR","Croatia"),
 ("98","HT","Haiti"),
 ("99","HU","Hungary"),
 ("100","ID","Indonesia"),
 ("101","IE","Ireland"),
 ("102","IL","Israel"); 
INSERT INTO 0_kv_empl_country VALUES("103","IM","Isle of Man"),
 ("104","IN","India"),
 ("105","IO","British Indian Ocean Territory"),
 ("106","IQ","Iraq"),
 ("107","IR","Iran, Islamic Republic of"),
 ("108","IS","Iceland"),
 ("109","IT","Italy"),
 ("110","JE","Jersey"),
 ("111","JM","Jamaica"),
 ("112","JO","Jordan"),
 ("113","JP","Japan"),
 ("114","KE","Kenya"),
 ("115","KG","Kyrgyzstan"),
 ("116","KH","Cambodia"),
 ("117","KI","Kiribati"),
 ("118","KM","Comoros"),
 ("119","KN","Saint Kitts and Nevis"),
 ("120","KP","Korea, Democratic People&#039;s Republic of"),
 ("121","KR","Korea, Republic of"),
 ("122","KW","Kuwait"),
 ("123","KY","Cayman Islands"),
 ("124","KZ","Kazakhstan"),
 ("125","LA","Lao People&#039;s Democratic Republic"),
 ("126","LB","Lebanon"),
 ("127","LC","Saint Lucia"),
 ("128","LI","Liechtenstein"),
 ("129","LK","Sri Lanka"),
 ("130","LR","Liberia"),
 ("131","LS","Lesotho"),
 ("132","LT","Lithuania"),
 ("133","LU","Luxembourg"),
 ("134","LV","Latvia"),
 ("135","LY","Libyan Arab Jamahiriya"),
 ("136","MA","Morocco"),
 ("137","MC","Monaco"),
 ("138","MD","Moldova, Republic of"),
 ("139","ME","Montenegro"),
 ("140","MF","Saint Martin"),
 ("141","MG","Madagascar"),
 ("142","MH","Marshall Islands"),
 ("143","MK","Macedonia"),
 ("144","ML","Mali"),
 ("145","MM","Myanmar"),
 ("146","MN","Mongolia"),
 ("147","MO","Macao"),
 ("148","MP","Northern Mariana Islands"),
 ("149","MQ","Martinique"),
 ("150","MR","Mauritania"),
 ("151","MS","Montserrat"),
 ("152","MT","Malta"),
 ("153","MU","Mauritius"),
 ("154","MV","Maldives"),
 ("155","MW","Malawi"),
 ("156","MX","Mexico"),
 ("157","MY","Malaysia"),
 ("158","MZ","Mozambique"),
 ("159","NA","Namibia"),
 ("160","NC","New Caledonia"),
 ("161","NE","Niger"),
 ("162","NF","Norfolk Island"),
 ("163","NG","Nigeria"),
 ("164","NI","Nicaragua"),
 ("165","NL","Netherlands"),
 ("166","NO","Norway"),
 ("167","NP","Nepal"),
 ("168","NR","Nauru"),
 ("169","NU","Niue"),
 ("170","NZ","New Zealand"),
 ("171","OM","Oman"),
 ("172","PA","Panama"),
 ("173","PE","Peru"),
 ("174","PF","French Polynesia"),
 ("175","PG","Papua New Guinea"),
 ("176","PH","Philippines"),
 ("177","PK","Pakistan"),
 ("178","PL","Poland"),
 ("179","PM","Saint Pierre and Miquelon"),
 ("180","PN","Pitcairn"),
 ("181","PR","Puerto Rico"),
 ("182","PS","Palestinian Territory, Occupied"),
 ("183","PT","Portugal"),
 ("184","PW","Palau"),
 ("185","PY","Paraguay"),
 ("186","QA","Qatar"),
 ("188","RO","Romania"),
 ("189","RS","Serbia"),
 ("190","RU","Russian Federation"),
 ("191","RW","Rwanda"),
 ("192","SA","Saudi Arabia"),
 ("193","SB","Solomon Islands"),
 ("194","SC","Seychelles"),
 ("195","SD","Sudan"),
 ("196","SE","Sweden"),
 ("197","SG","Singapore"),
 ("198","SH","Saint Helena"),
 ("199","SI","Slovenia"),
 ("200","SJ","Svalbard and Jan Mayen"),
 ("201","SK","Slovakia"),
 ("202","SL","Sierra Leone"),
 ("203","SM","San Marino"),
 ("204","SN","Senegal"); 
INSERT INTO 0_kv_empl_country VALUES("205","SO","Somalia"),
 ("206","SR","Suriname"),
 ("207","ST","Sao Tome and Principe"),
 ("208","SV","El Salvador"),
 ("209","SY","Syrian Arab Republic"),
 ("210","SZ","Swaziland"),
 ("211","TC","Turks and Caicos Islands"),
 ("212","TD","Chad"),
 ("213","TF","French Southern Territories"),
 ("214","TG","Togo"),
 ("215","TH","Thailand"),
 ("216","TJ","Tajikistan"),
 ("217","TK","Tokelau"),
 ("218","TL","Timor-Leste"),
 ("219","TM","Turkmenistan"),
 ("220","TN","Tunisia"),
 ("221","TO","Tonga"),
 ("222","TR","Turkey"),
 ("223","TT","Trinidad and Tobago"),
 ("224","TV","Tuvalu"),
 ("225","TW","Taiwan"),
 ("226","TZ","Tanzania, United Republic of"),
 ("227","UA","Ukraine"),
 ("228","UG","Uganda"),
 ("229","UM","United States Minor Outlying Islands"),
 ("230","US","United States"),
 ("231","UY","Uruguay"),
 ("232","UZ","Uzbekistan"),
 ("233","VA","Holy See (Vatican City State)"),
 ("234","VC","Saint Vincent and the Grenadines"),
 ("235","VE","Venezuela, Bolivarian Republic of"),
 ("236","VG","Virgin Islands, British"),
 ("237","VI","Virgin Islands, U.S."),
 ("238","VN","Viet Nam"),
 ("239","VU","Vanuatu"),
 ("240","WF","Wallis and Futuna"),
 ("241","WS","Samoa"),
 ("242","YE","Yemen"),
 ("243","YT","Mayotte"),
 ("244","ZA","South Africa"),
 ("245","ZM","Zambia"),
 ("246","ZW","Zimbabwe");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_cv` ### 

 DROP TABLE IF EXISTS 0_kv_empl_cv;

CREATE TABLE `0_kv_empl_cv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cv_title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `doc_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `exp_date` date NOT NULL,
  `notify_from` date NOT NULL,
  `alert` tinyint(1) NOT NULL,
  `related_to` int(11) NOT NULL,
  `filename` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
  `filesize` int(11) NOT NULL DEFAULT '0',
  `filetype` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `unique_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_cv VALUES("1","129","test","1","2020-07-02","2020-05-18","0","0","nature-2880x1800-wallpaper.jpg","0","","5d2841798e284");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_degree` ### 

 DROP TABLE IF EXISTS 0_kv_empl_degree;

CREATE TABLE `0_kv_empl_degree` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `degree` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `major` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `university` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `grade` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `year` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_degree VALUES("3","129","msc","computer science","anna university","A","2000-08-12");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_departments` ### 

 DROP TABLE IF EXISTS 0_kv_empl_departments;

CREATE TABLE `0_kv_empl_departments` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_departments VALUES("1","HRM","0"),
 ("2","CRM","0"),
 ("3","CCK2","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_designation` ### 

 DROP TABLE IF EXISTS 0_kv_empl_designation;

CREATE TABLE `0_kv_empl_designation` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_designation VALUES("1","Senior","0"),
 ("2","Junior","0"),
 ("3","SUB JUNIOR1","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_designation_group` ### 

 DROP TABLE IF EXISTS 0_kv_empl_designation_group;

CREATE TABLE `0_kv_empl_designation_group` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_designation_group VALUES("1","Senior","0"),
 ("2","Employees","0"),
 ("3","Labours","0"),
 ("4","JUNIORS1","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_doc_type` ### 

 DROP TABLE IF EXISTS 0_kv_empl_doc_type;

CREATE TABLE `0_kv_empl_doc_type` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `days` int(4) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_doc_type VALUES("1","Passport","45","0"),
 ("2","TEST1","20","0"),
 ("3","LMRA","30","0"),
 ("4","GOSI","10","0"),
 ("5","Driving License","60","0"),
 ("6","Contract","10","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_emails` ### 

 DROP TABLE IF EXISTS 0_kv_empl_emails;

CREATE TABLE `0_kv_empl_emails` (
  `id` int(40) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `empl_name` text COLLATE utf8_unicode_ci NOT NULL,
  `email_to` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `cc` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `subject` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `send_info` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_emails VALUES("2","101","testVj","nadhanshankar@gmail.com","nadhanshankar@gmail.com","testingemail","test mail","0"),
 ("3","107","Mizanur bhaiRahman","jothics8@gmail.com","kvvaradha@gmail.com","mail for appriciation","you did very good job","0"),
 ("4","107","Mizanur bhaiRahman","jothics8@gmail.com","kvvaradha@gmail.com","test","test email","1"),
 ("5","107","Mizanur bhaiRahman","jothics8@gmail.com","kvvaradha@gmail.com","retest","testing next","1");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_employment_types` ### 

 DROP TABLE IF EXISTS 0_kv_empl_employment_types;

CREATE TABLE `0_kv_empl_employment_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_employment_types VALUES("1","Permanent","0"),
 ("2","Temporary","0"),
 ("3","Contract Labout","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_esb` ### 

 DROP TABLE IF EXISTS 0_kv_empl_esb;

CREATE TABLE `0_kv_empl_esb` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` int(10) NOT NULL,
  `last_gross` double NOT NULL,
  `date` date NOT NULL,
  `days_worked` decimal(20,2) NOT NULL,
  `status` int(2) NOT NULL,
  `loan_amount` double NOT NULL,
  `amount` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_esic_pf` ### 

 DROP TABLE IF EXISTS 0_kv_empl_esic_pf;

CREATE TABLE `0_kv_empl_esic_pf` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `allowance_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `amt_limit` double NOT NULL,
  `date` date NOT NULL,
  `employer` double NOT NULL,
  `company` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_experience` ### 

 DROP TABLE IF EXISTS 0_kv_empl_experience;

CREATE TABLE `0_kv_empl_experience` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `company_name` varchar(260) NOT NULL,
  `company_location` varchar(160) NOT NULL,
  `department` varchar(150) NOT NULL,
  `designation` varchar(150) NOT NULL,
  `s_date` date NOT NULL,
  `e_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_experience VALUES("5","129","tcs","chennai","HR","manager","2011-07-01","2018-07-19");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_family` ### 

 DROP TABLE IF EXISTS 0_kv_empl_family;

CREATE TABLE `0_kv_empl_family` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` int(10) NOT NULL,
  `full_name` varchar(160) NOT NULL,
  `relation` int(11) NOT NULL,
  `filename` varchar(600) NOT NULL,
  `filesize` int(11) NOT NULL DEFAULT '0',
  `filetype` varchar(60) NOT NULL DEFAULT '',
  `unique_name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_family VALUES("1","129","sheela","1","01d2db351340173b5c3a2e85dfa0a397.jpg","0","","5d2841c4b1719");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_gazetted_holidays` ### 

 DROP TABLE IF EXISTS 0_kv_empl_gazetted_holidays;

CREATE TABLE `0_kv_empl_gazetted_holidays` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year` int(5) NOT NULL,
  `date` date NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_gazetted_holidays VALUES("1","2","2019-04-10","Test Holiday","0"),
 ("2","2","2019-05-01","GDG1","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_gosi_settings` ### 

 DROP TABLE IF EXISTS 0_kv_empl_gosi_settings;

CREATE TABLE `0_kv_empl_gosi_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nationality` int(5) NOT NULL,
  `employer` double NOT NULL,
  `employee` double NOT NULL,
  `allowances` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_gosi_settings VALUES("1","1","13","6","YToyOntpOjA7czoxOiIxIjtpOjE7czoyOiIyNyI7fQ=="),
 ("2","0","3","1","YToxOntpOjA7czoxOiIxIjt9");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_grade` ### 

 DROP TABLE IF EXISTS 0_kv_empl_grade;

CREATE TABLE `0_kv_empl_grade` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `min_salary` int(20) NOT NULL,
  `max_salary` int(20) NOT NULL,
  `hl` double NOT NULL,
  `al` double NOT NULL,
  `sl` double NOT NULL,
  `slh` double NOT NULL,
  `ml` double NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_grade VALUES("1","Manager","2000","20000","5","30","15","20","60","0"),
 ("2","Junior","10000","500000","5","30","15","20","60","0"),
 ("3","Accountant","12000","30000","5","30","15","20","60","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_history` ### 

 DROP TABLE IF EXISTS 0_kv_empl_history;

CREATE TABLE `0_kv_empl_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `option_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `option_value` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_change` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_history VALUES("3","117","home_phone","67676767671","2019-05-28"),
 ("4","117","empl_salutation","3","2019-05-29"),
 ("5","117","joining","2019-05-27","2019-05-28"),
 ("6","117","bank_name","SBI","2019-05-28"),
 ("7","117","4","3324334324","2019-05-28"),
 ("8","117","license_number","3324334324","2019-05-28"),
 ("9","117","4","3324334324345435534535353","2019-05-28"),
 ("10","117","license_number","3324334324345435534535353","2019-05-28"),
 ("11","117","6","2019-05-20","2019-05-28"),
 ("12","117","issue_date","2019-05-20","2019-05-28"),
 ("13","117","7","2020-05-20","2019-05-28"),
 ("14","117","expiry_date","2020-05-20","2019-05-28"),
 ("15","117","4","3324334","2019-05-28"),
 ("16","117","license_number","3324334","2019-05-28"),
 ("17","117","4","4321","2019-05-28"),
 ("18","117","license_number","4321","2019-05-28"),
 ("19","117","4","432178","2019-05-28"),
 ("20","117","license_number","432178","2019-05-28"),
 ("21","117","supervisor","104","2019-05-28"),
 ("22","117","supervisor","114","2019-05-28"),
 ("23","104","joining","2019-05-04","2019-05-28"),
 ("24","104","bond_period","0020-00-00","2019-05-28"),
 ("25","104","al","18","2019-05-28"),
 ("26","104","cl","18","2019-05-28"),
 ("27","104","ml","30","2019-05-28"),
 ("28","104","date_of_desig_change","2019-05-28","2019-05-28"),
 ("29","117","joining","2019-05-27","2019-05-28"),
 ("30","117","nominee_address","test","2019-05-28"),
 ("31","117","joining","2019-05-27","2019-05-28"),
 ("32","117","nominee_address","testc","2019-05-28"),
 ("33","117","address2","karur tamilnadu indiagf","2019-05-28"),
 ("34","104","joining","2019-05-04","2019-05-28"),
 ("35","104","date_of_desig_change","2019-05-28","2019-05-28"),
 ("36","115","p_period","2019-05-31","2019-05-29"),
 ("37","107","empl_firstname","Mizanur bhai","2019-05-30"),
 ("38","107","p_period","0","2019-05-30"),
 ("39","104","addr_line1","test","2019-05-30"),
 ("40","104","p_period","0","2019-05-30"),
 ("41","104","cmy_period","0","2019-05-30"),
 ("42","115","29","test note1","0000-00-00"),
 ("43","115","notes","test note1","0000-00-00"),
 ("44","115","29","test note12","2019-05-30"),
 ("45","115","notes","test note12","2019-05-30"),
 ("46","104","joining","2019-05-04","2019-06-03"),
 ("47","104","bond_period","0020-00-00","2019-06-03"),
 ("48","104","nominee_email","kvvaradha@gmail.com","2019-06-03"),
 ("49","107","joining","2013-03-01","2019-06-30"),
 ("50","107","bond_period","0020-00-00","2019-06-30"),
 ("51","111","weekly_off","Wed","2019-07-02"),
 ("52","111","joining","2019-05-27","2019-07-02"),
 ("53","123","4","6646464564566456564","2019-07-04"),
 ("54","123","license_number","6646464564566456564","2019-07-04"),
 ("55","123","7","2020-07-04","2019-07-04"),
 ("56","123","expiry_date","2020-07-04","2019-07-04"),
 ("57","129","3","18","2019-07-12"),
 ("58","129","license_type","18","2019-07-12"),
 ("59","129","4","1223656","2019-07-12"),
 ("60","129","license_number","1223656","2019-07-12");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_info` ### 

 DROP TABLE IF EXISTS 0_kv_empl_info;

CREATE TABLE `0_kv_empl_info` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `empl_salutation` varchar(9) COLLATE utf8_unicode_ci NOT NULL,
  `empl_firstname` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `empl_lastname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `addr_line1` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `addr_line2` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `address2` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `empl_city` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `empl_state` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country` int(5) NOT NULL,
  `gender` int(2) NOT NULL,
  `date_of_birth` date NOT NULL,
  `marital_status` int(2) NOT NULL,
  `home_phone` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `mobile_phone` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(2) NOT NULL,
  `date_of_status_change` date NOT NULL DEFAULT '0000-00-00',
  `reason_status_change` text COLLATE utf8_unicode_ci NOT NULL,
  `empl_pic` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `report_to` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `supervisor` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `ice_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ice_phone_no` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `n_period` date NOT NULL DEFAULT '0000-00-00',
  `p_period` date NOT NULL DEFAULT '0000-00-00',
  `cmy_period` date NOT NULL DEFAULT '0000-00-00',
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_info VALUES("1","129","25","1","mizanur","bhai s","north street","3/24","north street\n3/24\ncbe\ntamilnadu","cbe","tamilnadu","104","1","1999-07-12","2","89849849849","94574758754","jothi@kvcodes.com","1","0000-00-00","","","","","sheela","904586975","0000-00-00","2001-07-30","2020-09-12","test notes&lt;div&gt;$fields = array(&#039;company_name&#039;,&#039;company_location&#039;,&#039;department&#039;, &#039;designation&#039;,&#039;from&#039;, &#039;to&#039;);&lt;/div&gt;"),
 ("2","130","26","1","malar","vizhi","3E","VIJAY Street","karur","karur","tamilnadu","104","1","1999-07-12","1","","45345355","jothi@kvcodes.com1","1","0000-00-00","","","","","jo","654645646","0000-00-00","0000-00-00","0000-00-00","");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_job` ### 

 DROP TABLE IF EXISTS 0_kv_empl_job;

CREATE TABLE `0_kv_empl_job` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` int(10) NOT NULL,
  `grade` tinyint(2) NOT NULL,
  `al` double NOT NULL,
  `hl` double NOT NULL,
  `sl` double NOT NULL,
  `slh` double NOT NULL,
  `ml` double NOT NULL,
  `department` tinyint(2) NOT NULL,
  `nationality` int(11) NOT NULL DEFAULT '0',
  `medi_category` int(11) NOT NULL DEFAULT '0',
  `family` int(11) NOT NULL DEFAULT '0',
  `weekly_off` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `shift` int(3) NOT NULL,
  `desig_group` tinyint(2) NOT NULL,
  `desig` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `currency` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `joining` date NOT NULL,
  `bond_period` date NOT NULL DEFAULT '0000-00-00',
  `bond_doc` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `date_of_desig_change` date NOT NULL DEFAULT '0000-00-00',
  `empl_type` tinyint(2) NOT NULL,
  `empl_contract_type` int(11) NOT NULL DEFAULT '0',
  `expd_percentage_amt` double NOT NULL,
  `working_branch` tinyint(2) NOT NULL,
  `mod_of_pay` int(2) NOT NULL,
  `bank_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `acc_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `branch_detail` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ifsc` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ESIC` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `PF` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `PAN` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bloog_group` int(2) NOT NULL,
  `aadhar` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `nominee_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nominee_phone` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `nominee_email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `nominee_address` text COLLATE utf8_unicode_ci NOT NULL,
  `gross_pay_annum` double NOT NULL,
  `gross` double NOT NULL,
  `2` double NOT NULL DEFAULT '0',
  `3` double NOT NULL DEFAULT '0',
  `4` double NOT NULL DEFAULT '0',
  `5` double NOT NULL DEFAULT '0',
  `6` double NOT NULL DEFAULT '0',
  `7` double NOT NULL DEFAULT '0',
  `8` double NOT NULL DEFAULT '0',
  `9` double NOT NULL DEFAULT '0',
  `10` double NOT NULL DEFAULT '0',
  `11` double NOT NULL DEFAULT '0',
  `12` double NOT NULL DEFAULT '0',
  `13` double NOT NULL DEFAULT '0',
  `14` double NOT NULL DEFAULT '0',
  `15` double NOT NULL DEFAULT '0',
  `16` double NOT NULL DEFAULT '0',
  `17` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_job VALUES("1","129","3","10","5","10","5","10","1","7","2","3","YToxOntpOjA7czozOiJTdW4iO30=","0","2","2","USD","2017-07-12","2020-07-12","129-036751fda2944bff23f65c443e2f179c.jpg","0000-00-00","1","0","10","2","1","sbi4","4565444","kodumudi","5464","5675gfg","sdf","","1","657575757","sheela","34535353","jo@gmail.com1","karur","120000","10000","7000","0","0","0","1000","0","0","0","0","0","0","0","0","0","2000","0"),
 ("2","130","3","30","5","15","0","60","2","7","2","3","YToyOntpOjE7czozOiJNb24iO2k6MDtzOjM6IlN1biI7fQ==","0","2","2","USD","2019-07-12","2019-07-12","","0000-00-00","1","0","10","2","1","sbi","4565444","kodumudi","5464","5675gfg","sdf","","1","657575757","sheela","34535353","jo@gmail.com12","karur","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_leave_applied` ### 

 DROP TABLE IF EXISTS 0_kv_empl_leave_applied;

CREATE TABLE `0_kv_empl_leave_applied` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year` int(5) NOT NULL,
  `empl_id` int(10) NOT NULL,
  `leave_type` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `days` int(3) NOT NULL,
  `filename` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_leave_applied VALUES("1","2","103","ML","tes leavbe","2019-05-11","4","","1"),
 ("2","2","107","SL","tgest 2 leave","2019-05-20","4","","1"),
 ("3","2","107","ML","Medical test leave to add functionality in it.","2019-05-26","2","3.geesys.jpg","1"),
 ("5","2","107","AL","sdfasdfads","2019-05-25","3","5-g8.jpg","0"),
 ("6","2","107","AL","vacation","2019-07-11","2","","0"),
 ("7","2","113","AL","test leave","2019-07-10","2","","1");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_leave_days` ### 

 DROP TABLE IF EXISTS 0_kv_empl_leave_days;

CREATE TABLE `0_kv_empl_leave_days` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `leave_char` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `days` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_leave_days VALUES("1","101","2017-12-15","AL","21"),
 ("2","101","2017-12-15","SL","30"),
 ("3","101","2017-12-15","ML","5"),
 ("4","102","2017-12-21","AL","21"),
 ("5","102","2017-12-21","SL","4"),
 ("6","102","2017-12-21","ML","5"),
 ("7","103","2017-12-21","AL","21"),
 ("8","103","2017-12-21","SL","3"),
 ("9","103","2017-12-21","ML","6"),
 ("10","104","2018-01-01","AL","0"),
 ("11","104","2018-01-01","SL","0"),
 ("12","104","2018-01-01","ML","0"),
 ("13","105","2018-01-01","AL","0"),
 ("14","105","2018-01-01","SL","0"),
 ("15","105","2018-01-01","ML","0"),
 ("16","106","2018-01-03","AL","21"),
 ("17","106","2018-01-03","SL","5"),
 ("18","106","2018-01-03","ML","5"),
 ("19","107","2018-01-03","AL","21"),
 ("20","107","2018-01-03","SL","5"),
 ("21","107","2018-01-03","ML","5"),
 ("22","108","2018-01-04","AL","0"),
 ("23","108","2018-01-04","SL","0"),
 ("24","108","2018-01-04","ML","0"),
 ("25","109","2018-05-08","AL","0"),
 ("26","109","2018-05-08","SL","0"),
 ("27","109","2018-05-08","ML","0"),
 ("28","110","2018-09-06","AL","21"),
 ("29","110","2018-09-06","SL","4"),
 ("30","110","2018-09-06","ML","5"),
 ("31","111","2018-10-30","AL","30"),
 ("32","111","2018-10-30","SL","0"),
 ("33","111","2018-10-30","ML","0"),
 ("34","112","2018-12-24","AL","0"),
 ("35","112","2018-12-24","SL","0"),
 ("36","112","2018-12-24","ML","0"),
 ("37","113","2018-12-24","AL","0"),
 ("38","113","2018-12-24","SL","0"),
 ("39","113","2018-12-24","ML","0"),
 ("40","114","2018-12-24","AL","0"),
 ("41","114","2018-12-24","SL","0"),
 ("42","114","2018-12-24","ML","0"),
 ("43","115","2019-02-23","AL","30"),
 ("44","115","2019-02-23","SL","0"),
 ("45","115","2019-02-23","ML","0"),
 ("46","117","2019-02-23","AL","30"),
 ("47","117","2019-02-23","SL","0"),
 ("48","117","2019-02-23","ML","0"),
 ("49","118","2019-02-23","AL","30"),
 ("50","118","2019-02-23","SL","0"),
 ("51","118","2019-02-23","ML","0"),
 ("52","119","2019-02-23","AL","30"),
 ("53","119","2019-02-23","SL","0"),
 ("54","119","2019-02-23","ML","0"),
 ("55","120","2019-03-21","AL","30"),
 ("56","120","2019-03-21","SL","12"),
 ("57","120","2019-03-21","ML","40"),
 ("58","123","2019-03-23","AL","45"),
 ("59","123","2019-03-23","SL","0"),
 ("60","123","2019-03-23","ML","0"),
 ("61","127","2019-03-28","AL","30"),
 ("62","127","2019-03-28","SL","12"),
 ("63","127","2019-03-28","ML","20");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_leave_encashment` ### 

 DROP TABLE IF EXISTS 0_kv_empl_leave_encashment;

CREATE TABLE `0_kv_empl_leave_encashment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `department` int(11) NOT NULL,
  `year` int(5) NOT NULL,
  `month` int(5) NOT NULL,
  `date` date NOT NULL,
  `allowances` text COLLATE utf8_unicode_ci NOT NULL,
  `payable_days` double NOT NULL,
  `amount` double NOT NULL,
  `carry_forward` tinyint(1) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_leave_travel` ### 

 DROP TABLE IF EXISTS 0_kv_empl_leave_travel;

CREATE TABLE `0_kv_empl_leave_travel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nationality` int(5) NOT NULL,
  `amount` double NOT NULL,
  `eligibility` varchar(30) NOT NULL,
  `self` tinyint(4) NOT NULL,
  `family` int(3) NOT NULL,
  `family_amt` double NOT NULL,
  `ticket_per_yr` int(3) NOT NULL,
  `month` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_leave_travel VALUES("1","0","200","0","1","3","50","350","29.17"),
 ("2","3","280","0","1","0","0","1","23.333"),
 ("3","5","110","0","1","0","0","1","9.167"),
 ("5","0","100","0","1","0","0","1","8.33"),
 ("6","0","100","0","1","2","0","3","25");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_leave_types` ### 

 DROP TABLE IF EXISTS 0_kv_empl_leave_types;

CREATE TABLE `0_kv_empl_leave_types` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `char_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `frequency` int(5) NOT NULL,
  `deletable` tinyint(1) NOT NULL DEFAULT '1',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_leave_types VALUES("1","Annual Leave","al","12","0","0"),
 ("2","Sick Leave","sl","12","0","0"),
 ("3","Casual/General Leave","cl","12","0","0"),
 ("5","Hajj Leave","hl","60","1","1"),
 ("6","Death of Close relatives(Famil","dc","-2","1","1"),
 ("7","Maternity Leaves","ml","0","1","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_license` ### 

 DROP TABLE IF EXISTS 0_kv_empl_license;

CREATE TABLE `0_kv_empl_license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(11) NOT NULL,
  `license_category` int(4) NOT NULL,
  `license_type` int(4) NOT NULL,
  `license_number` varchar(40) NOT NULL,
  `issuing_country` int(4) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `filename` varchar(250) NOT NULL DEFAULT '',
  `unique_name` varchar(60) NOT NULL DEFAULT '',
  `filetype` varchar(50) DEFAULT '',
  `filesize` bigint(30) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_license VALUES("1","101","1","1"," 4t234t243t23 24","62","2019-04-05","2025-04-05","5c42f076c54d1.pdf","5ca78cb04919a","application/pdf","6647"),
 ("2","103","1","1","343","3","2019-05-01","2019-05-01","","","","0"),
 ("3","117","2","2","432178","3","2019-05-20","2020-05-20","","","","0"),
 ("4","123","1","1","6646464564566456564","3","2019-07-04","2020-07-04","ee5076a34ef1c1d722433a087fb1cb8c.jpg","5d1dbe9a4f9aa","image/jpeg","76489"),
 ("5","107","1","1","cvbxvbxc","3","2019-07-04","2019-07-04","cart-right.png","5d1dbfa50c7e9","image/png","24797"),
 ("7","111","2","2","1234","104","2019-07-04","2023-07-01","emailpress.png","5d1dc4bf7e09f","image/png","12346"),
 ("14","107","14","18","HFGH5454","104","2019-07-12","2022-07-12","","","","0"),
 ("15","129","14","18","1223656","104","2018-07-12","2022-07-12","WhatsApp Image 2017-09-02 at 9.42.48 PM (1).jpeg","5d283b9485610","image/jpeg","40945");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_license_category` ### 

 DROP TABLE IF EXISTS 0_kv_empl_license_category;

CREATE TABLE `0_kv_empl_license_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(250) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_license_category VALUES("1","Driving ","0"),
 ("2","TEST1","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_license_type` ### 

 DROP TABLE IF EXISTS 0_kv_empl_license_type;

CREATE TABLE `0_kv_empl_license_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(250) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_license_type VALUES("1","Driving","0"),
 ("2","GSGS3","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_lmra_fees` ### 

 DROP TABLE IF EXISTS 0_kv_empl_lmra_fees;

CREATE TABLE `0_kv_empl_lmra_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nationality` int(5) NOT NULL,
  `amount` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_lmra_fees VALUES("1","0","5"),
 ("2","1","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_loan` ### 

 DROP TABLE IF EXISTS 0_kv_empl_loan;

CREATE TABLE `0_kv_empl_loan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `loan_date` date NOT NULL,
  `start_date` date NOT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `rate` double NOT NULL,
  `loan_type_id` int(5) NOT NULL,
  `periods` int(5) NOT NULL,
  `monthly_pay` decimal(15,2) NOT NULL,
  `periods_paid` int(5) NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_loan VALUES("2","107","2019-04-17","2019-04-24","2019-04-10","30000.00","USD","1","1","10","3115.73","0","Active");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_loan_types` ### 

 DROP TABLE IF EXISTS 0_kv_empl_loan_types;

CREATE TABLE `0_kv_empl_loan_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_name` varchar(200) NOT NULL,
  `interest_rate` double NOT NULL,
  `allowance_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_loan_types VALUES("1","First Loan","8.33","3"),
 ("2","SECONDLOAN1","13","3");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_medical_premium` ### 

 DROP TABLE IF EXISTS 0_kv_empl_medical_premium;

CREATE TABLE `0_kv_empl_medical_premium` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(250) NOT NULL,
  `yrly_premium` double NOT NULL,
  `self` tinyint(1) NOT NULL,
  `family` int(3) NOT NULL,
  `family_amt` double NOT NULL,
  `total` double NOT NULL,
  `month` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_medical_premium VALUES("1","Category A","72","1","3","50","222","18.5"),
 ("2","Category B","150","1","2","25","200","16.67"),
 ("3","Category C","260","1","1","0","520","43.333333333333"),
 ("4","Category D","300","1","0","0","300","25"),
 ("5","Niormal","0","1","0","0","0","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_memo` ### 

 DROP TABLE IF EXISTS 0_kv_empl_memo;

CREATE TABLE `0_kv_empl_memo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_id` int(11) NOT NULL,
  `emplr_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_nationalities` ### 

 DROP TABLE IF EXISTS 0_kv_empl_nationalities;

CREATE TABLE `0_kv_empl_nationalities` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_nationalities VALUES("1","Bahraini","0"),
 ("2","Saudi","0"),
 ("3","Nepal","0"),
 ("4","European","0"),
 ("5","Qatar","0"),
 ("6","Asian","0"),
 ("7","Indian","0"),
 ("8","Bangladeshi","0"),
 ("9","Egyptian","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_option` ### 

 DROP TABLE IF EXISTS 0_kv_empl_option;

CREATE TABLE `0_kv_empl_option` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(150) NOT NULL,
  `option_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_option VALUES("1","weekly_off","YToxOntpOjA7czozOiJGcmkiO30="),
 ("2","empl_ref_type","1"),
 ("3","next_empl_id","131"),
 ("4","ot_factor","1.25"),
 ("5","special_ot_factor","1.50"),
 ("6","tax_used",""),
 ("7","enable_employee_access","1"),
 ("8","master_role","2"),
 ("9","home_country","2"),
 ("12","car_rate","6"),
 ("13","bike_rate","5"),
 ("14","salary_account",""),
 ("15","paid_from_account",""),
 ("16","travel_debit",""),
 ("17","travel_credit",""),
 ("18","petrol_debit",""),
 ("19","petrol_credit",""),
 ("20","debit_encashment",""),
 ("21","credit_encashment",""),
 ("22","max_leave_forward","10"),
 ("23","days_round_to_one_month","22"),
 ("24","esb_salary","0"),
 ("25","esb_country","1"),
 ("26","expd_percentage_amt","40"),
 ("27","zk_ip",""),
 ("28","BeginTime","08:00:00"),
 ("29","EndTime","18:00:00"),
 ("30","enable_esic",""),
 ("31","enable_pf",""),
 ("41","monthly_choice","1"),
 ("42","BeginDay","1"),
 ("43","EndDay","31"),
 ("44","license_mgr","1"),
 ("45","OT_BeginTime","7200"),
 ("46","OT_EndTime","28800"),
 ("47","n_period","2"),
 ("48","p_period","4"),
 ("49","c_period","2"),
 ("50","nationality",""),
 ("51","cm_period","2"),
 ("52","cy_period","1"),
 ("53","total_working_days","1");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_pick_type` ### 

 DROP TABLE IF EXISTS 0_kv_empl_pick_type;

CREATE TABLE `0_kv_empl_pick_type` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_pick_type VALUES("1","Languages","0"),
 ("2","Proficiency","0"),
 ("3","long1","0"),
 ("4","contract","0"),
 ("5","License Type","0"),
 ("6","License Category","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_picklist` ### 

 DROP TABLE IF EXISTS 0_kv_empl_picklist;

CREATE TABLE `0_kv_empl_picklist` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(3) NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_picklist VALUES("1","1","Tamil","0"),
 ("2","1","English","0"),
 ("3","2","Read","0"),
 ("4","2","Write","0"),
 ("5","2","Read &amp; Write","0"),
 ("6","1","Arabic","0"),
 ("10","4","Permanent","0"),
 ("11","4","Bahraini Contract","0"),
 ("12","4","Bahraini Permanent","0"),
 ("13","4","Open Contract","0"),
 ("14","6","Driving license","0"),
 ("16","1","Malayalam","0"),
 ("17","5","Permanent","0"),
 ("18","5","Renewable","0"),
 ("20","6","test license","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_salary` ### 

 DROP TABLE IF EXISTS 0_kv_empl_salary;

CREATE TABLE `0_kv_empl_salary` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `month` int(2) NOT NULL,
  `from_date` date NOT NULL DEFAULT '0000-00-00',
  `to_date` date NOT NULL DEFAULT '0000-00-00',
  `year` int(2) NOT NULL,
  `GL` tinyint(1) NOT NULL,
  `date` date NOT NULL,
  `currency` varchar(5) NOT NULL DEFAULT '',
  `rate` double NOT NULL DEFAULT '1',
  `al` double NOT NULL DEFAULT '0',
  `sl` double NOT NULL,
  `slh` double NOT NULL,
  `hl` double NOT NULL DEFAULT '0',
  `ml` double NOT NULL DEFAULT '0',
  `gross` double NOT NULL DEFAULT '0',
  `ctc` double NOT NULL DEFAULT '0',
  `lop_amount` double NOT NULL DEFAULT '0',
  `loans` text NOT NULL,
  `dimension` int(5) NOT NULL DEFAULT '0',
  `dimension2` int(5) NOT NULL DEFAULT '0',
  `adv_sal` double NOT NULL DEFAULT '0',
  `net_pay` double NOT NULL DEFAULT '0',
  `misc` double NOT NULL DEFAULT '0',
  `ot_other_allowance` double NOT NULL DEFAULT '0',
  `ot_earnings` double NOT NULL DEFAULT '0',
  `3` double NOT NULL DEFAULT '0',
  `2` double NOT NULL DEFAULT '0',
  `4` double NOT NULL DEFAULT '0',
  `5` double NOT NULL DEFAULT '0',
  `6` double NOT NULL DEFAULT '0',
  `7` double NOT NULL DEFAULT '0',
  `8` double NOT NULL DEFAULT '0',
  `9` double NOT NULL DEFAULT '0',
  `10` double NOT NULL DEFAULT '0',
  `11` double NOT NULL DEFAULT '0',
  `12` double NOT NULL DEFAULT '0',
  `13` double NOT NULL DEFAULT '0',
  `14` double NOT NULL DEFAULT '0',
  `15` double NOT NULL DEFAULT '0',
  `16` double NOT NULL DEFAULT '0',
  `17` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_salary VALUES("1","107","12","0000-00-00","0000-00-00","1","0","2019-07-12","USD","1","4","0","0","0","0","6700","7766.535479452","223.33","YTowOnt9","0","0","0","6412.17","0","0","0","0","5300","600","250","300","5","18.5","29.33","25","64.5","193.5","265.06849315068","530.13698630137","0","250","0"),
 ("2","129","1","0000-00-00","0000-00-00","2","0","2019-07-12","USD","1","0","0","0","0","0","10000","10000","0","YTowOnt9","0","0","0","10000","0","0","0","0","7000","0","0","1000","0","0","0","0","0","0","0","0","0","2000","0"),
 ("3","129","2","0000-00-00","0000-00-00","2","0","2019-07-12","USD","1","2","0","0","0","0","10000","10000","1333.33","YTowOnt9","0","0","0","8666.67","0","0","0","0","7000","0","0","1000","0","0","0","0","0","0","0","0","0","2000","0"),
 ("4","129","3","0000-00-00","0000-00-00","2","0","2019-07-12","USD","1","8","0","0","0","0","10000","10000","2000","YTowOnt9","0","0","0","8000","0","0","0","0","7000","0","0","1000","0","0","0","0","0","0","0","0","0","2000","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_salary_advance` ### 

 DROP TABLE IF EXISTS 0_kv_empl_salary_advance;

CREATE TABLE `0_kv_empl_salary_advance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` int(10) NOT NULL,
  `date` date NOT NULL,
  `month` int(10) NOT NULL,
  `year` int(10) NOT NULL,
  `amount` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_shifts` ### 

 DROP TABLE IF EXISTS 0_kv_empl_shifts;

CREATE TABLE `0_kv_empl_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `BeginTime` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `EndTime` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `dimension` int(11) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_shifts VALUES("1","First Shift","06:00:00","14:00:00","0","0"),
 ("2","Second","14:00:00","22:00:00","0","0"),
 ("3","Night Shift","22:00:00","06:00:00","0","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_skills` ### 

 DROP TABLE IF EXISTS 0_kv_empl_skills;

CREATE TABLE `0_kv_empl_skills` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `language` int(5) NOT NULL DEFAULT '0',
  `proficiency` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_skills VALUES("1","101","6","5"),
 ("2","101","1","5"),
 ("3","103","6","5"),
 ("4","107","6","3"),
 ("5","129","2","5");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_status_types` ### 

 DROP TABLE IF EXISTS 0_kv_empl_status_types;

CREATE TABLE `0_kv_empl_status_types` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_status_types VALUES("1","Active","0"),
 ("2","Inactive","0"),
 ("3","Resigned","0"),
 ("4","Decesed","0"),
 ("5","Terminated","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_tax_types` ### 

 DROP TABLE IF EXISTS 0_kv_empl_tax_types;

CREATE TABLE `0_kv_empl_tax_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `min_sal` int(10) NOT NULL,
  `max_sal` int(10) NOT NULL,
  `percentage` int(10) NOT NULL,
  `offset` int(10) NOT NULL,
  `year` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_tax_types VALUES("1","","15000","45000","12","423","0"),
 ("2","Single","15000","35000","12","423","0"),
 ("3","Simple","5000","15000","12","423","2"),
 ("4","Professional Tax ","500","1500","4","0","2");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_taxes` ### 

 DROP TABLE IF EXISTS 0_kv_empl_taxes;

CREATE TABLE `0_kv_empl_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `min_sal` int(10) NOT NULL,
  `max_sal` int(10) NOT NULL,
  `taxable_salary` double NOT NULL,
  `percentage` int(10) NOT NULL,
  `frequency` int(3) NOT NULL,
  `offset` int(10) NOT NULL,
  `year` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_training` ### 

 DROP TABLE IF EXISTS 0_kv_empl_training;

CREATE TABLE `0_kv_empl_training` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `training_desc` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `course` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cost` int(8) NOT NULL,
  `institute` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `s_date` date NOT NULL,
  `e_date` date NOT NULL,
  `notes` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_training VALUES("1","129","developing programming skill","php development","10000","infosys","2019-01-01","2019-07-31","please participate");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_visa_exp` ### 

 DROP TABLE IF EXISTS 0_kv_empl_visa_exp;

CREATE TABLE `0_kv_empl_visa_exp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nationality` int(5) NOT NULL,
  `self` tinyint(1) NOT NULL,
  `self_amt` double NOT NULL,
  `family` int(3) NOT NULL,
  `family_amt` double NOT NULL,
  `total` double NOT NULL,
  `month` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_visa_exp VALUES("1","1","1","0","0","0","0","0"),
 ("2","0","1","172","0","0","172","14.33"),
 ("3","0","1","172","1","0","172","14.33"),
 ("4","0","1","172","2","90","352","29.33");

 # -------------------------------------------------------------------------------------

