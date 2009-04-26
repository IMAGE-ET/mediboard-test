{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="system" script="object_selector"}}

<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">
      {{$list_count}} historiques trouvés{{if $list_count > $list|@count }}, seuls les 100 plus récents sont affichés{{/if}}
    </th>
  </tr>
  
  <tr>
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

    <th>{{mb_label object=$filter field=object_class}}</th>
    <td>
      <select name="object_class" class="str">
        <option value="0">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
          {{tr}}{{$curr_class}}{{/tr}} - {{$curr_class}} 
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field="_date_min"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_min" form="filterFrm" register=true}}</td>

  </tr>
  <tr>
    <th>{{mb_label object=$filter field=type}}</th>
    <td>{{mb_field object=$filter field=type canNull=true defaultOption="&mdash; Choisir un type"}}</td>

    <th>{{mb_label object=$filter field=object_id}}</th>
    <td>
    	{{mb_field object=$filter field=object_id canNull=true}}
      <button type="button" class="search" onclick="ObjectSelector.init()">Chercher un objet</button>
      <script type="text/javascript">
        ObjectSelector.init = function(){  
          this.sForm     = "filterFrm";
          this.sId       = "object_id";
          this.sView     = "object_id";
          this.sClass    = "object_class";
          this.onlyclass = "false";
          this.pop();
        } 
       </script>
    </td>
    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_max" form="filterFrm" register=true}} </td>
  </tr>
  <tr>
    <td class="button" colspan="10">
      <button class="search">{{tr}}Search{{/tr}}</button>
    </td>
  </tr>
</table>

</form>