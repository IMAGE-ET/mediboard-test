{{mb_script module=cabinet script=reglement}}
{{mb_script module=facturation script=rapport}}

{{assign var=type_aff value=1}}
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
  {{assign var=type_aff value=0}}
{{/if}}

{{if !$ajax}} 

<div style="float: right;"> 
  {{mb_include module=facturation template=inc_totaux_actes}}
</div>

<div>
  <a href="#" onclick="window.print()">
    Rapport
    {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
  </a>
</div>

<!-- Praticiens concern�s -->
{{foreach from=$listPrat item=_prat}}
<div>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</div>
{{/foreach}}

{{/if}}
  
{{if $filter->_type_affichage}}
<table class="main">
  {{foreach from=$listPlages item=_sejour key=_date}}
  {{if !$ajax}} 
  <tbody id="{{$_date}}">
  {{/if}}
    
  <tr>
    <td colspan="2">
      <br />
      <br />
      <strong onclick="Rapport.refresh('{{$_date}}')">
        <strong>Sortie r�elle le {{$_sejour.plage->sortie|date_format:$conf.longdate}}</strong>
      </strong>
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th colspan="2" class="narrow text">{{tr}}CFactureEtablissement{{/tr}}</th>
          <th style="width: 20%;">{{mb_label class=CFactureEtablissement field=patient_id}}</th>
          <th style="width: 20%;">Type</th>
          
          {{if $type_aff}}
            <th class="narrow">{{mb_title class=CFactureEtablissement field=_secteur1}}</th>
            <th class="narrow">{{mb_title class=CFactureEtablissement field=_secteur2}}</th>
            <th class="narrow">{{mb_title class=CConsultation field=_somme}}</th>
            <th style="width: 20%;">{{mb_title class=CFactureEtablissement field=du_patient}}</th>
            <th style="width: 20%;">{{mb_title class=CFactureEtablissement field=du_tiers}}</th>
          {{else}}
            <th class="narrow">Montant</th>
            <th class="narrow">Remise</th>
            <th class="narrow">{{mb_title class=CConsultation field=_somme}}</th>
            <th style="width: 20%;">{{mb_title class=CFactureEtablissement field=du_patient}}</th>
          {{/if}}
          
          <th>{{mb_title class=CConsultation field=patient_date_reglement}}</th>
        </tr>
        
        {{foreach from=$_sejour.factures item=_facture}}
        <tr>
          {{if $_facture->_id}}
          <td>
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_guid}}')">
              {{$_facture}}
            </strong>
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
            Sejour du {{mb_value object=$_sejour.plage field=_entree}}
            au {{mb_value object=$_sejour.plage field=_sortie}}
            
            {{foreach from=$_sejour.plage->_ref_operations item=operation}}
              <br/>Intervention du {{mb_value object=$operation field=date}}
              {{if $operation->libelle}}<br /> {{$operation->libelle}}{{/if}}
            {{/foreach}}
          
            {{foreach from=$_sejour.plage->_ref_consultations item=consult}}
              <br/>Consultation du {{$consult->_datetime|date_format:"%d %B %Y"}}
              {{if $consult->motif}}: {{$consult->motif}}{{/if}}
            {{/foreach}}
          </td>

          {{if $type_aff}}
            <td>{{mb_value object=$_facture field=_secteur1 empty=1}}</td>
            <td>{{mb_value object=$_facture field=_secteur2 empty=1}}</td>
            <td>{{mb_value object=$_facture field=_montant_total    empty=1}}</td>
          {{else}}
            <td>{{mb_value object=$_facture field=_montant_sans_remise empty=1}}</td>
            <td>{{mb_value object=$_facture field=remise empty=1}}</td>
            <td>{{mb_value object=$_facture field=_montant_avec_remise empty=1}}</td>
          {{/if}}

          <td>
            <table class="layout">
              {{foreach from=$_facture->_ref_reglements_patient item=_reglement}}
              <tr>
                <td class="narrow">
                  <button class="edit notext" type="button" onclick="Rapport.editReglement('{{$_reglement->_id}}', '{{$_date}}');">
                    {{tr}}Edit{{/tr}}
                  </button>
                </td>
                <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                <td>{{mb_value object=$_reglement field=mode}}</td>
                <td class="narrow">{{mb_value object=$_reglement field=date date=$_sejour.plage->sortie}}</td>
              </tr>
              {{/foreach}}
              
              {{if abs($_facture->_du_restant_patient) > 0.001}}
              <tr>
                <td colspan="4" class="button">
                  {{assign var=new_reglement value=$_facture->_new_reglement_patient}}
                  {{assign var=object_guid value=$new_reglement->_ref_object->_guid}}
                  <button class="add" type="button" onclick="Rapport.addReglement('{{$object_guid}}', '{{$new_reglement->emetteur}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}', '{{$_date}}');">
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
                  <button class="edit notext" type="button" onclick="Rapport.editReglement('{{$_reglement->_id}}', '{{$_date}}');">
                    {{tr}}Edit{{/tr}}
                  </button>
                </td>
                <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                <td>{{mb_value object=$_reglement field=mode}}</td>
                <td class="narrow">{{mb_value object=$_reglement field=date date=$_sejour.plage->sortie}}</td>
              </tr>
              {{/foreach}}

              {{if abs($_facture->_du_restant_tiers) > 0.001}}
              <tr>
                <td colspan="4" class="button">
                  {{assign var=new_reglement value=$_facture->_new_reglement_tiers}}
                  {{assign var=object_guid value=$new_reglement->_ref_object->_guid}}
                  <button class="add" type="button" onclick="Rapport.addReglement('{{$object_guid}}', '{{$new_reglement->emetteur}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}', '{{$_date}}');">
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
              {{if $_facture->_id}}
                {{mb_key object=$_facture}}
                {{mb_class object=$_facture}}
              {{else}}
                <input type="hidden" name="m" value="dPcabinet" />
                <input type="hidden" name="del" value="0" />
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
        <tr>
          <td colspan="4" style="text-align: right" >
            <strong>{{tr}}Total{{/tr}}</strong>
          </td>
          <td><strong>{{$_sejour.total.secteur1|currency}}</strong></td>
          <td><strong>{{$_sejour.total.secteur2|currency}}</strong></td>
          <td><strong>{{$_sejour.total.total|currency}}</strong></td>
          <td><strong>{{$_sejour.total.patient|currency}}</strong></td>
          {{if $type_aff}}
            <td><strong>{{$_sejour.total.tiers|currency}}</strong></td>
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