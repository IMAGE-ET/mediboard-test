{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
  <tr>
    <th class="category">Traitements personnels du dossier médical du patient</th>
  </tr>
  <tr>
    <td>
		{{if $dossier_medical->_ref_prescription}}
			<ul>
			{{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}
			  <li>
		      {{if $_line->fin}}
			      Du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->fin|date_format:"%d/%m/%Y"}} :
			    {{elseif $_line->debut}}
			      Depuis le {{$_line->debut|date_format:"%d/%m/%Y"}} :
			    {{/if}}
		      <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
				    {{$_line->_ucd_view}} ({{$_line->_forme_galenique}})
				  </span>
				</li>
			{{foreachelse}}
				Aucun traitement dans le dossier médical du patient
			{{/foreach}}
			</ul>
		{{/if}}
		</td>
  </tr>
</table>