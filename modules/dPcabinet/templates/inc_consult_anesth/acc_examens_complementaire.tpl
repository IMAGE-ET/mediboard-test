<script language="Javascript" type="text/javascript">
function calculClairance(){
  var oForm1 = document.editExamCompFrm;
  var oForm2 = document.editAnesthPatFrm; 
   if({{if $patient->_age && $patient->_age!="??" && $patient->_age>=18 && $patient->_age<=110}}1{{else}}0{{/if}}
      && oForm2.poid.value && !isNaN(parseFloat(oForm2.poid.value)) && parseFloat(oForm2.poid.value)>0
      && oForm1.creatinine.value && !isNaN(parseFloat(oForm1.creatinine.value)) && parseFloat(oForm1.creatinine.value)>0
      && parseFloat(oForm2.poid.value)>=35 && parseFloat(oForm2.poid.value)<=120
      && parseFloat(oForm1.creatinine.value)>=6 && parseFloat(oForm1.creatinine.value)<=70
      ){
     
     oForm1._clairance.value = Math.round(({{if $patient->sexe!="m"}}0.85*{{/if}}parseFloat(oForm2.poid.value)*(140-{{if $patient->_age!="??"}}{{$patient->_age}}{{else}}0{{/if}})/(parseFloat(oForm1.creatinine.value)*7.2))*100)/100;
   }else{
     oForm1._clairance.value = "";
   }
}
function calculPSA(){
  var oForm1 = document.editExamCompFrm;
  var oForm2 = document.editAnesthPatFrm;
  if(oForm2._vst.value && !isNaN(parseFloat(oForm2._vst.value))
     && oForm1.ht.value && !isNaN(parseFloat(oForm1.ht.value)) && parseFloat(oForm1.ht.value)>0
     && oForm1.ht_final.value && !isNaN(parseFloat(oForm1.ht_final.value)) && parseFloat(oForm1.ht_final.value)>0){
    
    oForm1._psa.value = Math.round(parseFloat(oForm2._vst.value)* (parseFloat(oForm1.ht.value) - parseFloat(oForm1.ht_final.value))/100);
  }else{
    oForm1._psa.value = "";
  }
}

function delExamComp(oForm){
  oForm.del.value = "1";
  submitExamComp(oForm);
}

function modifEtatExamComp(oForm){
  if(oForm.fait.value==1){
    oForm.fait.value = 0;
  }else{
    oForm.fait.value = 1;  
  }
  submitExamComp(oForm);
}

function submitExamComp(oForm) {
  if(oForm.examen){
    var examen = oForm.examen.value;
    var realisation = oForm.realisation.value;
  }
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListExamComp});
  oForm.reset();
  if(oForm.examen){
    oForm._hidden_examen.value = examen;
    oForm.realisation.value = realisation;
  }
}

function reloadListExamComp() {
  var UrllistExamComp= new Url;
  UrllistExamComp.setModuleAction("dPcabinet", "httpreq_vw_list_exam_comp");
  UrllistExamComp.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  UrllistExamComp.requestUpdate('listExamComp', { waitingText : null});
}
</script>

<table class="form">
  <tr>
    <td class="text">
      <form name="addExamCompFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_examcomp_aed" />
      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      <table class="form">
        <tr>
          <td><strong>Ajouter un examen complémentaire</strong></td>
          <td>
            {{mb_label object=$examComp field="examen"}}
            <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
                {{html_options options=$examComp->_aides.examen.no_enum}}
              </select>
              <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CExamComp', this.form._hidden_examen, 'examen')">{{tr}}New{{/tr}}</button>
          </td>
        </tr>
        <tr>
          <td>
            {{html_options name="realisation" options=$examComp->_enumsTrans.realisation}}
          </td>
          <td>
            <input type="hidden" name="_hidden_examen" value="" />
            <textarea name="examen" onblur="if(verifNonEmpty(this)){submitExamComp(this.form);}"></textarea>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.examen)){submitExamComp(this.form);}">Ajouter</button>
          </td>
        </tr>
      </table>      
      </form>
    </td>
    <td class="text" rowspan="2">
      <div id="listExamComp">
      {{include file="../../dPcabinet/templates/exam_comp.tpl"}}
      </div>
      
      <table class="form">
			  <!-- Documents ExamComp -->
			  <tr>
			    <th class="category">Documents</th>
			  </tr>
			  <tr>
			    <td>
			      {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
			      {{include file=../../dPcompteRendu/templates/inc_widget_documents.tpl praticien_id=$consult->_praticien_id suffixe=exam}}
			    
			      {{if $dPconfig.dPcabinet.CPrescription.view_prescription}}
            <hr />
            <div id="viewPrescriptionSejour">
            {{if $consult->_ref_consult_anesth->operation_id}}
              {{assign var=sejour value=$consult->_ref_consult_anesth->_ref_operation->_ref_sejour}}
              {{include file="../../dPprescription/templates/inc_widget_prescription.tpl" 
				      					totals_by_chapitre = $sejour->_totals_by_chapitre
                        prescriptions=$sejour->_ref_prescriptions
                        object_id=$sejour->_id 
                        object_class="CSejour" 
                        praticien_id=$app->user_id
                        suffixe=exam}}
            {{/if}}
            </div>
            {{/if}}
			    </td>
			  </tr>
      </table>
      
    </td>
  </tr>
  <tr>
    <td>
      <form name="editExamCompFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
      <table class="form">
        <tr>
          <th colspan="4" class="category">Résultats d'analyse</th>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="groupe"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="groupe" tabindex="101" onchange="submitForm(this.form)"}}
            /
            {{mb_field object=$consult_anesth field="rhesus" tabindex="102" onchange="submitForm(this.form)"}}
          </td>
          <th>{{mb_label object=$consult_anesth field="creatinine"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="creatinine" tabindex="108" size="4" onchange="calculClairance();submitForm(this.form);"}}
            mg/l
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="rai"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="rai" tabindex="103" onchange="submitForm(this.form)"}}
          </td>
          <th>{{mb_label object=$consult_anesth field="_clairance"}}</th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_clairance"  size="4" readonly="readonly"}}
            ml/min
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="hb"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="hb" tabindex="104" size="4" onchange="submitForm(this.form)"}}
            g/dl
          </td>
          <th>{{mb_label object=$consult_anesth field="na"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="na" tabindex="109" size="4" onchange="submitForm(this.form)"}}
            mmol/l
          </td>
        </tr>
        <tr> 
          <th>{{mb_label object=$consult_anesth field="ht"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="ht" tabindex="105" size="4" onchange="calculPSA();submitForm(this.form);"}}
            %
          </td>
          <th>{{mb_label object=$consult_anesth field="k"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="k" tabindex="110" size="4" onchange="submitForm(this.form)"}}
            mmol/l
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="ht_final"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="ht_final" tabindex="106" size="4" onchange="calculPSA();submitForm(this.form);"}}
            %
          </td>
          <th>{{mb_label object=$consult_anesth field="tp"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="tp" tabindex="111" size="4" onchange="submitForm(this.form)"}}
            %
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="_psa"}}</th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_psa"  size="4" readonly="readonly"}}
            ml/GR
          </td>
          <th>{{mb_label object=$consult_anesth field="tca" defaultFor="tca_temoin"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="tca_temoin" tabindex="112" maxlength="2" size="2" onchange="submitForm(this.form)"}}
            s /
            {{mb_field object=$consult_anesth field="tca" tabindex="113" maxlength="2" size="2" onchange="submitForm(this.form)"}}
             s
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="plaquettes"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="plaquettes" tabindex="107" size="6" onchange="submitForm(this.form)"}} (x1000) /mm3
          </td>
          <th>{{mb_label object=$consult_anesth field="tsivy" defaultFor="_min_tsivy"}}</th>
          <td>
            {{html_options tabindex="114" name="_min_tsivy" values=$mins output=$mins selected=$consult_anesth->_min_tsivy onchange="submitForm(this.form)"}}
            min
            {{html_options tabindex="115" name="_sec_tsivy" values=$secs output=$secs selected=$consult_anesth->_sec_tsivy onchange="submitForm(this.form)"}}
            s
          </td>
        </tr>
        <tr>
          <td colspan="2"></td>
          <th>{{mb_label object=$consult_anesth field="ecbu"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="ecbu" tabindex="116" onchange="submitForm(this.form)"}}
          </td>
        </tr>
      </table>    
      </form>
    </td>
  </tr>
</table>