<script type="text/javascript">

checkSelect = function(){
  var oForm = getForm("editScoreIGS");
  var score_igs = 0;
  
  oForm.select("input[type=radio]:checked:not(.empty_value)").each(function(oRadio){
    var radio_value = $V(oRadio);
    var checked_value = parseInt(radio_value,10);
    score_igs += checked_value;
    oRadio.up('tr').down('.value').update(checked_value);
  });
  
  $V(oForm.scoreIGS, score_igs);
}

empty_on_click = function(elem) {
  $A(getForm("editScoreIGS").elements[elem]).each(function(radio){
    radio.checked = false;
  });
  
  $("editScoreIGS_"+elem+"_").checked = true;
  
  getForm("editScoreIGS").elements[elem][0].up('tr').down('.value').update('');
  checkSelect();     
}

showLaboResult = function() {
  var url = new Url("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", "{{$sejour->_id}}");
  url.requestModal(800, 700);
}

Main.add(checkSelect);

</script>

<form name="editScoreIGS" method="post" action="?" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshFiches('{{$sejour->_id}}'); Control.Modal.close(); } });">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_exam_igs_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  {{mb_key object=$exam_igs}}
  {{if !$exam_igs->_id}}
    {{mb_field object=$exam_igs field=date value="now" hidden="1"}}
  {{/if}}
  <table class="tbl">
    <tr>
      <th class="title {{if $exam_igs->_id}}modify{{/if}}" colspan="10">
        <button type="button" style="float: right" onclick="showLaboResult();" class="search">Labo</button>
        {{if $exam_igs->_id}}
          <span style="float: left;">
            {{mb_field object=$exam_igs field=date form=editScoreIGS register=true}}
          </span>
          {{mb_include module=system template=inc_object_history object=$exam_igs}}
          {{tr}}{{$exam_igs->_class}}-title-modify{{/tr}} 
          <br />
          '{{$exam_igs}}'
        {{else}}
          {{tr}}{{$exam_igs->_class}}-title-create{{/tr}} 
        {{/if}}
    </tr>
    <tr>
      <th class="category">Paramètre</th>
      <th class="category" colspan="6">Sélection</th>
      <th class="category">Valeur</th>
      {{if !$exam_igs->_id}}
      <th class="category">Dernière<br />constante</th>
      {{/if}}
      <th class="category"></th>
    </tr>
    {{foreach from="CExamIGS"|static:fields item=_field}}
    <tr>
      <th>
        {{mb_label object=$exam_igs field=$_field}}
      </th>
      <td style="width: 80px;">
        {{mb_field object=$exam_igs field=$_field typeEnum="radio" onclick="checkSelect();" separator="</td><td style='width: 80px;'>"}}
         <input type="radio" value="" name="{{$_field}}" id="editScoreIGS_{{$_field}}_" class="empty_value" style="display: none;">
      </td>
      <!-- Calcul du nombre de td à rajouter pour compléter la ligne -->
      {{math equation=6-x x=$exam_igs->_specs.$_field->_list|@count assign=nb_colonne}}
      {{if $nb_colonne}}
        <td colspan="{{$nb_colonne}}" />
      {{/if}}
      <td class="value" style="text-align: center;"></td>
      {{if !$exam_igs->_id}}
      <td style="text-align: center;">
        {{if array_key_exists($_field, $last_constantes)}}
          {{$last_constantes.$_field}}
        {{/if}}
      </td>  
      {{/if}}
      <td>
        <button type='button' class='cancel notext' onclick="empty_on_click('{{$_field}}')"></button>
      </td>
    </tr>
    {{/foreach}}
    <tr>
      <th class="title">
        {{mb_label object=$exam_igs field="scoreIGS"}}
      </th>
      <td colspan="10">
        {{mb_field object=$exam_igs field="scoreIGS" readonly="readonly" style="font-weight: bold; text-align: center; font-size: 1.2em;"}}
        {{if $exam_igs->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form, { ajax:true, typeName:'cet examen IGS'}, {onComplete: function(){ refreshFiches('{{$sejour->_id}}'); Control.Modal.close(); } })">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
       </td>    
    </tr>
  </table>
</form>