<script type="text/javascript">
calculImcVst = function (oForm) {
   var sImcValeur = null;
   var fImc       = null;
   var fVst       = null;
   var poids      = parseFloat($V(oForm.poids));
   var taille     = parseInt($V(oForm.taille));

   if (poids && !isNaN(poids) && poids > 0) {
     fVst = {{if $constantes->_ref_patient->sexe=="m"}}70{{else}}65{{/if}} * poids;

     if (taille && !isNaN(taille) && taille > 0) {
       fImc = Math.round(100 * 100 * 100 * poids / (taille * taille))/100; // Math.round(x*100)/100 == round(x, 2)

       if (fImc < 15) {
         sImcValeur = "Inanition";
       }
       else if (fImc < 18.5) {
         sImcValeur = "Maigreur";
       }
       else if (fImc > 40) {
         sImcValeur = "Obésité morbide";
       }
       else if (fImc > 35) {
         sImcValeur = "Obésité sévère";
       }
       else if (fImc > 30) {
         sImcValeur = "Obésité modérée";
       }
       else if (fImc > 25) {
         sImcValeur = "Surpoids";
       }
     }
   }
   $V(oForm._vst, fVst);
   $V(oForm._imc, fImc);
   $('constantes_medicales_imc').innerHTML = sImcValeur;
   if(typeof(calculPSA) == 'function' && typeof(calculClairance) == 'function') {
     calculPSA(); 
     calculClairance();
   }
}
</script>

<form name="edit-constantes-medicales" action="?m=dPpatients" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
  <input type="hidden" name="datetime" value="now" />
  {{mb_field object=$constantes_context field=context_class hidden=1}}
  {{mb_field object=$constantes_context field=context_id hidden=1}}
  {{mb_field object=$constantes_context field=patient_id hidden=1}}
  
  <!-- Champs cachés pour etre utilisable par les autres scripts -->
  <input type="hidden" name="_poids" value="{{$constantes_context->poids}}" />
  <input type="hidden" name="_taille" value="{{$constantes_context->taille}}" />
  <input type="hidden" name="_pouls" value="{{$constantes_context->pouls}}" />
  <input type="hidden" name="_spo2" value="{{$constantes_context->spo2}}" />
  
  <table class="form">
    <tr>
      <th class="category"></th>
      <th class="category">Nouveau</th>
      <th class="category">Dernier</th>
      <th class="category">Date</th>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=poids}}</th>
      <td>
        {{mb_field object=$constantes_context field=poids tabindex="1" size="4" onchange="calculImcVst(this.form);submitFormAjax(this.form, 'systemMsg');"}} kg
      </td>
      <td>
        {{if $constantes->poids}}
          <a class="button tick notext" onclick="$V(document.forms['edit-constantes-medicales'].poids, {{$constantes->poids}}); return false;"> </a>
          {{mb_value object=$constantes field=poids}}kg
        {{/if}}
      </td>
      <td>{{$dates.poids|date_format:"%d/%m/%Y"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=taille}}</th>
      <td>
        {{mb_field object=$constantes_context field=taille tabindex="2" size="4" onchange="calculImcVst(this.form);submitFormAjax(this.form, 'systemMsg');"}} cm
      </td>
      <td>
        {{if $constantes->taille}}
          <a class="button tick notext" onclick="$V(document.forms['edit-constantes-medicales'].taille, {{$constantes->taille}}); return false;"> </a>
          {{mb_value object=$constantes field=taille}}cm
        {{/if}}
      </td>
      <td>{{$dates.taille|date_format:"%d/%m/%Y"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=_vst}}</th>
      <td>{{mb_field object=$constantes_context field=_vst size="4" readonly="readonly"}} ml</td>
      <td>{{mb_value object=$constantes field=_vst}}{{if $constantes->_vst}} ml{{/if}}</td>
      <td />
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=_imc}}</th>
      <td>{{mb_field object=$constantes_context field=_imc size="4" readonly="readonly"}}</td>
      <td>{{mb_value object=$constantes field=_imc}}</td>
      <td />
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=ta}}</th>
      <td>
        {{mb_field object=$constantes_context field=_ta_systole tabindex="3" size="1" onchange="submitFormAjax(this.form, 'systemMsg');"}} /
        {{mb_field object=$constantes_context field=_ta_diastole tabindex="4" size="1" onchange="submitFormAjax(this.form, 'systemMsg');"}} cm Hg
      </td>
      <td>
        {{if $constantes->ta}}
          <a class="button tick notext" 
          onclick="$V(document.forms['edit-constantes-medicales']._ta_systole, {{$constantes->_ta_systole}}); 
                   $V(document.forms['edit-constantes-medicales']._ta_diastole, {{$constantes->_ta_diastole}}); return false;"> </a>
          {{mb_value object=$constantes field=_ta_systole}} / {{mb_value object=$constantes field=_ta_diastole}} cm Hg
        {{/if}}
      </td>
      <td>{{$dates.ta|date_format:"%d/%m/%Y"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=pouls}}</th>
      <td>
        {{mb_field object=$constantes_context field=pouls tabindex="5" size="4" onchange="submitFormAjax(this.form, 'systemMsg');"}} /min
      </td>
      <td>
        {{if $constantes->pouls}}
          <a class="button tick notext" onclick="$V(document.forms['edit-constantes-medicales'].pouls, {{$constantes->pouls}}); return false;"> </a>
          {{mb_value object=$constantes field=pouls}}/min
        {{/if}}
      </td>
      <td>{{$dates.pouls|date_format:"%d/%m/%Y"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=spo2}}</th>
      <td>
        {{mb_field object=$constantes_context field=spo2 tabindex="6" size="4" onchange="submitFormAjax(this.form, 'systemMsg');"}} %
      </td>
      <td>
        {{if $constantes->spo2}}
          <a class="button tick notext" onclick="$V(document.forms['edit-constantes-medicales'].spo2, {{$constantes->spo2}}); return false;"> </a>
          {{mb_value object=$constantes field=spo2}} %
        {{/if}}
      </td>
      <td>{{$dates.spo2|date_format:"%d/%m/%Y"}}</td>
    </tr>
    <tr>
      <td id="constantes_medicales_imc" colspan="2" style="color:#F00;"></td>
    </tr>
  </table>
</form>