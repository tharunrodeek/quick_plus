# MySQL dump of database 'ALHAMER' on host 'localhost'
# Backup Date and Time: 2019-06-24 07:46 am
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

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
 ("17","17","1","","Payroll Input","0");

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
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_attendance VALUES("1","101","P","0","2019-04-07","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("2","101","P","0","2019-04-08","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("3","101","P","0","2019-04-09","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("4","101","P","0","2019-04-10","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("5","101","P","0","2019-04-11","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("6","101","P","0","2019-04-02","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("7","101","P","0","2019-04-03","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("8","101","P","0","2019-04-04","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("9","101","P","0","2019-04-21","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("10","101","P","0","2019-04-22","08:00:00","20:00:00","0","0","36000","0","0","0"),
 ("11","101","HD","0","2019-04-23","09:10:00","13:10:00","0","0","14400","0","0","0"),
 ("13","101","HD","1","2019-04-25","08:00:00","18:00:00","0","0","21600","7200","7200","0"),
 ("23","101","HD","1","2019-04-28","09:00:00","17:00:00","0","0","18000","7200","3600","0"),
 ("25","101","HD","1","2019-04-29","08:00:00","17:00:00","0","0","21600","7200","3600","0"),
 ("26","101","HD","1","2019-04-30","08:00:00","18:00:00","0","0","21600","7200","7200","0"),
 ("28","101","P","0","2019-04-01","08:00:00","20:00:00","0","0","36000","0","0","0"),
 ("29","101","P","0","2019-03-03","08:00:00","19:00:00","0","0","36000","3600","0","0"),
 ("30","101","P","0","2019-03-04","08:00:00","20:00:00","0","0","36000","7200","0","0"),
 ("31","101","P","0","2019-03-05","08:00:00","22:00:00","0","0","36000","7200","7200","0"),
 ("32","101","P","0","2019-03-06","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("33","101","P","0","2019-03-07","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("37","101","A","1","2019-04-24","10:00:00","12:00:00","0","0","0","0","0","0"),
 ("38","101","P","0","2019-03-10","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("39","101","P","0","2019-03-11","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("40","107","ML","2","2019-03-03","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("41","107","A","2","2019-03-04","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("42","107","CL","2","2019-03-05","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("43","107","P","2","2019-03-06","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("44","107","P","2","2019-03-07","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("45","107","P","2","2019-03-10","14:00:00","23:00:00","0","0","28800","3600","0","0"),
 ("46","107","P","2","2019-03-11","14:00:00","22:20:00","0","0","28800","1200","0","0"),
 ("47","107","P","2","2019-03-12","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("48","107","P","2","2019-03-13","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("49","107","P","2","2019-03-14","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("56","103","ML","0","2019-05-12","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("57","103","ML","0","2019-05-13","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("58","103","ML","0","2019-05-14","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("66","107","P","0","2019-06-02","14:16:08","14:20:20","0","0","252","0","0","0"),
 ("67","118","ML","0","2019-06-11","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("68","118","CL","0","2019-06-12","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("69","118","A","0","2019-06-13","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("70","118","CL","0","2019-06-16","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("71","118","AL","0","2019-06-17","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("72","104","P","0","2019-05-08","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("73","104","P","0","2019-05-09","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("74","104","P","0","2019-05-11","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("75","104","P","0","2019-05-12","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("76","104","P","0","2019-05-13","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("77","104","P","0","2019-05-14","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("78","104","P","0","2019-05-15","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("79","104","P","0","2019-05-16","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("80","104","P","0","2019-05-18","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("81","104","P","0","2019-05-19","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("82","104","P","0","2019-05-20","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("83","104","P","0","2019-05-21","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("84","104","P","0","2019-05-22","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("85","104","P","0","2019-05-23","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("86","104","P","0","2019-05-25","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("87","104","P","0","2019-05-26","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("88","104","A","0","2019-05-27","00:00:00","18:00:00","0","0","64800","0","0","0"),
 ("89","104","P","0","2019-05-28","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("90","104","P","0","2019-05-29","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("91","104","P","0","2019-05-30","08:00:00","18:00:00","0","0","36000","0","0","0"),
 ("92","107","P","2","2019-04-16","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("93","107","P","2","2019-04-17","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("94","107","P","2","2019-04-18","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("95","107","P","2","2019-04-20","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("96","107","P","2","2019-04-21","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("97","107","P","2","2019-04-22","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("98","107","P","2","2019-04-23","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("99","107","P","2","2019-04-24","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("100","107","P","2","2019-04-25","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("101","107","P","2","2019-04-27","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("102","107","P","2","2019-04-28","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("103","107","P","2","2019-04-29","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("104","107","P","2","2019-04-30","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("105","107","P","2","2019-05-01","14:00:00","23:00:00","0","0","28800","3600","0","0"),
 ("106","107","P","2","2019-05-02","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("107","107","P","2","2019-05-04","14:00:00","22:50:00","0","0","28800","3000","0","0"),
 ("108","107","P","2","2019-05-05","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("109","107","P","2","2019-05-06","14:00:00","21:50:00","0","0","28200","0","0","0"),
 ("110","107","P","2","2019-05-07","14:00:00","22:20:00","0","0","28800","1200","0","0"),
 ("111","107","P","2","2019-05-08","14:00:00","22:30:00","0","0","28800","1800","0","0"),
 ("112","107","P","2","2019-05-09","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("113","107","P","2","2019-05-11","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("114","107","P","2","2019-05-12","14:00:00","23:00:00","0","0","28800","3600","0","0"),
 ("115","107","P","2","2019-05-13","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("116","107","P","2","2019-05-14","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("117","107","P","2","2019-05-15","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("118","107","P","2","2019-05-16","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("119","107","P","2","2019-05-18","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("120","107","P","2","2019-05-19","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("122","107","P","2","2019-05-25","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("123","107","P","2","2019-05-28","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("124","107","P","2","2019-05-29","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("125","107","P","2","2019-05-30","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("126","107","P","2","2019-06-20","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("127","107","P","2","2019-06-22","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("129","107","P","2","2019-06-24","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("130","107","ML","2","2019-06-25","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("131","107","P","2","2019-06-26","14:00:00","22:00:00","0","0","28800","0","0","0"); 
INSERT INTO 0_kv_empl_attendance VALUES("132","107","P","2","2019-06-27","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("133","107","A","2","2019-06-23","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("134","107","A","2","2019-05-20","00:00:00","22:00:00","0","0","79200","0","0","0"),
 ("135","107","P","2","2019-05-21","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("136","107","P","2","2019-05-22","14:00:00","23:00:00","0","0","28800","3600","0","0"),
 ("137","107","P","2","2019-05-23","14:00:00","22:00:00","0","0","28800","0","0","0"),
 ("138","107","P","2","2019-05-26","14:00:00","22:10:00","0","0","28800","0","0","0");

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_cv VALUES("1","103","jhgjg","1","2019-05-02","2019-03-18","1","0","33.png","0","0","5cc96c132526e"),
 ("2","101","Test Documente","1","2019-07-17","2019-06-02","1","0","euro-bank.png","0","","5ccbe0c36f012"),
 ("3","","CPR","2","2019-07-24","2019-07-04","0","0","","0","",""),
 ("4","","","2","2019-07-24","2019-07-04","0","0","","0","","");

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_degree VALUES("1","101","test","test","test","A+","2015-04-05"),
 ("2","103","mca","342","2342","23","2019-05-01");

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_experience VALUES("1","101","Test","test","test","test","2019-04-05","2025-04-05"),
 ("2","103","fdgd","fgd","fdd","dfd","2019-05-01","2019-05-01"),
 ("3","104","ALHAMER","Defauly","CCK2","Senior","0000-00-00","2019-05-27"),
 ("4","104","ALHAMER","Defauly","CCK2","SUB JUNIOR1","2019-05-28","2019-05-27");

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_family VALUES("1","103","rette","1","Yznl20b.jpg","0","","5cc96cba155f1"),
 ("2","101","latha","1","drlogo.png","0","","5ccbdee119179"),
 ("3","107","Test wife","1","0aaac700e0d85e756b178cec21d6f73f.jpg","0","","5ce398ccbfa09"),
 ("4","113","ravi","2","","0","",""),
 ("5","104","wewqr","1","","0","","");

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
  `cl` double NOT NULL,
  `al` double NOT NULL,
  `sl` double NOT NULL,
  `ml` double NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_grade VALUES("1","Manager","2000","20000","18","18","0","30","0"),
 ("2","Junior","10000","500000","12","25","0","15","0"),
 ("3","Accountant","12000","30000","15","12","0","16","0");

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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
 ("48","104","nominee_email","kvvaradha@gmail.com","2019-06-03");

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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_info VALUES("6","101","12","1","test","Vj","52 devizes road, test","","Test Address","swindon","UK","95","1","1999-04-05","1","+919626960016","+919626960016","nadhanshankar@gmail.com","1","2019-05-03","test","0","103","","test","87891797","0000-00-00","0000-00-00","0000-00-00","hgfhgfhffjfh\n\n\nfdhdfh\n\n\nhdfhdhfd\n\nfhdhfdh"),
 ("9","102","0","1","jo","V","","","Thoppampatti, Athipalayam Post","karur","Tamil Nadu","104","1","1999-05-01","1","+919626960016","7575757575","kvvarad0ha@gmail.com","1","0000-00-00","","0","0","","Varadharajjo","6786868671","0000-00-00","0000-00-00","0000-00-00",""),
 ("10","103","1","1","ronith1","V","","","Thoppampatti, Athipalayam Post232","karur3","Tamil Nadu3","104","1","1999-05-01","1","+919626960016","+919626960016","kvvaradh34a@gmail.com","1","2019-05-01","hgjg","103.png","105","","Varadharaj Vdssd","+9196269600162","0000-00-00","0000-00-00","0000-00-00","The tax information return most familiar to the greatest number of people is the Form W-2, which reports wages and other forms of compensation paid to employees. There are also many forms used to report non-wage income, and to report transactions that may entitle a taxpayer to take a credit on an individual tax return."),
 ("13","104","5","1","Deepak","raj","test","karur","","","","2","1","1999-05-04","1","","23424323","","1","0000-00-00","","0","","","hg","4464646","0000-00-00","0000-00-00","0000-00-00","test notes1"),
 ("14","105","6","1","jothi","Varadha","5/124","north street","5/124,\nnorth street,\nkarur.","karur","tamil nadu","104","2","1991-10-16","2","04324292588","9089099887","info@jo.com","1","0000-00-00","","0","0","","varadharaj","8987898789","0000-00-00","0000-00-00","0000-00-00",""),
 ("17","106","10","1","Russel","Rahim","14 Argyll Mansions, Hammersmith Road,","test address","Test aderess\ntest street\ntest city\nkarur","London","styate","76","2","1996-04-09","2","7590072254","234234234234234","test@kvcodes.com","1","0000-00-00","","106.png","103","","teest ice","123123131231","0000-00-00","0000-00-00","0000-00-00",""),
 ("18","107","3","1","Mizanur bhai","Rahman","52 devizes road,","","52 devizes road,","swindon","UK","76","1","1999-05-11","1","9626960016","9626960016","jothics8@gmail.com","1","2019-05-29","","107.png","105","","Mizanur Rahman","23412341234123","2020-05-30","0000-00-00","0000-00-00","Test notes added to it &lt;div&gt;\n\n\n&lt;style type=\"text/css\"&gt;\ntt {\n	font-family: courier;\n}\ntd {\n	font-family: helvetica, sans-serif;\n}\ncaption {\n	font-family: helvetica, sans-serif;\n	font-size: 14pt;\n	text-align: left;\n}\n&lt;/style&gt;\n\n\n&lt;p&gt;&lt;/p&gt;&lt;table cellspacing=\"0\" cellpadding=\"3\"&gt;\n&lt;tbody&gt;&lt;tr&gt;\n&lt;td bgcolor=\"#B8E1E4\" valign=\"top\" align=\"center\" style=\"background: rgb(184, 225, 228); font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;b&gt; &lt;/b&gt;&lt;/td&gt;\n&lt;td bgcolor=\"#B8E1E4\" valign=\"top\" align=\"left\" style=\"background: rgb(184, 225, 228); font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;b&gt;Employee Expenses, Advances and Loans&lt;/b&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;#REF!&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Can the system integrate with human resources systems to retrieve employees payments information &lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Mandatory&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;No&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;#REF!&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Can the system provide an automatic offset in Payroll against outstanding advances when employee expenses are processed&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Mandatory&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Yes&lt;/td&gt;\n&lt;td colspan=\"6\" valign=\"bottom\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;if it&#039;s loan or advance salary can be detected against payroll&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;#REF!&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Can the system allow employee expenses to be entered on-line through the internet by the employee (thru Employee Self Service Portal)&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Desirable&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;no&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td bgcolor=\"#B8E1E4\" style=\"background: rgb(184, 225, 228); border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;b&gt;&lt;/b&gt;&lt;/td&gt;\n&lt;td bgcolor=\"#B8E1E4\" valign=\"top\" align=\"left\" style=\"background: rgb(184, 225, 228); font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;b&gt;Time and Attendance Management &lt;/b&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;132&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Ability to access Employee Self-Service and Manager Self Service &lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Desirable&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;No&lt;/td&gt;\n&lt;td colspan=\"2\" valign=\"bottom\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Additional &lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td colspan=\"7\" valign=\"bottom\" align=\"left\" style=\" font-size:10pt;\"&gt;The role spliting is complicated to bring these features here&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;#REF!&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;The proposed solution must have the feature of 24/7 flexible shifts&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Desirable&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;No&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;#REF!&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;The proposed solution must have the facility for the line managers to take actions on their staff attendance exceptions&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Desirable&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Yes&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td style=\"border-width: thin; border-color: rgb(0, 0, 0);\"&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td valign=\"top\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;#REF!&lt;/td&gt;\n&lt;td valign=\"top\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;The proposed solution must have real-time data capture facility&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Desirable&lt;/td&gt;\n&lt;td valign=\"center\" align=\"center\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;No&lt;/td&gt;\n&lt;td colspan=\"2\" valign=\"bottom\" align=\"left\" style=\"font-size: 9pt; border-width: thin; border-color: rgb(0, 0, 0);\"&gt;Additional &lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;td&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;&lt;/table&gt;&lt;/div&gt;"),
 ("19","108","4","1","saai","","","","","","","2","1","1999-05-18","1","","8787878787","jothics8@gmail.com","1","0000-00-00","","108.jpg","","","varadha","45654654654","0000-00-00","0000-00-00","0000-00-00",""),
 ("20","109","5","1","ronith","","","","","","","2","1","1999-05-18","1","","553252525","jothics84sgs@gmail.com","1","0000-00-00","","","","","haris","453535","0000-00-00","0000-00-00","0000-00-00",""),
 ("21","110","6","1","guna","","","","","","","2","1","1999-05-18","1","","4363636","jothicscscsc@gmail.com","1","0000-00-00","","","","","werrw","4535353","2019-05-25","2019-09-26","2020-07-27",""),
 ("22","111","7","1","yasvik","kumar","","","","","","2","1","1999-05-27","1","","6776767676","joth435fdfi@gmail.com","1","0000-00-00","","","","","ram","463643636","0000-00-00","0000-00-00","0000-00-00",""),
 ("23","112","8","1","Varadharaj","V 2","","","Thoppampatti, Athipalayam Post","karur","Tamil Nadu","2","1","1999-05-27","1","(962) 696-0016","(962) 696-0016","kvvaradha@gmail.com","1","0000-00-00","","","","","Varadharaj V 2","(962) 696-0016","0000-00-00","0000-00-00","0000-00-00",""),
 ("24","113","9","1","malar","","","","","","","2","1","1999-05-27","1","","765657567","jothi@kvcodes.com","1","0000-00-00","","","","","banu","56656556","0000-00-00","0000-00-00","0000-00-00",""),
 ("25","114","10","1","hari","","","","","","","2","1","1999-05-27","1","","45645654","ghgfhjothi@kvcodes.com","1","0000-00-00","","","","","rte","6564","0000-00-00","0000-00-00","0000-00-00",""),
 ("26","115","11","1","sekar","","","","","","","2","1","1999-05-27","1","","87687678","jothi@kvcodes.combvn","4","2019-05-30","no progress","","","","bnn","8686876","2019-06-22","2019-05-31","0000-00-00","test note edited"),
 ("27","116","12","1","yahoo","","","","","","","2","1","1999-05-27","1","","546546456","jothi@kvcodes.comfgfdg","1","0000-00-00","","","","","fdgdfg","340446","0000-00-00","0000-00-00","0000-00-00",""),
 ("28","117","13","3","kannan1","fsdfdsfds1","dsfds1","1gfdg","karur tamilnadu indiagf","ewrewrew","tn","2","2","1999-05-27","1","67676767671","45345435","jothi@kvcodes.comjhhj","1","0000-00-00","","","","114","rest","4353535","0000-00-00","0000-00-00","0000-00-00",""),
 ("29","118","14","1","sathish","kumar","","","Thoppampatti, Athipalayam Post","karur","Tamil Nadu","2","1","1999-06-11","1","(962) 696-0016","38725847358","kvvaradha@gmail.com1","1","0000-00-00","","","","","haris","92328742","0000-00-00","0000-00-00","0000-00-00","");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_job` ### 

 DROP TABLE IF EXISTS 0_kv_empl_job;

CREATE TABLE `0_kv_empl_job` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` int(10) NOT NULL,
  `grade` tinyint(2) NOT NULL,
  `al` double NOT NULL,
  `cl` double NOT NULL,
  `sl` double NOT NULL,
  `ml` double NOT NULL,
  `department` tinyint(2) NOT NULL,
  `nationality` int(11) NOT NULL DEFAULT '0',
  `medi_category` int(11) NOT NULL DEFAULT '0',
  `family` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_job VALUES("1","101","1","18","18","0","30","2","0","0","0","1","3","2","GBP","2019-02-01","0000-00-00","","0000-00-00","3","0","10","1","2","test","34232","test","234f3f","2342342","Kcodes","","3","123445672345","Varadharaj V","+919626960016","nadhanshankar@gmail.com","52 devizes road,","144000","12000","4800","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("2","102","1","18","18","0","30","2","0","0","0","0","2","2","USD","2019-05-01","0000-00-00","","0000-00-00","1","0","0","1","1","","","","","","Mr.","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("3","103","1","18","18","0","30","2","0","0","0","0","2","2","USD","2019-05-01","0000-00-00","","0000-00-00","1","0","0","1","1","","","","","","Mr.","","1","","","","","erqre","0","0","10","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("6","104","2","18","18","0","30","3","6","1","0","0","1","3","USD","2019-05-04","0020-00-00","","2019-05-28","3","10","0","1","1","","","","","","","","1","","","","kvvaradha@gmail.com","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("7","105","2","25","12","0","15","1","0","0","0","0","1","1","USD","2019-01-01","0000-00-00","","0000-00-00","3","0","3","1","1","sbi","","","","365d55","icici","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("11","106","1","18","18","0","30","2","0","0","0","1","3","1","USD","2017-05-11","0000-00-00","","0000-00-00","1","0","40","1","2","","","","","2342342","egoVisa","","2","123445672345","Varadharaj V","9626960016","kvvaradha@gmail.com","test address","60000","5000","2000","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("12","107","1","18","18","0","30","3","6","1","2","2","2","2","USD","2017-03-01","0000-00-00","","0000-00-00","1","0","0","1","2","","","","","","Mr.","","3","123445672345","Varadharaj V 2","9626960016","kvvaradha@gmail.com","Test","163554.552","13629.546","5300","0","600","250","300","0","0","0","0","0","0","0","0","0","250","0"),
 ("13","108","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-18","0000-00-00","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("14","109","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-18","0000-00-00","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("15","110","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-18","0000-00-00","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("16","111","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-20","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("17","112","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-20","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("18","0","0","0","0","0","0","0","0","0","0","0","0","","","0000-00-00","0000-00-00","-close.png","0000-00-00","0","0","0","0","0","","","","","","","","0","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("19","113","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-20","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("20","114","0","0","0","0","0","0","0","0","0","0","0","","","0000-00-00","0000-00-00","114-cross.png","0000-00-00","0","0","0","0","0","","","","","","","","0","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("21","114","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-20","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("22","115","0","0","0","0","0","0","0","0","0","0","0","","","0000-00-00","0000-00-00","115-search.png","0000-00-00","0","0","0","0","0","","","","","","","","0","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("23","115","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-20","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("24","116","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-20","116-in10.jpg","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("25","117","3","12","15","0","16","3","6","1","0","0","2","2","USD","2019-05-27","2020-05-31","117-close.png","0000-00-00","1","0","0","2","1","SBI","","","","","","","1","","","","","testc","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("26","118","3","12","12","12","12","3","6","1","0","0","2","2","USD","2019-06-11","0020-00-00","","0000-00-00","1","0","0","2","1","","","","","","","","1","","","","","","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_leave_applied VALUES("1","2","103","ML","tes leavbe","2019-05-11","4","","1"),
 ("2","2","107","GL","tgest 2 leave","2019-05-20","4","","1"),
 ("3","2","107","ML","Medical test leave to add functionality in it.","2019-05-26","2","3.geesys.jpg","1"),
 ("5","2","107","AL","sdfasdfads","2019-05-25","3","5-g8.jpg","0");

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_license VALUES("1","101","1","1"," 4t234t243t23 24","62","2019-04-05","2025-04-05","5c42f076c54d1.pdf","5ca78cb04919a","application/pdf","6647"),
 ("2","103","1","1","343","3","2019-05-01","2019-05-01","","","","0"),
 ("3","117","2","2","432178","3","2019-05-20","2020-05-20","","","","0");

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

INSERT INTO 0_kv_empl_loan VALUES("1","101","2019-05-03","2019-05-03","0000-00-00","30000.00","USD","1","1","2","15156.37","0","Active"),
 ("2","107","2019-04-17","2019-04-24","2019-04-10","30000.00","USD","1","1","10","3115.73","0","Active");

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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_option VALUES("1","weekly_off","YToxOntpOjA7czozOiJGcmkiO30="),
 ("2","empl_ref_type","1"),
 ("3","next_empl_id","119"),
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
 ("22","max_leave_forward","6"),
 ("23","days_round_to_one_month","22"),
 ("24","esb_salary","0"),
 ("25","esb_country","0"),
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
 ("52","cy_period","1");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_pick_type` ### 

 DROP TABLE IF EXISTS 0_kv_empl_pick_type;

CREATE TABLE `0_kv_empl_pick_type` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_pick_type VALUES("1","Languages","0"),
 ("2","Proficiency","0"),
 ("3","long1","0"),
 ("4","contract","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_picklist` ### 

 DROP TABLE IF EXISTS 0_kv_empl_picklist;

CREATE TABLE `0_kv_empl_picklist` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(3) NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_picklist VALUES("1","1","Tamil","0"),
 ("2","1","English","0"),
 ("3","2","Read","0"),
 ("4","2","Write","0"),
 ("5","2","Read &amp; Write","0"),
 ("6","1","Arabic","0"),
 ("10","4","Permanent","0"),
 ("11","4","Bahraini Contract","0"),
 ("12","4","Bahraini Permanent","0"),
 ("13","4","Open Contract","0");

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
  `cl` double NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO 0_kv_empl_salary VALUES("1","106","0","0000-00-00","0000-00-00","2","0","2019-05-11","","1","-4","0","-4","-2","0","0","0","","0","0","0","-1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("2","107","0","0000-00-00","0000-00-00","2","0","2019-05-11","","1","-6","0","-8","-4","0","0","0","","0","0","0","-1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"),
 ("3","107","3","0000-00-00","0000-00-00","2","0","2019-05-28","USD","1","1.5","0","0","0","6752.58","7607.0606849315","3828.57","YTowOnt9","0","0","0","2859.51","0","0","52.58","0","5300","600","250","300","5","18.5","29.33","25","64.5","193.5","318.08219178082","265.06849315068","0","250","0"),
 ("4","107","4","0000-00-00","0000-00-00","2","0","2019-06-24","USD","1","1.5","0","0","0","6700","7554.4806849315","3350","YTowOnt9","0","0","0","3235.5","0","0","0","0","5300","600","250","300","5","18.5","29.33","25","64.5","193.5","318.08219178082","265.06849315068","0","250","50"),
 ("5","107","5","0000-00-00","0000-00-00","2","0","2019-06-24","USD","1","1.5","0","0","0","6848.64","7703.1206849315","520.75","YToxOntpOjA7YTozOntpOjA7czoxOiIyIjtpOjE7czo3OiIzMTE1LjczIjtpOjI7czoxOiIxIjt9fQ==","0","0","0","3147.66","0","0","148.64","3115.73","5300","600","250","300","5","18.5","29.33","25","64.5","193.5","318.08219178082","265.06849315068","0","250","0");

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_shifts VALUES("1","First Shift","06:00:00","14:00:00","0","0"),
 ("2","Second","14:00:00","22:00:00","0","0");

 # -------------------------------------------------------------------------------------

### Structure of table `kv_empl_skills` ### 

 DROP TABLE IF EXISTS 0_kv_empl_skills;

CREATE TABLE `0_kv_empl_skills` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `language` int(5) NOT NULL DEFAULT '0',
  `proficiency` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_skills VALUES("1","101","6","5"),
 ("2","101","1","5"),
 ("3","103","6","5");

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 0_kv_empl_training VALUES("1","103","tdsdfsh","fds","566356","cncccccccccn","2019-05-01","2019-05-01","hdddddd"),
 ("2","101","program","mca","10000","bbi","2019-05-06","2019-09-20","training"),
 ("3","113","test","mcs","234","retert","2019-05-30","2019-05-30","vxv");

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

