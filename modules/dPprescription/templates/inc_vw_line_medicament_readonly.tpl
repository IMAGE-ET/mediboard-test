{{assign var=line value=$curr_line}}

<tbody id="line_medicament_{{$line->_id}}" class="hoverable 
  {{if $line->_traitement}}traitement{{else}}med{{/if}}
  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}">
  <!-- Header de la ligne -->
  <tr>
    <th colspan="5" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="element {{if $line->_traitement}}traitement{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole}}
         Main.add( function(){
           moveTbody($('line_medicament_{{$line->_id}}'));
         });
         {{/if}}
      </script>
      <div style="float: left;">
          {{if $line->_can_view_historique}}
            <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique"/>
          {{/if}}
          {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
          {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
          {{if $line->_traitement}}{{mb_label object=$line field="_traitement"}}&nbsp;{{/if}}
      </div>
      <div style="float: right;">
        {{if $line->_can_view_signature_praticien}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else if !$line->_traitement && !$line->_protocole}}
          {{$line->_ref_praticien->_view}}
        {{/if}}
        {{if !$line->_protocole}}
        <!-- Vue normale  -->
          {{if $line->_traitement}}
            Médecin traitant (Créé par {{$line->_ref_praticien->_view}})
          {{else}}
            {{if !$line->valide_pharma}}
              {{if $line->valide_infirmiere}}
                (Validé par l'infirmière)
              {{/if}}
            {{else}}
              (Validé par le pharmacien)
            {{/if}} 
          {{/if}}
        {{/if}}
      </div>
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit({{$line->_ref_produit->code_cip}})" style="font-weight: bold;">
        {{$line->_ucd_view}}
      </a>
    </th>
  </tr>
  <tr>
    <td style="text-align: center; width: 0.1%;">
      {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
      <br />
      {{/if}}  
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      <br />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
      <br />
      {{/if}}
      {{if $line->_ref_produit->_supprime}}
      <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
      {{/if}}
    </td>
    <td colspan="2">
      <!-- Date d'arret de la ligne -->
      <div style="float: right;">
      {{if $line->date_arret}}
        Arretée le {{mb_value object=$line field=date_arret}}
        {{if $line->time_arret}}
          à {{mb_value object=$line field=time_arret}}
        {{/if}}
      {{else}}
        Aucune date d'arrêt
      {{/if}}
      </div>
      
      <!-- Date de debut -->
      {{if $line->debut}}
        {{mb_label object=$line field=debut}}: 
        {{mb_value object=$line field=debut}}
        {{if $line->time_debut}}
          à {{mb_value object=$line field=time_debut}}
        {{/if}}
        -
      {{/if}}
      
      <!-- Duree de la ligne -->
      {{if $line->duree && $line->unite_duree}}
        {{mb_label object=$line field=duree}}: 
        {{mb_value object=$line field=duree}}  
        {{mb_value object=$line field=unite_duree}}
        -
      {{/if}}
      
      <!-- Date de fin -->
      {{if $line->fin}}
        {{mb_label object=$line field=fin}}: 
        {{mb_value object=$line field=fin}}
        {{if $line->time_fin}}
          à {{mb_value object=$line field=time_fin}}
        {{/if}}
      {{/if}}
      
      <!-- Date de fin prévue -->
      {{if $line->_fin}}
        {{mb_label object=$line field=_fin}}: 
        {{mb_value object=$line field=_fin}}
      {{/if}}
      
      {{if !$line->duree && !$line->debut && !$line->fin}}Aucune date{{/if}}
      {{if $line->commentaire}}, {{mb_value object=$line field="commentaire"}}{{/if}}
    </td>
  </tr>
  <tr>
    <!-- Affichage des alertes -->
    <td style="text-align: left;">
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
    </td>
    <td style="width: 1%;">Posologie:</td>
    <td>
    {{if $line->_ref_prises|@count}}
      <ul>
      {{foreach from=$line->_ref_prises item=_prise}}
        <li>{{$_prise->_view}}</li>
      {{/foreach}}
      </ul>
    {{else}}
      Aucune posologie
    {{/if}}
    </td>
  </tr>
</tbody>