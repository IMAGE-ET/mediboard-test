{{* $Id: vw_extract_passages.tpl 7641 2009-12-17 10:50:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="urgences" script="CExtractPassages"}}

<form name="listFilter" action="?" method="get" onsubmit="return CExtractPassages.refreshExtractPassages(this)">
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>

  <table class="main layout">
    <tr>
      <td class="separator expand" onclick="MbObject.toggleColumn(this, $(this).next())"></td>

      <td>
        <table class="main form">
          <tr>
            <th class="title" colspan="2">{{tr}}CExtractPassages{{/tr}}</th>
          </tr>

          <tr>
            <th style="width: 15%">{{tr}}CExtractPassages-date_extract{{/tr}}</th>
            <td class="text">
              {{mb_field class=CExtractPassages field=_date_min register=true form="listFilter"
                prop=dateTime onchange="\$V(this.form.elements.start, 0)" value="$date_min"}}
              <b>&raquo;</b>
              {{mb_field class=CExtractPassages field=_date_max register=true form="listFilter"
                prop=dateTime onchange="\$V(this.form.elements.start, 0)" value="$date_max"}}
            </td>
          </tr>

          <tr>
            <th style="width: 15%">{{tr}}CExtractPassages-_selection{{/tr}}</th>
            <td class="text">
              {{mb_field class=CExtractPassages field=debut_selection register=true form="listFilter"
              prop=dateTime onchange="\$V(this.form.elements.start, 0)" value=""}}
              <b>&raquo;</b>
              {{mb_field class=CExtractPassages field=fin_selection register=true form="listFilter"
              prop=dateTime onchange="\$V(this.form.elements.start, 0)" value=""}}
            </td>
          </tr>

          <tr>
            <th>{{mb_label class=CExtractPassages field="type"}}</th>
            <td>{{mb_field class=CExtractPassages field="type" onchange="this.form.onsubmit()" value=$type typeEnum="radio"}}</td>
          </tr>

          <tr>
            <td colspan="2">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
      </td>
    </tr>
   </table>
</form>

<div id="extractPassages">
</div>