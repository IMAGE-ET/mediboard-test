<script type="text/javascript">
	Main.add(window.print);
</script>

<button class="print not-printable" onclick="window.print()">{{tr}}Print{{/tr}}</button>

<table class="form" id="admission">
  <tr><th class="title" colspan="10">Fiche Patient ({{$today}})</th></tr>
  
  <tr>
  	<th>Nom - Prénom</th>
		<td>{{$patient->_view}}</td>
		
    <th rowspan="2">{{mb_label object=$patient field=adresse}}</th>
    <td rowspan="2">{{$patient->adresse|nl2br}} <br /> {{$patient->cp}} {{$patient->ville}}</td>
	</tr>
  <tr>
  	<th>Naissance - Sexe</th>
		<td>né(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}</td>
	</tr>
  <tr>
  	<th>{{mb_label object=$patient field=incapable_majeur}}</th>
		<td>{{mb_value object=$patient field=incapable_majeur}}</td>
		
    <th rowspan="3">{{mb_label object=$patient field=rques}}</th>
    <td rowspan="3">{{mb_value object=$patient field=rques}}</td>
	</tr>
  <tr>
  	<th>{{mb_label object=$patient field=tel}}</th>
		<td>{{mb_value object=$patient field=tel}}</td>
	</tr>
  <tr>
  	<th>{{mb_label object=$patient field=tel2}}</th>
		<td>{{mb_value object=$patient field=tel2}}</td>
	</tr>
	
  <tr><th class="category" colspan="10">Bénéficiaire de soins</th></tr>
  <tr>
    <th>{{mb_label object=$patient field="code_regime"}}</th>
    <td>{{mb_value object=$patient field="code_regime"}}</td>
		
    <th>{{mb_label object=$patient field="ald"}}</th>
    <td>{{mb_value object=$patient field="ald"}}</td>
  </tr>
	
  <tr>
    <th>{{mb_label object=$patient field="caisse_gest"}}</th>
    <td>{{mb_value object=$patient field="caisse_gest"}}</td>
		
    <th>{{mb_label object=$patient field="incapable_majeur"}}</th>
    <td>{{mb_value object=$patient field="incapable_majeur"}}</td>
  </tr>
	
  <tr>
    <th>{{mb_label object=$patient field="centre_gest"}}</th>
    <td>{{mb_value object=$patient field="centre_gest"}}</td>
		
    <th>{{mb_label object=$patient field="cmu"}}</th>
    <td>{{mb_value object=$patient field="cmu"}}</td>
  </tr>
	
  <tr>
    <th>{{mb_label object=$patient field="regime_sante"}}</th>
    <td>{{mb_value object=$patient field="regime_sante"}}</td>
		
    <th>{{mb_label object=$patient field="ATNC"}}</th>
    <td>{{mb_value object=$patient field="ATNC"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="deb_amo"}}</th>
    <td>{{mb_value object=$patient field="deb_amo"}}</td>
		
    <th>{{mb_label object=$patient field="fin_validite_vitale"}}</th>
    <td>{{mb_value object=$patient field="fin_validite_vitale"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="fin_amo"}}</th>
    <td>{{mb_value object=$patient field="fin_amo"}}</td>
		
    <th rowspan="2">{{mb_label object=$patient field="notes_amo"}}</th>
    <td rowspan="2">{{mb_value object=$patient field="notes_amo"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_exo"}}</th>
    <td>{{mb_value object=$patient field="code_exo"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_sit"}}</th>
    <td>{{mb_value object=$patient field="code_sit"}}</td>
    
    <th rowspan="2">{{mb_label object=$patient field="libelle_exo"}}</th>
    <td rowspan="2">{{mb_value object=$patient field="libelle_exo"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="regime_am"}}</th>
    <td>{{mb_value object=$patient field="regime_am"}}</td>
  </tr>

	
  {{if $patient->_ref_medecin_traitant->medecin_id || $patient->_ref_medecins_correspondants|@count}}
  <tr><th class="category" colspan="10">Correspondants médicaux</th></tr>
  <tr>
  {{if $patient->_ref_medecin_traitant->medecin_id}}
      <th>Médecin traitant: </th>
      <td>
        {{$patient->_ref_medecin_traitant->_view}}<br />
        {{$patient->_ref_medecin_traitant->adresse|nl2br}}<br />
        {{$patient->_ref_medecin_traitant->cp}} {{$patient->_ref_medecin_traitant->ville}}
      </td>
  {{/if}}
  
  {{if $patient->_ref_medecins_correspondants|@count}}
      <th>Correspondants <br />médicaux: </th>
      <td>
        {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp name=corresp}}
				  {{$curr_corresp->_ref_medecin->_view}}{{if !$smarty.foreach.corresp.last}}<br />{{/if}}
        {{/foreach}}
      </td>
  {{/if}}
	</tr>
  {{/if}}

  <tr><th class="category" colspan="10">Correspondance</th></tr>
  <tr>
    <th class="category" colspan="2" style="font-size: 1.0em;">Personne à prévenir</th>
    <th class="category" colspan="2" style="font-size: 1.0em;">Employeur</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_nom"}}</th>
    <td>
      {{mb_value object=$patient field="prevenir_nom"}}
      {{mb_value object=$patient field="prevenir_prenom"}}
    </td>
    
    <th>{{mb_label object=$patient field="employeur_nom"}}</th>
    <td>{{mb_value object=$patient field="employeur_nom"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="prevenir_adresse"}}</th>
    <td>
      {{mb_value object=$patient field="prevenir_adresse"}}<br />
      {{mb_value object=$patient field="prevenir_cp"}}
      {{mb_value object=$patient field="prevenir_ville"}}
    </td>
    
    <th>{{mb_label object=$patient field="employeur_adresse"}}</th>
    <td>
      {{mb_value object=$patient field="employeur_adresse"}}<br />
      {{mb_value object=$patient field="employeur_cp"}}
      {{mb_value object=$patient field="employeur_ville"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="prevenir_tel"}}</th>
    <td>{{mb_value object=$patient field="prevenir_tel"}}</td>
    
    <th>{{mb_label object=$patient field="employeur_tel"}}</th>
    <td>{{mb_value object=$patient field="employeur_tel"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_parente"}}</th>
    <td>{{mb_value object=$patient field="prevenir_parente"}}</td>
    
    <th>{{mb_label object=$patient field="employeur_urssaf"}}</th>
    <td>{{mb_value object=$patient field="employeur_urssaf"}}</td>
  </tr>
	
	
  <tr><th class="category" colspan="10">Assuré social</th></tr>
  <tr>
    <th>Nom / prénom</th>
    <td>
    	{{mb_value object=$patient field="assure_civilite"}}
    	{{mb_value object=$patient field="assure_nom"}} 
			
    	{{mb_value object=$patient field="assure_prenom"}}
		  {{mb_value object=$patient field="assure_prenom_2"}}
      {{mb_value object=$patient field="assure_prenom_3"}}
      {{mb_value object=$patient field="assure_prenom_4"}}
		</td>
		
    <th rowspan="3">{{mb_label object=$patient field="assure_adresse"}}</th>
    <td rowspan="3">
    	{{mb_value object=$patient field="assure_adresse"}}
		  {{mb_value object=$patient field="assure_cp"}}
			{{mb_value object=$patient field="assure_ville"}}
			{{mb_value object=$patient field="assure_pays"}}<br />
		</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="assure_nom_jeune_fille"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_naissance"}}</th>
    <td>
      {{mb_value object=$patient field="assure_naissance"}} 
      de sexe {{if $patient->assure_sexe == "m"}} masculin {{else}} féminin {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_lieu_naissance"}}</th>
    <td>
		  {{mb_value object=$patient field="assure_cp_naissance"}}
		  {{mb_value object=$patient field="assure_lieu_naissance"}}
			{{mb_value object=$patient field="_assure_pays_naissance_insee"}}
    
    <th>{{mb_label object=$patient field="assure_tel"}}</th>
    <td>{{mb_value object=$patient field="assure_tel"}}</td>
	</td>
  <tr>
    <th>{{mb_label object=$patient field="assure_nationalite"}}</th>
    <td>{{mb_value object=$patient field="assure_nationalite"}}</td>
    
    <th>{{mb_label object=$patient field="assure_tel2"}}</th>
    <td>{{mb_value object=$patient field="assure_tel2"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_profession"}}</th>
    <td>{{mb_value object=$patient field="assure_profession"}}</td>
      
    <th rowspan="2">{{mb_label object=$patient field="assure_rques"}}</th>
    <td rowspan="2">{{mb_value object=$patient field="assure_rques"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="assure_matricule"}}</th>
    <td>{{mb_value object=$patient field="assure_matricule"}}</td>
  </tr>
  
  {{if $patient->_ref_sejours|@count}}
  <tr><th class="category" colspan="10">Séjours précédents</th></tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr>
    <th>Dr {{$curr_sejour->_ref_praticien}}</th>
    <td colspan="3">
      Du {{mb_value object=$curr_sejour field=entree_prevue}}
      au {{mb_value object=$curr_sejour field=sortie_prevue}}
      <ul>
      {{foreach from=$curr_sejour->_ref_operations item="curr_op"}}
        <li>
          Intervention le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
          (Dr {{$curr_op->_ref_chir}})
        </li>
      {{foreachelse}}
        <li><em>Pas d'interventions</em></li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  
  {{if $patient->_ref_consultations|@count}}
  <tr><th class="category" colspan="10">Consultations</th></tr>
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <th>Dr {{$curr_consult->_ref_plageconsult->_ref_chir}}</th>
    <td colspan="3">le {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}</td>
  </tr>
  {{/foreach}}
  {{/if}}

</table>