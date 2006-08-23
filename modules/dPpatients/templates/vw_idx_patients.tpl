<!-- $Id$ -->

<script language="JavaScript" type="text/javascript">

function affNaissance() {
  var oForm      = document.find;
  var oCheckNaissance = oForm.check_naissance;
  var oNaissance = oForm.naissance;
  var oDay       = oForm.Date_Day;
  var oMonth     = oForm.Date_Month;
  var oYear      = oForm.Date_Year;
  if (oCheckNaissance.checked) {
    oDay.style.display   = "inline";
    oMonth.style.display = "inline";
    oYear.style.display  = "inline";
    oNaissance.value     = "on";
  } else {
    oDay.style.display   = "none";
    oMonth.style.display = "none";
    oYear.style.display  = "none";
    oNaissance.value     = "off";
  }
}

</script>

<table class="main">
  <tr>
    <td class="greedyPane">
      <form name="find" action="./index.php" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">Recherche d'un dossier patient</th>
        </tr>
  
        <tr>
          <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
          <td><input tabindex="1" type="text" name="nom" value="{{$nom}}" /></td>
        </tr>
        
        <tr>
          <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
          <td><input tabindex="2" type="text" name="prenom" value="{{$prenom}}" /></td>
        </tr>
        
        <tr>
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
                 time=$date
                 start_year=1900
                 field_order=DMY
                 all_extra="style='display:inline;'"}}
             {{else}}
            {{html_select_date
                 time=$date
                 start_year=1900
                 field_order=DMY
                 all_extra="style='display:none;'"}}
             {{/if}}
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="2"><button class="search" type="submit">Rechercher</button></td>
        </tr>
      </table>
      </form>

      <form name="fusion" action="index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_pat" />
      <table class="tbl">
        <tr>
          <th><button type="submit" class="search">Fusion</button></th>
          <th>Nom - Prénom</th>
          <th>Date de naissance</th>
          <th>Adresse</th>
          <th>Code Postal</th>
          <th>Ville</th>
        </tr>

        {{foreach from=$patients item=curr_patient}}
        <tr>
          <td><input type="checkbox" name="fusion_{{$curr_patient->patient_id}}" /></td>
          <td class="text"><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;id={{$curr_patient->patient_id}}">{{$curr_patient->_view}}</a></td>
          <td class="text"><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;id={{$curr_patient->patient_id}}">{{$curr_patient->_naissance}}</a></td>
          <td class="text"><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;id={{$curr_patient->patient_id}}">{{$curr_patient->adresse}}</a></td>
          <td class="text"><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;id={{$curr_patient->patient_id}}">{{$curr_patient->cp}}</a></td>
          <td class="text"><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;id={{$curr_patient->patient_id}}">{{$curr_patient->ville}}</a></td>
        </tr>
        {{/foreach}}
        
      </table>
      </form>

    </td>
 
    {{if $patient->patient_id}}
    <td class="pane" id="vwPatient">
    {{include file="inc_vw_patient.tpl"}}
    </td>
    {{/if}}
  </tr>
</table>