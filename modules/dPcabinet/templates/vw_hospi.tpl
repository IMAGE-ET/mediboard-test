<script type="text/javascript">
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

function reloadAfterSaveDoc(){
  window.location.href = window.location.href;
}

function pageMain() {
  PairEffect.initGroup("functionEffect", { 
    bStoreInCookie: true
  });
  regRedirectPopupCal("{{$dateRecherche}}", "index.php?m={{$m}}&tab={{$tab}}&dateRecherche=");
}
</script>

<table class="main">
  <tr>
    <td colspan="2">
      <form name="editFrmPratDate" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table style="width: 100%">
        <tr>
          <td>
            <label for="chirSel" title="Veuillez choisir un praticien">Praticiens</label>
            <select name="chirSel" onchange="submit()">
            <option value="0" {{if $chirSel == 0}}selected="selected"{{/if}}>&mdash; Selectionner un praticien &mdash;</option>
            {{foreach from=$listPrat item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $chirSel == $curr_prat->user_id}}selected="selected"{{/if}}>
              {{$curr_prat->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
          <th>
            {{$dateRecherche|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td class="HalfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Entrées</th>
        </tr>
        <tr>
          <th>Heure</th>
          <th>Patient</th>
          <th>Chambre</th>
        </tr>
        {{foreach from=$AfflistEntree item=curr_aff}}
        <tr>
          <td>{{$curr_aff->entree|date_format:"%H h %M"}}</td>
          <td>{{$curr_aff->_ref_sejour->_ref_patient->_view}}</td>
          <td>{{$curr_aff->_ref_lit->_view}}</td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="HalfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Sorties</th>
        </tr>
        <tr>
          <th>Heure</th>
          <th>Patient</th>
          <th>Chambre</th>
        </tr>
        {{foreach from=$AfflistSortie item=curr_aff}}
        <tr id="operSejour{{$curr_aff->sejour_id}}-trigger">
          <td>{{$curr_aff->sortie|date_format:"%H h %M"}}</td>
          <td style="background-image: none;padding: 2px;">{{$curr_aff->_ref_sejour->_ref_patient->_view}}</td>
          <td style="background-image: none;padding: 2px;">{{$curr_aff->_ref_lit->_view}}</td>
        </tr>
        
        <tbody class="functionEffect" id="operSejour{{$curr_aff->sejour_id}}">
        {{foreach from=$curr_aff->_ref_sejour->_ref_operations item=curr_oper}}
        <tr>
          <td></td>
          <td>
            {{foreach from=$curr_oper->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
            {{/foreach}}
          </td>
          <td>
            <table>
            {{foreach from=$curr_oper->_ref_documents item=curr_oper_doc}}
              <tr>
                <th>{{$curr_oper_doc->nom}}</th>
                <td class="button">
                  <form name="editDocumentFrm{{$curr_oper_doc->compte_rendu_id}}" action="?m={{$m}}" method="post">
                  <input type="hidden" name="m" value="dPcompteRendu" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_modele_aed" />
                  <input type="hidden" name="object_id" value="{{$curr_oper->operation_id}}" />
                  <input type="hidden" name="compte_rendu_id" value="{{$curr_oper_doc->compte_rendu_id}}" />
                  <button class="edit notext" type="button" onclick="editDocument({{$curr_oper_doc->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$curr_oper_doc->nom|escape:javascript}}'})" />
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
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_oper->operation_id}})">
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
        </tbody>
        {{/foreach}}        
      </table>
    </td>
  </tr>
</table>