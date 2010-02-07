{{* $Id: $ *}}

{{*
  * @package Mediboard
  * @subpackage dPmedicament
  * @version $Revision: 7814 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}
	
{{assign var=day_index   value=$tabindex}}
{{assign var=month_index value=$tabindex+1}}
{{assign var=year_index  value=$tabindex+2}}

{{html_select_date
  time=$date|replace:'%':''|default:'0000-00-00'
  start_year=1900
  day_value_format="%02d"
  month_format="%m &mdash; %B"
  field_order=DMY
  day_empty="&mdash;"
  month_empty="&mdash;"
  year_empty="&mdash;&mdash;"
  day_extra="tabindex='$day_index'"
  month_extra="tabindex='$month_index' style='width: 3.4em;'"
  year_extra="tabindex='$year_index'"
}}
