{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$one_field}}
  <div class="small-info">
    Veuillez renseigner au moins un champ dans le formulaire de gauche pour effectuer une recherche
  </div>
{{else}}

<h3>{{$count_patient}} dossiers trouvés (50 affichés)</h3>

<table class="main tbl">
  <tr>
    <th></th>
    <th>{{mb_title class=CPatient field=naissance}}</th>
    <th>{{mb_title class=CPatient field=adresse}}</th>
  </tr>
    
  {{foreach from=$list_patient item=_patient}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">{{$_patient}}</span>
      </td>
      <td>
        {{mb_value object=$_patient field=naissance}}
      </td>
      <td class="text">
        {{$_patient->adresse|spancate:30}} -
        {{$_patient->cp}}
        {{$_patient->ville|spancate:20}}
      </td>
    </tr>
  {{/foreach}}
</table>

{{/if}}