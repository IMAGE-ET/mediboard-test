{{if $formula_possible}}

{{main}}
ExFormula.tokens = {{$field_names|@json}};
{{/main}}

<form name="editFieldFormula-{{$ex_field->_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="@class" value="{{$ex_field->_class_name}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_field}}
  
  <table class="main form">
    <tr>
      <td>
        <div class="small-info">
          Insérez les champs avec les boutons <button class="right notext" type="button"></button>.
        </div>
      </td>
    </tr>
    <tr>
      <td>
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