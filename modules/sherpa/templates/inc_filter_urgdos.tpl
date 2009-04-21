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
    <th class="title" colspan="3">Recherche de dossiers UPATOU</th>
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
    <th>{{mb_label object=$filter field=anndos}}</th>
		<td>{{mb_field object=$filter field=anndos}}</td>
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
    <th>{{mb_title object=$filter field=anndos}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_urgdos&sel_numdos="}}
  
  {{foreach from=$dossiers item=_dossier}}
  <tr {{if $dossier->_id == $_dossier->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$_dossier->numdos}}">
        {{mb_value object=$_dossier field="numdos"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$_dossier->numdos}}">
        {{mb_value object=$_dossier field="malnum"}}
      </a>
    </td>
    <td class="text">
      <a href="{{$href}}{{$_dossier->numdos}}">
        {{mb_value object=$_dossier field="anndos"}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>