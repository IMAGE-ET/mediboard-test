<!--  $Id: vw_idx_listes.tpl 12241 2011-05-20 10:29:53Z flaviencrochard $ -->

    <form name="Edit" action="?m={{$m}}" class="{{$liste->_spec}}" method="post" onsubmit="return ListeChoix.onSubmit(this)">

    <input type="hidden" name="m"      value="{{$m}}" />
    <input type="hidden" name="del"    value="0" />
    <input type="hidden" name="dosql"  value="do_liste_aed" />
    {{mb_key object=$liste}}

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
  
    <tr>
      <th>{{mb_label object=$liste field=function_id}}</th>
      <td>
        <select name="function_id" class="{{$liste->_props.function_id}}" style="width: 12em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_function list=$funcs selected=$liste->function_id}}
        </select>
      </td>
    </tr>
    
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
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="ListeChoix.confirmDeletion(this)">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>
  