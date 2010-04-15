{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<td>
  {{$rpu->$debut|date_format:$dPconfig.time}}
	{{if $isImedsInstalled && ($debut == "bio_depart")}}
	  {{mb_include module=dPImeds template=inc_sejour_labo sejour=$_sejour link="$rpu_link#Imeds"}}
  {{/if}}
</td>
<td id="{{$fin}}-{{$rpu->_id}}">
  {{mb_include module=dPurgences template=inc_vw_fin_attente}}
</td>