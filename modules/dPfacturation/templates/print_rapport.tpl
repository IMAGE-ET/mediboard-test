{{mb_script module=cabinet script=reglement}}
{{mb_script module=facturation script=rapport}}

{{assign var=type_aff value=1}}
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
  {{assign var=type_aff value=0}}
{{/if}}

{{if !$ajax}} 

<div style="float: right;"> 
  {{mb_include module=facturation template=inc_totaux_rapport}}
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
      <strong>
        {{$_plage.plage->_ref_chir}}
        {{if $_plage.plage->_ref_pour_compte->_id}}
          pour le compte de {{$_plage.plage->_ref_pour_compte->_view}}
        {{/if}}
        
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
          <th colspan="2" class="narrow text">{{tr}}CFactureCabinet{{/tr}}</th>
          <th style="width: 20%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th style="width: 20%;">{{mb_label class=CConsultation field=tarif}}</th>
          
          {{if $type_aff}}
            <th class="narrow">{{mb_title class=CConsultation field=secteur1}}</th>
            <th class="narrow">{{mb_title class=CConsultation field=secteur2}}</th>
            <th class="narrow">{{mb_title class=CConsultation field=secteur3}}</th>
            <th class="narrow">{{mb_title class=CConsultation field=du_tva}}</th>
            <th class="narrow">{{mb_title class=CConsultation field=_somme}}</th>
            <th style="width: 20%;">{{mb_title class=CConsultation field=du_patient}}</th>
            <th style="width: 20%;">{{mb_title class=CConsultation field=du_tiers}}</th>
          {{else}}
            <th class="narrow">Montant</th>
            <th class="narrow">Remise</th>
            <th class="narrow">{{mb_title class=CConsultation field=_somme}}</th>
            <th style="width: 20%;">{{mb_title class=CConsultation field=du_patient}}</th>
          {{/if}}
          
          <th>{{mb_title class=CConsultation field=patient_date_reglement}}</th>
        </tr>
        
        {{foreach from=$_plage.factures item=_facture}}
        <tr id="line_facture_{{$_facture->_guid}}">
          {{if $_facture->_id}}
          <td>
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_guid}}')">
              {{$_facture}}
            </strong>
            {{if $_facture->group_id != $g}}
              <span class="compact"></br>({{$_facture->_ref_group}})</span>
            {{/if}}
          </td>
          <td>{{mb_include module=system template=inc_object_notes object=$_facture}}</td>
          {{else}}
          <td colspan="2">
            <strong>{{$_facture}}</strong>
          </td>
          {{/if}}
        
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
          
          <td>{{mb_value object=$_facture field=_secteur1 empty=1}}</td>
          {{if $type_aff}}
            <td>{{mb_value object=$_facture field=_secteur2 empty=1}}</td>
            <td>{{mb_value object=$_facture field=_secteur3 empty=1}}</td>
            <td>{{mb_value object=$_facture field=du_tva empty=1}}</td>
          {{else}}
            <td>{{mb_value object=$_facture field=remise empty=1}}</td>
          {{/if}}
          <td>{{mb_value object=$_facture field=_montant_avec_remise empty=1}}</td>

          <td>
            <table class="layout">
              {{foreach from=$_facture->_ref_reglements_patient item=_reglement}}
              <tr>
                <td class="narrow">
                  <button class="edit notext" type="button" onclick="Rapport.editReglement('{{$_reglement->_id}}', '{{$_reglement->date}}', '{{$_facture->_guid}}', '{{$_plage.plage->_guid}}');">
                    {{tr}}Edit{{/tr}}
                  </button>
                </td>
                <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                <td>{{mb_value object=$_reglement field=mode}}</td>
                <td class="narrow">{{mb_value object=$_reglement field=date date=$_plage.plage->date}}</td>
              </tr>
              {{/foreach}}
              
              {{if abs($_facture->_du_restant_patient) > 0.01}}
                <tr>
                  <td colspan="4" class="button">
                    {{assign var=new_reglement value=$_facture->_new_reglement_patient}}
                    {{assign var=object_guid value=$new_reglement->_ref_object->_guid}}
                    <button class="add" type="button" onclick="Rapport.addReglement('{{$object_guid}}', '{{$new_reglement->emetteur}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}', '{{$new_reglement->date}}', '{{$_plage.plage->_guid}}');">
                      {{tr}}Add{{/tr}} <strong>{{mb_value object=$new_reglement field=montant}}</strong>
                    </button>
                  </td>
                </tr>
              {{/if}}
            </table>
          </td>
          
          {{if $type_aff}}
          <td>
            <table class="layout">
              {{foreach from=$_facture->_ref_reglements_tiers item=_reglement}}
              <tr>
                <td class="narrow">
                  <button class="edit notext" type="button" onclick="Rapport.editReglement('{{$_reglement->_id}}', '{{$_reglement->date}}', '{{$_facture->_guid}}', '{{$_plage.plage->_guid}}');">
                    {{tr}}Edit{{/tr}}
                  </button>
                </td>
                <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                <td>{{mb_value object=$_reglement field=mode}}</td>
                <td class="narrow">{{mb_value object=$_reglement field=date date=$_plage.plage->date}}</td>
              </tr>
              {{/foreach}}

              {{if abs($_facture->_du_restant_tiers) > 0.01}}
              <tr>
                <td colspan="4" class="button">
                  {{assign var=new_reglement value=$_facture->_new_reglement_tiers}}
                  {{assign var=object_guid value=$new_reglement->_ref_object->_guid}}
                  <button class="add" type="button" onclick="Rapport.addReglement('{{$object_guid}}', '{{$new_reglement->emetteur}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}', '{{$new_reglement->date}}', '{{$_plage.plage->_guid}}');">
                    {{tr}}Add{{/tr}} <strong>{{mb_value object=$new_reglement field=montant}}</strong>
                  </button>
                </td>
              </tr>
              {{/if}}
            </table>
          </td>
          {{/if}}
          <td>
            <form name="edit-date-aquittement-{{$_facture->_guid}}" action="#" method="post">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              {{if $_facture->_id}}
                <input type="hidden" name="dosql" value="do_facturecabinet_aed" />
                <input type="hidden" name="facture_id" value="{{$_facture->_id}}" />
              {{else}}
                <input type="hidden" name="dosql" value="do_consultation_aed" />
                <input type="hidden" name="consultation_id" value="{{$_facture->_ref_last_consult->_id}}" />
              {{/if}}

              <input type="hidden" name="patient_date_reglement" class="date" value="{{$_facture->patient_date_reglement}}" />
              <button type="button" class="submit notext" onclick="onSubmitFormAjax(this.form);"></button>
              <script>
                Main.add(function(){
                  Calendar.regField(getForm("edit-date-aquittement-{{$_facture->_guid}}").patient_date_reglement);
                });
              </script>
            </form>
          </td>
        </tr>
        
        {{/foreach}}
        <tr id="{{$_plage.plage->_guid}}_total">
          <td colspan="4" style="text-align: right" >
            <strong>{{tr}}Total{{/tr}}</strong>
          </td>
          <td><strong>{{$_plage.total.secteur1|currency}}</strong></td>
          <td><strong>{{$_plage.total.secteur2|currency}}</strong></td>
          {{if $type_aff}}
            <td><strong>{{$_plage.total.secteur3|currency}}</strong></td>
            <td><strong>{{$_plage.total.du_tva|currency}}</strong></td>
          {{/if}}
          <td><strong>{{$_plage.total.total|currency}}</strong></td>
          <td><strong>{{$_plage.total.patient|currency}}</strong></td>
          {{if $type_aff}}
            <td><strong>{{$_plage.total.tiers|currency}}</strong></td>
          {{/if}}
          <td></td>
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