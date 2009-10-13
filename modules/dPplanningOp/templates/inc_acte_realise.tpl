{{if $codable->_ref_actes_ccam|@count}}
<td rowspan="{{$codable->_ref_actes_ccam|@count}}">

{{* A VERIFIER : 
  * codable->_view fontionne mal pour les interventions 
  *}}

{{if $codable->_class_name == "COperation"}}
	Intervention du {{mb_value object=$codable field=_datetime}}
	{{if $codable->libelle}}<br /> {{$codable->libelle}}{{/if}}
{{/if}}

{{if $codable->_class_name == "CConsultation"}}
	Consultation du {{$codable->_datetime|date_format:"%d %B %Y"}}
	{{if $codable->libelle}}: {{$codable->libelle}}{{/if}}
{{/if}}

{{if $codable->_class_name == "CSejour"}}
	Sejour du {{mb_value object=$codable field=_entree}}
	au {{mb_value object=$codable field=_sortie}} 
{{/if}}

</td>

{{counter start=0 skip=1 assign=_counter}}
{{foreach from=$codable->_ref_actes_ccam item="acte_ccam"}}
  {{if $_counter != 0}}
  <tr>
  {{/if}}
    <td><a href="#code-{{$acte_ccam->code_acte}}" onclick="viewCCAM('{{$acte_ccam->code_acte}}');">{{$acte_ccam->code_acte}}</a></td>
    <td>{{$acte_ccam->code_activite}}</td>
    <td>{{$acte_ccam->code_phase}}</td>
    <td>{{$acte_ccam->modificateurs}}</td>
    <td>{{$acte_ccam->code_association}}</td>
    <td>
      <button id="regle-{{$acte_ccam->_id}}" type="button"
        class="{{if $acte_ccam->regle}}cancel{{else}}tick{{/if}} notext"
        onclick="submitActeCCAM(getForm('reglement-{{$acte_ccam->_id}}'), {{$acte_ccam->_id}}, 'regle')">
        Changer
      </button>
      {{mb_value object=$acte_ccam field=montant_base}}
    </td>
    <td>
      {{if $acte_ccam->montant_depassement}}
        <button id="regle_dh-{{$acte_ccam->_id}}" type="button"
          class="{{if $acte_ccam->regle_dh}}cancel{{else}}tick{{/if}} notext"
          onclick="submitActeCCAM(getForm('reglement-{{$acte_ccam->_id}}'), {{$acte_ccam->_id}}, 'regle_dh')">
          Changer
        </button>
      {{/if}}
      {{mb_value object=$acte_ccam field=montant_depassement}}
    </td>
    <td>
      {{mb_value object=$acte_ccam field=_montant_facture}}
      <div id="divreglement-{{$acte_ccam->_id}}">
      <form name="reglement-{{$acte_ccam->_id}}" method="post" action="">
        <input type="hidden" name="dosql" value="do_acteccam_aed" />
        <input type="hidden" name="m" value="dPsalleOp" />
        <input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
        <input type="hidden" name="_check_coded" value="0" />
        <input type="hidden" name="regle" value="{{$acte_ccam->regle}}" />
        <input type="hidden" name="regle_dh" value="{{$acte_ccam->regle_dh}}" />
     
        {{foreach from=$acte_ccam->_modificateurs item="modificateur"}}
          <input type="hidden" name="modificateur_{{$modificateur}}" value="on" />
        {{/foreach}}
      </form>
      </div>
    </td>
  
  </tr>
  {{counter}}
{{/foreach}}
{{/if}}

