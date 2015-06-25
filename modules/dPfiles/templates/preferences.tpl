{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage 
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include template=inc_pref spec=str  var=directory_to_watch}}
{{mb_include template=inc_pref spec=bool var=debug_yoplet}}
{{mb_include template=inc_pref spec=enum var=mozaic_disposition values="1x2|2x1|2x2|2x3|3x2|3x3"}}
{{mb_include template=inc_pref spec=bool var=show_file_view}}

{{if "mbHost"|module_active}}
  {{mb_include template=inc_pref spec=bool var=upload_mbhost}}
{{/if}}