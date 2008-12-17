{{assign var=line value=$curr_line}}

  <!-- Header de la ligne -->
  <tr id="line_medicament_{{$line->_id}}" class="hoverable 
  {{if $line->_traitement}}traitement{{else}}med{{/if}}
  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}">
    <td style="text-align: center; width: 5%;">
      {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
      {{/if}}  
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
      {{/if}}
      {{if $line->_ref_produit->_supprime}}
      <br /><img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
      {{/if}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
    </td>
    <td style="width: 25%" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="text {{if $line->_traitement}}traitement{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole}}
         Main.add( function(){
           moveTbody($('line_medicament_{{$line->_id}}'));
         });
         {{/if}}
      </script>
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit({{$line->_ref_produit->code_cip}})" style="font-weight: bold;">
        {{$line->_ucd_view}}
      </a>
      {{if $line->_can_view_historique}}
        <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique"/>
      {{/if}}
      {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
      {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
      {{if $line->_traitement}}Traitement personnel&nbsp;{{/if}}
    </td>
    <td class="text" style="width: 20%">
        {{if $line->_can_view_signature_praticien}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else if !$line->_traitement && !$line->_protocole}}
          {{$line->_ref_praticien->_view}}
        {{/if}}
        {{if !$line->_protocole}}
        <!-- Vue normale  -->
          {{if $line->_traitement}}
            Médecin traitant (Créé par {{$line->_ref_praticien->_view}})
          {{/if}}
        {{/if}}
    </td>
    <td style="width: 15%">
      <!-- Date de debut -->
      {{if $line->debut}}
        {{mb_value object=$line field=debut}}
        {{if $line->time_debut}}
          à {{mb_value object=$line field=time_debut}}
        {{/if}}
      {{/if}}
    </td>
    <td style="width: 10%">
      <!-- Duree de la ligne -->
      {{if $line->duree && $line->unite_duree}}
        {{mb_value object=$line field=duree}}  
        {{mb_value object=$line field=unite_duree}}
      {{/if}}
    </td>

    <td style="width: 25%;" class="text">   
	    {{if $line->_ref_prises|@count}}
	      {{foreach from=$line->_ref_prises item=_prise name=prises}}
	        {{$_prise->_view}} {{if !$smarty.foreach.prises.last}}, {{/if}}
	      {{/foreach}}
	    {{else}}
	      Aucune posologie
	    {{/if}}
    </td>
   
  </tr>
