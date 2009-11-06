{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="system" script="object_selector"}}

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
<input type="hidden" name="dialog" value="{{$dialog}}" />
<input type="hidden" name="start" value="{{$start|default:0}}" onchange="this.form.submit()" />

<table class="form">
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
    <td>{{mb_field object=$filter field="_date_min" form="filterFrm" register=true}}</td>

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
    <td>{{mb_field object=$filter field="_date_max" form="filterFrm" register=true}} </td>
  </tr>
  <tr>
    <td class="button" colspan="10">
      <button class="search">{{tr}}Search{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

{{if $list_count < 100000}}
  {{mb_include module=system template=inc_pagination total=$list_count current=$start change_page='changePage'}}
{{else}}
  <div class="small-info">
    <strong>{{$list_count}}</strong> historiques trouvés, seuls les 100 plus récents sont affichés.<br />
    Veuillez préciser votre recherche pour avoir accès aux historiques plus anciens.
  </div>
{{/if}}