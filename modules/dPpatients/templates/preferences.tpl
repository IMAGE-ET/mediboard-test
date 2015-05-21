{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage 
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include template=inc_pref spec=bool var=vCardExport}}
{{mb_include template=inc_pref spec=str  var=medecin_cps_pref}}
{{mb_include template=inc_pref spec=bool var=sort_atc_by_date}}
{{mb_include template=inc_pref spec=bool var=new_date_naissance_selector}}
{{mb_include template=inc_pref spec=bool var=constantes_show_comments_tooltip}}
{{mb_include template=inc_pref spec=bool var=constantes_show_view_tableau}}

<tr>
  <th colspan="5" class="category">Carte Vitale</th>
</tr>

{{mb_include template=inc_pref spec=str  var=VitaleVisionDir}}
{{mb_ternary var=enum_vitale test="mbHost"|module_active value="none|vitaleVision|mbHost" other="none|vitaleVision"}}
{{mb_include template=inc_pref spec=enum var=LogicielLectureVitale values=$enum_vitale}}
{{mb_include template=inc_pref spec=enum var=update_patient_from_vitale_behavior values="choice|never|always"}}