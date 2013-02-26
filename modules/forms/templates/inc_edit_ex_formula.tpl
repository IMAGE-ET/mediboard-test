{{if $formula_possible}}

{{main}}
ExFormula.tokens = {{$field_names|@json}};
{{/main}}

<form name="editFieldFormula-{{$ex_field->_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="@class" value="{{$ex_field->_class}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_field}}
  
  <table class="main form">
    <tr>
      <td>
        <div class="small-info">
          Insérez les champs avec les boutons <button class="right notext" type="button"></button>.
          <br />
          <button type="button" class="help" onclick="(new Url('forms', 'view_doc_formula')).popup(800,800)">Aide sur les formules</button>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        {{assign var=_spec_type value=$ex_field->_spec_object->getSpecType()}}
        
        {{if "CExClassField::formulaCanArithmetic"|static_call:$_spec_type}}
          <select onchange="ExFormula.insertText($V(this)); this.selectedIndex = 0;">
            <option value=""> &ndash; Date définie </option>
            {{foreach from="CExClassField"|static:_formula_constants item=_const}}
              <option value="{{$_const}}">{{$_const}}</option>
            {{/foreach}}
          </select>
          
          <select onchange="ExFormula.insertText($V(this)); this.selectedIndex = 0;">
            <option value=""> &ndash; Fonction de dates </option>
            {{foreach from="CExClassField"|static:_formula_intervals key=_const item=_view}}
              <option value="{{$_const}}( ¤ )">{{$_view}}</option>
            {{/foreach}}
          </select>

          <label>
            {{mb_field object=$ex_field field=result_in_title typeEnum=checkbox}}
            {{tr}}CExClassField-result_in_title{{/tr}}
          </label>
        {{/if}}
        
        {{* <button class="sum" type="button" onclick="ExFormula.sumAllFields()">Somme de tous les champs</button> *}}
        {{mb_field object=$ex_field field=_formula}}
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

{{else}}
  <em>Formule pour le moment indisponible pour ce type de champ</em>
{{/if}}