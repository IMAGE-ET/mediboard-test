{{assign var=type_aff value=1}}
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
  {{assign var=type_aff value=0}}
{{/if}}
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
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}
        <tr>
          <td>Paiments pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
        </tr>
      </table>
    </td>
    
    <td class="halfPane">
      {{mb_include module=facturation template=inc_totaux_rapport}}
    </td>
  </tr>
  {{if $filter->_type_affichage}}
  {{foreach from=$listReglements key=key_date item=_date}}
  <tr>
    <td colspan="2"><strong>Règlements du {{$key_date|date_format:$conf.longdate}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th rowspan="2"  colspan="2" class="narrow text">{{tr}}CFactureCabinet{{/tr}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=_prat_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=_date}}: {{mb_label class=CConsultation field=tarif}}</th>
          
          {{if $type_aff}}
            <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=_secteur1}}</th>
            <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=_secteur2}}</th>
            <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=_secteur3}}</th>
            <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=du_tva}}</th>
          {{else}}
            <th rowspan="2" class="narrow">Montant</th>
            <th rowspan="2" class="narrow">Remise</th>
          {{/if}}
          
          <th rowspan="2" class="narrow">Total</th>
          <th rowspan="2" class="narrow">{{mb_label class=CReglement field=mode}}</th>
          <th colspan="2" class="narrow">{{mb_title class=CReglement field=emetteur}}</th>
        </tr>
        {{if $type_aff}}
          <tr>
            <th class="narrow">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
            <th class="narrow">{{tr}}CReglement.emetteur.tiers{{/tr}}</th>
          </tr>
        {{else}}
          <th class="narrow" colspan="2">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
        {{/if}}
        
        {{foreach from=$_date.reglements item=_reglement}}
        {{assign var=_object value=$_reglement->_ref_object}}
        {{assign var=facture value=$_reglement->_ref_facture}}
        {{assign var=prat_id value=$facture->_ref_praticien->_id}}
        
        <tr>
          <td>
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$facture->_guid}}')">
              {{$facture}}
            </strong>
            {{if $facture->group_id != $g}}
              <span class="compact"></br>({{$facture->_ref_group}})</span>
            {{/if}}
          </td>
          <td>{{mb_include module=system template=inc_object_notes object=$facture}}</td>          
          <td class="text">
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$facture->_ref_praticien}}
          </td>

          <td class="text">
            {{assign var=patient value=$facture->_ref_patient}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
          </td>

          <td class="text">
            {{foreach from=$facture->_ref_consults item=_consult}}
            <div>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
                {{mb_value object=$_consult field=_date}}: {{mb_value object=$_consult field=tarif}}
              </span>
            </div>   
            {{foreachelse}}
            <div class="empty">{{tr}}CConsultation.none{{/tr}}</div>
            {{/foreach}}
          </td>
          
          {{if $type_aff}}
            <td {{if $facture->_secteur1 < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_secteur1}}</td>
            <td {{if $facture->_secteur2 < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_secteur2}}</td>
            <td {{if $facture->_secteur3 < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_secteur3}}</td>
            <td {{if $facture->du_tva < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=du_tva}}</td>
            <td {{if $facture->_montant_avec_remise < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_montant_avec_remise}}</td>
          {{else}}
            <td style="text-align: right;">{{mb_value object=$facture field=_montant_sans_remise}}</td>
            <td style="text-align: right;">{{mb_value object=$facture field=remise}}</td>
            <td style="text-align: right;">{{mb_value object=$facture field=_montant_avec_remise}}</td>
          {{/if}}

          <td style="text-align: center;">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_reglement->_guid}}')">{{mb_value object=$_reglement field=mode}}</span>
          </td>
          
          <td style="text-align: right;">
            {{if $_reglement->emetteur == "patient"}}
              {{mb_value object=$_reglement field=montant}}
            {{/if}}
          </td>
          
          {{if $type_aff}}
            <td  style="text-align: right;">
              {{if $_reglement->emetteur == "tiers"}}
                {{mb_value object=$_reglement field=montant}}
              {{/if}}
            </td>
          {{/if}}
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="{{if $type_aff}}10{{else}}8{{/if}}"></td>
          <td><strong>{{tr}}Total{{/tr}}</strong></td>
          <td><strong>{{$_date.total.patient|currency}} </strong></td>
          {{if $type_aff}}
            <td><strong>{{$_date.total.tiers|currency}}</strong></td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>