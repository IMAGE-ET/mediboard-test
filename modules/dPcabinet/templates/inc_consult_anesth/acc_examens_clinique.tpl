<script type="text/javascript">
function calculImcVst(oForm){
   oForm = document.editAnesthPatFrm;
   var sImcValeur = "";
   var fImc       = "";
   var fVst       = "";
   if(oForm.poid.value && !isNaN(parseFloat(oForm.poid.value)) && parseFloat(oForm.poid.value)>0){
     fVst = {{if $patient->sexe=="m"}}70{{else}}65{{/if}}*parseFloat(oForm.poid.value);
     if(oForm.taille.value && !isNaN(parseInt(oForm.taille.value)) && parseInt(oForm.taille.value)>0){
       fImc = Math.round(100 * parseFloat(oForm.poid.value) / (parseInt(oForm.taille.value) * parseInt(oForm.taille.value) * 0.0001))/100; // Math.round(x*100)/100 == round(x, 2)
       if(fImc < 15){
         sImcValeur = "Inanition";
       }else if(fImc < 18.5){
         sImcValeur = "Maigreur";
       }else if(fImc > 40){
         sImcValeur = "Obésité morbide";
       }else if(fImc > 35){
         sImcValeur = "Obésité sévère";
       }else if(fImc > 30){
         sImcValeur = "Obésité modérée";
       }else if(fImc > 25){
         sImcValeur = "Surpoid";
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
    <td>
      <form name="editAnesthPatFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
      <table class="form">
        <tr>
          <th>{{mb_label object=$consult_anesth field="poid"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="poid" tabindex="1" size="4" onchange="javascript:calculImcVst();submitForm(this.form);"}}
            kg
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$consult_anesth field="taille"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="taille" tabindex="2" size="4" onchange="javascript:calculImcVst();submitForm(this.form);"}}
            cm
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$consult_anesth field="_vst"}}</th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_vst" size="4"  readonly="readonly"}}
            ml
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$consult_anesth field="_imc"}}</th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_imc" size="4" readonly="readonly"}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$consult_anesth field="tasys"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="tasys" tabindex="3" size="2" onchange="submitForm(this.form);"}}
            /
            {{mb_field object=$consult_anesth field="tadias" tabindex="4" size="2" onchange="submitForm(this.form);"}}
            cm Hg
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$consult_anesth field="pouls"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="pouls" size="4" tabindex="5" onchange="submitForm(this.form);"}}
            / min
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$consult_anesth field="spo2"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="spo2" tabindex="6" size="4" onchange="submitForm(this.form);"}}
            %
          </td>
        </tr>
        <tr>
          <td id="imcValeur" colspan="2" style="color:#F00;">{{$consult_anesth->_imc_valeur}}</td>
        </tr>
      </table>
      </form>
    </td>
  
    <td class="greedyPane">
      <table class="form">
        <tr>
          <td>
            <form name="editAnesthExamenCardio" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              {{mb_label object=$consult_anesth field="examenCardio"}}
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
              <select name="_helpers_examenCardio" onchange="pasteHelperContent(this); this.form.examenCardio.onchange();">
                <option value="">&mdash; Choisir une aide</option>
                {{html_options options=$consult_anesth->_aides.examenCardio.no_enum}}
              </select>
              <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.examenCardio)">{{tr}}New{{/tr}}</button>
              <br />
              {{mb_field object=$consult_anesth field="examenCardio" onchange="submitFormAjax(this.form, 'systemMsg')"}}
            </form>
          </td>
        </tr>
      </table>

      <table class="form">
        <tr>
          <td>
            <form name="editAnesthExamenPulmo" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              {{mb_label object=$consult_anesth field="examenPulmo"}}
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
              <select name="_helpers_examenPulmo" onchange="pasteHelperContent(this); this.form.examenPulmo.onchange();">
                <option value="">&mdash; Choisir une aide</option>
                {{html_options options=$consult_anesth->_aides.examenPulmo.no_enum}}
              </select>
              <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.examenPulmo)">{{tr}}New{{/tr}}</button>
              <br />
              {{mb_field object=$consult_anesth field="examenPulmo" onchange="submitFormAjax(this.form, 'systemMsg')"}}
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <td >
            <form name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
            {{mb_label object=$consult field="examen"}}
            <select name="_helpers_examen" onchange="pasteHelperContent(this); this.form.examen.onchange();">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$consult->_aides.examen.no_enum}}
            </select>
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen)">{{tr}}New{{/tr}}</button>
            <br />
            {{mb_field object=$consult field="examen" onchange="submitFormAjax(this.form, 'systemMsg')"}}
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>