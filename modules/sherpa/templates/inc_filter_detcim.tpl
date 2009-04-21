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
      Recherche d'un {{tr}}{{$filter->_class_name}}{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=iddiag}}</th>
		<td>{{mb_field object=$filter field=iddiag}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=idinterv}}</th>
		<td>{{mb_field object=$filter field=idinterv}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=numdos}}</th>
		<td>{{mb_field object=$filter field=numdos}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=coddia}}</th>
		<td>{{mb_field object=$filter field=coddia}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=typdia}}</th>
		<td>{{mb_field object=$filter field=typdia defaultOption="&mdash; Tous"}}</td>
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
    <th>{{mb_title object=$filter field=iddiag}}</th>
    <th>{{mb_title object=$filter field=idinterv}}</th>
    <th>{{mb_title object=$filter field=numdos}}</th>
    <th>{{mb_title object=$filter field=coddia}}</th>
    <th>{{mb_title object=$filter field=typdia}}</th>
  </tr>

  {{assign var=href value="?m=sherpa&tab=view_detcim&sel_iddiag="}}
  
  {{foreach from=$detscim item=_detcim}}
  <tr {{if $detcim->_id == $_detcim->_id && $detcim->numdos == $_detcim->numdos}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$_detcim->_id}}">
        {{mb_value object=$_detcim field=iddiag}}
      </a>
    <td>{{mb_value object=$_detcim field=idinterv}}</td>
    <td>{{mb_value object=$_detcim field=numdos}}</td>
    <td>{{mb_value object=$_detcim field=coddia}}</td>
    <td>{{mb_value object=$_detcim field=typdia}}</td>
  </tr>
  {{/foreach}}
</table>