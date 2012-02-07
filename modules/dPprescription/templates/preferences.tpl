{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage 
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include template=inc_pref spec=enum var=easy_mode values="0|1"}}
{{mb_include template=inc_pref spec=enum var=show_transmissions_form values="0|1"}}
{{mb_include template=inc_pref spec=enum var=hide_old_lines values="0|1"}}
{{mb_include template=inc_pref spec=enum var=show_hour_onmouseover_plan_soins values="0|1"}}
{{mb_include template=inc_pref spec=bool var=lt_checked_externe}}
{{mb_include template=inc_pref spec=bool var=dci_checked_externe}}
{{mb_include template=inc_pref spec=bool var=duplicata_checked_externe}}
{{mb_include template=inc_pref spec=bool var=date_empty_externe}}
{{mb_include template=inc_pref spec=bool var=options_ordo_checked}}