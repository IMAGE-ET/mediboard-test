<script type="text/javascript">
onSubmitReglement = function(form) {
  return onSubmitFormAjax(form, { 
    onComplete: function() { 
      location.reload();
    } 
  } );
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Retours Noemie
							{{mb_include module=system template=inc_interval_date from=$_date_min to=$_date_max}}
            </a>
          </th>
        </tr>

        <!-- Praticiens concernés -->
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}

      </table>
    </td>
		
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="category" colspan="2">Réglements concernés</th>
        </tr>
        <tr>
          <th class="category">Nombre</th>
          <td>{{$total.nb}} consultation(s)</td>
        </tr>
        <tr>
          <th class="category">Valeur</th>
          <td>{{$total.value|currency}}</td>
        </tr>
        <tr>
          <th class="category">Tout valider</th>
          <td>
            <form name="reglement-add-tiers-multi" action="?m={{$m}}" method="post" onsubmit="return onSubmitReglement(this);">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_multi_noemie_aed" />
              <input type="hidden" name="date" value="now" />
              <input type="hidden" name="emetteur" value="tiers" />
              <input type="hidden" name="mode" value="virement" />
              <button class="add" type="submit">{{tr}}All{{/tr}}</button>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{mb_label class=CConsultation field=_prat_id}}</th>
    <th>{{mb_label class=CConsultation field=patient_id}}</th>
    <th>{{mb_label class=CConsultation field=_date}}</th>
    <th>{{mb_label class=CConsultation field=du_tiers}}</th>
    <th>{{mb_label class=CConsultation field=_du_tiers_restant}}</th>
    <th>{{tr}}Validate{{/tr}}</th>
  </tr>
  {{foreach from=$listConsults item=_consult}}
  <tr>
    <td class="text">
      {{assign var=prat_id value=$_consult->_ref_chir->_id}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$listPrat.$prat_id}}
    </td>

    <td class="text">
      {{assign var=patient value=$_consult->_ref_patient}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
    </td>

    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
        {{mb_value object=$_consult field=_date}}
      </span>
    </td>
          
    <td>{{$_consult->du_tiers|currency}}</td>
    <td>{{$_consult->_du_tiers_restant|currency}}</td>
    <td>
      <form name="reglement-add-tiers-{{$_consult->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitReglement(this);">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_reglement_aed" />
        <input type="hidden" name="date" value="now" />
        <input type="hidden" name="emetteur" value="tiers" />
        {{mb_field object=$_consult field="consultation_id" hidden=1}}
        <button class="add notext" type="submit">{{tr}}Add{{/tr}}</button>
        {{mb_field object=$_consult->_new_tiers_reglement field="montant"}}
        {{mb_field object=$_consult->_new_tiers_reglement field="mode"}}
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>