<table class="form" id="admission">
  <tr><th class="title" colspan="2"><a href="javascript:window.print()">Fiche Patient</a></th></tr>
  
  <tr><th>Date: </th><td>{$today}</td></tr>
  
  <tr><th class="category" colspan="2">Informations sur le patient</th></tr>
  
  <tr><th>Nom / Prenom: </th><td>{$patient->_view}</td></tr>
  <tr><th>Date de naissance / Sexe: </th><td>né(e) le {$patient->_jour}/{$patient->_mois}/{$patient->_annee}
  de sexe {if $patient->sexe == "m"} masculin {else} féminin {/if}</td></tr>
  <tr><th>Incapable majeur: </th><td>{if $patient->incapable_majeur == "n"} non {else} oui {/if}</td></tr>
  <tr><th>Telephone: </th><td>{$patient->tel}</td></tr>
  <tr><th>Portable: </th><td>{$patient->tel2}</td></tr>
  <tr><th>Adresse: </th><td>{$patient->adresse|nl2br} - {$patient->cp} {$patient->ville}</td></tr>
  <tr><th>Remarques: </th><td>{$patient->rques|nl2br}</td></tr>
  
  {if $patient->_ref_medecin_traitant->medecin_id || $patient->_ref_medecin1->medecin_id || $patient->_ref_medecin2->medecin_id || $patient->_ref_medecin3->medecin_id}
  <tr><th class="category" colspan="2">Medecins correspondants</th></tr>
  {/if}
  
  {if $patient->_ref_medecin_traitant->medecin_id}
  <tr><th>Medecin traitant: </th><td>{$patient->_ref_medecin_traitant->_view}</td></tr>
  <tr><th></th><td>{$patient->_ref_medecin_traitant->adresse|nl2br}<br />{$patient->_ref_medecin_traitant->cp} {$patient->_ref_medecin_traitant->ville}</td></tr>
  {/if}
  {if $patient->_ref_medecin1->medecin_id}
  <tr><th>Medecin correspondant 1: </th><td>{$patient->_ref_medecin1->_view}</td></tr>
  <tr><th></th><td>{$patient->_ref_medecin1->adresse|nl2br}<br />{$patient->_ref_medecin1->cp} {$patient->_ref_medecin1->ville}</td></tr>
  {/if}
  {if $patient->_ref_medecin2->medecin_id}
  <tr><th>Medecin correspondant 2: </th><td>{$patient->_ref_medecin2->_view}</td></tr>
  <tr><th></th><td>{$patient->_ref_medecin2->adresse|nl2br}<br />{$patient->_ref_medecin2->cp} {$patient->_ref_medecin2->ville}</td></tr>
  {/if}
  {if $patient->_ref_medecin3->medecin_id}
  <tr><th>Medecin correspondant 3: </th><td>{$patient->_ref_medecin3->_view}</td></tr>
  <tr><th></th><td>{$patient->_ref_medecin3->adresse|nl2br}<br />{$patient->_ref_medecin3->cp} {$patient->_ref_medecin3->ville}</td></tr>
  {/if}
  
  {if $patient->_ref_operations|@count}
  <tr><th class="category" colspan="2">Antécédents chirurgicaux</th></tr>
  {foreach from=$patient->_ref_operations item=curr_op}
  <tr>
    <th>Dr. {$curr_op->_ref_chir->_view}</th>
    <td>le {$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}</td>
  </tr>
  {/foreach}
  {/if}
  
  {if $patient->_ref_consultations|@count}
  <tr><th class="category" colspan="2">Consultations</th></tr>
  {foreach from=$patient->_ref_consultations item=curr_consult}
  <tr>
    <th>Dr. {$curr_consult->_ref_plageconsult->_ref_chir->_view}</th>
    <td>le {$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}</td>
  </tr>
  {/foreach}
  {/if}

</table>