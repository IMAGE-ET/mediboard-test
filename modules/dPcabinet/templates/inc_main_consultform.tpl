{{if "dPmedicament"|module_active}}
{{mb_script module="medicament" script="medicament_selector"}}
{{/if}}
<script type="text/javascript">

Main.add(function () {
  
   // UpdateFields de l'autocomplete des traitements
  updateFieldTraitement = function(selected) {
    var dn = selected.childElements();
    var oForm = getForm('editFrmExams');
    $V(oForm.traitement, $V(oForm.traitement)+dn[3].innerHTML.stripTags().strip()+'\n');
    $V(oForm.produit, "");
  };

  // Autocomplete des medicaments
  var oForm = getForm('editFrmExams');
  if(oForm && oForm.produit) {
    var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
    urlAuto.autoComplete(oForm.produit, "_traitement_auto_complete", {
      minChars: 3,
      updateElement: updateFieldTraitement, 
      callback: function(input, queryString){
        return (queryString + "&produit_max=40"); 
      }
    } );
  }
});

</script>

{{mb_default var=readonly value=0}}
{{mb_default var=isPrescriptionInstalled value=isset($modules.dPprescription|smarty:nodefaults)}}

<table class="form">
  <tr>
    <td>
    	<table class="main layout">
        <tr>
          <td style="width: 50%;">
			      <!-- Fiches d'examens -->
			      {{mb_script module="cabinet" script="exam_dialog"}}
			      <script type="text/javascript">
			        {{if !$readonly}}
			          ExamDialog.register('{{$consult->_id}}');
			        {{/if}}
			      
			        onExamComplete = function(){
			          FormObserver.changes = 0;
			        }
			      </script>
			    </td>
          
          {{if "forms"|module_active}}
            <td>
              {{unique_id var=unique_id_exam_forms}}
              
              <script type="text/javascript">
                Main.add(function(){
                  ExObject.loadExObjects("{{$consult->_class}}", "{{$consult->_id}}", "{{$unique_id_exam_forms}}", 0.5);
                });
              </script>
              
              <fieldset id="list-ex_objects">
                <legend>Formulaires</legend>
                <div id="{{$unique_id_exam_forms}}"></div>
              </fieldset>
            </td>
          {{/if}}
        </tr>
      </table>
      
      {{if $consult->_id}}
      <form name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: onExamComplete})">
      <input type="hidden" name="m" value="cabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_key object=$consult}}
      
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
        
          <fieldset>
            <legend>
              {{mb_label object=$consult field=$field}}
              {{if $field == "traitement" && $isPrescriptionInstalled && !$readonly}}
                <input type="text" name="produit" value="" size="12" class="autocomplete" style="margin-top: -3px; margin-bottom: -2px;" />
                <div style="display:none; width: 350px;" class="autocomplete" id="_traitement_auto_complete"></div>
              {{/if}}
            </legend>
            {{if $readonly}}
              {{mb_value object=$consult field=$field}}
            {{else}}
              {{mb_field object=$consult field=$field rows=$text_rows onchange="this.form.onsubmit()" form="editFrmExams"
                aidesaisie="validateOnBlur: 0"}}
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