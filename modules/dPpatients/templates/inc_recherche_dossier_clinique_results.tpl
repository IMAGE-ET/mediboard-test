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

{{mb_include module=system template=inc_pagination 
     total=$count_patient change_page="changePage" step=30 current=$start}}

<table class="main tbl">
  <tr>
    <th></th>
    <th style="width: 1%;">{{mb_title class=CPatient field=naissance}}</th>
    <th>{{mb_title class=CPatient field=adresse}}</th>
    <th style="width: 1%;"></th>
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
      <td>
        <button type="button" class="search notext" onclick="Patient.view({{$_patient->_id}})" style="margin: -1px;">
          Dossier complet
        </button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="4">Aucun dossier</td>
    </tr>
  {{/foreach}}
</table>

{{/if}}