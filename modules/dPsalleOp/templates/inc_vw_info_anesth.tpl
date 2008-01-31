{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
<table class="form">
  <!-- Affichage d'information complementaire pour l'anestesie -->
  <tr>
    <td class="button">
      Vu par {{$consult_anesth->_ref_consultation->_ref_chir->_view}}
    </td>
  </tr>
  
  <tr>
   <td>
   <table style="width: 100%">
   
<tr>
  <th colspan="3" class="category">Conditions d'intubation</th>
</tr>
<tr>
  {{if $consult_anesth->mallampati}}
  <td rowspan="4">
    <img src="images/pictures/{{$consult_anesth->mallampati}}.gif" alt="{{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}" />
    <br />Mallampati<br />de {{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}
   </td>
   {{/if}}
	        
   <th>Ouverture de la bouche</th>
     <td>
       {{tr}}CConsultAnesth.bouche.{{$consult_anesth->bouche}}{{/tr}}
     </td>
   </tr>
   <tr>
     <th>Distance thyro-mentonnière</th>
     <td>{{tr}}CConsultAnesth.distThyro.{{$consult_anesth->distThyro}}{{/tr}}</td>
   </tr>
	 <tr>
	   <th>Etat bucco-dentaire</th>
	   <td>{{$consult_anesth->etatBucco}}</td>
	 </tr>
	 <tr>
	   <th>Conclusion</th>
	   <td>{{$consult_anesth->conclusion}}</td>
	  </tr>
	  <tr>
	   {{if $consult_anesth->_intub_difficile}}
	    <td colspan="3"  style="text-align:center;color:#F00;">
	      Intubation Difficile Prévisible
	    </td>
	  {{else}}
	    <td colspan="3" style="text-align:center;">
	       Pas Intubation Difficile Prévisible
	     </td>        
	  {{/if}}
	  </tr>     
	  <tr>
        <th class="category" colspan="3">
          Examens Complémentaires
        </th>
      </tr>
      
      <tr>
      {{foreach from=$listChamps item=aChamps}}
        <td>
          {{if $aChamps}}
          <table>
          {{/if}}
          {{foreach from=$aChamps item=champ}}
            {{assign var="donnees" value=$unites.$champ}}
            <tr>
              <th>{{$donnees.nom}}</th>
              <td>
                {{if $champ=="tca"}}
                  {{$consult_anesth->tca_temoin}} s / {{$consult_anesth->tca}}
                {{elseif $champ=="tsivy"}}
                  {{$consult_anesth->tsivy|date_format:"%Mm%Ss"}}
                {{elseif $champ=="ecbu"}}
                  {{tr}}CConsultAnesth.ecbu.{{$consult_anesth->ecbu}}{{/tr}}
                {{else}}
                  {{$consult_anesth->$champ}}
                {{/if}}
                {{$donnees.unit}}
              </td>
            </tr>
          {{/foreach}}
          {{if $aChamps}}</table>{{/if}}
        </td>
      {{/foreach}}
      </tr>
         
      {{foreach from=$consult_anesth->_ref_consultation->_types_examen key=curr_type item=list_exams}}
      {{if $list_exams|@count}}
      <tr>
        <th>
          Examens Complémentaires : {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
        </th>
        <td class="text">
          <ul>
            {{foreach from=$list_exams item=curr_examcomp}}
            <li>
              {{$curr_examcomp->examen}}
              {{if $curr_examcomp->fait}}
                (Fait)
              {{else}}
                (A Faire)
              {{/if}}
            </li>
            {{/foreach}}
          </ul>
        </td>
      </tr>
     {{/if}}
     {{foreachelse}}
     <tr>
      <td>
        Pas d'examen complémentaire
      </td>
    </tr>
    {{/foreach}}
   
    
    {{if $consult_anesth->_ref_consultation->_ref_exampossum->_id}}
      <tr>
        <th>Score Possum</th>
        <td>
          Morbidité : {{mb_value object=$consult_anesth->_ref_consultation->_ref_exampossum field="_morbidite"}}%<br />
          Mortalité : {{mb_value object=$consult_anesth->_ref_consultation->_ref_exampossum field="_mortalite"}}%
        </td>
      </tr>
    {{/if}}
    
    {{if $consult_anesth->_ref_consultation->_ref_examnyha->_id}}
      <tr>
        <th>Clasification NYHA</th>
        <td>{{mb_value object=$consult_anesth->_ref_consultation->_ref_examnyha field="_classeNyha"}}</td>
      </tr>   
    {{/if}}
    
    {{if $consult_anesth->_ref_consultation->rques}}
      <tr>
        <th>
          Remarques
        </th>
        <td>
          {{$consult_anesth->_ref_consultation->rques|nl2br}}
        </td>
      </tr>
    {{/if}}
  
    <tr>
      <th class="category" colspan="3">Liste des Documents Edités</th>
    </tr>
    <tr>
      <td>
        {{if $consult_anesth->_ref_consultation->_ref_documents}}
        <ul>
        {{foreach from=$consult_anesth->_ref_consultation->_ref_documents item=currDoc}}
          <li>{{$currDoc->nom}}</li>
        {{/foreach}}
        </ul>
        {{else}}
          Aucun Document
        {{/if}}
      </td>
    </tr>
  
    {{if $consult_anesth->premedication}}
    <tr>
        <th class="category">
          Prémédication
        </th>
      </tr>
      <tr>
        <td>
          {{$consult_anesth->premedication|nl2br}}
        </td>
      </tr>
    {{/if}}
    {{if $consult_anesth->prepa_preop}}
      <tr>
        <th class="category">
          Préparation pré-opératoire
        </th>
      </tr>
      <tr>
        <td>
          {{$consult_anesth->prepa_preop|nl2br}}
        </td>
      </tr>
    {{/if}}
  </table>
  </td>
  </tr>
  
</table>
