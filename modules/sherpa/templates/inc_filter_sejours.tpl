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
    <th class="title" colspan="3">Recherche d'un séjour</th>
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
    <th colspan="1">
      <label for="Date_Day" title="Date de début du sejour">
        Date d'entrée
      </label>
    </th>
    <td colspan="2">
      {{html_select_date
           prefix="debut"
           time=$datent
           start_year=2005
           field_order=DMY
           day_empty="Jour"
           month_empty="Mois"
           year_empty="Année"
           all_extra="style='display:inline;'"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="litcod"}}</th>
		<td>{{mb_field object=$filter field="litcod"}}</td>
  </tr>
    <tr>
    <th>{{mb_label object=$filter field="sercod"}}</th>
		<td>{{mb_field object=$filter field="sercod"}}</td>
  </tr>
    <tr>
    <th>{{mb_label object=$filter field="pracod"}}</th>
		<td>{{mb_field object=$filter field="pracod"}}</td>
  </tr>
  <tr>
    <th colspan="1">
      <label for="Date_Day" title="Date de fin du sejour">
        Date de sortie
      </label>
    </th>
    <td colspan="2">
      {{html_select_date
           prefix="fin"
           time=$datsor
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
    <th>{{mb_label object=$filter field="numdos"}}</th>
    <th>{{mb_label object=$filter field="malnum"}}</th>
    <th>{{mb_label object=$filter field="datent"}}</th>
    <th>{{mb_label object=$filter field="litcod"}}</th>
    <th>{{mb_label object=$filter field="sercod"}}</th>
    <th>{{mb_label object=$filter field="pracod"}}</th>
    <th>{{mb_label object=$filter field="datsor"}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_sejours&sel_numdos="}}
  
  {{foreach from=$sejours item=curr_sejour}}
  <tr {{if $sejour->_id == $curr_sejour->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="numdos"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="malnum"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="datent"}}
      </a>
    </td>
    <td class="text"> 
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="litcod"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="sercod"}}
      </a>
    </td>
        <td class="text">
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="pracod"}}
      </a>
    </td>
        <td class="text">
      <a href="{{$href}}{{$curr_sejour->numdos}}">
        {{mb_value object=$curr_sejour field="datsor"}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>