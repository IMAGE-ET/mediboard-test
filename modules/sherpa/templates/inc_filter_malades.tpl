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
    <th class="title" colspan="3">
      Recherche d'un malade
      ({{$maladesCount}} {{tr}}found{{/tr}})
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="malnum"}}</th>
		<td>{{mb_field object=$filter field="malnum"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="malnom"}}</th>
		<td>{{mb_field object=$filter field="malnom"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="malpre"}}</th>
		<td>{{mb_field object=$filter field="malpre"}}</td>
  </tr>
  <tr>
    <th colspan="1">
      <label for="Date_Day" title="Date de naissance du malade à rechercher">
        Date de naissance
      </label>
    </th>
    <td colspan="2">
      {{html_select_date
           time=$dateMal
           start_year=1900
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
    <th>{{mb_label object=$filter field="malnum"}}</th>
    <th>{{mb_label object=$filter field="malnom"}}</th>
    <th>{{mb_label object=$filter field="malpre"}}</th>
    <th>{{mb_label object=$malade field="datnai"}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_malades&sel_malnum="}}
  
  {{foreach from=$malades item=curr_malade}}
  <tr {{if $malade->_id == $curr_malade->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$curr_malade->malnum}}">
        {{mb_value object=$curr_malade field="malnum"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$curr_malade->malnum}}">
        {{mb_value object=$curr_malade field="malnom"}}
      </a>
    </td>
    <td class="text"> 
      <a href="{{$href}}{{$curr_malade->malnum}}">
        {{mb_value object=$curr_malade field="malpre"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$curr_malade->malnum}}">
        {{mb_value object=$curr_malade field="datnai"}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>