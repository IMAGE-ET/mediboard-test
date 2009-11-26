{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
    	Médecins traitants les plus prescripteurs
    	(max. {{$max}})
    </th>
  </tr>

  <tr>
    <th colspan="3">{{mb_label class=CPatient field=medecin_traitant}}</th>
    <th>Nombre de patients</th>
  </tr>

	{{foreach from=$prescripteurs key=medecin_id item=nb_patients}}
  <tr>
    <td>
      {{assign var=medecin value=$medecins.$medecin_id}}
	    <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}')">
	    {{$medecin}}
	    </span>
    </td>
    <td class="text">{{$medecin->adresse}}, {{$medecin->cp}} {{$medecin->ville}}</td>
    <td>{{mb_value object=$medecin field=tel}}</td>
    <td class="button">{{$nb_patients}}</td>
  </tr>
	{{foreachelse}}
	<tr>
		<td colspan="2"><em>{{tr}}None{{/tr}}</em></td>
	</tr>
	
	{{/foreach}}
</table>

