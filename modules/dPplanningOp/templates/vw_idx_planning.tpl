<!-- $Id$ -->

<script type="text/javascript">

function selectCR(id, form) {
  var modele = form.modele.value;
  if(modele != 0)
    editModele(id, modele);
}

function editDocument(compte_rendu_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function createDocument(modele_id, operation_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("modele_id", modele_id);
  url.addParam("object_id", operation_id);
  url.popup(700, 700, "Document");
}

function validerCompteRendu(form) {
  if (confirm('Veuillez confirmer la validation du compte-rendu')) {
    form.cr_valide.value = "1";
    form.submit();
  }
}

function supprimerCompteRendu(form) {
  if (confirm('Veuillez confirmer la suppression')) {
    form.compte_rendu.value = "";
    form.cr_valide.value = "0";
    form.submit();
  }
}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <form action="index.php" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="selChir">Chirurgien</label>
      <select name="selChir" onchange="this.form.submit()">
        <option value="-1">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listChir item=curr_chir}}
        <option value="{{$curr_chir->user_id}}" {{if $curr_chir->user_id == $selChir}} selected="selected" {{/if}}>
          {{$curr_chir->_view}}
        </option>
        {{/foreach}}
      </select>
      </form>
    </td>
  </tr>
  <tr>
    <th>
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
    <th class="greedyPane">
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Date</th>
          <th>Plage</th>
          <th>Opérations</th>
          <th>Temps pris</th>
        </tr>
        {{foreach from=$listPlages item=curr_plage}}
        {{if $curr_plage.id_spec}}
        <tr>
          <td style="background: #aae" align="right">{{$curr_plage.date|date_format:"%a %d %b %Y"}}</td>
          <td style="background: #aae" align="center">{{$curr_plage.debut|date_format:"%Hh%M"}} à {{$curr_plage.fin|date_format:"%Hh%M"}}</td>
          <td style="background: #aae" align="center">{{$curr_plage.total}}</td>
          <td style="background: #aae" align="center">Plage de spécialité</td>
        </tr>
        {{else}}
        <tr>
          <td align="right"><a href="index.php?m={{$m}}&amp;tab=0&amp;date={{$curr_plage.date|date_format:"%Y-%m-%d"}}&amp;urgences=0">{{$curr_plage.date|date_format:"%a %d %b %Y"}}</a></td>
          <td align="center">{{$curr_plage.debut|date_format:"%Hh%M"}} à {{$curr_plage.fin|date_format:"%Hh%M"}}</td>
          <td align="center">{{$curr_plage.total}}</td>
          <td align="center">{{$curr_plage.duree|date_format:"%Hh%M"}}</td>
        </tr>
        {{/if}}
        {{/foreach}}
        {{if $listUrgences|@count}}
        <tr>
          <td align="right"><a href="index.php?m={{$m}}&amp;tab=0&amp;urgences=1">Urgences</a></td>
          <td align="center">-</td>
          <td align="center">{{$listUrgences|@count}}</td>
          <td align="center">-</td>
        </tr>
        {{/if}}
      </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>Patient</th>
          <th>Actes médicaux</th>
          <th>Heure prévue</th>
          <th>Durée</th>
          <th>Compte-rendu</th>
        </tr>
        {{if $urgences}}
        {{foreach from=$listUrgences item=curr_op}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;">
            {{if $curr_op->annulee}}
            [ANNULEE]
            {{else}}
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->time_operation|date_format:"%Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          <td>
            <table>
            {{foreach from=$curr_op->_ref_documents item=document}}
              <tr>
                <th>{{$document->nom}}</th>
                <td class="button">
                  <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
                  <input type="hidden" name="m" value="dPcompteRendu" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_modele_aed" />
                  <input type="hidden" name="object_id" value="{{$curr_op->operation_id}}" />
                  <input type="hidden" name="compte_rendu_id" value="{{$document->compte_rendu_id}}" />
                  <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="this.form.del.value = 1; this.form.submit()"></button>
                  </form>
                </td>
              </tr>
            {{/foreach}}
            </table>
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_op->operation_id}})">
                    <option value="">&mdash; Choisir un modèle</option>
                    <optgroup label="CRO">
                    {{foreach from=$crList item=curr_cr}}
                    <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                    <optgroup label="Document d'hospi">
                    {{foreach from=$hospiList item=curr_hospi}}
                    <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                  </select>
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
        {{/foreach}}
        {{else}}
        {{foreach from=$listDay item=curr_plage}}
        <tr>
          <th colspan="6">{{$curr_plage->_ref_salle->nom}} : de {{$curr_plage->debut|date_format:"%Hh%M"}} à {{$curr_plage->fin|date_format:"%Hh%M"}}</th>
        </tr>
        {{foreach from=$curr_plage->_ref_operations item=curr_op}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;">
            {{if $curr_op->annulee}}
            [ANNULEE]
            {{else}}
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->time_operation|date_format:"%Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          <td>
            <table>
            {{foreach from=$curr_op->_ref_documents item=document}}
              <tr>
                <th>{{$document->nom}}</th>
                <td class="button">
                  <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
                  <input type="hidden" name="m" value="dPcompteRendu" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_modele_aed" />
                  <input type="hidden" name="object_id" value="{{$curr_op->operation_id}}" />
                  <input type="hidden" name="compte_rendu_id" value="{{$document->compte_rendu_id}}" />
                  <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="this.form.del.value = 1; this.form.submit()"></button>
                  </form>
                </td>
              </tr>
            {{/foreach}}
            </table>
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_op->operation_id}})">
                    <option value="">&mdash; Choisir un modèle</option>
                    <optgroup label="CRO">
                    {{foreach from=$crList item=curr_cr}}
                    <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                    <optgroup label="Document d'hospi">
                    {{foreach from=$hospiList item=curr_hospi}}
                    <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                  </select>
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
        {{/foreach}}
        {{/foreach}}
        {{/if}}
      </table>
    </td>
  </tr>
</table>