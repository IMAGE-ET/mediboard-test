{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table width="100%" bgcolor="#cccccc">
  <tr>
    <th align="center">
      <form action="?" name="selection" method="get" >
      {{include file="inc_select_lang.tpl"}}

      <input type="hidden" name="m" value="dPcim10" />
      <input type="hidden" name="tab" value="vw_idx_chapter" />
      <input type="hidden" name="code" value="{{$cim10->code}}" />

      <h1>Liste des chapitres de la CIM10</h1>
      </form>
    </th>
  </tr>
  <tr>
    <td valign="top" align="center">
      <table width="750" bgcolor="#dddddd">
        {{foreach from=$chapter item=curr_chapter}}
        <tr>
          <td valign="top" align="right">
            <b>{{$curr_chapter.rom}}</b>
          </td>
          <td valign="top" align="left">
            <a href="?m={{$m}}&amp;tab=vw_full_code&amp;code={{$curr_chapter.code|escape:'url'}}"><b>{{$curr_chapter.text}}</b></a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>