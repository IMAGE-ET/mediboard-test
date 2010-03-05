<form name="filterFrm" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id=0{{if $dialog}}&amp;object_class={{$filter->object_class}}&amp;object_id={{$filter->object_id}}{{/if}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />


<table class="form">
  <tr>
    <th class="title" colspan="6">
      {{$list_idSante400|@count}} identifiants 
      {{if $list_idSante400|@count != $count_idSante400}}
      sur {{$count_idSante400}}
      {{/if}}
      trouvés
    </th>
  </tr>

  <tr>
    <td>{{mb_label object=$filter field="object_class"}}</td>
    <td>
      <select name="object_class" class="str maxLength|25">
        <option value="">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <td>{{mb_label object=$filter field="object_id"}}</td>
    <td>
      <input name="object_id" class="ref" value="{{$filter->object_id}}" />
      <button class="search" type="button" onclick="ObjectSelector.initFilter()">Chercher</button>
      <script type="text/javascript">
        ObjectSelector.initFilter = function(){
          this.sForm     = "filterFrm";
          this.sId       = "object_id";
          this.sClass    = "object_class";  
          this.onlyclass = "false";
          this.pop();
        }
      </script>
    </td>
  </tr>

  <tr>
    <td>{{mb_label object=$filter field="id400"}}</td>
    <td>{{mb_field object=$filter field="id400" canNull=true}}</td>
    <td>{{mb_label object=$filter field="tag"}}</td>
    <td>{{mb_field object=$filter field="tag" size=30}}</td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>

</form>


