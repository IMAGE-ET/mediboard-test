{{if $sejour->_canRead}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="{{if @$modules.brancardage->_can->read}}4{{else}}3{{/if}}">
        {{tr}}CSejour-back-consultations{{/tr}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label class=CPlageConsult field="chir_id"}}</th>
      <th>{{tr}}Date{{/tr}}</th>
      <th>{{tr}}Hour{{/tr}}</th>
      {{if @$modules.brancardage->_can->read}}
        <th>{{tr}}CBrancardage{{/tr}}</th>
      {{/if}}
    </tr>
    <tbody id="consults-sejour-{{$sejour->_guid}}">
      {{foreach from=$sejour->_ref_consultations item=_consult}}
        <tr>
          <td>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
          </td>
          <td>{{$_consult->_date|date_format:$conf.date}}</td>
          <td>{{mb_value object=$_consult field=heure}}</td>
          {{if @$modules.brancardage->_can->read}}
            <td class="button">
              {{mb_script module=brancardage script=creation_brancardage ajax="true"}}
              {{assign var=service_id value=$sejour->service_id}}
              <div id="patient_pret-{{$_consult->sejour_id}}">
                {{mb_include module=brancardage template=inc_exist_brancard colonne="patient_pret" sejour_id=$sejour->_id
                brancardage=$_consult->_ref_brancardage see_sejour=true destination="CService" destination_guid=""}}
              </div>
            </td>
          {{/if}}
        </tr>
      {{foreachelse}}
        <tr><td colspan="{{if @$modules.brancardage->_can->read}}4{{else}}3{{/if}}" class="empty">{{tr}}CConsultation.none{{/tr}}</td></tr>
      {{/foreach}}
    </tbody>
  </table>

{{elseif $sejour->_id}}
  <div class="small-info">Vous n'avez pas accès au détail des consultations.</div>
{{/if}}