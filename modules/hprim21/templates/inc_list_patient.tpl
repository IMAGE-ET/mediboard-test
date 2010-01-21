<form name="find" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />

<table class="form">
  <tr>
    <th class="category" colspan="4">Recherche d'un dossier patient externe</th>
  </tr>

  <tr>
    <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
    <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
    <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
    <td><input tabindex="4" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
    <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
    <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
    <td><input tabindex="5" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="jeuneFille" title="Nom de naissance">Nom de naissance</label></th>
    <td><input tabindex="3" type="text" name="jeuneFille" value="{{$jeuneFille|stripslashes}}" /></td>
    <td colspan="2" />
  </tr>
  
  <tr>
    <th colspan="2">
      <label for="Date_Day" title="Date de naissance du patient à rechercher">
        Date de naissance
      </label>
      <input type="hidden" name="naissance" {{if $naissance == "on"}}value="on"{{else}}value="off"{{/if}} />
    </th>
    <td colspan="2">
         {{html_select_date
           time=0000-00-00
           start_year=1900
           field_order=DMY
           day_empty="Jour"
           month_empty="Mois"
           year_empty="Année"
           day_extra="tabindex='7'"
           month_extra="tabindex='8'"
           year_extra="tabindex='9'"
           all_extra="style='display:inline;'"}}   
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="4">
      <button class="search" type="submit">
        {{tr}}Search{{/tr}}
      </button>
    </td>
  </tr>
</table>
</form>

<table class="tbl">
  <tr>
    {{/if}}
    <th>
      Patient
      ({{$patientsCount}} {{tr}}found{{/tr}})
    </th>
    <th>Date de naissance</th>
    <th>Adresse</th>
  </tr>

  {{assign var="tabPatient" value="vw_idx_patients&patient_id="}}
  
  {{foreach from=$patients item=curr_patient}}
  <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="_view"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="naissance"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="adresse1"}}
        {{mb_value object=$curr_patient field="adresse2"}}
        {{mb_value object=$curr_patient field="cp"}}
        {{mb_value object=$curr_patient field="ville"}}
      </a>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="100"><em>Aucun résultat exact</em></td>
  </tr>
  {{/foreach}}
  {{if $patientsSoundex|@count}}
  <tr>
    <th colspan="4">
      Résultats proches
      ({{$patientsSoundexCount}} {{tr}}found{{/tr}})
      
    </th>
  </tr>
  {{/if}}
  {{foreach from=$patientsSoundex item=curr_patient}}
  <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="_view"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="naissance"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="adresse1"}}
        {{mb_value object=$curr_patient field="adresse2"}}
        {{mb_value object=$curr_patient field="cp"}}
        {{mb_value object=$curr_patient field="ville"}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  
</table>
