<!-- $Id$ -->

{{mb_script module=cabinet script=reglement}}
{{mb_script module=cabinet script=rapport}}

{{if !$ajax}} 

<div style="float: right;"> 
  {{mb_include template=inc_totaux_rapport}}
</div>

<div>
  <a href="#" onclick="window.print()">
    Rapport
    {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
  </a>
</div>

<div>
  Réglements pris en compte : 
  {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}
</div>

{{if $filter->_etat_reglement_patient}}
<div>
  Paiements patients :
  {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_patient}}{{/tr}}
</div>
{{/if}}

{{if $filter->_etat_reglement_tiers}}
<div>
  Paiements tiers :
  {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_tiers}}{{/tr}}
</div>
</tr>
{{/if}}
    
<!-- Praticiens concernés -->
{{foreach from=$listPrat item=_prat}}
<div>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</div>
{{/foreach}}

{{/if}}
  
{{if $filter->_type_affichage}}
<table class="main">
  {{foreach from=$listPlages item=_plage}}
  {{if !$ajax}} 
  <tbody id="{{$_plage.plage->_guid}}">
  {{/if}}
    
  <tr>
    <td colspan="2">
      <br />
      <br />
      <strong onclick="Rapport.refresh('{{$_plage.plage->_id}}')">
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
          <th style="width: 20%;">{{mb_label class=CConsultation field=tarif}}</th>
          
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
          <td><strong {{if $_facture->_id}} onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_guid}}')" {{/if}}>{{$_facture}}</strong></td>
        
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
            <table class="layout">
              {{foreach from=$_facture->_ref_reglements_patient item=_reglement}}
              <tr>
                <td class="narrow">
                  <button class="edit notext" type="button" onclick="Rapport.editReglement('{{$_reglement->_id}}', '{{$_plage.plage->_id}}');">
                    {{tr}}Edit{{/tr}}
                  </button>
                </td>
                <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                <td>{{mb_value object=$_reglement field=mode}}</td>
                <td class="narrow">{{mb_value object=$_reglement field=date date=$_plage.plage->date}}</td>
              </tr>
              {{/foreach}}
              
              {{if abs($_facture->_du_restant_patient) > 0.001}}
              <tr>
                <td colspan="4" class="button">
                  {{assign var=new_reglement value=$_facture->_new_reglement_patient}}
                  {{assign var=object_guid value=$new_reglement->_ref_object->_guid}}
                  <button class="add" type="button" onclick="Rapport.addReglement('{{$object_guid}}', '{{$new_reglement->emetteur}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}', '{{$_plage.plage->_id}}');">
                    {{tr}}Add{{/tr}} <strong>{{mb_value object=$new_reglement field=montant}}</strong>
                  </button>
                </td>
              </tr>
              {{/if}}
            </table>
          </td>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <td>
            <table class="layout">
              {{foreach from=$_facture->_ref_reglements_tiers item=_reglement}}
              <tr>
                <td class="narrow">
                  <button class="edit notext" type="button" onclick="Rapport.editReglement('{{$_reglement->_id}}', '{{$_plage.plage->_id}}');">
                    {{tr}}Edit{{/tr}}
                  </button>
                </td>
                <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                <td>{{mb_value object=$_reglement field=mode}}</td>
                <td class="narrow">{{mb_value object=$_reglement field=date date=$_plage.plage->date}}</td>
              </tr>
              {{/foreach}}

              {{if abs($_facture->_du_restant_tiers) > 0.001}}
              <tr>
                <td colspan="4" class="button">
                  {{assign var=new_reglement value=$_facture->_new_reglement_tiers}}
                  {{assign var=object_guid value=$new_reglement->_ref_object->_guid}}
                  <button class="add" type="button" onclick="Rapport.addReglement('{{$object_guid}}', '{{$new_reglement->emetteur}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}', '{{$_plage.plage->_id}}');">
                    {{tr}}Add{{/tr}} <strong>{{mb_value object=$new_reglement field=montant}}</strong>
                  </button>
                </td>
              </tr>
              {{/if}}
            </table>

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
