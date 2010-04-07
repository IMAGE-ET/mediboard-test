{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function changePage(start) {
  $V(getForm("filterFrm").start, start);
}

Main.add(function(){
  var form = getForm("filterFrm");
  form.getElements().each(function(e){
    e.observe("change", function(){
      $V(form.start, 0);
    });
  });
});
</script>

<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="start" value="{{$start|default:0}}" onchange="this.form.submit()" />
<input type="hidden" name="ip_adress" value="" />

<table class="main form">
  <tr>
    <th>{{mb_label object=$filter field="_date_min"}}</th>
    <td>{{mb_field object=$filter field="_date_min" form="filterFrm" register=true}}</td>
    <th>{{mb_label object=$filter field=user_id}}</th>
    <td>
      <select name="user_id" class="ref">
        <option value="">&mdash; Tous les utilisateurs</option>
        {{foreach from=$listUsers item=curr_user}}
        <option value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $filter->user_id}}selected="selected"{{/if}}>
          {{$curr_user}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td>{{mb_field object=$filter field="_date_max" form="filterFrm" register=true}}</td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td class="button" colspan="4"><button type="submit" class="search">{{tr}}Filter{{/tr}}</button></td>
  </tr>
</table>

</form>

{{mb_include module=system template=inc_pagination total=$list_count current=$start step=30 change_page='changePage'}}

<table class="tbl">
  <tr>
    <th>Nb. de Logs</th>
    <th>IP</th>
    <th>Dernier log</th>
    <th>Utilisateurs</th>
  </tr>
  {{foreach from=$total_list item=_log}}
  <tr>
    <td>{{$_log.total}}</td>
    <td>{{$_log.ip}}</td>
    <td>{{$_log.date_max|date_format:$dPconfig.datetime}}</td>
    <td class="text">
      {{foreach from=$_log.users item=_user}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_user}}
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
</table>