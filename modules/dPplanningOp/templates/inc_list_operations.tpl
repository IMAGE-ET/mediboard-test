<input id="currDateJSAccess" name="currDateJSAccess" type="hidden" value="{{$date}}" />
{{if !$board}}
<div style="font-weight:bold; height:20px; text-align:center;">
  {{$date|date_format:"%A %d %B %Y"}}
  <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
  <script type="text/javascript">
    regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_idx_planning&date=");
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
            Interventions
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>Actes</th>
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
            <a href="{{$curr_op->_ref_sejour->_ref_patient->_dossier_cabinet_url}}"
              class="tooltip-trigger"
              onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CPatient', object_id: {{$curr_op->_ref_sejour->_ref_patient->_id}} } })"
            >
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
          <td style="text-align: center;" {{if $curr_op->annulee}}class="cancelled{{/if}}">
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
          
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) Document.create(this.value, {{$curr_op->_id}})">
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
                  <br />
                  <select name="_choix_pack" onchange="if (this.value) DocumentPack.create(this.value, {{$curr_op->_id}})">
                    <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
                    {{foreach from=$packList item=curr_pack}}
                      <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
                    {{foreachelse}}
                      <option value="">{{tr}}pack-none{{/tr}}</option>
                    {{/foreach}}
                  </select>
                </td>
              </tr>
            </table>
            </form>
            
            <div id="document-{{$curr_op->_id}}">
              {{include file="../../dPsalleOp/templates/inc_vw_list_documents.tpl" selOp=$curr_op}}
            </div>
            
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
            <a href="{{$curr_op->_ref_sejour->_ref_patient->_dossier_cabinet_url}}"
              class="tooltip-trigger"
              onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CPatient', object_id: {{$curr_op->_ref_sejour->_ref_patient->_id}} } })"
            >
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
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
          <td style="text-align: center;" {{if $curr_op->annulee}}class="cancelled{{/if}}">
            {{if $curr_op->annulee}}
              [ANNULEE]
            {{else}}
            <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{if $curr_op->time_operation != "00:00:00"}}
                Validé pour {{$curr_op->time_operation|date_format:"%Hh%M"}}
              {{else}}
                Non validé
              {{/if}}
              <br />
              {{if $curr_op->horaire_voulu}}
              (souhaité pour {{$curr_op->horaire_voulu|date_format:"%Hh%M"}})
              {{/if}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          {{if !$boardItem}}
          <td>
            <form name="newDocumentFrm-{{$curr_op->_id}}" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) Document.create(this.value, {{$curr_op->_id}})">
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
                  <br />
                  <select name="_choix_pack" onchange="if (this.value) DocumentPack.create(this.value, {{$curr_op->_id}})">
                    <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
                    {{foreach from=$packList item=curr_pack}}
                      <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
                    {{foreachelse}}
                      <option value="">{{tr}}pack-none{{/tr}}</option>
                    {{/foreach}}
                  </select>
                  <script type="text/javascript">
                    modeleSelector[{{$curr_op->_id}}] = new ModeleSelector("newDocumentFrm-{{$curr_op->_id}}", null, "_modele_id", "_object_id");
                  </script>
                  <button type="button" class="search" onclick="modeleSelector[{{$curr_op->_id}}].pop('{{$curr_op->_id}}','{{$curr_op->_class_name}}','{{$curr_op->chir_id}}')">Modèle</button>
							    <input type="hidden" name="_modele_id" value="" />
							    <input type="hidden" name="_object_id" value="" onchange="Document.create(this.form._modele_id.value, this.value, '{{$curr_op->_id}}','{{$curr_op->_class_name}}'); this.value=''; this.form._modele_id.value=''; "/>
                </td>
              </tr>
            </table>
            </form>
            <div id="document-{{$curr_op->_id}}">
              {{include file="../../dPsalleOp/templates/inc_vw_list_documents.tpl" selOp=$curr_op}}
            </div>
          </td>
          {{/if}}
        </tr>
        {{/foreach}}
        {{/foreach}}
        {{/if}}
      </table>