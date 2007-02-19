<script type="text/javascript">
function calculImcVst(){
   var oForm = document.editAnesthPatFrm;
   var sImcValeur = "";
   var fImc       = "";
   var fVst       = "";
   if(oForm.poid.value && !isNaN(parseFloat(oForm.poid.value)) && parseFloat(oForm.poid.value)>0){
     fVst = {{if $patient->sexe=="m"}}70{{else}}65{{/if}}*parseFloat(oForm.poid.value);
     if(oForm.taille.value && !isNaN(parseInt(oForm.taille.value)) && parseInt(oForm.taille.value)>0){
       fImc = round(parseFloat(oForm.poid.value) / (parseInt(oForm.taille.value) * parseInt(oForm.taille.value) * 0.0001),2);
       if(fImc < {{if $patient->sexe=="m"}}20{{else}}19{{/if}}){
         sImcValeur = "Maigreur";
       }else if(fImc > {{if $patient->sexe=="m"}}25{{else}}24{{/if}} && fImc <=30){
         sImcValeur = "Surpoids";
       }else if(fImc > 30 && fImc <=40){
         sImcValeur = "Obésité";
       }else if(fImc > 40){
         sImcValeur = "Obésité morbide";
       }
     }
   }
   oForm._vst.value = fVst;
   oForm._imc.value = fImc;
   $('imcValeur').innerHTML = sImcValeur;
   calculPSA(); 
   calculClairance();  
}
</script>

<table class="form">
  <tr>
    <td class="HalfPane">
      <form name="editAnesthPatFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" type="hidden" spec=""}}
      <table class="form">
        <tr>
          <th><label for="poid" title="Poids du patient">Poids</label></th>
          <td>
            {{mb_field object=$consult_anesth field="poid" tabindex="1" size="4" onchange="javascript:calculImcVst();submitForm(this.form);"}}
            kg
          </td>
          <th><label for="tasys" title="Pression arterielle">TA</label></th>
          <td>
            {{mb_field object=$consult_anesth field="tasys" tabindex="3" size="2" onchange="submitForm(this.form);"}}
            /
            {{mb_field object=$consult_anesth field="tadias" tabindex="4" size="2" onchange="submitForm(this.form);"}}
            cm Hg
          </td>
        </tr>
        <tr>
          <th><label for="taille" title="Taille du patient">Taille</label></th>
          <td>
            {{mb_field object=$consult_anesth field="taille" tabindex="2" size="4" onchange="javascript:calculImcVst();submitForm(this.form);"}}
            cm
          </td>
          <th><label for="pouls" title="Pouls du patient">Pouls</label></th>
          <td>
            {{mb_field object=$consult_anesth field="pouls" size="4" tabindex="5" onchange="submitForm(this.form);"}}
            / min
          </td>
        </tr>
        <tr>
          <th><label for="_vst" title="Volume Sanguin Total du patient">VST</label></th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_vst" size="4" type="text" readonly="readonly"}}
            ml
          </td>
          <th><label for="spo2" title="Spo2">Spo2</label></th>
          <td>
            {{mb_field object=$consult_anesth field="spo2" tabindex="6" size="4" onchange="submitForm(this.form);"}}
            %
          </td>
        </tr>
        <tr>
          <th><label for="_imc" title="Indice de Masse Corporel du Patient">IMC</label></th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_imc" size="4" type="text" readonly="readonly"}}
          </td>
          <td id="imcValeur" colspan="2" style="color:#F00;">{{$consult_anesth->_imc_valeur}}</td>
        </tr>
        </table>
      </form>
    </td>
    <td class="HalfPane">
      <form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_field object=$consult field="consultation_id" type="hidden" spec=""}}
      {{mb_field object=$consult field="_check_premiere" type="hidden" spec=""}}
      <label for="examen" title="Bilan de l'examen clinique">Examens</label>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this);this.form.examen.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.examen}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen)"></button><br />
      {{mb_field object=$consult field="examen" onchange="submitFormAjax(this.form, 'systemMsg')"}}<br />
      </form>
    </td>
  </tr>
</table>      
      
{{include file="inc_consult_anesth/intubation.tpl"}}