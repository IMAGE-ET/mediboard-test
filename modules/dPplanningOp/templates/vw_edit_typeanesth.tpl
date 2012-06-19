<table class="main">
  <tr>
    <!-- Liste des types d'anesthésie -->
    <td class="halfPane" colspan="2">
      <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_typeanesth&amp;type_anesth_id=0">
        {{tr}}CTypeAnesth.create{{/tr}}
      </a>
      <table class="tbl">
        <tr>
          <th>{{mb_title object=$type_anesth field=name}}</th>
          <th>{{tr}}CTypeAnesth-back-operations{{/tr}}</th>
          <th>{{mb_title object=$type_anesth field=ext_doc}}</th>
          <th>{{mb_title object=$type_anesth field=actif}}</th>
        </tr>
        {{foreach from=$types_anesth item=_type_anesth}}
        <tr>
          <td class="text">
            <a href="?m={{$m}}&amp;tab=vw_edit_typeanesth&amp;type_anesth_id={{$_type_anesth->_id}}" title="{{tr}}CTypeAnesth-modify{{/tr}}">
              {{$_type_anesth->name}}
            </a>
          </td>
          <td>
            {{$_type_anesth->_count_operations}}
          </td>
          <td class="text {{if !$_type_anesth->ext_doc}} empty {{/if}}">
						{{mb_value object=$_type_anesth field=ext_doc}}
          </td>
          <td>{{mb_value object=$_type_anesth field=actif}}</td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>

    <!-- Formulaire d'anesthésie -->
    <td class="halfPane">
      <form name="editType" action="?m={{$m}}&amp;tab=vw_edit_typeanesth" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_typeanesth_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$type_anesth}}

      <table class="form">
        {{mb_include module=system template=inc_form_table_header object=$type_anesth}}
        <tr>
          <th>{{mb_label object=$type_anesth field="name"}}</th>
          <td>{{mb_field object=$type_anesth field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$type_anesth field="ext_doc"}}</th>
          <td>{{mb_field object=$type_anesth field="ext_doc" emptyLabel="Choose"}}</td>
        </tr>  
        <tr>
          <th>{{mb_label object=$type_anesth field="actif"}}</th>
          <td>{{mb_field object=$type_anesth field="actif"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $type_anesth->_id}}
              <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{objName:'{{$type_anesth->name|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
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