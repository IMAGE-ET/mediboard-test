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
      <textarea name="examen"></textarea>
      <button class="submit" type="button" onclick="submitExamComp(this.form);">Ajouter</button>
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
            <select name="groupe" title="{{$consult_anesth->_props.groupe}}" onchange="submitForm(this.form)">
              {{html_options values=$consult_anesth->_enums.groupe output=$consult_anesth->_enums.groupe selected=$consult_anesth->groupe}}
            </select>
            /
            <select name="rhesus" title="{{$consult_anesth->_props.rhesus}}" onchange="submitForm(this.form)">
              <option value="?" {{if $consult_anesth->rhesus == "?"}}selected="selected"{{/if}}>?</option>
              <option value="NEG" {{if $consult_anesth->rhesus == "NEG"}}selected="selected"{{/if}}>NEG</option>
              <option value="POS" {{if $consult_anesth->rhesus == "POS"}}selected="selected"{{/if}}>POS</option>
            </select>
          </td>
          <th><label for="ht" title="Hématocrite">Ht</label></th>
          <td>
            <input type="text" size="4" name="ht" title="{{$consult_anesth->_props.ht}}" value="{{$consult_anesth->ht}}" onchange="calculPSA();submitForm(this.form);" />
            %
          </td>
        </tr>
        <tr>
          <th><label for="creatinine" title="Créatinine">Créatinine</label></th>
          <td>
            <input type="text" size="4" name="creatinine" title="{{$consult_anesth->_props.creatinine}}" value="{{$consult_anesth->creatinine}}" onchange="calculClairance();submitForm(this.form);" />
            mg/l
          </td>
          <th><label for="ht_final" title="Hématocrite">Ht final</label></th>
          <td>
            <input type="text" size="4" name="ht_final" title="{{$consult_anesth->_props.ht_final}}" value="{{$consult_anesth->ht_final}}" onchange="calculPSA();submitForm(this.form);" />
            %
          </td>
        </tr>
        <tr>
          <th><label for="_clairance" title="Clairance Créatinine">Clairance</label></th>
          <td class="readonly">
            <input type="text" size="4" name="_clairance" value="{{$consult_anesth->_clairance}}" readonly="readonly" />
            ml/min
          </td>
          <th><label for="_psa" title="Pertes Sanguines Acceptables">PSA</label></th>
          <td class="readonly">
            <input type="text" size="4" name="_psa" value="{{$consult_anesth->_psa}}" readonly="readonly" />
            ml/GR
          </td>
        </tr>
        <tr> 
          <th><label for="hb" title="Hb">Hb</label></th>
          <td>
            <input type="text" size="4" name="hb" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.hb}}" value="{{$consult_anesth->hb}}" />
            g/dl
          </td>
          <th><label for="tp" title="Taux de prothrombine">TP</label></th>
          <td>
            <input type="text" size="4" name="tp" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.tp}}" value="{{$consult_anesth->tp}}" />
            %
          </td>
        </tr>
        <tr>
          <th><label for="na" title="Na+">Na+</label></th>
          <td>
            <input type="text" size="4" name="na" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.na}}" value="{{$consult_anesth->na}}" />
            mmol/l
          </td>
          <th><label for="tca" title="Temps de Céphaline avec Activateur">TCA</label></th>
          <td>
            <input type="text" name="tca_temoin" maxlength="2" size="2" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.tca_temoin}}" value="{{$consult_anesth->tca_temoin}}" />
            s /
            <input type="text" name="tca" maxlength="2" size="2" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.tca}}" value="{{$consult_anesth->tca}}" />
             s
          </td>
        </tr>
        <tr>
          <th><label for="k" title="K+">K+</label></th>
          <td>
            <input type="text" size="4" name="k" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.k}}" value="{{$consult_anesth->k}}" />
            mmol/l
          </td>
          <th><label for="tsivy" title="Temps de saignement par la méthode d'Ivy">TS Ivy</label></th>
          <td>
            <select name="_min_tsivy" onchange="submitForm(this.form)">
            {{foreach from=$mins item=minute}}
              <option value="{{$minute}}" {{if $consult_anesth->_min_tsivy == $minute}} selected="selected" {{/if}}>{{$minute}}</option>
            {{/foreach}}
            </select> min
            <select name="_sec_tsivy" onchange="submitForm(this.form)">
            {{foreach from=$secs item=seconde}}
              <option value="{{$seconde}}" {{if $consult_anesth->_sec_tsivy == $seconde}} selected="selected" {{/if}}>{{$seconde}}</option>
            {{/foreach}}     
            </select> s
          </td>
        </tr>
        <tr>
          <th><label for="plaquettes" title="Plaquettes">Plaquettes</label></th>
          <td>
            <input type="text" size="6" name="plaquettes" onchange="submitForm(this.form)" title="{{$consult_anesth->_props.plaquettes}}" value="{{$consult_anesth->plaquettes}}" />
          </td>
          <th><label for="ecbu" title="Examen Cytobactériologique des Urines">ECBU</label></th>
          <td>
            <select name="ecbu" title="{{$consult_anesth->_props.ecbu}}" onchange="submitForm(this.form)">
            {{html_options values=$consult_anesth->_enums.ecbu output=$consult_anesth->_enums.ecbu selected=$consult_anesth->ecbu}}
            </select>            
          </td>
        </tr>
        <tr>
          <th><label for="rai" title="Recherche d'agglutinines irrégulières">RAI</label></th>
          <td>
            <select name="rai" title="{{$consult_anesth->_props.rai}}" onchange="submitForm(this.form)">
            {{html_options values=$consult_anesth->_enums.rai output=$consult_anesth->_enums.rai selected=$consult_anesth->rai}}
            </select>
          </td>
          <td colspan="2"></td>
        </tr>
      </table>    
      </form>
    </td>
  </tr>
</table>