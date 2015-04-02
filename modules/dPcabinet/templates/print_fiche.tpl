{{mb_default var=display value=false}}
<script>
  printFiche = function(dossier_anesth_id) {
    var url = new Url("dPcabinet", "print_fiche");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.addParam("print", true);
    url.popup(700, 500, "printFiche");
  };

  Main.add(function(){
    if ($('anesth_tab_group')){
      $('anesth_tab_group').select('a[href=#fiche_anesth]')[0].removeClassName('wrong');
    }
  });
</script>

{{if !@$offline || @$multi}}
  {{if !$offline}}
        </td>
      </tr>
    </table>
  {{/if}}

  {{if $print && !@$multi}}
    <script>
      Main.add(window.print);
    </script>
  {{/if}}

  {{assign var=tbl_class value="print"}}
{{else}}
  {{assign var=tbl_class value="main form"}}
{{/if}}

{{assign var="consult"   value=$dossier_anesth->_ref_consultation}}
{{assign var="patient"   value=$consult->_ref_patient}}
{{assign var="operation" value=$dossier_anesth->_ref_operation}}
{{assign var="sejour"    value=$operation->_ref_sejour}}

{{if $operation->_id && $display}}
  {{mb_script module="cabinet" script="edit_consultation" ajax=true}}
  <script>
    refreshFicheAnesthOp = function(form) {
      var rep= confirm('Êtes-vous sûr de vouloir délier ce dossier à l\'intervention?');
      if (rep) {
        return onSubmitFormAjax(form, {onComplete: function() {
          var url = new Url("cabinet", "print_fiche");
          url.addParam("operation_id", "{{$operation->_id}}");
          url.addParam("offline", false);
          url.addParam("display", true);
          url.addParam("pdf"    , 0);
          url.requestUpdate("fiche_anesth");
        }});
      }
    }
  </script>
  <button type="button" class="print" onclick="printFiche('{{$dossier_anesth->_id}}');" style="float:left;">
    Imprimer la fiche
  </button>
  <button type="button" class="edit" onclick="Consultation.editModalDossierAnesth('{{$consult->_id}}', '{{$dossier_anesth->_id}}', refreshFicheAnesth);" style="float:left;">
    Modifier le dossier d'anesthésie
  </button>
  <form name="addInterv-{{$operation->_id}}" action="?m={{$m}}" method="post" onsubmit="return refreshFicheAnesthOp(this);">
    <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
    <input type="hidden" name="del" value="{{if $consult->_refs_dossiers_anesth|@count == 1}}0{{else}}1{{/if}}" />
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="operation_id" value=""/>
    <input type="hidden" name="sejour_id" value=""/>
    {{mb_key object=$dossier_anesth}}
    <button type="button" class="unlink" onclick="return refreshFicheAnesthOp(this.form);" style="float:left;">
      Supprimer le {{if $consult->_refs_dossiers_anesth|@count == 1}}lien à l'intervention{{else}}dossier d'anesthésie{{/if}}
    </button>
  </form>
{{/if}}
{{if $display && $dossiers|@count != 0}}
  <span style="display:inline-block;float:right;" onmouseover="ObjectTooltip.createDOM(this, 'DetailDossiers');">
    {{$dossiers|@count}} Dossiers d'anesthésie
  </span>
  <div  style="display: none;">
    <table class="tbl" id="DetailDossiers">
      <tr>
        <th class="title" colspan="4">Dossiers d'anesthésie</th>
      </tr>
      {{foreach from=$dossiers item=_dossier}}
        <tr>
          <td>{{tr}}CConsultation{{/tr}} du {{$_dossier->_ref_consultation->_date|date_format:$conf.date}}</td>
          <td>Dr {{$_dossier->_ref_consultation->_ref_chir->_view}}</td>
          <td>{{if $_dossier->operation_id}} <strong>{{$_dossier->_ref_operation->_view}}</strong>{{/if}}</td>
          <td><button type="button" class="print notext" onclick="printFiche('{{$_dossier->_id}}');"></button></td>
        </tr>
      {{/foreach}}
    </table>
  </div><br/>
{{/if}}

{{mb_include module=cabinet template=inc_header_fiche_anesth}}

<table class="{{$tbl_class}}" style="page-break-after: always">
  <tr>
    <td colspan="2" class="text" style="word-break: break-all">
      <table style="width: 100%">
        <tr>
          <th class="category" colspan="2">
            Intervention
            {{if $other_intervs|@count > 1 && $operation->_id && $pos_curr_interv}}
              ({{$pos_curr_interv}}/{{$other_intervs|@count}})
            {{/if}}
          </th>
        </tr>
        <tr>
          <td colspan="2">
            {{if $operation->_id}}
              {{if $sejour}}
              Admission en {{tr}}CSejour.type.{{$sejour->type}}{{/tr}}
              le <strong>{{$sejour->_entree|date_format:"%A %d/%m/%Y à %Hh%M"}}</strong>
              pour <strong>{{$sejour->_duree_prevue}} nuit(s)</strong>
              <br />
              {{/if}}
            {{else}}
              Intervention le <strong>{{$dossier_anesth->date_interv|date_format:"%A %d/%m/%Y"}}</strong>
              par le <strong>Dr {{$dossier_anesth->_ref_chir->_view}}</strong>
              <br />
              {{$dossier_anesth->libelle_interv}}
            {{/if}}
          </td>
        </tr>
        {{if $operation->_id}}
        <tr>
          <td colspan="2" class="text">
            Intervention le
            <strong>
              {{$operation->date|date_format:"%A %d/%m/%Y"}}
            </strong>
            {{if $operation->libelle}}
              - {{$operation->libelle}}
            {{/if}}
            <ul>
              {{if $operation->libelle}}
                <li><em>[{{$operation->libelle}}]</em></li>
              {{/if}}
              {{foreach from=$operation->_ext_codes_ccam item=curr_code}}
              <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}}) (coté {{tr}}COperation.cote.{{$operation->cote}}{{/tr}})</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td class="halfPane">
            {{if $operation->_id}}
              <table>
                {{if $conf.dPplanningOp.COperation.show_duree_uscpo != "0"}}
                <tr>
                  <th style="font-weight: normal;">USCPO</th>
                  <td style="font-weight: bold;">
                    {{if !$operation->passage_uscpo}}
                      Non
                    {{else}}
                      {{$operation->duree_uscpo}} nuit(s)
                    {{/if}}
                  </td>
                </tr>
                {{/if}}
                <tr>
                  <th style="font-weight: normal;">Anesthésie prévue</th>
                  <td style="font-weight: bold;">
                    {{$operation->_lu_type_anesth}}
                  </td>
                </tr>
                <tr>
                  <th style="font-weight: normal;">{{tr}}COperation-position{{/tr}}</th>
                  <td style="font-weight: bold;">
                    {{tr}}COperation.position.{{$operation->position}}{{/tr}}
                  </td>
                </tr>
                <tr>
                  <th style="font-weight: normal;">{{tr}}COperation-cote{{/tr}}</th>
                  <td style="font-weight: bold;">
                    {{tr}}COperation.cote.{{$operation->cote}}{{/tr}}
                  </td>
                </tr>
              </table>
            {{elseif $operation->position}}
            Position : <strong>{{tr}}COperation.position.{{$operation->position}}{{/tr}}</strong>
            {{/if}}
          </td>
          <td class="halfPane text">
            <strong>Techniques Complémentaires</strong>
            <ul>
              {{foreach from=$dossier_anesth->_ref_techniques item=curr_tech}}
              <li>
                {{$curr_tech->technique}}
              </li>
              {{foreachelse}}
              <li>Pas de technique complémentaire prévue</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
        {{if $operation->rques}}
        <tr>
          <td colspan="2" style="font-weight: bold;">
            <div class="small-warning">
              {{mb_value object=$operation field=rques}}
            </div>
          </td>
        </tr>
        {{/if}}
        {{if $consult->rques}}
        <tr>
          <td colspan="2">
            <strong>Remarques</strong>
            {{$consult->rques|nl2br}}
          </td>
        </tr>
      {{/if}}
      {{if $other_intervs|@count >= 2}}
        <tr>
          <th class="category" colspan="2">Autres interventions reliées</th>
        </tr>
        {{foreach from=$other_intervs item=_op}}
          {{if $_op->_id != $dossier_anesth->operation_id}}
            <tr>
              <td colspan="2">
                Intervention le <strong>{{$_op->_datetime_best|date_format:"%A %d/%m/%Y"}}</strong>
                {{if $_op->libelle}}
                  - {{$_op->libelle}}
                {{/if}}
                {{if $_op->cote}}
                  - {{mb_label object=$_op field=cote}} {{mb_value object=$_op field=cote}}
                {{/if}}
              </td>
            </tr>
          {{/if}}
        {{/foreach}}
      {{/if}}
      </table>
    </td>
  </tr>

  {{assign var=const_med value=$patient->_ref_constantes_medicales}}
  {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
  {{assign var=ant value=$dossier_medical->_ref_antecedents_by_type}}
  <tr>
    <td class="halfPane text" {{if !$dossier_medical->_count_allergies}}colspan="2"{{/if}} style="word-break: break-all">
      <table style="width: 100%">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
        </tr>
        <tr>
          <td colspan="2">
            {{$patient->_view}}
            {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
          </td>
        </tr>
        {{if $patient->nom_jeune_fille}}
        <tr>
          <th>{{mb_label object=$patient field=nom_jeune_fille}}</th>
          <td>{{$patient->nom_jeune_fille}}</td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2" class="text">
            Né{{if $patient->sexe != "m"}}e{{/if}} le {{mb_value object=$patient field=naissance}}
            ({{$patient->_age}})
            - sexe {{tr}}CPatient.sexe.{{$patient->sexe}}{{/tr}}<br />
            {{if $patient->profession}}Profession : {{$patient->profession}}<br />{{/if}}
            {{if $const_med->poids}}<strong>{{$const_med->poids}} kg</strong> - {{/if}}
            {{if $const_med->taille}}<strong>{{$const_med->taille}} cm</strong> - {{/if}}
            {{if $const_med->_imc}}IMC : <strong>{{$const_med->_imc}}</strong>
              {{if $const_med->_imc_valeur}}({{$const_med->_imc_valeur}}){{/if}}
            {{/if}}
            {{if $const_med->_poids_ideal}}
              - Poids idéal (Formule de Lorentz) : <strong>{{$const_med->_poids_ideal}} kg</strong>
            {{/if}}
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <table>
              {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
              {{if $dossier_medical->groupe_sanguin != "?" || $dossier_medical->rhesus != "?"}}
              <tr>
                <th style="font-weight: normal;">Groupe sanguin</th>
                <td style="font-weight: bold; font-size:130%;">&nbsp;{{tr}}CDossierMedical.groupe_sanguin.{{$dossier_medical->groupe_sanguin}}{{/tr}} &nbsp;{{tr}}CDossierMedical.rhesus.{{$dossier_medical->rhesus}}{{/tr}}</td>
              </tr>
              {{/if}}
              {{if $dossier_anesth->rai && $dossier_anesth->rai!="?"}}
              <tr>
                <th style="font-weight: normal;">RAI</th>
                <td style="font-weight: bold; font-size:130%;">&nbsp;{{tr}}CConsultAnesth.rai.{{$dossier_anesth->rai}}{{/tr}}</td>
              </tr>
              {{/if}}
              <tr>
                <th style="font-weight: normal;">ASA</th>
                <td style="font-weight: bold;">{{if $operation->_id}}{{mb_value object=$operation field=ASA}}{{else}}{{mb_value object=$dossier_anesth field=ASA}}{{/if}}</td>
              </tr>
              <tr>
                <th style="font-weight: normal;">VST</th>
                <td style="font-weight: bold;">
                  {{if $const_med->_vst}}{{$const_med->_vst}} ml{{/if}}
                </td>
              </tr>
              <tr>
                <th style="font-weight: normal;">APFEL</th>
                <td style="font-weight: bold;">{{math equation="a+b+c+d" a=$dossier_anesth->apfel_femme b=$dossier_anesth->apfel_non_fumeur c=$dossier_anesth->apfel_atcd_nvp d=$dossier_anesth->apfel_morphine}}</td>
              </tr>
              {{if $dossier_anesth->_psa}}
              <tr>
                <th style="font-weight: normal;">PSA</th>
                <td style="font-weight: bold;">
                  {{$dossier_anesth->_psa}} ml/GR
                </td>
                <td colspan="2"></td>
              </tr>
              {{/if}}
            </table>
          </td>
        </tr>
      </table>
    </td>
    {{if $dossier_medical->_count_allergies}}
    <td class="halfPane text" style="word-break: break-all">
      <table style="width: 100% ">
        <tr>
          <th class="category" colspan="2">Allergies</th>
        </tr>
        <tr>
          <td class="text" style="font-weight: bold; font-size:130%;">
          {{if $dossier_medical->_ref_antecedents_by_type && $dossier_medical->_ref_antecedents_by_type.alle|@count}}
            <div class="small-warning">
            {{foreach from=$dossier_medical->_ref_antecedents_by_type.alle item=currAnt}}
              <ul>
                <li>
                  {{if $currAnt->date}}
                    {{mb_value object=$currAnt field=date}} :
                  {{/if}}
                  {{$currAnt->rques}}
                </li>
              </ul>
            {{/foreach}}
            </div>
          {{else}}
            <ul>
              <li>Pas d'allergie saisie</li>
            </ul>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
    {{/if}}
  </tr>
  <tr>
    <td class="halfPane text" rowspan="2" style="word-break: break-all">
      <table style="width: 100%">
        <tr>
          <th class="category">{{tr}}CAntecedent.more{{/tr}}</th>
        </tr>
        <tr>
          <td class="text">
          {{if $dossier_medical->_ref_antecedents_by_type}}
            {{foreach from=$dossier_medical->_ref_antecedents_by_type key=keyAnt item=currTypeAnt}}
              {{if $currTypeAnt}}
                <strong>{{tr}}CAntecedent.type.{{$keyAnt}}{{/tr}}</strong>
                {{foreach from=$currTypeAnt item=currAnt}}
                <ul>
                  <li>
                    {{if $currAnt->appareil}}<strong>{{tr}}CAntecedent.appareil.{{$currAnt->appareil}}{{/tr}}</strong>{{/if}}
                    {{if $currAnt->date}}
                      {{mb_value object=$currAnt field=date}} :
                    {{/if}}
                    {{$currAnt->rques}}
                  </li>
                </ul>
                {{/foreach}}
              {{/if}}
            {{/foreach}}
          {{else}}
            <ul>
            <li>Pas d'antécédents</li>
            </ul>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>

    {{if is_array($dossier_medical->_ref_traitements) || $dossier_medical->_ref_prescription}}
    <!-- Traitements -->
    <td class="halfPane text">
      <table style="width: 100%">
        <tr>
          <th class="category">Traitements</th>
        </tr>
        {{if is_array($dossier_medical->_ref_traitements)}}
        <tr>
          <td class="text">
            <ul>
              {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Depuis {{mb_value object=$curr_trmt field=debut}}
                  jusqu'à {{mb_value object=$curr_trmt field=fin}} :
                {{elseif $curr_trmt->debut}}
                  Depuis {{mb_value object=$curr_trmt field=debut}} :
                {{/if}}
                <i>{{$curr_trmt->traitement}}</i>
              </li>
              {{foreachelse}}
                {{if $dossier_medical->absence_traitement}}
                  <li>{{tr}}CTraitement.absence{{/tr}}</li>
                {{elseif !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
                  <li>{{tr}}CTraitement.none{{/tr}}</li>
                {{/if}}
              {{/foreach}}
            </ul>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td class="text">
            <ul>
              {{if $dossier_medical->_ref_prescription}}
                {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line_med}}
                  <li>
                    <a href="#1" onclick="Prescription.viewProduit(null,'{{$_line_med->code_ucd}}','{{$_line_med->code_cis}}');">
                      {{$_line_med->_ucd_view}}
                    </a>
                    {{if $_line_med->_ref_prises|@count}}
                      ({{foreach from=$_line_med->_ref_prises item=_prise name=foreach_prise}}
                        {{$_prise->_view}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
                      {{/foreach}})
                    {{/if}}
                    {{if $_line_med->commentaire}}
                    ({{$_line_med->commentaire}})
                    {{/if}}
                    {{if $_line_med->debut || $_line_med->fin}}
                      <span class="compact">({{mb_include module=system template=inc_interval_date from=$_line_med->debut to=$_line_med->fin}})</span>
                    {{/if}}
                  </li>
                {{/foreach}}
              {{/if}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
    {{/if}}
    </tr>
    <tr>

    <!-- Examens cliniques -->
    <td class="halfPane text">
      <table style="width: 100%">
        <tr>
          <th class="category" colspan="6">Examens Clinique</th>
        </tr>
        <tr>
          <th style="font-weight: normal;">Pouls</th>
          <td style="font-weight: bold;">
            {{if $const_med->pouls}}
            {{$const_med->pouls}} / min
            {{else}}
            ?
            {{/if}}
          </td>
          <th style="font-weight: normal;">TA</th>
          <td style="font-weight: bold;">
            {{if $const_med->ta_gauche}}
              {{$const_med->_ta_gauche_systole}} / {{$const_med->_ta_gauche_diastole}} cm Hg
            {{elseif $const_med->ta_droit}}
              {{$const_med->_ta_droit_systole}} / {{$const_med->_ta_droit_diastole}} cm Hg
            {{elseif $const_med->ta}}
              {{$const_med->_ta_systole}} / {{$const_med->_ta_diastole}} cm Hg
            {{else}}
              ?
            {{/if}}
          </td>
          <th style="font-weight: normal;">Spo2</th>
          <td class="text" style="font-weight: bold;">
            {{if $const_med->spo2}}
            {{$const_med->spo2}} %
            {{else}}
            ?
            {{/if}}
          </td>
        </tr>

        <tr>
          <th style="font-weight: normal;">Examen cardiovasculaire</th>
          <td colspan="5" style="font-weight: bold;" class="text">{{$dossier_anesth->examenCardio}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Examen pulmonaire</th>
          <td colspan="5" style="font-weight: bold;" class="text">{{$dossier_anesth->examenPulmo}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Examen digestif</th>
          <td colspan="5" style="font-weight: bold;" class="text">{{$dossier_anesth->examenDigest}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Examen autre</th>
          <td colspan="5" style="font-weight: bold;" class="text">{{$dossier_anesth->examenAutre}}</td>
        </tr>

        {{if $consult->examen}}
        <tr>
          <th style="font-weight: normal;">Examens</th>
          <td colspan="5" style="font-weight: bold;" colspan="5" class="text">{{$consult->examen|nl2br}}</td>
        </tr>
        {{/if}}
      </table>
    </td>

  </tr>
</table>

{{if !$display}}
  {{mb_include module=cabinet template=inc_header_fiche_anesth}}
{{/if}}

<table class="{{$tbl_class}}">
  <tr>
    <td style="word-break: break-all">
      <table style="width: 100%">
        <tr>
          <th colspan="3" class="category">Conditions d'intubation</th>
        </tr>


        {{if !$conf.dPcabinet.CConsultAnesth.show_mallampati}}
        <tr>
          <th style="font-weight: normal;">Mallampati</th>
          <td style="font-weight: bold;">
           {{mb_value object=$dossier_anesth field="mallampati"}}
          </td>
        </tr>
        {{/if}}
        <tr>
          {{if $dossier_anesth->mallampati && $conf.dPcabinet.CConsultAnesth.show_mallampati}}
            <td rowspan="5" class="button text">
              <img src="images/pictures/{{$dossier_anesth->mallampati}}.png" alt="{{tr}}CConsultAnesth.mallampati.{{$dossier_anesth->mallampati}}{{/tr}}" />
              <br />Mallampati<br />de {{tr}}CConsultAnesth.mallampati.{{$dossier_anesth->mallampati}}{{/tr}}
            </td>
          {{/if}}
          <th style="font-weight: normal;">Ouverture de la bouche</th>
          <td style="font-weight: bold;">
            {{tr}}CConsultAnesth.bouche.{{$dossier_anesth->bouche}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Distance thyro-mentonnière</th>
          <td style="font-weight: bold;">{{tr}}CConsultAnesth.distThyro.{{$dossier_anesth->distThyro}}{{/tr}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">{{tr}}CConsultAnesth.mob_cervicale{{/tr}}</th>
          <td style="font-weight: bold;">{{tr}}CConsultAnesth.mob_cervicale.{{$dossier_anesth->mob_cervicale}}{{/tr}}</td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Critères de ventilation</th>
          <td style="font-weight: bold;" class="text">
            {{if $dossier_anesth->plus_de_55_ans}}{{mb_label object=$dossier_anesth field=plus_de_55_ans}}<br/>{{/if}}
            {{if $dossier_anesth->imc_sup_26}}{{mb_label object=$dossier_anesth field=imc_sup_26}}<br/>{{/if}}
            {{if $dossier_anesth->edentation}}{{mb_label object=$dossier_anesth field=edentation}}<br/>{{/if}}
            {{if $dossier_anesth->ronflements}}{{mb_label object=$dossier_anesth field=ronflements}}<br/>{{/if}}
            {{if $dossier_anesth->barbe}}{{mb_label object=$dossier_anesth field=barbe}}{{/if}}
            {{if $dossier_anesth->piercing}}{{mb_label object=$dossier_anesth field=piercing}}{{/if}}
          </td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Etat bucco-dentaire</th>
          <td style="font-weight: bold;" class="text">{{$dossier_anesth->etatBucco}}
          <br />
            {{if $etatDents}}
              {{$etatDents|nl2br}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th style="font-weight: normal;">Conclusion</th>
          <td style="font-weight: bold;" class="text">{{$dossier_anesth->conclusion}}</td>
        </tr>
        <tr>
        {{if $dossier_anesth->_intub_difficile}}
          <td colspan="3" style="font-weight: bold; text-align:center; color:#F00;">
            {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
          </td>
        {{else}}
          <td colspan="3" style="font-weight: bold; text-align:center;">
            Pas d'intubation difficile prévisible
          </td>
        {{/if}}
        </tr>
      </table>

      <table style="width: 100%">
        <tr>
          <th class="category" colspan="3">
            Examens Complémentaires
          </th>
        </tr>

        <tr>
        {{foreach from=$listChamps item=aChamps}}
          <td>
            <table>
            {{foreach from=$aChamps item=champ}}
              {{assign var="donnees" value=$unites.$champ}}
              <tr>
                <th style="font-weight: normal;">{{$donnees.nom}}</th>
                <td class="text" style="font-weight: bold;">
                  {{if $champ=="tca"}}
                    {{$dossier_anesth->tca_temoin}} s / {{$dossier_anesth->tca}}
                  {{elseif $champ=="tsivy"}}
                    {{$dossier_anesth->tsivy|date_format:"%Mm%Ss"}}
                  {{elseif $champ=="ecbu"}}
                    {{tr}}CConsultAnesth.ecbu.{{$dossier_anesth->ecbu}}{{/tr}}
                  {{elseif $champ == "date_analyse"}}
                    {{mb_value object=$dossier_anesth field=date_analyse}}
                  {{else}}
                    {{mb_value object=$dossier_anesth field=$champ}}
                  {{/if}}
                  {{$donnees.unit}}
                </td>
              </tr>
            {{/foreach}}
            </table>
          </td>
        {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
  {{if $dossier_anesth->result_ecg || $dossier_anesth->result_rp || ($dossier_anesth->result_autre && $app->user_prefs.viewAutreResult)}}
  <tr>
    <td style="word-break: break-all">
      <table style="width: 100%">
        <tr>
          <td style="width: {{if $app->user_prefs.viewAutreResult}}33%{{else}}50%{{/if}}">
            {{if $dossier_anesth->result_ecg}}
            <strong>{{mb_label object=$dossier_anesth field="result_ecg"}}</strong>
            <br />
            {{mb_value object=$dossier_anesth field="result_ecg"}}
            {{/if}}
          </td>
          <td>
            {{if $dossier_anesth->result_rp}}
            <strong>{{mb_label object=$dossier_anesth field="result_rp"}}</strong>
            <br />
            {{mb_value object=$dossier_anesth field="result_rp"}}
            {{/if}}
          </td>
          {{if $app->user_prefs.viewAutreResult}}
            <td>
              {{if $dossier_anesth->result_autre}}
              <strong>{{mb_label object=$dossier_anesth field="result_autre"}}</strong>
              <br />
              {{mb_value object=$dossier_anesth field="result_autre"}}
              {{/if}}
            </td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td style="word-break: break-all">
      <table style="width: 100%">
        {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
        {{if $list_exams|@count}}
        <tr>
          <th>
            Examens Complémentaires : {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
          </th>
          <td>
            <ul>
              {{foreach from=$list_exams item=curr_examcomp}}
              <li>
                {{$curr_examcomp->examen}}
              </li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
       {{/if}}
       {{foreachelse}}
       <tr>
        <td>
          Pas d'examen complémentaire
        </td>
      </tr>
      {{/foreach}}
      </table>

      <table style="width: 100%">
      {{if $consult->_ref_exampossum->_id}}
        <tr>
          <th>Score Possum</th>
          <td>
            Morbidité : {{mb_value object=$consult->_ref_exampossum field="_morbidite"}}%<br />
            Mortalité : {{mb_value object=$consult->_ref_exampossum field="_mortalite"}}%
          </td>
        </tr>
      {{/if}}

      {{if $consult->_ref_examnyha->_id}}
        <tr>
          <th>Clasification NYHA</th>
          <td>{{mb_value object=$consult->_ref_examnyha field="_classeNyha"}}</td>
        </tr>
      {{/if}}
      </table>

      <table style="width: 100%; padding-bottom: 10px;">
        <tr>
          <th class="category">
            Liste des Documents Edités
          </th>
        </tr>
        <tr>
          <td>
            <ul>
            {{foreach from=$dossier_anesth->_ref_documents item=currDoc}}
              <li>{{$currDoc->nom}}<br />
            {{foreachelse}}
            Aucun Document de consultation d'anesthésie
            {{/foreach}}
            {{foreach from=$consult->_ref_documents item=currDoc}}
              <li>{{$currDoc->nom}}<br />
            {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>

      <table style="width: 100%; padding-bottom: 10px;">
        <tr>
          <th class="category">
            Prémédication
          </th>
        </tr>

        {{if $dossier_anesth->premedication}}
          <tr>
            <td>
              {{$dossier_anesth->premedication|nl2br}}
            </td>
          </tr>
        {{/if}}
        {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
        <tr>
          <td>
            <ul>
              {{foreach from=$lines item=_line}}
                {{if $_line instanceof CPrescriptionLineMedicament}}
                  {{mb_include module="prescription" template="inc_print_medicament" med=$_line print=0 dci=0}}
                {{/if}}
                {{if $_line instanceof CPrescriptionLineMix}}
                  {{mb_include module="prescription" template="inc_print_prescription_line_mix" perf=$_line print=0 dci=0}}
                {{/if}}
                {{if $_line instanceof CPrescriptionLineElement}}
                  {{mb_include module="prescription" template="inc_print_element" elt=$_line}}
                {{/if}}
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>

      {{if $lines_per_op|@count}}
        <table style="width: 100%; padding-bottom: 10px;">
          <tr>
            <th class="category">
              Per-op
            </th>
          </tr>
          {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
          <tr>
            <td>
              <ul>
                {{foreach from=$lines_per_op item=_line_per_op}}
                  {{if $_line_per_op instanceof CPrescriptionLineMedicament}}
                    {{mb_include module="prescription" template="inc_print_medicament" med=$_line_per_op print=0 dci=0}}
                  {{/if}}
                  {{if $_line_per_op instanceof CPrescriptionLineMix}}
                    {{mb_include module="prescription" template="inc_print_prescription_line_mix" perf=$_line_per_op print=0 dci=0}}
                  {{/if}}
                  {{if $_line_per_op instanceof CPrescriptionLineElement}}
                    {{mb_include module="prescription" template="inc_print_element" elt=$_line_per_op}}
                  {{/if}}
                {{/foreach}}
              </ul>
            </td>
          </tr>
        </table>
      {{/if}}

      {{if $dossier_anesth->prepa_preop}}
      <table style="width: 100%">
        <tr>
          <th class="category">
            Préparation pré-opératoire
          </th>
        </tr>
        <tr>
          <td>
            {{$dossier_anesth->prepa_preop|nl2br}}
          </td>
        </tr>
      </table>
      {{/if}}

      {{if $dossier_medical->_ext_codes_cim}}
      <table style="width: 100%">
        <tr>
          <th class="category">Diagnostics PMSI du patient</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
              <li>
                {{$curr_code->code}}: {{$curr_code->libelle}}
              </li>
              {{foreachelse}}
              <li>Pas de diagnostic</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>

  {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
  <tr>
    <td style="word-break: break-all">
      <table style="width: 100%">
        <tr>
          <th class="category" colspan="3">Facteurs de risque</th>
        </tr>
        <tr>
          <th class="category">Facteur</th>
          <th class="category">Patient</th>
          <th class="category">Chirurgie</th>
        </tr>
        <tr>
          <th>Maladie thrombo-embolique</th>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical field="risque_thrombo_patient"}}
          </td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_thrombo_chirurgie"}}
          </td>
        </tr>
        <tr>
          <th>MCJ</th>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical field="risque_MCJ_patient"}}
          </td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_MCJ_chirurgie"}}
          </td>
        </tr>
        <tr>
          <th>Risque Anesthesique - Antibioprophylaxie</th>
          <td style="text-align: center;">&mdash;</td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_antibioprophylaxie"}}
          </td>
        </tr>
        <tr>
          <th>Risque Anesthesique - Prophylaxie</th>
          <td style="text-align: center;">&mdash;</td>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical_sejour field="risque_prophylaxie"}}
         </td>
        </tr>
        <tr>
          <th>{{tr}}{{mb_label object=$dossier_medical field="facteurs_risque"}}{{/tr}}</th>
          <td style="text-align: center;">
            {{mb_value object=$dossier_medical field="facteurs_risque"}}
          </td>
          <td></td>
        </tr>
     </table>
   </td>
  </tr>
  {{/if}}

  <tr>
    <th class="category">Visite de pré-anesthésie {{if $operation->date_visite_anesth}}- {{$operation->date_visite_anesth|date_format:$conf.date}}{{/if}}</th>
  </tr>
  {{if $operation->date_visite_anesth}}
  <tr>
    <td style="word-break: break-all">
      <table>
        <tr>
          <th>{{mb_label object=$operation field="prat_visite_anesth_id"}}</th>
          <td>{{mb_value object=$operation field="prat_visite_anesth_id"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field="rques_visite_anesth"}}</th>
          <td>{{mb_value object=$operation field="rques_visite_anesth"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field="autorisation_anesth"}}</th>
          <td>{{mb_value object=$operation field="autorisation_anesth"}}</td>
        </tr>
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <td style="word-break: break-all">
      <table>
        <tr>
          <th>{{mb_label object=$operation field="prat_visite_anesth_id"}}</th>
          <td></td>
        </tr>
        <tr style="height: 4em;">
          <th>{{mb_label object=$operation field="rques_visite_anesth"}}</th>
          <td></td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field="autorisation_anesth"}}</th>
          <td>Oui - Non</td>
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>

{{if !@$offline}}
<table class="main">
  <tr>
    <td>
{{/if}}