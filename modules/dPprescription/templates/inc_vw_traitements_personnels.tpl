<table class="form">
  <tr>
    <th class="title">Traitements du patient</th>
  </tr>
  <tr>
    <td>
      <ul>
      {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}
        <li>
          <button class="add notext" type="button" onclick="window.opener.transfertLineTP('{{$_line->_id}}', '{{$sejour_id}}')">
            {{tr}}Add{{/tr}}
          </button>
          {{if $_line->fin}}
            Du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->fin|date_format:"%d/%m/%Y"}} :
          {{elseif $_line->debut}}
            Depuis le {{$_line->debut|date_format:"%d/%m/%Y"}} :
          {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
            <a href=#1 onclick="window.opener.Prescription.viewProduit(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');"
              style="white-space: normal">
              {{$_line->_ucd_view}} ({{$_line->_forme_galenique}}) -
              {{if $_line->_ref_prises|@count}}
                {{foreach from=$_line->_ref_prises item=_prise name=prises}}
                  {{$_prise->_view}} 
                  {{if !$smarty.foreach.prises.last}}, {{/if}}
                {{/foreach}}
              {{else}}
                Aucune posologie
              {{/if}}
            </a>
          </span>
        </li>
      {{foreachelse}}
        <li class="empty">{{tr}}CTraitement.none{{/tr}}</li>
      {{/foreach}}
      </ul>
      {{if $dossier_medical->_ref_traitements|@count && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count}}
      <hr style="width: 50%;" />
      {{/if}}
    </td>
  </tr>
</table>