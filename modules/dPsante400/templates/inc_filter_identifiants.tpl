<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />


<table class="form">
  <tr>
    <th class="category" colspan="6">
      {{if $list_idSante400|@count == 100}}
      Plus de 100 identifiants, seuls les 100 plus récents sont affichés
      {{else}}
      {{$list_idSante400|@count}} identifiants trouvés
      {{/if}}
    </th>
  </tr>

  <tr>
    <td>{{mb_label object=$filter field="object_class"}}</td>
    <td>
      <select name="object_class" class="str maxLength|25">
        <option value="">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses|smarty:nodefaults item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <td>{{mb_label object=$filter field="object_id"}}</td>
    <td>
      <input name="object_id" class="ref" value="{{$filter->object_id}}" />
      <button class="search" type="button" onclick="ObjectSelector.initObjectFilter()">Chercher</button>
      <script type="text/javascript">
        ObjectSelector.initObjectFilter = function(){
          var oForm = document.filterFrm;
          this.eId = oForm.object_id;
          this.eClass = oForm.object_class;  
          this.onlyclass = "false";
          this.pop();
        }
      </script>
    </td>
  </tr>

  <tr>
    <td>{{mb_label object=$filter field="id400"}}</td>
    <td>{{mb_field object=$filter field="id400" canNull="true"}}</td>
    <td>{{mb_label object=$filter field="tag"}}</td>
    <td>{{mb_field object=$filter field="tag" size="40"}}</td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>

</form>


