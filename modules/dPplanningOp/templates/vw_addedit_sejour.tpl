<!-- $Id: vw_addedit_planning.tpl 117 2006-06-13 12:54:06Z Rhum1 $ -->

<script type="text/javascript">
{literal}

function checkFormSejour() {
  var oForm = document.editFrm;
  
  if (!checkForm(oForm)) {
    return false;
  }

  if (!checkDuree()) {
    return false;
  }

  if (!checkDureeHospi()) {
    return false;
  }
  
  return true;
}

function checkDureeHospi() {
  var form = document.editFrm;

  field1 = form.type_adm;
  field2 = form.duree_hospi;
  if (field1 && field2) {
    if (field1[0].checked && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1");
      field2.focus();
      return false;
    }
  }

  return true;
}

function checkChir() {
  var form = document.editFrm;
  var field = null;
  
  if (field = form.chir_id) {
    if (field.value == 0) {
      alert("Chirurgien manquant");
      popChir();
      return false;
    }
  }
  return true;
}

function checkDuree() {
  var form = document.editFrm;
  field1 = form._hour_op;
  field2 = form._min_op;
  if (field1 && field2) {
    if (field1.value == 0 && field2.value == 0) {
      alert("Temps opératoire invalide");
      field1.focus();
      return false;
    }
  }
  return true
}

function modifSejour() {
  var oForm = document.editFrm;
  if (oForm.saisi_SHS.value == 'o') {
    oForm.modif_SHS.value = 1;
    oForm.saisi_SHS.value = 'n';
  }
}

function confirmAnnulation() {
  if (confirm("Veuillez confirmer l'annulation")) {
	 var oForm = document.editFrm;
	 oForm.annule.value = 1; 
	 oForm.submit();
  }
}

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(800, 500, "Patient");
}

function setPat(patient_id, _patient_view) {
  var oForm = document.editFrm;

  if (patient_id) {
    oForm.patient_id.value = patient_id;
    oForm._patient_view.value = _patient_view;
  }
}

function popCode(type) {
  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(document.editFrm.chir_id, "chir");
  url.addParam("type", type)
  url.popup(600, 500, type);
}

function setCode( key, type ) {
  if (key) {
    var form = document.editFrm;
    var field = form.CIM10_code;
    if (type == 'ccam')  field = form._codeCCAM;
    field.value = key;
  }
}

function pageMain() {
//  regFieldCalendar("editFrm", "date_anesth");
  regFieldCalendar("editFrm", "_date_entree_prevue");
  regFieldCalendar("editFrm", "_date_sortie_prevue");
}

{/literal}
</script>

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkFormSejour()">

<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="sejour_id" value="{$sejour->sejour_id}" />
<input type="hidden" name="saisi_SHS" value="{$sejour->saisi_SHS}" />
<input type="hidden" name="modif_SHS" value="{$sejour->modif_SHS}" />
<input type="hidden" name="annulee" value="0" />

<table class="main">
  {if $sejour->sejour_id}
  <tr>
    <td>
      <a class="button" href="index.php?m={$m}&amp;tab={$tab}&amp;sejour_id=0">
        Programmer un nouveau séjour
      </a>
    </td>
    <td>
      <a class="button" href="index.php?m={$m}&amp;tab=vw_edit_planning&amp;sejour_id={$sejour->sejour_id}">
        Programmer une nouvelle intervention dans ce séjour
      </a>
    </td>
  </tr>
  {/if}

  <tr>
    {if $sejour->sejour_id}
    <th colspan="2" class="title" style="color: #f00;">
      <a style="float:right;" href="javascript:view_log('CSejour',{$sejour->sejour_id})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Modification du séjour {$sejour->_view}
    </th>
    {else}
    <th colspan="2" class="title">      
      Création d'un nouveau séjour
    </th>
    {/if}
  </tr>
  
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="3">
            Informations concernant le séjour
          </th>
        </tr>

        <tr>
          <th>
            <label for="praticien_id" title="Praticien responsable. Obligatoire">Praticien :</label>
          </th>
          <td colspan="2">
            <select name="praticien_id" title="{$sejour->_props.praticien_id}">
              <option value="">&mdash; Choisir un praticien</option>
              {foreach from=$listPraticiens item=curr_praticien}
              <option value="{$curr_praticien->user_id}" {if $praticien->user_id == $curr_praticien->user_id} selected="selected" {/if}>
              {$curr_praticien->_view}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>
            <input type="hidden" name="patient_id" title="{$sejour->_props.patient_id}" ondblclick="popPat()" value="{$patient->patient_id}" />
            <label for="patient_id" title="Patient concerné. Obligatoire">Patient :</label>
          </th>
          <td class="readonly">
          	<input type="text" name="_patient_view" size="30" value="{$patient->_view}" readonly="readonly" />
          </td>
          <td class="button">
          	<input type="button" value="Rechercher un patient" onclick="popPat()" />
          </td>
        </tr>

        <tr>
          <th class="category" colspan="3">Admission</th>
        </tr>

        <tr>
          <th>
          	<label for="_date_entree_prevue" title="Choisir une date d'entrée">Entrée prévue :</label>
          </th>
          <td class="date">
            <div id="editFrm__date_entree_prevue_da">{$sejour->_date_entree_prevue|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="_date_entree_prevue" title="date|notNull" value="{$sejour->_date_entree_prevue}" onchange="modifSejour()"/>
            <img id="editFrm__date_entree_prevue_trigger" src="./images/calendar.gif" alt="calendar"/>
          </td>
          <td>
            <select name="_hour_entree_prevue">
            {foreach from=$hours item=hour}
              <option value="{$hour}" {if $sejour->_hour_entree_prevue == $hour} selected="selected" {/if}>{$hour}</option>
            {/foreach}
            </select>
            :
            <select name="_min_entree_prevue">
            {foreach from=$mins item=min}
              <option value="{$min}" {if $sejour->_min_entree_prevue == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>

        <tr>
          <th>
          	<label for="_date_sortie_prevue" title="Choisir une date d'entrée">Sortie prévue :</label>
          </th>
          <td class="date">
            <div id="editFrm__date_sortie_prevue_da">{$sejour->_date_sortie_prevue|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="_date_sortie_prevue" title="date|moreEquals|_date_entree_prevue|notNull" value="{$sejour->_date_sortie_prevue}" onchange="modifSejour()"/>
            <img id="editFrm__date_sortie_prevue_trigger" src="./images/calendar.gif" alt="calendar"/>
          </td>
          <td>
            <select name="_hour_sortie_prevue">
            {foreach from=$hours item=hour}
              <option value="{$hour}" {if $sejour->_hour_sortie_prevue == $hour} selected="selected" {/if}>{$hour}</option>
            {/foreach}
            </select>
            :
            <select name="_min_sortie_prevue">
            {foreach from=$mins item=min}
              <option value="{$min}" {if $sejour->_min_sortie_prevue == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>

        <tr>
          <td class="button" colspan="3">
          {if $sejour->sejour_id}
            <input type="submit" value="Modifier" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'le {$sejour->_view|escape:"javascript"}'{rdelim});" />
            <input type="button" value="Annuler" onclick="confirmAnnulation();" />
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
