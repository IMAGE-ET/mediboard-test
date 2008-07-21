<table class="main">
<tr>
  <td class="halfPane">
    <table class="tbl">
      <tr>
        <th class="title" colspan="4">{{tr}}CTypeEi{{/tr}}</th>
      </tr>
      <tr>
        <th>{{tr}}CTypeEi.name{{/tr}}</th>
        <th>{{tr}}CTypeEi.concerne{{/tr}}</th>
        <th>{{tr}}CTypeEi.desc{{/tr}}</th>
      </tr>
      {{foreach from=$type_ei_list key=id item=type}}
      <tr>
      <td><a href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id={{$type->_id}}" title="Voir ou modifier le modèle de fiche">
      {{mb_value object=$type field=name}}
      </a>
      </td>
      <td><a href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id={{$type->_id}}" title="Voir ou modifier le modèle de fiche">
      {{mb_value object=$type field=concerne}}
      </a>
      </td>
      <td style="absolute"><a href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id={{$type->_id}}" title="Voir ou modifier le modèle de fiche">
      {{mb_value object=$type field=desc}}
      </a>
      </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
  <td class="halfPane">
    <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id=0">{{tr}}CTypeEi.create{{/tr}}</a>
    <form name="edit_type_ei" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_typeEi_aed" />
      <input type="hidden" name="type_ei_id" value="{{$type_ei->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $type_ei->_id}}
          <th class="title modify" colspan="2">{{tr}}CTypeEi.modify{{/tr}} {{$type_ei->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CTypeEi.create{{/tr}}</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$type_ei field="name"}}</th>
          <td>{{mb_field object=$type_ei size=30 field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$type_ei field="concerne"}}</th>
          <td>{{mb_field object=$type_ei field="concerne"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$type_ei field="desc"}}</th>
          <td>{{mb_field object=$type_ei field="desc"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $type_ei->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$type_ei->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>        
      </table>
    </form>
  </td>
</tr>
</table>