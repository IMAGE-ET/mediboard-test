<!-- $Id$ -->

<script type="text/javascript">
{literal}
function checkForm() {
  var form = document.paramFrm;
    
  if (form.deb.value > form.fin.value) {
    alert("Date de début superieure à la date de fin");
    return false;
  }
  
  popPlages();
}

function popPlages() {
  var form = document.paramFrm;

  var url = new Url;
  url.setModuleAction("dPcabinet", "print_plages");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.chir);
  url.popup(700, 550, "Planning");
}

function pageMain() {
  regFieldCalendar("paramFrm", "deb");
  regFieldCalendar("paramFrm", "fin");
}

{/literal}
</script>

<form name="paramFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la période</th></tr>
        <tr>
          <th><label for="deb" title="Date de début de la période">Début:</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_deb_da">{$deb|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="deb" value="{$deb}" />
            <img id="paramFrm_deb_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
        </tr>
        <tr>
          <th><label for="fin" title="Date de fin de la période">Fin:</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_fin_da">{$fin|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="fin" value="{$fin}" />
            <img id="paramFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix des paramètres de tri</th></tr>
        <tr>
          <th>Praticien:</th>
          <td><select name="chir">
            <option value="0">&mdash; Tous</option>
            {foreach from=$listChir item=curr_chir}
	            <option value="{$curr_chir->user_id}">{$curr_chir->_view}</option>
            {/foreach}
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
            <input type="button" value="Afficher" onclick="checkForm()" />
          </td>
        </tr>
        </table>

    </td>
  </tr>
</table>