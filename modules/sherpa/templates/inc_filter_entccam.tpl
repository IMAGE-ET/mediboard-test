{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="find" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />

<table class="form">
  <tr>
    <th class="title" colspan="3">Recherche d'un {{tr}}{{$filter->_class_name}}{{/tr}}</th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="idinterv"}}</th>
		<td>{{mb_field object=$filter field="idinterv"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="numdos"}}</th>
		<td>{{mb_field object=$filter field="numdos"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="malnum"}}</th>
		<td>{{mb_field object=$filter field="malnum"}}</td>
  </tr>
  <tr>
    <th>
      <label for="Day" title="Date de l'intervention">
        Date
      </label>
    </th>
    <td >
      {{html_select_date
           prefix=""
           time=$date
           start_year=2005
           field_order=DMY
           day_empty="Jour"
           month_empty="Mois"
           year_empty="Année"
           all_extra="style='display:inline;'"}}
    </td>
  </tr>
  <tr>
    <td class="button" colspan="3">
      <button class="search" type="submit">Rechercher</button>
    </td>
  </tr>
</table>

</form>

<table class="tbl">
  <tr>
    <th>{{mb_title object=$filter field="idinterv"}}</th>
    <th>{{mb_title object=$filter field="numdos"}}</th>
    <th>{{mb_title object=$filter field="malnum"}}</th>
    <th>{{mb_title object=$filter field="debint"}}</th>
    <th>{{mb_title object=$filter field="finint"}}</th>
    <th>{{mb_title object=$filter field="pracod"}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_entccam&sel_idinterv="}}
  
  {{foreach from=$entsccam item=_entccam}}
  <tr {{if $entccam->_id == $_entccam->_id && $entccam->numdos == $_entccam->numdos}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$_entccam->idinterv}}{{if $_entccam->idinterv == '0'}}&amp;sel_numdos={{$_entccam->numdos}}{{/if}}">
        {{mb_value object=$_entccam field="idinterv"}}
      </a>
    <td>
      {{mb_value object=$_entccam field="numdos"}}
    </td>
    <td>
      {{mb_value object=$_entccam field="malnum"}}
    </td>
    <td>
      {{mb_value object=$_entccam field="debint"}}
    </td>
    <td> 
      {{mb_value object=$_entccam field="finint"}}
    </td>
    <td>
      {{mb_value object=$_entccam field="pracod"}}
    </td>
  </tr>
  {{/foreach}}
</table>