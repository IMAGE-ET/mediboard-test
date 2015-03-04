{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=etablissement script=Group}}

<script>
  changePage = function(page) {
    $V(getForm("filter-etab_externes").elements.start, page);
  }
</script>

<table class="main">
  <tr>
    <td>
      <table class="tbl layout">
        <tr>
          <td style="width: 35%;">
            <button class="new" onclick="Group.editCEtabExterne()">
              {{tr}}CEtabExterne-title-create{{/tr}}
            </button>
          </td>
          <td>
            <form name="filter-etab_externes" method="get" action="?">
              <input type="hidden" name="m" value="dPetablissement" />
              <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
              <input type="hidden" name="start" value="{{$current_page}}" onchange="this.form.submit()" />

              <input type="text" name="keywords" value="{{$keywords}}" onchange="this.form.start.value = 0" />
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      {{mb_include module=system template=inc_pagination total=$total_etab_externes current=$current_page change_page="changePage" step=40}}
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th></th>
          <th>{{mb_label class=CEtabExterne field=nom}}</th>
          <th>{{mb_label class=CEtabExterne field=cp}}</th>
          <th>{{mb_label class=CEtabExterne field=ville}}</th>
          <th>{{mb_label class=CEtabExterne field=finess}}</th>
        </tr>
        {{foreach from=$list_etab_externes item=_etab}}
          <tr id="{{$_etab->_guid}}-row">
            <td class="narrow">
              <button class="edit notext" onclick="Group.editCEtabExterne('{{$_etab->_id}}')"></button>
            </td>
            <td>
              {{mb_value object=$_etab field=nom}}
            </td>
            <td class="narrow">
              {{mb_value object=$_etab field=cp}}
            </td>
            <td class="narrow">
              {{mb_value object=$_etab field=ville}}
            </td>
            <td class="narrow">
              {{mb_value object=$_etab field=finess}}
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td class="empty" colspan="5">{{tr}}CEtabExterne.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
