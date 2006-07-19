<!-- $Id$ -->

<script type="text/javascript">
function checkFormPrint() {
  var form = document.paramFrm;
    
  if(!(checkForm(form))){
    return false;
  }
  
  popPlanning();
}

function popCode(type) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(document.paramFrm.chir);
  url.addParam("type", type);
  url.popup(600, 500, type);
}

function setCode(code, type) {
  var oForm = document.paramFrm;
  var oField = oForm.code_ccam;
  oField.value = code;
}

function popPlanning() {
  form = document.paramFrm;
  var url = new Url;
  url.setModuleAction("dPbloc", "view_planning");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.vide);
  url.addElement(form.code_ccam, "CCAM");
  url.addElement(form.type);
  url.addElement(form.chir);
  url.addElement(form.spe);
  url.addElement(form.salle);
  url.popup(700, 550, 'Planning');
}

function pageMain() {
  regFieldCalendar("paramFrm", "deb");
  regFieldCalendar("paramFrm", "fin");
}

</script>


<form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la période</th></tr>
        <tr>
          <th><label for="deb" title="Date de début de la recherche">Début</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_deb_da">{{$deb|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="deb" title="date|notNull" value="{{$deb}}" />
            <img id="paramFrm_deb_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
        </tr>
        <tr>
          <th><label for="fin" title="Date de fin de la recherche">Fin</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_fin_da">{{$fin|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="fin" title="date|moreEquals|deb|notNull" value="{{$fin}}" />
            <img id="paramFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <th><label for="vide" title="Afficher ou cacher les plages vides dans le rapport">Afficher les plages vides</label></th>
          <td colspan="2"><input type="checkbox" name="vide" /></td>
        </tr>
        <tr>
          <th><label for="code_ccam" title="Rechercher en fonction d'un code CCAM">Code CCAM</label></th>
          <td><input type="text" name="code_ccam" size="10" value="" /></td>
          <td class="button"><button type="button" class="search" onclick="popCode('ccam')">sélectionner un code</button></td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix des paramètres de tri</th></tr>
        <tr>
          <th><label for="type" title="Recherche en fonction de la présence dans le planning">Affichage des interventions</label></th>
          <td><select name="type">
            <option value="0">&mdash; Toutes les interventions &mdash;</option>
            <option value="1">insérées dans le planning</option>
            <option value="2">à insérer dans le planning</option>
          </select></td>
        </tr>
        <tr>
          <th><label for="chir" title="Rechercher en fonction du praticien">Praticiens</label></th>
          <td><select name="chir">
            <option value="0">&mdash; Tous les praticiens &mdash;</option>
            {{foreach from=$listPrat item=curr_prat}}
              <option value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="spe" title="Rechercher en fonction d'une spécialité opératoire">Specialité</label></th>
          <td><select name="spe">
            <option value="0">&mdash; Toutes les spécialités &mdash;</option>
            {{foreach from=$listSpec item=curr_spec}}
              <option value="{{$curr_spec->function_id}}">{{$curr_spec->text}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="salle" title="Rechercher en fonciton d'une salle d'opération">Salle</label></th>
          <td><select name="salle">
            <option value="0">&mdash; Toutes les salles &mdash;</option>
            {{foreach from=$listSalles item=curr_salle}}
	            <option value="{{$curr_salle->id}}">{{$curr_salle->nom}}</option>
            {{/foreach}}
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form"><tr><td class="button"><button class="print" type="button" onclick="checkFormPrint()">Afficher</button></td></tr></table>

    </td>
  </tr>
</table>

</form>