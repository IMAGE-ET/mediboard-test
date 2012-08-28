{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
{{if !$offline}}
  Main.add(window.print);
{{/if}}

function printPage(element){
  {{if !$offline}}window.print(); return;{{/if}}
  
  var mainCourante = $("main-courante-container").clone(true);
  var container = DOM.div({}, mainCourante);
  var dossiers = mainCourante.select('div.dossier > .content');
  
  dossiers.each(function(e){
    container.insert(e);
  });
  
  container.print();
}
</script>

<div id="main-courante-container">
  
<table class="main">
  <tr>
    <th>
      {{if $offline}}
        <button style="float: left;" onclick="window.print()" class="print not-printable">Main courante</button>
        <button style="float: left;" onclick="printPage(this)" class="print not-printable">Dossiers</button>
        <span style="float: right;">
          {{$dateTime|date_format:$conf.datetime}}
        </span>
      {{/if}}
       <a href="#print" onclick="printPage(this)">
        Résumé des Passages aux Urgences du 
        {{$date|date_format:$conf.longdate}}
        <br /> Total: {{$sejours|@count}} RPU
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="narrow text">{{mb_title class=CRPU field=_entree}}</th>
          <th>{{mb_title class=CRPU field=_patient_id}}</th>
          <th style="width: 8em;">{{mb_title class=CRPU field=ccmu}}</th>
          <th>{{mb_title class=CRPU field=diag_infirmier}}</th>
          <th class="narrow">Heure PeC</th>
          <th style="width: 8em;">{{mb_title class=CRPU field=_responsable_id}}</th>  
          <th class="narrow">
            {{mb_title class=CSejour field=mode_sortie}} 
            <br/> &amp; 
            {{mb_title class=CRPU field=orientation}}
          </th>
          <th class="narrow">{{mb_title class=CRPU field=_sortie}}</th>
        </tr>
        
        {{foreach from=$sejours item=sejour}}
        {{assign var=rpu value=$sejour->_ref_rpu}}
        {{assign var=patient value=$sejour->_ref_patient}}
        {{assign var=consult value=$rpu->_ref_consult}}
        <tr>
          <td style="text-align: right;">
            {{mb_value object=$sejour field=entree}}
            {{if $sejour->_veille}}
              <br/> Admis la veille
            {{/if}}
          </td>
          <td class="text">
            {{if $offline && $rpu->_id}}
              <button class="search notext not-printable" onclick="$('modal-{{$sejour->_id}}').up('tr').show(); modalwindow = modal($('modal-{{$sejour->_id}}'));">
                {{tr}}Show{{/tr}}
              </button>
             {{/if}}
            {{assign var=rpu_link value="#`$patient->_guid`"}}
            {{mb_include template=inc_rpu_patient}}
          </td>
        {{if $rpu->_id}}
          <td class="ccmu-{{$rpu->ccmu}} text">
            {{if $rpu->ccmu}}
              {{mb_value object=$rpu field="ccmu"}}
            {{/if}}
          </td>
          <td class="text">
            {{if $rpu->date_at}} 
            <img src="images/icons/accident_travail.png" />
            {{/if}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
              {{$rpu->diag_infirmier|nl2br}}
            </span>
          </td>    
          <td>{{mb_value object=$consult field="heure"}}</td>      
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}</td>
          <td>
            {{if $sejour->sortie_reelle}}
              {{mb_value object=$sejour field="mode_sortie"}}
            {{/if}}
            {{if $sejour->mode_sortie == "transfert"}}
              <br />
              &gt; <strong>{{mb_value object=$sejour field=etablissement_sortie_id}}</strong>
            {{/if}}
            {{if $sejour->mode_sortie == "mutation"}}
              <br />
              &gt; <strong>{{mb_value object=$sejour field=service_sortie_id}}</strong>
            {{/if}}
            {{if $rpu->orientation}}
              <br />
              {{mb_value object=$rpu field="orientation"}}
            {{/if}}
            <em>{{mb_value object=$sejour field=commentaires_sortie}}</em>
          </td>
          
          
          {{if $sejour->type != "urg"}}
            <td colspan="2" class="text arretee">
              <strong>{{mb_value object=$sejour field=type}}</strong>
            </td>

          {{elseif $sejour->annule}}
          <td class="cancelled" colspan="2">
            {{tr}}Cancelled{{/tr}}
          </td>
          
          {{elseif $rpu->mutation_sejour_id}}
          {{mb_include template=inc_dossier_mutation colspan=2}}
            
          {{else}}
            {{if !$sejour->sortie_reelle}}
              <td />
            {{else}}
              <td style="text-align: right;">{{mb_value object=$sejour field=_sortie}}</td>
            {{/if}}
          {{/if}}
        {{else}}
          <!-- Pas de RPU pour ce séjour d'urgence -->
          <td colspan="10">
            <div class="small-warning">
              Ce séjour d'urgence n'est pas associé à un RPU.
            </div>
          </td>
        {{/if}}
        </tr>
        
        <!-- Modal window -->
        <tr style="display: none;" class="modal-row">
          <td colspan="8">
            {{if $offline && $rpu->_id}}
              {{assign var=sejour_id value=$sejour->_id}}
              <div id="modal-{{$sejour->_id}}" style="height: 90%; min-width: 700px; overflow: auto;" class="dossier">
                <button style="float: right" class="cancel not-printable" onclick="modalwindow.close(); $('modal-{{$sejour->_id}}').up('tr').hide()">{{tr}}Close{{/tr}}</button>
                <button style="float: right" class="print not-printable" onclick="$(this).next('.content').print()">{{tr}}Print{{/tr}}</button>
                
                <div class="content" style="page-break-before: always;">
                  {{$offlines.$sejour_id|smarty:nodefaults}}
                </div>
              </div>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th class="title" colspan="1">
      Statistiques d'entrée
      <small>({{$stats.entree.total}})</small>
    </th>
    <th class="title" colspan="2">
      Statistiques de sorties
      <small>({{$stats.sortie.total}})</small>
    </th>
  </tr>
  
  <tr>
    <th>{{mb_title class=CPatient field=_age}}</th>
    <th>
      {{mb_title class=CSejour field=etablissement_sortie_id}}
      <small>({{$stats.sortie.transferts_count}})</small>
    </th>
    <th>
      {{mb_title class=CSejour field=service_sortie_id}}
      <small>({{$stats.sortie.mutations_count}})</small>
    </th>
  </tr>
  
  <tr>
    <td>
      <ul>
        <li>
          Patients de moins de 1 ans : 
          <strong>{{$stats.entree.less_than_1}}</strong>
        </li>
        <li>
          Patients de 75 ans ou plus : 
          <strong>{{$stats.entree.more_than_75}}</strong>
        </li>
      </ul>
    </td>

    <td>
      <ul>
        {{foreach from=$stats.sortie.etablissements_transfert item=_etablissement_transfert}}
        <li>
          {{$_etablissement_transfert.ref}} : 
          <strong>{{$_etablissement_transfert.count}}</strong>
        </li>
        {{foreachelse}}
        <li class="empty">{{tr}}None{{/tr}}</li>
        {{/foreach}}
      </ul>
    </td>

    <td>
      <ul>
        {{foreach from=$stats.sortie.services_mutation item=_service_mutation}}
        <li>
          {{$_service_mutation.ref}} : 
          <strong>{{$_service_mutation.count}}</strong>
        </li>
        {{foreachelse}}
        <li class="empty">{{tr}}None{{/tr}}</li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
</table>

</div>