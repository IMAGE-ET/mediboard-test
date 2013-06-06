{{if $codable->_ref_actes|@count}}

<td rowspan="{{$codable->_ref_actes|@count}}">
  {{if $codable->_class == "COperation"}}
    Intervention du {{mb_value object=$codable field=_datetime_best}}
    {{if $codable->libelle}}<br /> {{$codable->libelle}}{{/if}}
  {{/if}}
  
  {{if $codable->_class == "CConsultation"}}
    Consultation du {{$codable->_datetime|date_format:"%d %B %Y"}}
    {{if $codable->motif}}: {{$codable->motif}}{{/if}}
  {{/if}}
  
  {{if $codable->_class == "CSejour"}}
    Sejour du {{mb_value object=$codable field=_entree}}
    au {{mb_value object=$codable field=_sortie}} 
  {{/if}}
</td>

{{counter start=0 skip=1 assign=_counter}}
{{foreach from=$codable->_ref_actes item="acte"}}
  {{if $_counter != 0}}
  <tr>
  {{/if}}
    <td>
      {{if $acte->_class == "CActeCCAM"}}
        <a href="#code-{{$acte->code_acte}}" onclick="viewCCAM('{{$acte->code_acte}}');">{{$acte->code_acte}}</a>
      {{elseif $acte->_class == "CActeTarmed"}}
        <a href="#code-{{$acte->code}}"  onclick="viewTarmed('{{$acte->code}}');">{{$acte->code}}</a>
      {{else}}
        {{$acte->code}}
      {{/if}}
    </td>
    {{if $acte->_class == "CActeCCAM"}}
      <td>{{$acte->code_activite}}</td>
      <td>{{$acte->code_phase}}</td>
      <td>{{$acte->modificateurs}}</td>
      <td>{{$acte->code_association}}</td>
    {{else}}
      <td colspan="4"></td>
    {{/if}}
    <td>
      {{if $acte->_class == "CActeCCAM"}}
        <button id="regle-{{$acte->_id}}" type="button"
          class="{{if $acte->regle}}cancel{{else}}tick{{/if}} notext"
          onclick="submitActeCCAM(getForm('reglement-{{$acte->_guid}}'), '{{$acte->_id}}', 'regle')">
          Changer
        </button>
      {{/if}}
      {{mb_value object=$acte field=montant_base}}
    </td>
    <td>
      {{if $acte->montant_depassement && $acte->_class == "CActeCCAM"}}
        <button id="regle_dh-{{$acte->_id}}" type="button"
          class="{{if $acte->regle_dh}}cancel{{else}}tick{{/if}} notext"
          onclick="submitActeCCAM(getForm('reglement-{{$acte->_id}}'), '{{$acte->_id}}', 'regle_dh')">
          Changer
        </button>
      {{/if}}
      {{mb_value object=$acte field=montant_depassement}}
    </td>
    <td>
      {{mb_value object=$acte field=_montant_facture}}
      {{if $acte->_class == "CActeCCAM"}}
        <div id="divreglement-{{$acte->_guid}}">
          <form name="reglement-{{$acte->_guid}}" method="post" action="">
            <input type="hidden" name="dosql" value="do_acteccam_aed" />
            <input type="hidden" name="m" value="dPsalleOp" />
            <input type="hidden" name="acte_id" value="{{$acte->_id}}" />
            <input type="hidden" name="_check_coded" value="0" />
            <input type="hidden" name="regle" value="{{$acte->regle}}" />
            <input type="hidden" name="regle_dh" value="{{$acte->regle_dh}}" />
            {{foreach from=$acte->_modificateurs item="modificateur"}}
              <input type="hidden" name="modificateur_{{$modificateur}}" value="on" />
            {{/foreach}}
          </form>
        </div>
      {{/if}}
    </td>
  </tr>
  {{counter}}
{{/foreach}}
{{/if}}