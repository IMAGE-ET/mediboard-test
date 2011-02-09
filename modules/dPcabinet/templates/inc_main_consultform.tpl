{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

Main.add(function () {
  
   // UpdateFields de l'autocomplete des traitements
  updateFieldTraitement = function(selected) {
    var dn = selected.childElements();
    oForm = getForm('editFrmExams');
    $V(oForm.traitement, $V(oForm.traitement)+dn[3].innerHTML.stripTags().strip()+'\n');
    $V(oForm.produit, "");
  }

  // Autocomplete des medicaments
  if(getForm('editFrmExams').produit) {
    var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
    urlAuto.autoComplete(getForm('editFrmExams').produit, "_traitement_auto_complete", {
      minChars: 3,
      updateElement: updateFieldTraitement, 
      callback: function(input, queryString){
        return (queryString + "&produit_max=40"); 
      }
    } );
  }
});

</script>

{{assign var=aide_autocomplete value=$conf.dPcabinet.CConsultation.aide_autocomplete}}
{{if !@$readonly}}
  {{assign var=readonly value=0}}
{{/if}}

<table class="form">
  <tr>
    <td>
      <!-- Fiches d'examens -->
      {{mb_include_script module="dPcabinet" script="exam_dialog"}}
      
      <script type="text/javascript">
        {{if !$readonly}}
          ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
        {{/if}}
      
        onExamComplete = function(){
          FormObserver.changes = 0;
        }
      </script>
      
      {{if $consult->_id}}
      <form name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: onExamComplete})">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_key object=$consult}}
      
      <div class="small-info text">
        <strong>Amélioration des aides à la saisie</strong><br />
        Les aides à la saisie ont évolué pour vous en simplifier l'utilisation.<br />
        Pour en savoir plus, <a href="#1" onclick="(new Url('dPcompteRendu', 'vw_aides_saisie_help')).requestModal(700, 600);">cliquez ici</a>
      </div>
      
      {{assign var=exam_count value=$consult->_exam_fields|@count}}
      {{math assign=text_rows equation="12/round(c/2)" c=$exam_count}}
      
      <table class="layout main">
        {{foreach name=exam_fields from=$consult->_exam_fields key=current item=field}}
        {{assign var=last value=$smarty.foreach.exam_fields.last}}
        
        {{if !$last && $current mod 2 == 0}}
        <tr>
          <td class="halfPane">
        {{elseif $current mod 2 == 1}}
          <td class="halfPane">
        {{else}}
        <tr>
          <td colspan="2">
        {{/if}}
          {{* Beginning *}}
        
          {{if !$readonly}}
          <script type="text/javascript">
            Main.add(function() {
              new AideSaisie.AutoComplete(getForm("editFrmExams").elements.{{$field}}, {
                objectClass: "{{$consult->_class_name}}",
                timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
                validateOnBlur: 0
              });
            });
          </script>
          {{/if}}
          <fieldset>
            <legend>
              {{mb_label object=$consult field=$field}}
              {{if $field == "traitement" && $isPrescriptionInstalled}}
                <input type="text" name="produit" value="" size="12" class="autocomplete" style="margin-top: -3px; margin-bottom: -2px;" />
                <div style="display:none; width: 350px;" class="autocomplete" id="_traitement_auto_complete"></div>
              {{/if}}
            </legend>
            {{if $readonly}}
              {{mb_value object=$consult field=$field}}
            {{else}}
              {{mb_field object=$consult field=$field rows=$text_rows onchange="this.form.onsubmit()"}}
            {{/if}}
          </fieldset>
        {{* End *}}
        {{if !$last && $current mod 2 == 0}}
          </td>
        {{elseif $current mod 2 == 1}}
          </td>
        </tr>
        {{else}}
          </td>
        </tr>
        {{/if}}
      {{/foreach}}
      </table>
      </form>
      
      {{else}}
      <div class="small-info">Consultation non réalisée</div>
      {{/if}}
    </td>
  </tr>
</table>