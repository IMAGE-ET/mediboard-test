{literal}
<script type="text/javascript">

function cancelTarif() {
  var oForm = document.tarifFrm;
  oForm.secteur1.value = 0;
  oForm.secteur2.value = 0;
  oForm.tarif.value = "";
  oForm.paye.value = "0";
  oForm.date_paiement.value = "";
  submitFdr(oForm);
}

function modifTarif() {
  var oForm = document.tarifFrm;
  var secteurs = oForm.choix.value;
  if(secteurs != '') {
    var pos = secteurs.indexOf("/");
    var size = secteurs.length;
    var secteur1 = eval(secteurs.substring(0, pos));
    var secteur2 = eval(secteurs.substring(pos+1, size));
    oForm.secteur1.value = secteur1;
    oForm.secteur2.value = secteur2;
    oForm._somme.value = secteur1 + secteur2;
    for (i = 0;i < oForm.choix.length;++i)
    if(oForm.choix.options[i].selected == true)
     oForm.tarif.value = oForm.choix.options[i].text;
   } else {
     oForm.secteur1.value = 0;
     oForm.secteur2.value = 0;
     oForm._somme.value = '';
     oForm.tarif.value = '';
   }  
}

function effectuerReglement() {
  var oForm = document.tarifFrm;
  oForm.paye.value = "1";
  oForm.date_paiement.value = makeDATEFromDate(new Date());
  submitFdr(oForm);
}

function putTiers() {
  var form = document.tarifFrm;
  form.type_tarif.value = form._tiers.checked ? "tiers" : "";
}

function editDocument(compte_rendu_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function createDocument(oSelect, consultation_id) {
  if (modele_id = oSelect.value) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", consultation_id);
    url.popup(700, 700, "Document");
  }
  
  oSelect.value = "";
}

function newExam(oSelect, consultation_id) {
  if (sAction = oSelect.value) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen"); 
  }

  oSelect.value = ""; 
}

function reloadFdr() {
  var mainUrl = new Url;
  mainUrl.setModuleAction("dPcabinet", "httpreq_vw_fdr_consult");
  mainUrl.addParam("selConsult", document.editFrm.consultation_id.value);
  mainUrl.requestUpdate('fdrConsult', { waitingText : null });
}

function submitFdr(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadFdr });
}

</script>
{/literal}

<table class="form">
  <tr>
    <th class="category">Fichiers liés</th>
    <th class="category">Documents</th>
    <th colspan="2" class="category">Règlement</th>
  </tr>
  <tr>
    <td class="text">
      <form name="newExamen" action="?m=dPcabinet">
        <label for="type_examen" title="Type d'examen complémentaire à effectuer"><strong>Examens complémentaires :</strong></label>
        <select name="type_examen" onchange="newExam(this, {$consult->consultation_id})">
          <option value="">&mdash; Choisir un type d'examen</option>
          <option value="exam_audio">Audiogramme</option>
        </select>
      </form>
      <strong>Fichiers</strong>
      <ul>
        {foreach from=$consult->_ref_files item=curr_file}
        <li>
          <form name="uploadFrm{$curr_file->file_id}" action="?m=dPcabinet" enctype="multipart/form-data" method="post" onsubmit="checkForm(this)">
            <a href="mbfileviewer.php?file_id={$curr_file->file_id}">{$curr_file->file_name}</a>
            ({$curr_file->_file_size})
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_file_aed" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="file_id" value="{$curr_file->file_id}" />
            <button type="button"
              onclick="confirmDeletion(this.form, {ldelim}typeName:'le fichier',objName:'{$curr_file->file_name|escape:javascript}',ajax:1,target:'systemMsg'{rdelim},{ldelim}onComplete:reloadFdr{rdelim})"/>
              <img src="modules/dPcabinet/images/cross.png" />
            </button>
          </form>
        </li>
        {foreachelse}
          <li>Aucun fichier disponible</li>
        {/foreach}
      </ul>
      <form name="uploadFrm" action="?m=dPcabinet" enctype="multipart/form-data" method="post" onsubmit="checkForm(this)">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="dosql" value="do_file_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="file_consultation" value="{$consult->consultation_id}" />
        <input type="file" name="formfile" size="0" /><br />
        <input type="submit" value="ajouter" />
      </form>
    </td>
    <td>
    
    <table class="form">
      {foreach from=$consult->_ref_documents item=document}
      <tr>
        <th>{$document->nom}</th>
        <td class="button">
          <form name="editDocumentFrm{$document->compte_rendu_id}" action="?m={$m}" method="post">
          <input type="hidden" name="m" value="dPcompteRendu" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_modele_aed" />
          <input type="hidden" name="object_id" value="{$consult->consultation_id}" />
          <input type="hidden" name="compte_rendu_id" value="{$document->compte_rendu_id}" />
          <button type="button" onclick="editDocument({$document->compte_rendu_id})">
            <img src="modules/dPcabinet/images/edit.png" /> 
          </button>
          <button type="button" onclick="this.form.del.value = 1; submitFdr(this.form)">
            <img src="modules/dPcabinet/images/trash.png" /> 
          </button>
          </form>
        </td>
      </tr>
      {/foreach}
    </table>
    
    <form name="newDocumentFrm" action="?m={$m}" method="post">
    <table class="form">
      <tr>
        <td>
          <select name="_choix_modele" onchange="createDocument(this, {$consult->consultation_id})">
            <option value="">&mdash; Choisir un modèle</option>
            {if $listModelePrat|@count}
            <optgroup label="Modèles du praticien">
              {foreach from=$listModelePrat item=curr_modele}
              <option value="{$curr_modele->compte_rendu_id}">{$curr_modele->nom}</option>
              {/foreach}
            </optgroup>
            {/if}
            {if $listModeleFunc|@count}
            <optgroup label="Modèles du cabinet">
              {foreach from=$listModeleFunc item=curr_modele}
              <option value="{$curr_modele->compte_rendu_id}">{$curr_modele->nom}</option>
              {/foreach}
            </optgroup>
            {/if}
          </select>
        </td>
      </tr>
    </table>
    
    </form>
    
    </td>
    <td>
      <form name="tarifFrm" action="?m={$m}" method="post" onsubmit="checkForm(this)">

      <input type="hidden" name="m" value="{$m}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="consultation_id" value="{$consult->consultation_id}" />
      <input type="hidden" name="_check_premiere" value="{$consult->_check_premiere}" />
 
      <table class="form">
        {if !$consult->tarif}
        <tr>
          <th><label for="choix" title="Type de tarif pour la consultation. Obligatoire.">Choix du tarif :</label></th>
          <td>
            <select name="choix"  title="notNull|str" onchange="modifTarif()">
              <option value="" selected="selected">&mdash; Choix du tarif</option>
              {if $tarifsChir|@count}
              <optgroup label="Tarifs praticien">
              {foreach from=$tarifsChir item=curr_tarif}
                <option value="{$curr_tarif->secteur1}/{$curr_tarif->secteur2}">{$curr_tarif->description}</option>
              {/foreach}
              </optgroup>
              {/if}
              {if $tarifsCab|@count}
              <optgroup label="Tarifs cabinet">
              {foreach from=$tarifsCab item=curr_tarif}
                <option value="{$curr_tarif->secteur1}/{$curr_tarif->secteur2}">{$curr_tarif->description}</option>
              {/foreach}
              </optgroup>
              {/if}
            </select>
          </td>
        </tr>
        {/if}
        {if $consult->paye == "0"}
        <tr>
          <th><label for="_somme" title="Somme à régler. Obligatoire.">Somme à régler :</label></th>
          <td>
            <input type="text" size="4" name="_somme" title="notNull|currency" value="{$consult->secteur1+$consult->secteur2}" /> €
            <input type="hidden" name="secteur1" value="{$consult->secteur1}" />
            <input type="hidden" name="secteur2" value="{$consult->secteur2}" />
            <input type="hidden" name="tarif" value="{if $consult->tarif != null}{$consult->tarif}{/if}" />
            <input type="hidden" name="paye" value="0" />
            <input type="hidden" name="date_paiement" value="" />
          </td>
        </tr>
        {else}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="secteur1" value="{$consult->secteur1}" />
            <input type="hidden" name="secteur2" value="{$consult->secteur2}" />
            <input type="hidden" name="tarif" value="{$consult->tarif}" />
            <input type="hidden" name="paye" value="{$consult->paye}" />
            <input type="hidden" name="date_paiement" value="{$consult->date_paiement}" />
            <strong>{$consult->secteur1+$consult->secteur2} € ont été réglés : {$consult->type_tarif}</strong>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="button" value="Annuler" onclick="cancelTarif()" />
          </td>
        </tr>
        {/if}
        {if $consult->tarif && $consult->paye == "0"}
        <tr>
          <th>
            <label for="type_tarif" title="Moyen de paiement">Moyen de paiement :</label>
          </th>
          <td>
            <select name="type_tarif">
              <option value="cheque"  {if $consult->type_tarif == "cheque" }selected="selected"{/if}>Chèques     </option>
              <option value="CB"      {if $consult->type_tarif == "CB"     }selected="selected"{/if}>CB          </option>
              <option value="especes" {if $consult->type_tarif == "especes"}selected="selected"{/if}>Espèces     </option>
              <option value="tiers"   {if $consult->type_tarif == "tiers"  }selected="selected"{/if}>Tiers-payant</option>
              <option value="autre"   {if $consult->type_tarif == "autre"  }selected="selected"{/if}>Autre       </option>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="button" value="Règlement effectué" onclick="effectuerReglement()" />
            <input type="button" value="Annuler" onclick="cancelTarif()"/>
          </td>
        </tr>
        {elseif $consult->paye == "0"}
        <tr>
          <th><label for="_tiers" title="Le règlement s'effectue par tiers-payant">Tiers-payant ?</label></th>
          <td>
            <input type="checkbox" name="_tiers" onchange="putTiers()" />
            <input type="hidden" name="type_tarif" value="" />
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="button" value="Valider ce tarif" onclick="submitFdr(this.form)" />
            <input type="button" value="Annuler" onclick="cancelTarif()"/>
          </td>
        </tr>
        {/if}
      </table>
      </form>
    </td>
  </tr>
</table>
          
