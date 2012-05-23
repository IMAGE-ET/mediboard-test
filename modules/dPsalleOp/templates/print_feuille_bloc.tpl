{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function(){
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
  url.addParam("patient_id", {{$operation->_ref_sejour->patient_id}});
  url.addParam("context_guid", "{{$operation->_ref_sejour->_guid}}");
  url.addParam("selection[]", ["pouls", "ta_gauche", "frequence_respiratoire", "score_sedation", "spo2", "diurese"]);
  url.addParam("date_min", "{{$operation->_datetime_reel}}");
  url.addParam("date_max", "{{$operation->_datetime_reel_fin}}");
  url.addParam("print", 1);
  url.requestUpdate("constantes");
});

</script>

{{assign var=sejour value=$operation->_ref_sejour}}
{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=consult_anesth value=$operation->_ref_consult_anesth}}

<table class="print">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;">
        <tr>
          <th class="title" colspan="4">
            <a href="#" onclick="window.print()">
              Feuille de Bloc
            </a>
          </th>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="print">
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
        </tr>
        <tr>
          <td colspan="2">{{$patient->_view}}</td>
        </tr>
        <tr>
          <td colspan="2">
            Né{{if $patient->sexe != "m"}}e{{/if}} le {{mb_value object=$patient field=naissance}}
            ({{$patient->_age}} ans)
            - sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}
          </td>
        </tr>
      </table>
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>

        <tr>
          <th>{{mb_label object=$operation->_ref_plageop field=date}}</th>
          <td class="greedyPane">
            {{mb_value object=$operation->_ref_plageop field=date}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=salle_id}}</th>
          <td class="text">
            {{$operation->_ref_salle->_view}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=libelle}}</th>
          <td class="text">
            {{if $operation->libelle}}
              {{mb_value object=$operation field=libelle}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field=ASA}}</th>
          <td>
            {{if $consult_anesth->ASA}}
              {{mb_value object=$consult_anesth field=ASA}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=cote}}</th>
          <td>{{mb_value object=$operation field=cote}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field=position}}</th>
          <td>
            {{if $consult_anesth->position}}
              {{mb_value object=$consult_anesth field=position}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=chir_id}}</th>
          <td class="text">Dr {{$operation->_ref_chir->_view}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=anesth_id}}</th>
          <td class="text">
            {{if $operation->_ref_anesth->user_id}}
              Dr {{$operation->_ref_anesth->_view}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=type_anesth}}</th>
          <td class="text">
            {{if $operation->type_anesth}}
              {{mb_value object=$operation field=type_anesth}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Personnel</th>
          <td class="text">
            Non Disponible
          </td>
        </tr>
        <tr>
          <th>Matériel</th>
          <td class="text">
            {{if $operation->materiel}}
              {{mb_value object=$operation field=materiel}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>       
        <tr>
          <th>Remarques</th>
          <td class="text">
            {{if $operation->rques}}
              {{mb_value object=$operation field=rques}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Visite de pré-anesthésie</th>
        </tr>
        {{if $operation->prat_visite_anesth_id}}
        <tr>
          <th>{{mb_label object=$operation field=prat_visite_anesth_id}}</th>
          <td>Dr {{$operation->_ref_anesth_visite->_view}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=date_visite_anesth}}</th>
          <td>{{$operation->date_visite_anesth|date_format:$conf.date}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=rques_visite_anesth}}</th>
          <td>{{mb_value object=$operation field=rques_visite_anesth}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=autorisation_anesth}}</th>
          <td>{{mb_value object=$operation field=autorisation_anesth}}</td>
        </tr>
        {{else}}
        <tr>
          <td colspan="2">Non saisie</td>
        </tr>
        {{/if}}
      </table>
      
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Horaires</th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=debut_prepa_preop}}</th>
          <td class="halfPane">{{mb_value object=$operation field=debut_prepa_preop}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=fin_prepa_preop}}</th>
          <td class="halfPane">{{mb_value object=$operation field=fin_prepa_preop}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=entree_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=entree_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=induction_debut}}</th>
          <td class="halfPane">{{mb_value object=$operation field=induction_debut}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=induction_fin}}</th>
          <td class="halfPane">{{mb_value object=$operation field=induction_fin}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=pose_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=pose_garrot}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=debut_op}}</th>
          <td class="halfPane">{{mb_value object=$operation field=debut_op}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=fin_op}}</th>
          <td class="halfPane">{{mb_value object=$operation field=fin_op}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=retrait_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=retrait_garrot}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=sortie_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=sortie_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=entree_reveil}}</th>
          <td class="halfPane">{{mb_value object=$operation field=entree_reveil}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=sortie_reveil}}</th>
          <td class="halfPane">{{mb_value object=$operation field=sortie_reveil}}</td>
        </tr>
      </table>    
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Durées</th>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=_presence_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_presence_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=_duree_interv}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_interv}}</td>
        </tr>
        {{if $operation->_duree_garrot}}
        <tr>
          <th>{{mb_label object=$operation field=_duree_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_garrot}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$operation field=_duree_sspi}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_sspi}}</td>
        </tr>
      </table>      
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <table width="100%" style="border-spacing: 0px;font-size: 100%;">
        <tr>
          <th class="category" colspan="5">Actes CCAM</th>
        </tr>
        {{assign var="styleBorder" value="border: solid #aaa 1px;"}}
        <tr>
          <th style="{{$styleBorder}}text-align:left;">Code</th>
          <th style="{{$styleBorder}}text-align:left;">Exécutant</th>
          <th style="{{$styleBorder}}text-align:left;">Activité</th>
          <th style="{{$styleBorder}}text-align:left;">Phase &mdash; Modifs.</th>
          <th style="{{$styleBorder}}text-align:left;">Association</th>
        </tr>
        {{foreach from=$operation->_ref_actes_ccam item=currActe}}
        <tr>
          <td class="text" style="{{$styleBorder}}">
            <strong>{{$currActe->code_acte}}</strong><br />
            {{$currActe->_ref_code_ccam->libelleLong}}
          </td>
          <td class="text" style="{{$styleBorder}}">{{$currActe->_ref_executant->_view}}</td>
          <td style="{{$styleBorder}}">{{$currActe->code_activite}}</td>
          <td style="{{$styleBorder}}">
            {{$currActe->code_phase}}
            {{if $currActe->modificateurs}}
            &mdash; {{$currActe->modificateurs}}
            {{/if}}
          </td>
          <td style="{{$styleBorder}}">{{$currActe->_guess_association}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>

<table class="print"> 
  <tr>
    <th class="category" colspan="4">Evenements per-opératoires</th>
  </tr>   
  {{foreach from=$perops key=datetime item=_perops_by_datetime}}
    {{foreach from=$_perops_by_datetime item=_perop}}
      <tr>
          <td style="text-align: center;">{{mb_ditto name=date value=$datetime|date_format:$conf.date}}</td>
          <td style="text-align: center;">{{mb_ditto name=time value=$datetime|date_format:$conf.time}}</td>
        {{if $_perop instanceof CAnesthPerop}}
          <td style="font-weight: bold;" colspan="2" class="greedyPane">
            {{if $_perop->incident}}
              Incident :
            {{/if}}
            {{mb_value object=$_perop field=libelle}}
          </td>
        {{elseif $_perop instanceof CAdministration}}
          {{assign var=unite value=""}}
          {{if $_perop->_ref_object instanceof CPrescriptionLineMedicament || $_perop->_ref_object instanceof CPrescriptionLineMixItem}}
            {{assign var=unite value=$_perop->_ref_object->_ref_produit->libelle_unite_presentation}}
          {{/if}}

          <td colspan="2" class="greedyPane">
            {{if $_perop->_ref_object instanceof CPrescriptionLineElement}}
              {{$_perop->_ref_object->_view}}
            {{else}}
              {{$_perop->_ref_object->_ucd_view}}
            {{/if}}
            <strong>{{$_perop->quantite}} {{$unite}} </strong>
          </td>
        {{elseif $_perop instanceof CPrescriptionLineMix}}
          <td>
            {{if $datetime == $_perop->_pose}}
            Pose de la perfusion - 
            {{else}}
            Retrait de la perfusion - 
            {{/if}} 
            {{$_perop->_short_view}}
          </td>
        {{else}}
          <td colspan="2" class="greedyPane">
          {{foreach from=$_perop key=type item=_constante}}
            {{if $_constante}}
            <strong>{{tr}}CConstantesMedicales-{{$type}}{{/tr}}:</strong> {{$_constante}}<br />
            {{/if}}
          {{/foreach}}
          </td>
        {{/if}}
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>

<div id="constantes"></div>