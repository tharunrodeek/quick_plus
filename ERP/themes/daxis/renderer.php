<?php
/*-------------------------------------------------------+
| Saai Theme for FrontAccounting
| http://www.directaxistech.com/
+--------------------------------------------------------+
| Author: Kvvaradha  
| Email: admin@directaxistech.com
+--------------------------------------------------------+*/
include_once("kvcodes.inc");
create_tbl_option();
function addhttp($url) {
		    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		        $url = "http://" . $url;
		    }
		    return $url;
		}
	class renderer{
		function get_icon($category){
			global  $path_to_root, $SysPrefs;

			if ($SysPrefs->show_menu_category_icons)
				$img = $category == '' ? 'right.gif' : $category.'.png';
			else	
				$img = 'right.gif';
			return "<img src='$path_to_root/themes/".user_theme()."/images/$img' style='vertical-align:middle;' border='0'>&nbsp;&nbsp;";
		}

		function wa_header(){
			if(isset($_GET['application']) && ($_GET['application'] == 'orders' || $_GET['application'] == 'orders#header'))
				page(trans($help_context = "Sales"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'AP'|| $_GET['application'] == 'AP#header'))
				page(trans($help_context = "Purchases"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'stock'|| $_GET['application'] == 'stock#header'))
				page(trans($help_context = "Items & Services"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'manuf'|| $_GET['application'] == 'manuf#header'))
				page(trans($help_context = "Manufacturing"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'proj'|| $_GET['application'] == 'proj#header'))
				page(trans($help_context = "Dimensions"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'assets'|| $_GET['application'] == 'assets#header'))
				page(trans($help_context = "Fixed Assets"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'GL'|| $_GET['application'] == 'GL#header'))
				page(trans($help_context = "GL & Banking"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'extendedhrm'|| $_GET['application'] == 'extendedhrm#header'))
				page(trans($help_context = "HRM and Payroll"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'system'|| $_GET['application'] == 'system#header'))
				page(trans($help_context = "Setup Menu"), false, true);
			elseif(!isset($_GET['application']) || ($_GET['application'] == 'dashboard'|| $_GET['application'] == 'dashboard#header'))
				page(trans($help_context = "Dashboard"), false, true);



            elseif(!isset($_GET['application']) || ($_GET['application'] == 'app_report'|| $_GET['application'] == 'app_report#header'))
                page(trans($help_context = "APP REPORT"), true, true);


			else
				page(trans($help_context = "Main Menu"), false, true);
		}

		function wa_footer(){
			end_page(false, true);
		}

		function menu_header($title, $no_menu, $is_index){
			global $path_to_root, $SysPrefs, $db_connections, $icon_root, $version ;			
			
			require_once("ExtraSettings.php"); ?>
			<script> 
			(function() {
			    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
			    link.type = 'image/x-icon';
			    link.rel = 'shortcut icon';
			    <?php if(kv_get_option('favicon') != 'false' && file_exists(dirname(__FILE__).'/images/'.kv_get_option('favicon'))){
			    	echo " link.href = '$path_to_root/themes/".user_theme()."/images/".kv_get_option('favicon')."?".rand(2,5)."'; ";
			    }else {
			    	echo "link.href = '$path_to_root/themes/".user_theme()."/images/favicon.ico?".rand(2,5)."';";
			    } ?>
			    
			    document.getElementsByTagName('head')[0].appendChild(link);
			}());
			</script> <?php 
		echo '</style><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
		<link href="'.$path_to_root.'/themes/daxis/css/fontello.css" rel="stylesheet" type="text/css"> 
 <link rel="stylesheet" href="'.$path_to_root.'/themes/daxis/css/animation.css">
 <link href="https://fonts.googleapis.com/css?family=Raleway:200,200i,300,400" rel="stylesheet">
 <link href="'.$path_to_root.'/../assets/css/style.bundle.css" rel="stylesheet">
     <link href="'.$path_to_root.'/../assets/plugins/general/plugins/flaticon/flaticon.css" rel="stylesheet" type="text/css" />
    <link href="'.$path_to_root.'/../assets/plugins/general/plugins/flaticon2/flaticon.css" rel="stylesheet" type="text/css" />
 
 
 <script src="'.$path_to_root.'/../assets/plugins/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
 
 <script src="'.$path_to_root.'/../assets/plugins/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>

 <script src="'.$path_to_root.'/../assets/plugins/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
 <script src="'.$path_to_root.'/../assets/js/config.js" type="text/javascript"></script>
 <script src="'.$path_to_root.'/../assets/js/general.js" type="text/javascript"></script>
 
 


 
   
   




 
 
 
 <script>
			var KTAppOptions = {
				"colors": {
					"state": {
						"brand": "#5d78ff",
						"light": "#ffffff",
						"dark": "#282a3c",
						"primary": "#5867dd",
						"success": "#34bfa3",
						"info": "#36a3f7",
						"warning": "#ffb822",
						"danger": "#fd3995"
					},
					"base": {
						"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
						"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
					}
				}
			};
		</script>
 
 

 ';

			if(kv_get_option('color_scheme') != 'false'){
				$color_scheme = kv_get_option('color_scheme'); 
			}else{
				$color_scheme= 'default';
			}
			echo '<link rel="stylesheet" href="'.$path_to_root.'/themes/daxis/css/colorschemes/'.$color_scheme.'.css">';
			require_once("ExtraSettings.php"); 
			echo '<div class="wrapper">'; // tabs

			$indicator = "$path_to_root/themes/".user_theme(). "/images/ajax-loader.gif";
			if (!$no_menu) {
                $applications = $_SESSION['App']->applications;
                $local_path_to_root = $path_to_root;
                $sel_app = $_SESSION['sel_app'];
                if (file_exists(dirname(__FILE__) . '/images/' . kv_get_option('logo'))) {
                    $logo_img = kv_get_option('logo') . '?' . rand(2, 5);
                } else
                    $logo_img = 'Saaisaran.png?' . rand(2, 5);

                echo '<div class="sidebar" id="sidebar">
      <div class="sidebar-wrapper">
        <div class="logo">
          <a href="' . $path_to_root . '"> <img src="' . $path_to_root . '/themes/daxis/images/' . $logo_img . '" ><!--' . $db_connections[user_company()]["name"] . ' --> </a>
          <a class="simple-text" href="' . $path_to_root . '/admin/display_prefs.php?" > ' . $_SESSION['wa_current_user']->name . '</a>     </div>
        <nav id="nav"><ul class="nav">';
                if (kv_get_option('hide_dashboard') == 0) {
                    echo '<li>  <a class="' . ((isset($_GET['application']) && $_GET['application'] == 'dashboard') ? 'active' : '') . '" href="' . $path_to_root . '?application=dashboard"> <i class="icon-av_timer"></i> <p> ' . trans("Dashboard") . ' </p></a> </li>';
                }

                $icon_root = 'av_timer';
                foreach ($applications as $app) {
                    if ($_SESSION["wa_current_user"]->check_application_access($app)) {
                        if (trim($app->id) == 'orders')
                            $icon_root = 'monetization_on';
                        elseif (trim($app->id) == 'AP')
                            $icon_root = 'add_shopping_cart';
                        elseif (trim($app->id) == 'stock')
                            $icon_root = 'storage';
                        elseif (trim($app->id) == 'manuf')
                            $icon_root = 'location_city';
                        elseif (trim($app->id) == 'assets')
                            $icon_root = 'receipt';
                        elseif (trim($app->id) == 'proj')
                            $icon_root = 'dialpad';
                        elseif (trim($app->id) == 'GL')
                            $icon_root = 'account_balance_wallet';
                        elseif (trim($app->id) == 'system')
                            $icon_root = 'settings';
                        else
                            $icon_root = 'av_timer';

                        $acc = access_string($app->name);

//						 if ($_SESSION["wa_current_user"]->can_access_page($app->id))  {
                        echo "<li><a class='" . ($sel_app == $app->id ? 'active' : '')
                            . "' href='$local_path_to_root/index.php?application=" . $app->id
                            . "'> <i class='icon-" . $icon_root . "'></i> <p>" . ($app->id == trans('GL') ? trans('Financial Accounting') : $acc[0]) . " </p></a>";
//						 }

                        if (trim($app->id) == 'FrontHrm') {

                            echo '<li>  <a class="' . ((isset($_GET['application']) && $_GET['application'] == 'dashboard') ? 'active' : '') . '" href="#" > <i class="icon-av_timer"></i> <p> ' . trans("CRM") . ' </p></a> </li>';


                        }


                    }
                }


                if (in_array($_SESSION["wa_current_user"]->access, [2])) {

                    echo '<li>  <a target="_blank" href="' . $path_to_root . '/axis-reports/public/invoice_report" > <i class="icon-av_timer"></i> <p> ' . trans("Report") . ' </p></a> </li>';

                }


                echo "<img style='width:258px;' src='http://13.233.39.112/img/eid_mubarak.jpeg'>";

                echo "</ul></nav></div> 
  				</div>  <!-- End of Sidebar --> ";
                // top status bar

                if ($sel_app == 'orders')
                    $icon_root = 'monetization_on';
                elseif ($sel_app == 'AP')
                    $icon_root = 'add_shopping_cart';
                elseif ($sel_app == 'stock')
                    $icon_root = 'storage';
                elseif ($sel_app == 'manuf')
                    $icon_root = 'location_city';
                elseif ($sel_app == 'assets')
                    $icon_root = 'receipt';
                elseif ($sel_app == 'proj')
                    $icon_root = 'dialpad';
                elseif ($sel_app == 'GL')
                    $icon_root = 'account_balance_wallet';
                else
                    $icon_root = 'settings';

            }
            if ($no_menu){
                echo '<div class="main-panel" id="main-panel">
			    
			    
			    
			    <script>
			    
			    
			    
			    var trans ;
			    
			    $(document).ready(function() {
			      
			        $(".axispro-lang-btn").click(function (e) {
                        var lang = $(this).data("lang");
                        var path_to_root = "'.$path_to_root.'";
                        
                        
                        
                        
                        $.post( path_to_root+"/access/change_language.php", { lang: lang })
                              .done(function( data ) {
                                window.location.reload();
                              });
                    })
			        
			    });
			    
                    
            </script>
			    
			    
			    <div class="content" style="padding-top: 0;">';



			// ajax indicator for installer and popups



			    //echo '<button><a href="'.$path_to_root.'/../?application=dashboard">BACK TO DASHBOARD</a></button>';


			    ?>




                <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed " data-ktheader-minimize="on">

                    <?php include  "$path_to_root/../header-top.php";?>
                    <?php include  "$path_to_root/../header-menu.php";?>

                </div>



				<?php echo "<center><table class='tablestyle_noborder'>"
					."<tr><td><img id='ajaxmark' src='$indicator' align='center' style='visibility:hidden;' alt='ajaxmark'></td></tr>"
					."</table></center> ";
					echo '<div class="content inner-box-content">';
			} elseif ($title && !$is_index)	{
				/*echo "<center><table id='title'><tr><td width='100%' class='titletext'>$title</td>"
				."<td align=right>"
				.(user_hints() ? "<span id='hints'></span>" : '')
				."</td>"
				."</tr></table></center>";	*/			
			}
			
			echo "<img id='ajaxmark' src='$indicator' align='center' style='visibility:hidden;' alt='ajaxmark'>";
		}

		function menu_footer($no_menu, $is_index){
			global $version, $path_to_root, $Pagehelp, $Ajax, $SysPrefs;

			include_once($path_to_root . "/includes/date_functions.inc");

			if(kv_get_option('powered_name') != 'false'){
				$app_title = kv_get_option('powered_name');
			}else 
				$app_title = 'Vanigam';

			if(kv_get_option('powered_url') != 'false'){
				$powered_url = addhttp(kv_get_option('powered_url'));
			}else 
				$powered_url = 'http://frontaccounting.com';			
			
			echo '</div>';

			if ($no_menu == false){

				if(isset($_GET['application']) && $_GET['application'] == 'stock')
					echo '</div>';
				echo '<footer class="footer">
            	<div class="container-fluid">
                <nav class="pull-left">
                    <ul> <li> <a target="_blank" href="'.$powered_url.'" tabindex="-1">'.$app_title.' ';
					if(kv_get_option('hide_version')== 0 )
					{
//					    echo $version;
					}
				echo '</a>- <a href="http://www.directaxistech.com" >  www.directaxistech.com </a>' .show_users_online().' </li>';
				if (isset($_SESSION['wa_current_user'])) {
					$phelp = implode('; ', $Pagehelp);
					$Ajax->addUpdate(true, 'hotkeyshelp', $phelp);
					echo "<li> ".$phelp."</li>";
				}
				echo '</ul>
                </nav>
                <p class="copyright pull-right">
                    Copyrights &copy; '.date('Y').' <a href="'.$powered_url.'" target="_blank"></a> 
                </p>
            </div>
        </footer>';
    } echo '</div>
</div>'; ?>


<script>
var toggleMenu = function(){
            var m = document.getElementById('sidebar'),
                c = m.className;
              m.className = c.match( ' active' ) ? c.replace( ' active', '' ) : c + ' active';

              var m = document.getElementById('main-panel'),
                c = m.className;
              m.className = c.match( ' active' ) ? c.replace( ' active', '' ) : c + ' active';
        }




</script>
</div>
</div><?php 
		}

		function display_applications(&$waapp)	{
			global $path_to_root;

			$selected_app = $waapp->get_selected_application();
			if (!$_SESSION["wa_current_user"]->check_application_access($selected_app))
				return;

			if (method_exists($selected_app, 'render_index'))	{
				$selected_app->render_index();
				return;
			}

			if( !isset($_GET['application']) || $_GET['application'] == 'dashboard'){	
				require("dashboard.php");
			}
			else if ($_GET['application'] == 'app_report') {

                require("app_report.php");

            }else{

				echo '<div class="MenuPage"> ';
				foreach ($selected_app->modules as $module)	{
	        		if (!$_SESSION["wa_current_user"]->check_module_access($module))
	        			continue;
					// image
					echo '<div class="MenuPart"><div class="subHeaders"> '.$module->name.'</div>';
					echo '<ul class="left">';

					foreach ($module->lappfunctions as $appfunction){
						$img = $this->get_icon($appfunction->category);
						if ($appfunction->label == "")
							echo "&nbsp;<br>";
						elseif ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)) {
							echo '<li>'.$img.menu_link($appfunction->link, $appfunction->label)."</li>";
						}
						//elseif (!$_SESSION["wa_current_user"]->hide_inaccessible_menu_items())	{
							//echo '<li>'.$img.'<span class="inactive">'.access_string($appfunction->label, true)."</span></li>";
						//}
					}
					echo "</ul>";
					if (sizeof($module->rappfunctions) > 0)	{
						echo "<ul class='right'>";
						foreach ($module->rappfunctions as $appfunction){
							$img = $this->get_icon($appfunction->category);
							if ($appfunction->label == "")
								echo "&nbsp;<br>";
							elseif ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)) {
								echo '<li>'.$img.menu_link($appfunction->link, $appfunction->label)."</li>";
							}
							//elseif (!$_SESSION["wa_current_user"]->hide_inaccessible_menu_items())	{
								//echo '<li>'.$img.'<span class="inactive">'.access_string($appfunction->label, true)."</span></li>";
							//}
						}
						echo "</ul>";
					}
					echo "<div style='clear: both;'></div>";
				}
				echo "</div></div> </div> </div>";
			}			
  		}
	}
