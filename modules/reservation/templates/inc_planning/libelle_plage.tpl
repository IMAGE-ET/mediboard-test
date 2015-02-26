{{*
 * $Id$
 *  
 * @category Reservation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
<span>

  <span class="data" style="display: none;" data-duree="{{$sejour->_duree}}" data-entree_prevue='{{$sejour->entree_prevue}}' data-sortie_prevue='{{$sejour->sortie_prevue}}' data-sejour_id='{{$sejour->_id}}' data-preop='{{if $operation->presence_preop}}{{$operation->presence_preop|date_format:"%H:%M"}}{{else}}00:00{{/if}}' data-postop='{{if $operation->presence_postop}}{{$operation->presence_postop|date_format:"%H:%M"}}{{else}}00:00{{/if}}' data-traitement='{{$charge->_id}}' data-pec='{{$sejour->type_pec}}'></span>
  <!-- CADRE DROIT -->
  <span style="float:right; text-align: right">
    <!-- only switzerland -->
    {{if $conf.ref_pays == 2 }}
      {{if $liaison_sejour}}
        <strong onclick="Prestations.edit('{{$sejour->_id}}', 'sejour', '{{$operation->date}}')">{{$liaison_sejour}}</strong>
      {{/if}}

      {{if "dPplanningOp CSejour use_charge_price_indicator"|conf:"CGroups-$g" != "no" && $charge->_id}}
        <strong style='background: #{{$charge->color}}; padding:2px; color:#{{$charge->_font_color}};'>{{$charge->code}}</strong>
      {{/if}}
    {{/if}}

    {{if $conf.reservation.display_dossierBloc_button}}
      <button class="bistouri notext" onclick="modalDossierBloc('{{$operation->_id}}')">Dossier Bloc</button>
    {{/if}}

    <!-- facture -->
    {{if $conf.dPplanningOp.CFactureEtablissement.use_facture_etab && $conf.reservation.display_facture_button && $facture->_id}}
      {{assign var=close value=$facture->cloture}}
      {{if $conf.dPfacturation.Other.use_field_definitive}}
        {{assign var=close value=$facture->definitive}}
      {{/if}}

      {{if $close}}
        {{assign var=couleur value="blue"}}
      {{else}}
        {{assign var=couleur value="#FF0"}}
      {{/if}}

      {{if $facture->patient_date_reglement}}
        {{assign var=couleur value="green"}}
      {{/if}}
      <button class="calcul notext" onclick="Facture.edit({{$facture->_id}}, '{{$facture->_class}}')" style="border-left: {{$couleur}} 3px solid;">Facture</button>
    {{/if}}

  </span>

  <br/>
  <span onmouseover='ObjectTooltip.createEx(this, "{{$patient->_guid}}")'>
    <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}">{{$patient->_view}} ({{$patient->sexe}})<br/></span>
    [{{mb_value object=$patient field=naissance}}] {{$lit}}
  </span>

  {{if @$modules.mvsante->_can->read && "mvsante"|module_active}}
    <span style="float: right">
      {{mb_include module=planningOp template=inc_button_infos_interv operation_id=$operation->_id}}
    </span>
  {{/if}}

  {{if $interv_en_urgence}}
    <span style='float: right' title='Intervention en urgence'><img src='images/icons/attente_fourth_part.png' /></span>
  {{/if}}

  <br/>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$operation->_ref_chir}}
  <br/><span style='font-size: 11px; font-weight: bold;' onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}')">{{$debut_op|date_format:"%H:%M"}} - {{$fin_op|date_format:"%H:%M"}}<br/>
    {{$operation->libelle}}</span><hr/>


  coté : <strong>{{$operation->cote}}</strong><br/>
  {{if $operation->_ref_type_anesth}}
    Type anest. : <strong>{{$operation->_ref_type_anesth}}</strong><br/>
  {{/if}}


  <!-- bloc allergie & atcd -->
  {{if $patient->_ref_dossier_medical->_count_allergies > 0 || $count_atcd > 0 }}
    <hr/>
  {{/if}}

  {{if $patient->_ref_dossier_medical->_count_allergies > 0}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" ><img src="images/icons/warning.png" alt="WRN"/></span>
  {{/if}}

  {{if $count_atcd > 0}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_ref_dossier_medical->_guid}}', 'antecedents');" ><img src="images/icons/antecedents.gif" alt=\"WRN\"/></span>
  {{/if}}
  <hr/>

  Sejour: <span onmouseover='ObjectTooltip.createEx(this, "{{$sejour->_guid}}")'>{{mb_value object=$sejour field=entree}}</span>
  {{if $operation->materiel}}
    <span>{{mb_value object=$operation field=materiel}}</span>
  {{/if}}
  {{if $operation->exam_per_op}}
    <span>{{mb_value object=$operation field=exam_per_op}}</span>
  {{/if}}


  {{if $chir_2->_id}}
    <br/><span onmouseover='ObjectTooltip.createEx(this, "{{$chir_2->_guid}}")'>{{$chir_2->_view}}</span>
  {{/if}}

  {{if $chir_3->_id}}
    <br/><span onmouseover='ObjectTooltip.createEx(this, "{{$chir_3->_guid}}")'>{{$chir_3->_view}}</span>
  {{/if}}

  {{if $chir_4->_id}}
    <br/><span onmouseover='ObjectTooltip.createEx(this, "{{$chir_4->_guid}}")'>{{$chir_4->_view}}</span>
  {{/if}}

  {{if $anesth->_id}}
    <img src="images/icons/anesth.png" alt="WRN"/><span onmouseover="ObjectTooltip.createEx(this, '{{$anesth->_guid}}')">{{$anesth->_view|smarty:nodefaults}}</span>
  {{/if}}

  {{if $operation->rques}}
    <hr/>
    <strong>Rques:</strong> {{$operation->rques}}
  {{/if}}

  {{if count($besoins)}}
    <span class='compact' style='color: #000'>
      {{foreach from=$besoins item=_besoin}}
        {{$_besoin->_ref_type_ressource->libelle}},
      {{/foreach}}
    </span>
  {{/if}}

</span>