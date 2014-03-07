{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    CUserLog.auto.delay(2);
  });
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="3">{{tr}}sanitize_userlogs-removers{{/tr}}</th>
  </tr>
  <tr>
    <th>{{mb_title class=CUserLog field=object_class}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
  </tr>

  {{foreach from=$removers item=_remover}}
  <tr>
    <td>{{$_remover.0|default:'-'}}</td>
    <td>{{$_remover.1|default:'-'}}</td>
    <td>{{$_remover.2|default:'-'}}</td>
  </tr>
  {{/foreach}}
</table>

{{if $log->_id < $offset}}
  <div class="small-info">
    {{tr var1=$log->_id}}sanitize_userlogs-message-finished{{/tr}}
  </div>
{{else}}
  <div class="small-{{$purge|ternary:'warning':'info'}}">
    {{tr var1=$count var2=$min var3=$max}}sanitize_userlogs-message-foundrows-{{$purge|ternary:'purge':'count'}}{{/tr}}
  </div>
{{/if}}

<form name="Sanitize" action="?m={{$m}}" method="get" onsubmit="return CUserLog.sanitize(this);">
<input name="purge" type="hidden" value="" />

<table class="form">
  <tr>
    <td>
      <label for="offset">{{tr}}Offset{{/tr}}</label>
      <input type="text" name="offset" value="{{$offset}}" />
    </td>
    <td>
      <label for="step">{{tr}}Step{{/tr}}</label>
      <input type="text" name="step" value="{{$step}}" />
    </td>
    
    <td>
      <input type="checkbox" name="auto" {{if $auto}} checked="checked" {{/if}} />
      <label for="auto">{{tr}}Auto{{/tr}}</label>
    </td>

    <td class="button">
      <button class="search" type="button" onclick="$V(this.form.purge, '0'); this.form.onsubmit();">{{tr}}Count{{/tr}}</button>
      <button class="trash"  type="button" onclick="$V(this.form.purge, '1'); this.form.onsubmit();">{{tr}}Purge{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
