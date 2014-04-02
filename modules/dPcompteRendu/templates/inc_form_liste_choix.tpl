<!--  $Id: vw_idx_listes.tpl 12241 2011-05-20 10:29:53Z flaviencrochard $ -->

<form name="Edit" method="post" class="{{$liste->_spec}}" onsubmit="return ListeChoix.onSubmit(this)">
  {{mb_class object=$liste}}
  {{mb_key   object=$liste}}
  <input type="hidden" name="del"    value="0" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$liste}}
  
    <tr>
      <th>{{mb_label object=$liste field=user_id}}</th>
      <td>
        <select name="user_id" class="{{$liste->_props.user_id}}" style="width: 12em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$liste->user_id}}
        </select>
      </td>
    </tr>

    {{if $access_function}}
    <tr>
      <th>{{mb_label object=$liste field=function_id}}</th>
      <td>
        <select name="function_id" class="{{$liste->_props.function_id}}" style="width: 12em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_function list=$funcs selected=$liste->function_id}}
        </select>
      </td>
    </tr>
    {{/if}}

    {{if $access_group}}
    <tr>
      <th>{{mb_label object=$liste field=group_id}}</th>
      <td>
        <select name="group_id" class="{{$liste->_props.group_id}}" style="width: 12em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$etabs item=curr_etab}}
            <option value="{{$curr_etab->_id}}" {{if $curr_etab->_id == $liste->group_id}} selected="selected" {{/if}}>
              {{$curr_etab->_view}}
            </option>
         {{/foreach}}
        </select>
      </td>
    </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$liste field=nom}}</th>
      <td>{{mb_field object=$liste field=nom}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$liste field=compte_rendu_id}}</th>
      <td>
        <select name="compte_rendu_id" style="width: 20em;">
          <option value="">&mdash; {{tr}}All{{/tr}}</option>
          
          {{foreach from=$modeles key=owner item=_modeles}}
          <optgroup label="{{$owners.$owner}}">
            {{foreach from=$_modeles item=_modele}}
            <option value="{{$_modele->_id}}" {{if $liste->compte_rendu_id == $_modele->_id}} selected="selected" {{/if}}>
              [{{tr}}{{$_modele->object_class}}{{/tr}}] {{$_modele->nom}} 
            </option>
            {{foreachelse}}
            <option disabled="disabled">{{tr}}None{{/tr}}</option>
            {{/foreach}}
          </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $liste->_id}}
        <button id="inc_form_list_choix_button_save" class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="ListeChoix.confirmDeletion(this)">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button id="inc_form_list_choix_button_create" class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
  