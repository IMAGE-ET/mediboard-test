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
     
     oForm1._clairance.value = round({{if $patient->sexe!="m"}}0.85*{{/if}}parseFloat(oForm2.poid.value)*(140-{{if $patient->_age!="??"}}{{$patient->_age}}{{else}}0{{/if}})/(parseFloat(oForm1.creatinine.value)*7.2),2);
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
    
    oForm1._psa.value = round(parseFloat(oForm2._vst.value)* (parseFloat(oForm1.ht.value) - parseFloat(oForm1.ht_final.value))/100,0);
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
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListExamComp});
  oForm.reset();
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
      <form name="editexamcompFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_examcomp_aed" />
      <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
      <label for="examen" title="Ajouter un examen complementaire">Examen Complémentaire</label>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this)">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$examComp->_aides.examen}}
      </select><br />
      <textarea name="examen" onblur="if(this.value!=''){submitExamComp(this.form);}"></textarea>
      <button class="submit" type="button" onclick="if(this.form.examen.value!=''){submitExamComp(this.form);}">Ajouter</button>
      </form>
    </td>
    <td class="text" id="listExamComp" rowspan="2">
      {{include file="exam_comp.tpl"}}
    </td>
  </tr>
  <tr>
    <td>
      <form name="editExamCompFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <table class="form">
        <tr>
          <th><label for="groupe" title="Groupe sanguin">Groupe</label></th>
          <td>
            {{if $consult_anesth->groupe}}
            {{assign var="selected" value=$consult_anesth->groupe}}
            {{else}}
            {{assign var="selected" value=$consult_anesth->_enums.groupe.0}}
            {{/if}}
            {{html_options tabindex="101" name="groupe" title=$consult_anesth->_props.groupe options=$consult_anesth->_enumsTrans.groupe selected=$selected onchange="submitForm(this.form)"}}
            /
            {{if $consult_anesth->rhesus}}
            {{assign var="selected" value=$consult_anesth->rhesus}}
            {{else}}
            {{assign var="selected" value=$consult_anesth->_enums.rhesus.0}}
            {{/if}}
            {{html_options tabindex="102"  name="rhesus" title=$consult_anesth->_props.rhesus options=$consult_anesth->_enumsTrans.rhesus selected=$selected onchange="submitForm(this.form)"}}
          </td>
          <th><label for="creatinine" title="Créatinine">Créatinine</label></th>
          <td>
            <input tabindex="108" type="text" size="4" name="creatinine" title="{{$consult_anesth->_props.creatinine}}" value="{{$consult_anesth->creatinine}}" onchange="calculClairance();submitForm(this.form);" />
            mg/l
          </td>
        </tr>
        <tr>
          <th><label for="rai" title="Recherche d'agglutinines irrégulières">RAI</label></th>
          <td>
            {{if $consult_anesth->rai}}
            {{assign var="selected" value=$consult_anesth->rai}}
            {{else}}
            {{assign var="selected" value=$consult_anesth->_enums.rai.0}}
            {{/if}}
            {{html_options tabindex="103" name="rai" title=$consult_anesth->_props.rai options=$consult_anesth->_enumsTrans.rai selected=$selected onchange="submitForm(this.form)"}}
          </td>
          <th><label for="_clairance" title="Clairance Créatinine">Clairance</label></th>
          <td class="readonly">
            <input type="text" size="4" name="_clairance" value="{{$consult_anesth->_clairance}}" readonly="readonly" />
            ml/min
          </td>
        </tr>
        <tr>
          <th><label for="hb" title="Hb">Hb</label></th>
          <td>
            <input tabindex="104" type="text" size="4" name="hb" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.hb}}" value="{{$consult_anesth->hb}}" />
            g/dl
          </td>
          <th><label for="na" title="Na+">Na+</label></th>
          <td>
            <input tabindex="109" type="text" size="4" name="na" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.na}}" value="{{$consult_anesth->na}}" />
            mmol/l
          </td>
        </tr>
        <tr> 
          <th><label for="ht" title="Hématocrite">Ht</label></th>
          <td>
            <input tabindex="105" type="text" size="4" name="ht" title="{{$consult_anesth->_props.ht}}" value="{{$consult_anesth->ht}}" onchange="calculPSA();submitForm(this.form);" />
            %
          </td>
          <th><label for="k" title="K+">K+</label></th>
          <td>
            <input tabindex="110" type="text" size="4" name="k" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.k}}" value="{{$consult_anesth->k}}" />
            mmol/l
          </td>
        </tr>
        <tr>
          <th><label for="ht_final" title="Hématocrite finale">Ht final</label></th>
          <td>
            <input tabindex="106" type="text" size="4" name="ht_final" title="{{$consult_anesth->_props.ht_final}}" value="{{$consult_anesth->ht_final}}" onchange="calculPSA();submitForm(this.form);" />
            %
          </td>
          <th><label for="tp" title="Taux de prothrombine">TP</label></th>
          <td>
            <input tabindex="111" type="text" size="4" name="tp" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.tp}}" value="{{$consult_anesth->tp}}" />
            %
          </td>
        </tr>
        <tr>
          <th><label for="_psa" title="Pertes Sanguines Acceptables">PSA</label></th>
          <td class="readonly">
            <input type="text" size="4" name="_psa" value="{{$consult_anesth->_psa}}" readonly="readonly" />
            ml/GR
          </td>
          <th><label for="tca" title="Temps de Céphaline avec Activateur">TCA</label></th>
          <td>
            <input tabindex="112" type="text" name="tca_temoin" maxlength="2" size="2" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.tca_temoin}}" value="{{$consult_anesth->tca_temoin}}" />
            s /
            <input tabindex="113" type="text" name="tca" maxlength="2" size="2" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.tca}}" value="{{$consult_anesth->tca}}" />
             s
          </td>
        </tr>
        <tr>
          <th><label for="plaquettes" title="Plaquettes">Plaquettes</label></th>
          <td>
            <input tabindex="107" type="text" size="6" name="plaquettes" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.plaquettes}}" value="{{$consult_anesth->plaquettes}}" />
          </td>
          <th><label for="tsivy" title="Temps de saignement par la méthode d'Ivy">TS Ivy</label></th>
          <td>
            {{html_options tabindex="114" name="_min_tsivy" values=$mins output=$mins selected=$consult_anesth->_min_tsivy onchange="submitForm(this.form)"}}
            min
            {{html_options tabindex="115" name="_sec_tsivy" values=$secs output=$secs selected=$consult_anesth->_sec_tsivy onchange="submitForm(this.form)"}}
            s
          </td>
        </tr>
        <tr>
          <td colspan="2"></td>
          <th><label for="ecbu" title="Examen Cytobactériologique des Urines">ECBU</label></th>
          <td>
            {{if $consult_anesth->ecbu}}
            {{assign var="selected" value=$consult_anesth->ecbu}}
            {{else}}
            {{assign var="selected" value=$consult_anesth->_enums.ecbu.0}}
            {{/if}}
            {{html_options tabindex="116" name="ecbu" title=$consult_anesth->_props.ecbu options=$consult_anesth->_enumsTrans.ecbu selected=$selected onchange="submitForm(this.form)"}}
          </td>
        </tr>
      </table>    
      </form>
    </td>
  </tr>
</table>