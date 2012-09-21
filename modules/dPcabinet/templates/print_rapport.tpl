<!-- $Id$ -->

{{mb_script module=cabinet script=reglement}}
{{if !$ajax}} 

<script type="text/javascript">	
PlageConsult = {
  refresh: function(plage_id) {
    var url = new Url("dPcabinet", "print_rapport");
    url.addParam("plage_id", plage_id);
    url.requestUpdate("CPlageconsult-" + plage_id);
  },
  onSubmit: function(form, plage_id) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        PlageConsult.showObsolete();
        PlageConsult.refresh(plage_id);
      }
    } );
  },
  showObsolete: function() {
    var oStyle = {
      opacity: 0.9,
      position: 'absolute'
    };
    $("obsolete-totals").setStyle(oStyle).clonePosition("totals").show();
  }
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport
              {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
            </a>
          </th>
        </tr>

        <!-- Praticiens concernés -->
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}

        <tr>
          <td>Règlements pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
        </tr>
        {{if $filter->_etat_reglement_patient}}
        <tr>
          <td>
            Paiements patients :
            {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_patient}}{{/tr}}
          </td>
        </tr>
        {{/if}}
        {{if $filter->_etat_reglement_tiers}}
        <tr>
          <td>
            Paiements tiers :
            {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_tiers}}{{/tr}}
          </td> 
        </tr>
        {{/if}}
      </table>
    </td>
    
    <td class="halfPane">
      <div id="obsolete-totals" style="background-color: #888; display: none">
        <div class="big-warning">
          <p>Les totaux sont obsolètes suite à la saisie de règlements</p>
          <a class="button change" onclick="location.reload()">{{tr}}Refresh{{/tr}} {{tr}}Now{{/tr}}</a>
        </div>
      </div>
      
      <table id="totals" class="tbl">
        {{foreach from=$reglement->_specs.emetteur->_list item=emetteur}}
          <tr>
            <th class="title" colspan="9">Réglements {{tr}}CReglement.emetteur.{{$emetteur}}{{/tr}}</th>
          </tr>
  
          <tr>
            <th>{{mb_label object=$reglement field=mode}}</th>
            <th>{{tr}}Total{{/tr}}</th>
            {{foreach from=$reglement->_specs.mode->_list item=_mode}}
            <th>{{tr}}CReglement.mode.{{$_mode}}{{/tr}}</th>
            {{/foreach}}
            <th>Impayé</th>
          </tr>
  
          <tr>
            <th class="category">Nb réglements</th>
            {{assign var=nb_reglement_name value="nb_reglement_$emetteur"}}
            <td>{{$recapReglement.total.$nb_reglement_name}}</td>
            {{foreach from=$reglement->_specs.mode->_list item=_mode}}
            <td>{{$recapReglement.$_mode.$nb_reglement_name}}</td>
            {{/foreach}}
            <td>{{$recapReglement.total.$nb_reglement_name}}</td>
          </tr>
  
          <tr>
            <th class="category">Total réglements</th>
            {{assign var=du_name value="du_$emetteur"}}
            <td>{{$recapReglement.total.$du_name|currency}}</td>
            {{foreach from=$reglement->_specs.mode->_list item=_mode}}
            <td>{{$recapReglement.$_mode.$du_name|currency}}</td>
            {{/foreach}}
            {{assign var=reste_name value="reste_$emetteur"}}
            <td>{{$recapReglement.total.$reste_name|currency}}</td>
          </tr>
        {{/foreach}}
         
        <tr>
          <th class="title" colspan="9">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th>Nb de {{tr}}CConsultation{{/tr}}</th>
          <td colspan="7">{{$recapReglement.total.nb_consultations}}</td>
        </tr>
        
        {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <tr>
            <th>
              {{tr}}Total{{/tr}}
              {{mb_label class=CConsultation field=secteur1}}
            </th>
            <td colspan="4">{{$recapReglement.total.secteur1|currency}}</td>
            <th colspan="4">{{mb_label class=CConsultation field=_somme}}</th>
          </tr>
          <tr>
            <th>
              {{tr}}Total{{/tr}}
              {{mb_label class=CConsultation field=secteur2}}
            </th>
            <td colspan="4">{{$recapReglement.total.secteur2|currency}}</td>
            <td colspan="4" class="button">
              {{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}
            </td>
          </tr>
          <tr>
            <th>Total réglé patient</th>
            <td colspan="4">{{$recapReglement.total.du_patient|currency}}</td>
            <th colspan="4">Total réglé</th>
          </tr>
          <tr>
            <th>Total réglé tiers</th>
            <td colspan="4">{{$recapReglement.total.du_tiers|currency}}</td>
            <td colspan="4" class="button">
              {{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}
            </td>
          </tr>
          <tr>
            <th>Total non réglé patient</th>
            <td colspan="4">{{$recapReglement.total.reste_patient|currency}}</td>
            <th colspan="4">Total non réglé</th>
          </tr>
          <tr>
            <th>Total non réglé tiers</th>
            <td colspan="4">{{$recapReglement.total.reste_tiers|currency}}</td>
            <td colspan="4" class="button">
              {{$recapReglement.total.reste_patient+$recapReglement.total.reste_tiers|currency}}
            </td>
          </tr>
        {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
          <tr>
            <th>{{mb_label class=CConsultation field=_somme}}</th>
            {{assign var=total_du value=$recapReglement.total.secteur1+$recapReglement.total.secteur2}}
            <td colspan="8">{{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}</td>
          </tr>
          <tr>
            <th>Total réglé</th>
            {{assign var=regle value=$recapReglement.total.du_patient+$recapReglement.total.du_tiers}}
            <td colspan="7">{{$regle|currency}}</td>
          </tr>
          <tr>
            <th>Total non réglé</th>
            <td colspan="7">{{$total_du-$regle|currency}}</td>
          </tr>
        {{/if}}
      </table>
    </td>
  </tr>
{{/if}}
  
  {{if $filter->_type_affichage}}
  {{foreach from=$listPlages item=_plage}}
  {{if !$ajax}} 
  <tbody id="{{$_plage.plage->_guid}}">
  {{/if}}
    
  <tr>
    <td colspan="2">
      <br />
      <br />
      <strong onclick="PlageConsult.refresh('{{$_plage.plage->_id}}')">
        {{$_plage.plage->_ref_chir}}
        &mdash; {{$_plage.plage->date|date_format:$conf.longdate}}
        de {{$_plage.plage->debut|date_format:$conf.time}} 
        à  {{$_plage.plage->fin|date_format:$conf.time}} 

        {{if $_plage.plage->libelle}} 
        : {{mb_value object=$_plage.plage field=libelle}}	   
        {{/if}}

      </strong>
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th class="narrow text">{{tr}}CFactureConsult{{/tr}}</th>
          <th style="width: 20%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th style="width: 10%;">{{mb_label class=CConsultation field=tarif}}</th>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
          <th class="narrow">{{mb_title class=CConsultation field=secteur1}}</th>
          <th class="narrow">{{mb_title class=CConsultation field=secteur2}}</th>
          <th class="narrow">{{mb_title class=CConsultation field=_somme}}</th>
          {{/if}}
          
          {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <th class="narrow">Montant</th>
          <th class="narrow">Remise</th>
          {{/if}}
          
          <th style="width: 20%;">{{mb_title class=CConsultation field=du_patient}}</th>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
          <th style="width: 20%;">{{mb_title class=CConsultation field=du_tiers}}</th>
          {{/if}}
          
        </tr>
        {{foreach from=$_plage.factures item=_facture}}
        <tr>
          <td><strong>{{$_facture}}</strong></td>
        
          <td class="text">
            <a name="{{$_facture->_guid}}">
              {{assign var=patient value=$_facture->_ref_patient}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
                {{$patient}}
              </span>
            </a>
          </td>
          <td class="text">
            {{foreach from=$_facture->_ref_consults item=_consult}}
            <div {{if !$_consult->tarif}} class="empty" {{/if}}>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
                {{mb_value object=$_consult field=_date}}: {{mb_value object=$_consult field=tarif default=None}}
              </span>
            </div>   
            {{foreachelse}}
            <div class="empty">{{tr}}CConsultation.none{{/tr}}</div>
            {{/foreach}}
          </td>

          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
          <td>{{mb_value object=$_facture field=_montant_secteur1 empty=1}}</td>
          <td>{{mb_value object=$_facture field=_montant_secteur2 empty=1}}</td>
          <td>{{mb_value object=$_facture field=_montant_total    empty=1}}</td>
          {{/if}}
          
          {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <td>{{mb_value object=$_facture field=_montant_sans_remise empty=1}}</td>
          <td>{{mb_value object=$_facture field=remise empty=1}}</td>
          <td>{{mb_value object=$_facture field=_montant_avec_remise empty=1}}</td>
          {{/if}}

          <td>
            {{foreach name=reglement_patient from=$_facture->_ref_reglements_patient item=_reglement}}
              {{assign var=_reglement_id value=$_reglement->_id}}
              <form name="reglement-del-{{$_reglement_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="cabinet" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              {{mb_key object=$_reglement}}
              <button class="remove" type="submit">{{mb_value object=$_reglement field=montant}}</button>
              {{mb_value object=$_reglement field=mode}}
              </form>
              <br />
              <form name="reglement-chdate-{{$_reglement_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="cabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              {{mb_key object=$_reglement}}
              {{mb_field object=$_reglement field=object_id       hidden=1}}
              {{mb_field object=$_reglement field=object_class    hidden=1}}
              {{mb_field object=$_reglement field=montant         hidden=1}}
              {{mb_field object=$_reglement field=mode            hidden=1}}
              {{mb_field object=$_reglement field=date register=true form="reglement-chdate-$_reglement_id" onchange="this.form.onsubmit()"}}
              </form>
              {{if !$smarty.foreach.reglement_patient.last}}
              <hr />
              {{/if}}
            {{/foreach}}
            

            {{if $_facture->_du_restant_patient > 0.001}}
              {{assign var=new_reglement value=$_facture->_new_reglement_patient}}
              <form name="reglement-add-patient-{{$_facture->_guid}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="cabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              <input type="hidden" name="date" value="now" />
              <input type="hidden" name="emetteur" value="patient" />
              {{mb_field object=$new_reglement field=object_id    hidden=1}}
              {{mb_field object=$new_reglement field=object_class hidden=1}}
              {{mb_field object=$new_reglement field=montant}}
              {{mb_field object=$new_reglement field=mode emptyLabel="Choose" onchange="Reglement.updateBanque(this)"}}
              {{mb_field object=$new_reglement field=banque_id options=$banques style="width: 7em; display: none;"}}
              <button class="add notext" type="submit">+</button>
              </form>
            {{/if}}

          </td>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <td>
            {{foreach name=reglement_tier from=$_facture->_ref_reglements_tiers item=_reglement}}
              {{assign var=_reglement_id value=$_reglement->_id}}
              <form name="reglement-del-{{$_reglement_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="cabinet" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              {{mb_key object=$_reglement}}
              <button class="remove" type="submit">{{mb_value object=$_reglement field=montant}}</button>
              {{mb_value object=$_reglement field=mode}}
              </form>
              <br />
              <form name="reglement-chdate-{{$_reglement_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="cabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              {{mb_key object=$_reglement}}
              {{mb_field object=$_reglement field=object_id       hidden=1}}
              {{mb_field object=$_reglement field=object_class    hidden=1}}
              {{mb_field object=$_reglement field=montant         hidden=1}}
              {{mb_field object=$_reglement field=mode            hidden=1}}
              {{mb_field object=$_reglement field=date register=true form="reglement-chdate-$_reglement_id" onchange="this.form.onsubmit()"}}
              </form>
              {{if !$smarty.foreach.reglement_tier.last}}
              <hr />
              {{/if}}
            {{/foreach}}
            
            {{if $_facture->_du_restant_tiers > 0.001}}
              {{assign var=new_reglement value=$_facture->_new_reglement_tiers}}
              <form name="reglement-add-tiers-{{$_facture->_guid}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="cabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              <input type="hidden" name="date" value="now" />
              <input type="hidden" name="emetteur" value="tiers" />
              {{mb_field object=$new_reglement field=object_id    hidden=1}}
              {{mb_field object=$new_reglement field=object_class hidden=1}}
              {{mb_field object=$new_reglement field=montant}}
              {{mb_field object=$new_reglement field=mode emptyLabel="Choose" onchange="Reglement.updateBanque(this)"}}
              {{mb_field object=$new_reglement field=banque_id options=$banques style="width: 7em; display: none;"}}
              <button class="add notext" type="submit">+</button>
            </form>
            {{/if}}
          </td>
          {{/if}}
        </tr>
        
        {{/foreach}}
        <tr>
          <td colspan="3" style="text-align: right" >
            <strong>{{tr}}Total{{/tr}}</strong>
          </td>
          <td><strong>{{$_plage.total.secteur1|currency}}</strong></td>
          <td><strong>{{$_plage.total.secteur2|currency}}</strong></td>
          <td><strong>{{$_plage.total.total|currency}}</strong></td>
          <td><strong>{{$_plage.total.patient|currency}}</strong></td>
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
            <td><strong>{{$_plage.total.tiers|currency}}</strong></td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  
  {{if !$ajax}} 
  </tbody>
  {{/if}}
  {{/foreach}}
  {{/if}}
  
  
{{if !$ajax}} 
</table>
{{/if}}
