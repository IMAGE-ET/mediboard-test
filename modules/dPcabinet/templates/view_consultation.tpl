<table class="form" id="admission">
  <tr>
    <th class="title" colspan="2">
      <span style="float:right;font-size:12px;">
        
      </span>
      <a href="#" onclick="window.print()">Fiche de consultation</a>
    </th>
  </tr>
  <tr>
    <th>Date </th>
    <td>{{$today|date_format:"%A %d/%m/%Y"}}</td>
  </tr>
  <tr>
    <th>Praticien</th>
    <td>Dr. {{$prat_id->_view}}</td>
  </tr>
  
  <tr>
  	<th>Adresse </th>
    <td>
      {{$prat_id->_ref_function->adresse}} &mdash;
      {{$prat_id->_ref_function->cp}} {{$prat_id->_ref_function->ville}}
    </td>
  </tr>
  <tr>
    <th class="category" colspan="2">Renseignements concernant le patient</th>
  </tr>
  <tr>
    <th>Nom / Prénom </th>
    <td>{{$patient->_view}}</td>
  </tr>
  
  <tr>
    <th>Date de naissance / Sexe </th>
    <td>
      né(e) le {{$patient->_naissance}}
      de sexe 
      {{if $patient->sexe == "m"}}masculin{{else}}féminin{{/if}}
    </td>
  </tr>
 
  <tr>
    <th>Téléphone </th>
    <td>{{$patient->_tel1}} {{$patient->_tel2}} {{$patient->_tel3}} {{$patient->_tel4}} {{$patient->_tel5}}</td>
  </tr>

  <tr>
    <th>Medecin traitant </th>
    <td>{{$patient->_ref_medecin_traitant->_view}}</td>
  </tr>
  
  <tr>
    <th>Adresse </th>
    <td>
      {{$patient->adresse}} &mdash;
      {{$patient->cp}} {{$patient->ville}}
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Renseignements relatifs à la consultation</th>
  </tr>
  
  <tr>
    <th>Consultation </th>
    <td>      
      le {{$consultation->_ref_plageconsult->date|date_format:"%A %d/%m/%Y"}} à {{$consultation->heure}}
    </td>
  </tr>
</table>