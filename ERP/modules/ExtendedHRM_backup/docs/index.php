<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");

$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}

include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");


if(isset($_POST['CleanIt'])){
	display_warning(trans("Caution: Once"));

	kv_truncate_Complete_HRM();
	meta_forward($path_to_root.'/modules/ExtendedHRM/docs/index.php?clear_demo=yes&cleared=yes');
} 


if(isset($_GET['tut']) && $_GET['tut'] == 'text'){

	meta_forward("http://docs.kvcodes.com/extended-hrm", "");

	page(trans("Text Documentation"));	?>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Play" rel="stylesheet">
	<style> 
	p, li a {
		font-size:14px;
		font-family: 'Open Sans', sans-serif;
		text-align: justify;
		line-height: 20px;
	}
	h2{ 
		font-size: 18px;
		font-family: 'Play', sans-serif;
	}
	</style>
	<div style="width:80%; margin:0 auto; ">
	<center> <h1> HRM Documentation </h1></center>
		<ul style="list-style:none;">
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#intro';?>"> Introduction</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#req';?>"> Requirements</a> </li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#install';?>">Installation </a> </li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#core_add';?>">Core Config</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#config';?>">Configuration And Setup</a></li>			
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#allowances';?>"> Allowances</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#encashment';?>"> Leave Encashment</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#empl';?>">Employees Management</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#atdnce';?>">Employee Attendance</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#loan';?>">Employee Loan</a></a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#pay';?>"> Payroll</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#atch';?>"> Attachments and CV</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#inq';?>"> Inquires</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#api';?>"> API for attendance</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#general';?>"> General Features</a></li>

		</ul> 

		<div id="intro" > 

			<h2> Introduction </h2>
			<p> The Extended HRM is compact and maintains employee records and perform the Attendance and Payroll operation. With its help, you can manage employee's  informations and you can print their names list with contact details based on department list. </p>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/home.png'; ?>" style="max-width: 85%;"> </center>
			<p> Extended HRM extension can allows you to change the employee details anytime and maintain it in real-time. It Supports Full Attendance and Half day attendance,and few other attendance types to record daily routine attendances. <p>
			<p> Payroll can be processed for each and every employee independently and it will record the details of payslip form components in the database for the payroll history management.</p>
			<p> This is elaborated version of my Simple HRM Extension. You can play around it with facility to configure employee payroll independently, without working with formula. </p>
			<p> Loan feature to keep track employee loan and also maximum allowed loan size also meassured here with this feature. </p>
			<p> You can make PDF reports to save and email the reports to anyone.</p>

			<p> Let's see the functionalities and Requirements in details with help of below elaborated tutorial. </p> 

		</div>

		<div id="req" > 

			<h2> Requirements </h2>
			<p>  </p>
			<p> For the FrontAccounting2.4 RC,There are some installation problem with the core.  But the extension works fine in all aspects within FrontAccounting 2.4 RC1. </p>
			<p> I have some extended readme for the 2.4RC1 users. </p>
			<p> It works inside each company separatelyand hence supports Multi Company. You can enable and activate it for any of the companies under the same FrontAccounting. </p>
			<p> FrontAccounting is more helpfull for the Small and Medium Enterprise(SME). So This is a very good extension to work inside it for the amount of 100- 500 employees. If you have good and speedy RAM and Processor for the System(Server). You can use more than 1000 employees in it. </p>
		</div>

		<div id="install" > 

			<h2> Installation </h2>
			<p> Installing HRM Extension is quite similar to other extension you are installing from FrontAccounting repo. Let me give you the simple steps to follow and install in it.  </p>
			<p> Always remember, before trying with any new extension of plugin, just take a complete copy of database and whole FrontAccounting directory. It's good for backup and keep separately for future restoration.</p> 
			<p> Get the <b>HRMbyKvcodes.zip</b> and extract it on to the working FrontAccounting extension directory.
				`ROOT OF FA/modules/`.</p> 
				<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/zip-extract.png'; ?>" style="max-width: 85%;"> </center>
			<p> Now, Login to your FrontAccounting main company and goto <b><i>`Setup-> Install/Activate Extensions`</i></b>. </p>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/installation.png'; ?>" style="max-width: 85%;"> </center>
			<p> Now, it will show you the <b><i>`Payroll`</i></b> in the extensions list. you can simply install it by clicking the 'Install' Icon. Once it installed, you have to activate it for company.</p>

			<p> So, you have to select the company you want to work HRM module from the top drop down.("Activated For'Your  Company Name' "). </p>

			<p> Here, check the Payroll checkbox to install the tables and activate the extension to your desired company. </p>


		</div>
		<div id="core_add" > 
			<h2> Add Types to Core </h2>
			<p>There are some specific changes needs to do within Core of FA. Here is it Just open your FrontAccounting files folder and followed by </p>
			<code>ROOT_OF_FA/admin/db/transactions_db.inc Here you can find the function below</code>
			
			<pre><span class="x">function get_systype_db_info($type)</span></pre>
			
			<p> Around line no :183 here you have to add one more line within it. </p>
			<pre><span class="x">case    ST_EMPLOYEE   : return array(TB_PREF."kv_empl_salary", null, "id", "empl_id", "date");</span>	
				<span class="x">case     ST_EMPLOYEE_LOAN   : return array(TB_PREF."kv_empl_loan", null, "id", "empl_id", "date");</span>	
				<span class="x">case     ST_EMPLOYEE_ENCASHMENT   : return array(TB_PREF."kv_empl_leave_encashment", null, "id", "empl_id", "date");</span>				
			</pre>
			<p> Add the above line just end of the similar line within the function.</p>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/transactions_db.png'; ?>" style="max-width: 85%;"> </center>
			<code> ROOT_OF_FA/includes/sysnames.inc</code>
			<p>Here within the $systypes array</p>
			
			<pre><span class="x">ST_EMPLOYEE =&gt; trans("Employee Salary"),</span>
			<span class="x">ST_EMPLOYEE_LOAN =&gt; trans("Employee Loan"),</span>
			<span class="x">ST_EMPLOYEE_ENCASHMENT =&gt; trans("Employee Leave Encashment"),</span>
			</pre>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/sysnames.png'; ?>" style="max-width: 85%;"> </center>
			<code> ROOT_OF_FA/includes/types.inc</code>
			<p> Here define our type employee with 99 .</p>
			
			<pre><span class="x">define('ST_EMPLOYEE', 99);</span>
			<span class="x">define('ST_EMPLOYEE_LOAN', 98);</span>
			<span class="x">define('ST_EMPLOYEE_ENCASHMENT', 97);</span>
			</pre>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/types.png'; ?>" style="max-width: 85%;"> </center>
		</div>
		<div id="config" > 

			<h2> Configuration And Setup </h2>
			<p> After Installing HRM module, you need to setup some configurations based on your need and choice.</p>

			<p> Let's begin with one by one.</p>

			<p> Now,you can able to see a new tab or new menu in the FrontAccounting.Which is 'HRM'. But, the links where disabled due to access permissions. we need to setup the access to the right users. </p> 
			<p> Goto `<b><i>Setup -> Access Setup</i></b>`.  and select the desired user role to provide access permission.or System administrator for testing purpose.</p>
				<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/role-setup.png'; ?>" style="max-width: 85%;"> </center>
			<p> The below, components are already got access persmission and you can find our access level's at the bottom of the list. </p> 
			<p> check all the  checkbox to enable the full access to the right user roles. </p>

			<p> Save the role and loggout and login again to get users access on the HRM module. </p>
			<p> After getting access, you are prompted to <b><i>`HRM-> Settings`</i></b> to provide basic settings for the HRM module.</p>  
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/setup-hrm.png'; ?>" style="max-width: 85%;"> </center>
			<p> There after, you are requested to add employees and followed by their profile details completely. </p> 

		</div>
		<div id="allowances"> 
			<h2> Allowances Setup </h2>
			<p> We have different set of employee group, which actually needs to use different allowances for each group, so here we are using Grades as the employee group and set the difference in allowances. </p>
			<p> You can create allowances, which can be inputted from Profile page, Payroll calculation page, and Direct Percentage on either gross or basic salary. </p>
			<p> We can define the Allowance type,either its employee earnings, deductions or CTC( Company or employeer contribution). </p>
			<p> Formula's can be applied to from the allowances setup, which can be used in payroll calculation.</p>
		</div>
		<div id="encashment"> 
			<h2> Leave Encashment </h2>
			<p> This is one a new feature in my HRM. I hope you might need to use this for vocation and leave salary feature. This widely applied with different combinations of allowances. So I show all the allowances of an employee with checkbox, you can include them by tick it.</p>
			<p> The Leave encashment has COA credit and debit code selection settings under the HRM settings. Which helps to manage the company accrual for it.</p>
			<p> You have inquiry and reports for the Leave encashment just like Payslip. </p>
		</div>
		<div id="empl" > 

			<h2> Employee Management </h2>
			<p> Employee Record maintainance will be done through the HRM. Here you have more profile data's you can keep track the employee details and update it when it changed. </p> 
			<p> You can enter the Employee Profile and job details at first tab. There After, when you select the same employee from the list, you can add their education, and work experience with it. </p> 
			<p> Edit, Delete Employee records will be done here. </p> 
			<p> User management will be easy with simple Interface like Customers and suppliers. </p> 
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/employee-profile.png'; ?>" style="max-width: 85%;"> </center>
			
			<p> The Payroll and  Attendance details of a selected employee can be viewed in the following tabs.</p>

		</div>

		<div id="atdnce" > 

			<h2> Attendance </h2>
			<p> Each Employee Attendance will be recorded independently. And also, those attendance records are maintained to calculate Loss of Pay(LOP) Days and amount with it.</p>
			<p> Select the Date of attendance and select the department to enter the details of attendance in it. </p> 
				<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/attendance.png'; ?>" style="max-width: 85%;"> </center>
			<p> Employee  attendance can be editable for the entered attendances. </p>  

		</div>

		<div id="loan" > 

			<h2> Employee Loan  </h2>
			<p>  Employees Loan can be allowed to get multiple loans at a time. Each loan EMI will be detected in the salary. And you can define the employee maximum loan availability from the settings. </p>
			<p> This will show the EMI start date and end date of payment. you can configure the start date, based on the start date, we can able to configure the end date with calculating the periods. </p> 
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/loan.png'; ?>" style="max-width: 85%;"> </center>
			<p> EMI can be added automatically on the employees' Payslip form during the generation of  payslip and Payout. </p> 

			<p> We can edit the loan amount and its periods, before it start collecting installments.</p>
			<p> In the middle of the month also the employee can able to settle the loan balance from the loan inquiry page.</p>

			<p> Employee Loan will calculate the Monthly EMI and maximum allowed EMI for an employee for a month. </p> 		
			

		</div>

		<div id="pay" > 

			<h2> Payroll </h2>
			<p> Every month, we have to prepare Monthly payout as Payslip. You dont need to keep track the absent days and loan EMI as well. It will be calcualted automatically and provide you the details on the deduction side. Monthly Payroll Process can be done easily without keeping fuzzy attendance and loan EMI calculations. </p>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/payroll.png'; ?>" style="max-width: 85%;"> </center>
			<p> Payslips are prepared with your manual entry or Miscellaneous Expense and Professional Tax. </p>
			<p>Also, you can print from any employee Payslip. </p>
			<p> Inorder to create Payslip, you need to select the fiscal year and month of payout and employee name from the list. </p> 

		</div>

		<div id="atch" > 

			<h2> Attachments </h2>
			<p> Employee attachment such as CV, or some informative Dcouments can be added here with this employee attachment. </p>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/attachment.png'; ?>" style="max-width: 85%;"> </center>
			<p> Useful for the purpose of handling employee CV And Details or history of employment. </p> 			

		</div>

		<div id="inq" > 

			<h2> Inquires </h2>
			<p> Generally, the Employee details, loan, attendance, and Payroll Details need to viewed together in one page and which can be allowed to view and moderate some changes in it. </p>
			<center> <img src="<?php echo $path_to_root.'/modules/ExtendedHRM/images/inquires.png'; ?>" style="max-width: 85%;"> </center>
			<p> Inquiry Pages helps you to make the search and findings of specific details and reports. </p>

		</div>
		<div id="api" > 

			<h2> API For attendance </h2>
			<p> This feature allows you to feed employee attendance details from your biometric system. We have an API sample in PHP, but you can do the API with any other supported language of your bio metric system.  </p>
			<p>
			You can send only one employee attendance at a time. Through bio metric, we can allow one user at a time.</p>
			<pre style="background: #ffeb3b73;    padding: 2%;   border: 1px solid #FF9800;">
				$site_url = "http://hrm.kvcodes.com/244";  // Your FA URL
				 //Employee Attendance API to connect and insert into the system
				$post = [
				    'email' => 'hruser',  // FA Admin Login
				    'pwd' => '123456',  // FA Admin Password
				    'company' => '0',   //the company installed,you can get this number from the config_db. Just get the array number
				    'empl_id' => 101,  //Employee ID
				    'data' => array('date' => "2018-02-03", 'in' => '09:05:00', 'out' => '06:35:00')
				    //'data' => array('date' => "2018-02-13", 'in' => '2018-02-13 09:00:00', 'out' => '2018-02-13 06:30:00')
				];


				$url = $site_url.'/modules/ExtendedHRM/api/UpdateSingleEmplAttendance';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
				$response = curl_exec($ch); 

				var_export($response); 

				$res = json_decode($response); 

				print_r($res);  </pre>
				You can download a sample php file for the complete API <a href="../api-sample.php.txt" > Sample HERE</a>
	</div>
	<div id="general">
		<h2> General Features and Functionality </h2>
		<p> I have interduced the multicurreny feature in it. Which actually works for each employees currency. The Payroll settings and his related financial informations are stored in Employee currency. But in GL and company records. it keeps in company currency. The Exchagne rate feature uses from the core FA. So it has be updated periodically to get right exchange rate. </p>
		<p> Daily Attendance inquiry is available to check and get the details of absent employees</p>
		<p> Transalation of the entire HRM will be also programmed.So you can get the translator strings and with help of Poedit you can make this happen. </p>
		<p> Backup and restore the database of HRM </p>
		<p> Gazetted Holiday settings for overall company</p>
		<p> Advance Salary for the temporary payment to employee. Its very short term like within a month advance. other features should goto loan module</p>

	</div>
	<?php 
}elseif(isset($_GET['tut']) && $_GET['tut'] == 'video'){ 
	page(trans("Video Documentation"));  ?>
<div style="width:80%; margin:0 auto; ">
		<ul style="list-style:none;">
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#intro';?>"> Introduction</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#install';?>">Installation </a> </li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#config';?>"><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#req';?>">Configuration And Setup</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#empl';?>">Employees Management</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#atdnce';?>">Employee Attendance</a></li>
			<li> <a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#loan';?>">Employee Loan</a></a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#pay';?>"> Payroll</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#atch';?>"> Attachments and CV</a></li>
			<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text#inq';?>"> Inquires</a></li>
		</ul> 

		<div id="intro" > 

			<h2> Introduction </h2>
			<p> Installing HRM Extension is quite similar to other extension you are installing from FrontAccounting repo. Let me give you the simple steps to follow and install in it.  </p>
			
		</div>

		<div id="install" > 

			<h2> Installation </h2>
			<p> This is a compact extension to maintain employee records and perform the Attendance and Payroll operations, Also you can maintain a records of it, </p>
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>

		</div>

		<div id="config" > 

			<h2> Configuration And Setup </h2>
			<p> After Installing HRM module, you need to setup some configurations based on your need and choice.</p>

			<p> Let's take a look in video.</p>
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>

		<div id="empl" > 

			<h2> Employee Management </h2>
				<p> Employee Record maintainance will be done through the HRM. Here you have more profile data's you can keep track the employee details and update it when it changed. </p> 
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>

		<div id="atdnce" > 

			<h2> Attendance </h2>
			<p> Each Employee Attendance will be recorded independently. And also, those attendance records are maintained to calculate Loss of Pay(LOP) Days and amount with it.</p>
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>

		<div id="loan" > 

			<h2> Employee Loan  </h2>
			<p>  Employees Loan can be allowed to get multiple loans at a time. Each loan EMI will be detected in the salary. And you can define the employee maximum loan availability from the settings. </p>

			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>

		<div id="pay" > 

			<h2> Payroll </h2>
			<p> Every month, we have to prepare Monthly payout as Payslip. You dont need to keep track the absent days and loan EMI as well. It will be calcualted automatically and provide you the details on the deduction side. Monthly Payroll Process can be done easily without keeping fuzzy attendance and loan EMI calculations. </p>
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>

		<div id="atch" > 

			<h2> Attachments </h2>
		<p> Employee attachment such as CV, or some informative Dcouments can be added here with this employee attachment. </p>
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>

		<div id="inq" > 

			<h2> Inquires </h2>
			<p> Generally, the Employee details, loan, attendance, and Payroll Details need to viewed together in one page and which can be allowed to view and moderate some changes in it. </p>
			<iframe src="http://www.youtube.com/embed/W7qWa52k-nE"
	   width="560" height="315" frameborder="0" allowfullscreen></iframe>
		</div>
		
		</div>


<?php 
}elseif(isset($_GET['clear_demo']) && $_GET['clear_demo'] == 'yes'){
		page(trans("Clear Demo"));

		display_warning(trans("Caution: Once you clean the data, you can't get it back !."));
		
		start_form();
		submit_center('CleanIt', trans("Are you Sure to  clean it?"));
		end_form();	


} else{
	page(trans("Demos And Documentation")); ?>

<div style="height: 60%; width:60%; margin:0 auto; "> 
	<ul style="width: 48%;float:left; list-style:none;" > 
	<li> <a href="<?php  echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?clear_demo=yes' ;?>" > Clear Demo Or Existing Data!.</a> </li>	
	<li><a href="<?php echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=text'; ?>">  </a>
	</li>
	<li >
	<!--<a href="<?php //echo $path_to_root.'/modules/ExtendedHRM/docs/index.php?tut=video'; ?>"> <!--Video Tutorials </a>
	</li> --> 
	</ul>
	<div style="clear:both;" > </div>
</div>
<?php 

}

end_page(); ?>