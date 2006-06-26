<!-- $Id$ -->

{literal}
<script type="text/javascript">

function requestInfoPat() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_get_last_refs");
  url.addElement(document.editFrm.patient_id);
  url.requestUpdate("infoPat", {
    waitingText: "Chargement des antécédants du patient"
  });
}

function popChir() {
  var url = new Url;
  url.setModuleAction("mediusers", "chir_selector");
  url.popup(400, 250, "Praticien");
}

function setChir( key, val ){
  var f = document.editFrm;
  f.chir_id.value = key;
  f._chir_name.value = val;
}

function popPat() {
  var url = new Url;
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(800, 500, "Patient");
  myNode = document.getElementById("infoPat");
  myNode.innerHTML = "";
}

function setPat( key, val ) {
  var f = document.editFrm;

  if (val != '') {
    f.patient_id.value = key;
    f._pat_name.value = val;
    myNode = document.getElementById("clickPat");
    myNode.innerHTML = "++ Infos patient (cliquez pour afficher) ++";
    myNode.setAttribute("onclick", "requestInfoPat()");
  }
}

function popRDV() {
  var oForm = document.editFrm;
  var url = new Url;
  url.setModuleAction("dPcabinet", "plage_selector");
  url.addElement(oForm.chir_id);
  url.addElement(oForm.plageconsult_id);
  url.popup(800, 600, "Plage");
}

function setRDV(heure, id, date, freq, chirid, chirname ) {
  var f = document.editFrm;
  f.plageconsult_id.value = id;
  f._date.value = date;
  f.heure.value = heure;
  f.duree.value = freq;
  f.chir_id.value = chirid;
  f._chir_name.value = chirname;
}

</script>
{/literal}

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_consultation_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="consultation_id" value="{$consult->consultation_id}" />
<input type="hidden" name="compte_rendu" value="{$consult->compte_rendu|escape:"html"}" />
<input type="hidden" name="annule" value="0" />
<input type="hidden" name="arrivee" value="" />
<input type="hidden" name="chrono" value="{$smarty.const.CC_PLANIFIE}" />

<table class="main" style="margin: 4px; border-spacing: 0px;">
  {if $consult->consultation_id}
  <tr>
    <td><a class="button" href="index.php?m={$m}&amp;consultation_id=0">Créer une nouvelle consultation</a></td>
  </tr>
  {/if}
  <tr>
    {if $consult->consultation_id}
      <th class="title" colspan="5" style="color: #f00;">
        <a style="float:right;" href="javascript:view_log('CConsultation',{$consult->consultation_id})">
          <img src="images/history.gif" alt="historique" />
        </a>
        Modification de la consultation de {$pat->_view} pour le Dr. {$chir->_view}
      </th>
    {else}
      <th class="title" colspan="5">Création d'une consultation</th>
    {/if}
  </tr>
  <tr>
    <td>
  
      <table class="form">
        <tr><th class="category" colspan="3">Informations sur la consultation</th></tr>
        
        <tr>
          <th>
            <label for="chir_id" title="Praticien pour la consultation">Praticien</label>
            <input type="hidden" name="chir_id" title="notNull|ref" value="{$chir->user_id}" ondblclick="popChir()" />
          </th>
            <td class="readonly">
              <input type="text" name="_chir_name" size="30" value="{$chir->_view}" readonly="readonly" />
              <button type="button" onclick="setChir(0, '')">
                <img src="modules/{$m}/images/cross.png" alt="X" />
              </button>
            </td>
            <td class="button"><input type="button" value="choisir un praticien" onclick="popChir()" /></td>
        </tr>

        <tr>
          <th class="mandatory">
            <input type="hidden" name="patient_id" value="{$pat->patient_id}" />
            <label for="chir_id" title="Patient pour la consultation">Patient</label>
          </th>
          <td class="readonly"><input type="text" name="_pat_name" size="30" value="{$pat->_view}" readonly="readonly" /></td>
          <td class="button"><input type="button" value="rechercher un patient" onclick="popPat()" /></td>
        </tr>
        
        <tr>
          <th><label for="motif" title="Motif de la consultation">Motif</label></th>
          <td colspan="2"><textarea name="motif" title="{$consult->_props.motif}" rows="3">{$consult->motif}</textarea></td>
        </tr>

        <tr>
          <th><label for="rques" title="Remartques de la consultation" >Remarques</label></th>
          <td colspan="2"><textarea name="rques" title="{$consult->_props.rques}" rows="3">{$consult->rques}</textarea></td>
        </tr>

      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Rendez-vous</th></tr>

        <tr>
          <th><label for="premiere" title="Première consultation de ce patient avec le praticien?">Consultation</label></th>
          <td>
            <input type="checkbox" name="_check_premiere" value="1" {if $consult->_check_premiere} checked="checked" {/if} />
            <label for="_check_premiere" title="Première consultation de ce patient avec le praticien">Première consultation</label>
          </td>
          <td rowspan="4" class="button">
            <input type="button" value="Selectionner" onclick="popRDV()" />
          </td>
        </tr>

        <tr>
          <th><label for="plageconsult_id" title="Date du rendez-vous de consultation">Date</label></th>
          <td class="readonly">
            <input type="text" name="_date" value="{$consult->_date|date_format:"%A %d/%m/%Y"}" readonly="readonly" />
            <input type="hidden" name="plageconsult_id" title="{$consult->_props.plageconsult_id}" value="{$consult->plageconsult_id}" ondblclick="popRDV()" />
          </td>
        </tr>

        <tr>
          <th><label for="heure" title="Heure du rendez-vous de consultation">Heure</label></th>
          <td class="readonly">
            <input type="text" name="heure" value="{$consult->heure|date_format:"%H:%M"}" size="3" readonly="readonly" />
          </td>
        </tr>
        <tr>
          <th><label for="_duree" title="Durée prévue de la consultation">Durée</label></th>
          <td>
            <select name="duree">
              <option value="1" {if $consult->duree == 1} selected="selected" {/if}>simple</option>
              <option value="2" {if $consult->duree == 2} selected="selected" {/if}>double</option>
              <option value="3" {if $consult->duree == 3} selected="selected" {/if}>triple</option>
            </select>
          </td>
        </tr>

      </table>
      
      <table class="form">
        <tr>
          {if $pat->patient_id}
          <th id="clickPat" class="category" onclick="requestInfoPat()">
            ++ Infos patient (cliquez pour afficher) ++
          {else}
          <th id="clickPat" class="category">
            Infos patient (indisponibles)
          {/if}
          </th>
        </tr>
        <tr>
          <td id="infoPat" class="text"></td>
        </tr>
        <tr>
          <td id="infoPat2" class="text"></td>
        </tr>
      </table>
    
    </td>
  </tr>

  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
          {if $consult->consultation_id}
            <input type="reset" value="Réinitialiser" />
            <input type="submit" value="Modifier" />
            <input type="button" value="Supprimer" style="cursor: pointer;" onclick="confirmDeletion(this.form,{ldelim}typeName:'la consultation de',objName:'{$consult->_ref_patient->_view|escape:javascript}'{rdelim})" />
          {else}
            <input type="submit" value="Créer" />
          {/if}
          </td>
        </tr>
      </table>
    
    </td>
  </tr>

</table>

</form>