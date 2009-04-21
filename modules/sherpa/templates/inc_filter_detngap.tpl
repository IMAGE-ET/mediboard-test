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
    <th>{{mb_label object=$filter field="idacte"}}</th>
		<td>{{mb_field object=$filter field="idacte"}}</td>
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
    <th>{{mb_title object=$filter field="idacte"}}</th>
    <th>{{mb_title object=$filter field="idinterv"}}</th>
    <th>{{mb_title object=$filter field="numdos"}}</th>
    <th>{{mb_title object=$filter field="malnum"}}</th>
    <th>{{mb_title object=$filter field="pracod"}}</th>
    <th>{{mb_title object=$filter field="codact"}}</th>
    <th>{{mb_title object=$filter field="actqte"}}</th>
    <th>{{mb_title object=$filter field="quant"}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_detngap&sel_idacte="}}
  
  {{foreach from=$detsngap item=_detngap}}
  <tr {{if $detngap->_id == $_detngap->_id && $detngap->numdos == $_detngap->numdos}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$_detngap->_id}}">
        {{mb_value object=$_detngap field="idacte"}}
      </a>
    <td>{{mb_value object=$_detngap field="idinterv"}}</td>
    <td>{{mb_value object=$_detngap field="numdos"}}</td>
    <td>{{mb_value object=$_detngap field="malnum"}}</td>
    <td>{{mb_value object=$_detngap field="pracod"}}</td>
    <td>{{mb_value object=$_detngap field="codact"}}</td>
    <td>{{mb_value object=$_detngap field="actqte"}}</td>
    <td>{{mb_value object=$_detngap field="quant" }}</td>
  </tr>
  {{/foreach}}
  
</table>