      <form name="find" action="./index.php" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="4">Recherche d'un dossier patient</th>
        </tr>
  
        <tr>
          <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
          <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
          <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
          <td><input tabindex="3" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
          <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
          <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
          <td><input tabindex="4" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="soundex" title="Faire une recherche phonetique sur le patient">Utiliser la phonétique</label></th>
          <td>
            <input type="checkbox" name="check_soundex" onclick="chgSoundex()" {{if $soundex == "on"}}checked="checked"{{/if}}/>
            <input type="hidden"   name="soundex" {{if $soundex == "on"}}value="on"{{else}}value="off"{{/if}} />
          </td>
          <th>
            <label for="check_naissance" title="Date de naissance du patient à rechercher">
              <input type="checkbox" name="check_naissance" onclick="affNaissance()" {{if $naissance == "on"}}checked="checked"{{/if}}/>
              <input type="hidden" name="naissance" {{if $naissance == "on"}}value="on"{{else}}value="off"{{/if}} />
              Date de naissance
            </label>
          </th>
          <td>
            {{if $naissance == "on"}}
            {{html_select_date
                 time=$datePat
                 start_year=1900
                 field_order=DMY
                 all_extra="style='display:inline;'"}}
             {{else}}
            {{html_select_date
                 time=$datePat
                 start_year=1900
                 field_order=DMY
                 all_extra="style='display:none;'"}}
             {{/if}}
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="4">
            {{if $board}}
            <button class="search" type="button" onclick="updateListPatients()">Rechercher</button>
            {{else}}
            <button class="search" type="submit">Rechercher</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>

      <form name="fusion" action="index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_pat" />
      <table class="tbl">
        <tr>
          <th><button type="submit" class="search">Fusion</button></th>
          <th>Patient</th>
          <th>Date de naissance</th>
          <th>Adresse</th>
        </tr>

        {{if $board}}
        {{assign var="href" value="index.php?m=dPpatients&tab=vw_full_patients&patient_id="}}
        {{else}}
        {{assign var="href" value="index.php?m=dPpatients&tab=vw_idx_patients&patient_id="}}
        {{/if}}
        {{foreach from=$patients item=curr_patient}}
        <tr>
          <td><input type="checkbox" name="fusion_{{$curr_patient->patient_id}}" /></td>
          <td class="text">
            <a href="{{$href}}{{$curr_patient->patient_id}}">
              {{$curr_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}{{$curr_patient->patient_id}}">
              {{$curr_patient->_naissance}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}{{$curr_patient->patient_id}}">
              {{$curr_patient->adresse}}, {{$curr_patient->cp}} {{$curr_patient->ville}}
            </a>
          </td>
        </tr>
        {{/foreach}}
        
      </table>
      </form>