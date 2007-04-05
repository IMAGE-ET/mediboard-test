<input id="currDateJSAccess" name="currDateJSAccess" type="hidden" value="{{$date}}" />

{{if !$board}}
<div style="font-weight:bold; height:20px; text-align:center;">
  {{$date|date_format:"%A %d %B %Y"}}
  <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
  <script type="text/javascript">
    regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab=vw_idx_planning&date=");
  </script>
</div>
{{/if}}

{{if $boardItem}}
      <table class="tbl" style="font-size: 9px;">
{{else}}
      <table class="tbl">
{{/if}}
        <tr>
          <th class="title" colspan="5">
            {{if $boardItem}}
              Interventions
            {{else}}
              Opérations
            {{/if}}
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>Actes médicaux</th>
          <th>Heure prévue</th>
          <th>Durée</th>
          {{if !$boardItem}}
            <th>Compte-rendu</th>
          {{/if}}
        </tr>
        {{if $urgences}}
        {{foreach from=$listUrgences item=curr_op}}
        <tr>
          <td class="text">
            <a href="{{$curr_op->_ref_sejour->_ref_patient->_dossier_cabinet_url}}">
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
              {{if $curr_op->libelle}}
                <em>[{{$curr_op->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              {{if !$board}}
              : {{$curr_code->libelleLong}}
              {{/if}}
              <br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;">
            {{if $curr_op->annulee}}
            [ANNULEE]
            {{else}}
            <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->_datetime|date_format:"le %d/%m/%Y à %Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
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
                  <input type="hidden" name="object_id" value="{{$curr_op->_id}}" />
                  {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
                  <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}'})" />
                  </form>
                </td>
              </tr>
            {{/foreach}}
            </table>
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_op->_id}})">
                    <option value="">&mdash; Choisir un modèle</option>
                    <optgroup label="Opération">
                    {{foreach from=$crList item=curr_cr}}
                    <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                    <optgroup label="Hospitalisation">
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
            <a href="{{$curr_op->_ref_sejour->_ref_patient->_dossier_cabinet_url}}">
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{if $curr_op->libelle}}
                <em>[{{$curr_op->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              {{if !$board}}
              : {{$curr_code->libelleLong}}
              {{/if}}
              {{if $boardItem}}
              : {{$curr_code->libelleLong|truncate:50:"...":false}}
              {{/if}}
              <br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;">
            {{if $curr_op->annulee}}
            [ANNULEE]
            {{else}}
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->time_operation|date_format:"%Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          {{if !$boardItem}}
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
                  <input type="hidden" name="object_id" value="{{$curr_op->_id}}" />
                  {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
                  <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}',ajax:1,target:'systemMsg'},{onComplete:reloadAfterSaveDoc})" />
                  </form>
                </td>
              </tr>
            {{/foreach}}
            </table>
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_op->_id}})">
                    <option value="">&mdash; Choisir un modèle</option>
                    <optgroup label="Opération">
                    {{foreach from=$crList item=curr_cr}}
                    <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                    <optgroup label="Hospitalisation">
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
          {{/if}}
        </tr>
        {{/foreach}}
        {{/foreach}}
        {{/if}}
      </table>