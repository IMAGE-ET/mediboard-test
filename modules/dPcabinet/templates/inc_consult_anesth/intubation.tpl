{{mb_default var=_is_dentiste value=0}}
{{mb_script module=cabinet script=intubation}}

<script type="text/javascript">
SchemaDentaire.oListEtats = {{$list_etat_dents|@json}};

Main.add(function () {
  var states = [0, 'defaut', 'bridge', 'pivot', 'mobile', 'appareil', 'implant'];
  SchemaDentaire.initialize("dents-schema", states);
} );
</script>

<form name="etat-dent-edit" action="?" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_etat_dent_aed" />
  <input type="hidden" name="etat_dent_id" value="" />
  <input type="hidden" name="_patient_id" value="{{$consult->_ref_patient->_id}}" />
  <input type="hidden" name="dent" value="" />
  <input type="hidden" name="etat" value="" />
</form>

{{if !$_is_dentiste}}
<form name="editFrmIntubation" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
{{mb_key object=$consult_anesth}}
{{mb_field object=$consult_anesth field=intub_difficile hidden=true}}
{{/if}}
<table class="form">
  <tr>
    <td class="narrow">
      <fieldset>
        <legend>Etat bucco-dentaire</legend>
        <div id="dents-schema" style="position: relative;">
          <img id="dents-schema-image" src="images/pictures/dents.png?build={{$version.build}}" border="0" usemap="#dents-schema-map" /> 
          <map id="dents-schema-map" name="dents-schema-map">
            <area shape="circle" coords="127,112, 30" href="#1" id="dent-10" /><!-- Central haut adulte -->
            <area shape="circle" coords="116,33, 11" href="#1" id="dent-11" />
            <area shape="circle" coords="97,44, 11" href="#1" id="dent-12" />
            <area shape="circle" coords="79,55, 12" href="#1" id="dent-13" />
            <area shape="circle" coords="70,74, 12" href="#1" id="dent-14" />
            <area shape="circle" coords="61,93, 13" href="#1" id="dent-15" />
            <area shape="circle" coords="55,118, 17" href="#1" id="dent-16" />
            <area shape="circle" coords="51,146, 16" href="#1" id="dent-17" />
            <area shape="circle" coords="50,174, 15" href="#1" id="dent-18" />
            <area shape="circle" coords="137,33, 11" href="#1" id="dent-21" />
            <area shape="circle" coords="156,44, 11" href="#1" id="dent-22" />
            <area shape="circle" coords="174,55, 12" href="#1" id="dent-23" />
            <area shape="circle" coords="183,74, 12" href="#1" id="dent-24" />
            <area shape="circle" coords="192,94, 13" href="#1" id="dent-25" />
            <area shape="circle" coords="198,118, 17" href="#1" id="dent-26" />
            <area shape="circle" coords="201,146, 16" href="#1" id="dent-27" />
            <area shape="circle" coords="203,174, 15" href="#1" id="dent-28" />
            <area shape="circle" coords="127,272, 30" href="#1" id="dent-30" /><!-- Central bas adulte -->
            <area shape="circle" coords="135,356, 9" href="#1" id="dent-31" />
            <area shape="circle" coords="150,349, 9" href="#1" id="dent-32" />
            <area shape="circle" coords="164,338, 11" href="#1" id="dent-33" />
            <area shape="circle" coords="177,322, 11" href="#1" id="dent-34" />
            <area shape="circle" coords="186,303, 12" href="#1" id="dent-35" />
            <area shape="circle" coords="195,279, 18" href="#1" id="dent-36" />
            <area shape="circle" coords="199,250, 16" href="#1" id="dent-37" />
            <area shape="circle" coords="203,222, 15" href="#1" id="dent-38" />
            <area shape="circle" coords="118,356, 9" href="#1" id="dent-41" />
            <area shape="circle" coords="103,348, 9" href="#1" id="dent-42" />
            <area shape="circle" coords="89,338, 11" href="#1" id="dent-43" />
            <area shape="circle" coords="76,323, 11" href="#1" id="dent-44" />
            <area shape="circle" coords="66,304, 12" href="#1" id="dent-45" />
            <area shape="circle" coords="58,279, 18" href="#1" id="dent-46" />
            <area shape="circle" coords="54,250, 16" href="#1" id="dent-47" />
            <area shape="circle" coords="49,223, 15" href="#1" id="dent-48" />
            <area shape="circle" coords="324,162, 19" href="#1" id="dent-50" /><!-- Central haut enfant -->
            <area shape="circle" coords="318,114, 7" href="#1" id="dent-51" />
            <area shape="circle" coords="307,120, 8" href="#1" id="dent-52" />
            <area shape="circle" coords="298,131, 9" href="#1" id="dent-53" />
            <area shape="circle" coords="290,147, 11" href="#1" id="dent-54" />
            <area shape="circle" coords="285,166, 12" href="#1" id="dent-55" />
            <area shape="circle" coords="331,114, 7" href="#1" id="dent-61" />
            <area shape="circle" coords="342,120, 8" href="#1" id="dent-62" />
            <area shape="circle" coords="351,131, 9" href="#1" id="dent-63" />
            <area shape="circle" coords="357,147, 11" href="#1" id="dent-64" />
            <area shape="circle" coords="363,166, 12" href="#1" id="dent-65" />
            <area shape="circle" coords="324,231, 19" href="#1" id="dent-70" /><!-- Central haut enfant -->
            <area shape="circle" coords="330,271, 6" href="#1" id="dent-71" />
            <area shape="circle" coords="339,265, 7" href="#1" id="dent-72" />
            <area shape="circle" coords="350,255, 8" href="#1" id="dent-73" />
            <area shape="circle" coords="357,243, 8" href="#1" id="dent-74" />
            <area shape="circle" coords="365,227, 10" href="#1" id="dent-75" />
            <area shape="circle" coords="319,271, 6" href="#1" id="dent-81" />
            <area shape="circle" coords="309,265, 7" href="#1" id="dent-82" />
            <area shape="circle" coords="298,255, 8" href="#1" id="dent-83" />
            <area shape="circle" coords="291,242, 8" href="#1" id="dent-84" />
            <area shape="circle" coords="282,228, 10" href="#1" id="dent-85" />
          </map>
        </div>
      </fieldset>
    </td>
    {{if !$_is_dentiste}}
    <td>
      <fieldset>
        <legend>Conditions d'intubation</legend>
        <table class="layout main">
          <tr>
            <td colspan="2">
              <button class="cancel" type="button" style="float: right;" onclick="resetIntubation(this.form)">{{tr}}Reset{{/tr}}</button>
              <table class="layout main">
                <tr>
                  {{foreach from=$consult_anesth->_specs.mallampati->_locales key=curr_mallampati item=trans_mallampati}}
                  <td class="button">
                    <div id="mallampati_bg_{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}class="mallampati-selected"{{/if}}>
                    <label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{$trans_mallampati}}">
                      <img src="images/pictures/{{$curr_mallampati}}.png?build={{$version.build}}" />
                      <br />
                      <input type="radio" name="mallampati" value="{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}checked="checked" {{/if}} onclick="$V(this.form.intub_difficile, ''); verifIntubDifficileAndSave(this.form);" />
                      {{$trans_mallampati}}
                    </label>
                    </div>
                  </td>
                  {{/foreach}}
                </tr>
              </table>
              <input type="radio" style="display: none;" name="mallampati" value="" {{if !$consult_anesth->mallampati}}checked="checked" {{/if}} onclick="$V(this.form.intub_difficile, ''); verifIntubDifficileAndSave(this.form);" />
            </td>
          </tr>
          <tr>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="bouche" defaultFor="bouche_m20"}}</legend>
                {{mb_field object=$consult_anesth field="bouche" typeEnum="radio" separator="<br />" onclick="\$V(this.form.intub_difficile, ''); verifIntubDifficileAndSave(this.form);"}}
                <input type="radio" style="display: none;" name="bouche" value="" {{if !$consult_anesth->bouche}}checked="checked"{{/if}} onclick="$V(this.form.intub_difficile, ''); verifIntubDifficileAndSave(this.form);" />
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="distThyro" defaultFor="distThyro_m65"}}</legend>
                {{mb_field object=$consult_anesth field="distThyro" typeEnum="radio" separator="<br />" onclick="\$V(this.form.intub_difficile, ''); verifIntubDifficileAndSave(this.form);"}}
                <input type="radio" style="display: none;" name="distThyro" value="" {{if !$consult_anesth->distThyro}}checked="checked"{{/if}} onclick="$V(this.form.intub_difficile, ''); verifIntubDifficileAndSave(this.form);" />
              </fieldset>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <fieldset>
                <legend>Critères de ventilation</legend>
                <table class="layout">
                  <tr>
                    <td>
                      {{mb_field object=$consult_anesth field=plus_de_55_ans typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
                      {{mb_label object=$consult_anesth field=plus_de_55_ans}}
                    </td>
                    <td>
                      {{mb_field object=$consult_anesth field=edentation typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
                      {{mb_label object=$consult_anesth field=edentation}}
                    </td>
                    <td>
                      {{mb_field object=$consult_anesth field=barbe typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
                      {{mb_label object=$consult_anesth field=barbe}}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{mb_field object=$consult_anesth field=imc_sup_26 typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
                      {{mb_label object=$consult_anesth field=imc_sup_26}}
                    </td>
                    <td>
                      {{mb_field object=$consult_anesth field=ronflements typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
                      {{mb_label object=$consult_anesth field=ronflements}}
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
          <tr>
            <td colspan="2"><hr /></td>
          </tr>
          <tr>
            <td colspan="2">
              {{mb_label object=$consult_anesth field="etatBucco"}}
            </td>
          </tr>
          <tr>
            <td colspan="2">
              {{mb_field object=$consult_anesth field="etatBucco" onchange="submitFormAjax(this.form, 'systemMsg')" form="editFrmIntubation"
                aidesaisie="validateOnBlur: 0"}}
            </td>
          </tr>
          <tr>
            <td colspan="2">
              {{mb_label object=$consult_anesth field="conclusion"}}
            </td>
          </tr>
          <tr>
            <td colspan="2">
              {{mb_field object=$consult_anesth field="conclusion" onchange="submitFormAjax(this.form, 'systemMsg')" form="editFrmIntubation"
                aidesaisie="validateOnBlur: 0"}}
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button type="button" id="force_pas_difficile" class="tick"
                {{if !$consult_anesth->_intub_difficile}}style="display: none;"{{/if}}
                onclick="$V(this.form.intub_difficile, '0'); verifIntubDifficileAndSave(this.form);" >Pas difficile</button>
              <button type="button" id="force_difficile" class="tick"
              {{if $consult_anesth->_intub_difficile}}style="display: none;"{{/if}}
                onclick="$V(this.form.intub_difficile, '1'); verifIntubDifficileAndSave(this.form);">Difficile</button>
              <div id="divAlertIntubDiff"
                style="color: {{if $consult_anesth->_intub_difficile}}#F00;{{else}}#000;{{/if}}"
                {{if !$consult_anesth->_intub_difficile}}class="hatching opacity-50"{{/if}}>
                <strong>Intubation Difficile Prévisible</strong>
              </div>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
    {{/if}}
  </tr>
</table>
</form>