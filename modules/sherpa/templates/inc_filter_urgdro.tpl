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
    <th class="title" colspan="3">Recherche d'une ouverture de droits UPATOU</th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=numdos}}</th>
		<td>{{mb_field object=$filter field=numdos}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=malnum}}</th>
		<td>{{mb_field object=$filter field=malnum}}</td>
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
    <th>{{mb_title object=$filter field=numdos}}</th>
    <th>{{mb_title object=$filter field=malnum}}</th>
    <th>{{mb_title object=$filter field=datarr}}</th>
    <th>{{mb_title object=$filter field=datdep}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_urgdro&sel_numdos="}}
  
  {{foreach from=$droits item=curr_droit}}
  <tr {{if $droit->_id == $curr_droit->_id}}class="selected"{{/if}}>
    <td>
      <a href="{{$href}}{{$curr_droit->numdos}}">
        {{mb_value object=$curr_droit field=numdos}}
      </a>
    </td>

    <td>{{mb_value object=$curr_droit field=malnum}}</td>
    <td>{{mb_value object=$curr_droit field=datarr}}</td>
    <td>{{mb_value object=$curr_droit field=datdep}}</td>
  </tr>
  {{/foreach}}
</table>