{{main}}
ExFormula.tokens = {{$field_names|@json}};
{{/main}}

<form name="editGroupFormula-{{$ex_group->_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="@class" value="{{$ex_group->_class_name}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_group}}
  
  <table class="main form">
    <tr>
      <th class="category">Formule</th>
    </tr>
    <tr>
      <td>
        <div class="small-info">
          Insérez les champs avec les boutons <button class="right notext" type="button"></button>.
        </div>
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$ex_group field=formula_result_field_id}}
        
        <select name="formula_result_field_id" class="{{$ex_group->_props.formula_result_field_id}}" style="max-width: 20em;">
          <option value=""> &ndash; Choisir un champ </option>
          {{foreach from=$result_fields item=_field}}
            <option value="{{$_field->_id}}" {{if $ex_group->formula_result_field_id == $_field->_id}} selected="selected" {{/if}}>
              {{$_field->_locale}}
            </option>
          {{/foreach}}
        </select>

        <button class="sum" type="button" onclick="ExFormula.sumAllFields()">Somme de tous les champs</button>

        {{mb_field object=$ex_group field=_formula}}
      </td>
    </tr>
    <tr>
      <td>
        <div class="small-warning" id="formula-unknown-fields" style="display: none;">
          Certains champs ne sont pas reconnus: <strong></strong>
        </div>
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>