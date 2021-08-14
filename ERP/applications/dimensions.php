<?php
/**********************************************************************
    Direct Axis Technology L.L.C.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
class dimensions_app extends application
{
	function __construct()
	{
		$dim = get_company_pref('use_dimension');
		parent::__construct("proj", trans($this->help_context = "&Dimensions"), $dim);

		if ($dim > 0)
		{
			$this->add_module(trans("Transactions"));
			$this->add_lapp_function(0, trans("Dimension &Entry"),
				"dimensions/dimension_entry.php?", 'SA_DIMENSION', MENU_ENTRY);
			$this->add_lapp_function(0, trans("&Outstanding Dimensions"),
				"dimensions/inquiry/search_dimensions.php?outstanding_only=1", 'SA_DIMTRANSVIEW', MENU_TRANSACTION);

			$this->add_module(trans("Inquiries and Reports"));
			$this->add_lapp_function(1, trans("Dimension &Inquiry"),
				"dimensions/inquiry/search_dimensions.php?", 'SA_DIMTRANSVIEW', MENU_INQUIRY);

			$this->add_rapp_function(1, trans("Dimension &Reports"),
				"reporting/reports_main.php?Class=4", 'SA_DIMENSIONREP', MENU_REPORT);
			
			$this->add_module(trans("Maintenance"));
			$this->add_lapp_function(2, trans("Dimension &Tags"),
				"admin/tags.php?type=dimension", 'SA_DIMTAGS', MENU_MAINTENANCE);

			$this->add_extensions();
		}
	}
}

