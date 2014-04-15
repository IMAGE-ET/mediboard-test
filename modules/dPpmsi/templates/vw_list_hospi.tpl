<script type="text/javascript">
  Main.add(function () {
    Calendar.regField(getForm("changeDate").date, null, {noView: true});
  });
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="8">
      Liste des {{$listSejours|@count}} personne(s) hospitalisée(s) au {{$date|date_format:$conf.longdate}}

      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=CSejour field=facture}}</th>
    <th>{{mb_title class=CSejour field=_NDA}}</th>
    <th>{{mb_label class=CSejour field=praticien_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>
      {{mb_title class=CSejour field=_entree}} /
      {{mb_title class=CSejour field=_sortie}}
    </th>
    <th>DP</th>
    <th>Actes</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    <tr>
      <td {{if !$_sejour->facture}}class="empty"{{/if}}>
        {{if $_sejour->facture}}
          <img src="images/icons/tick.png" alt="ok" />
        {{else}}
          <img src="images/icons/cross.png" alt="alerte" />
        {{/if}}
      </td>
      <td class="text">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
        </strong>
      </td>

      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </td>

      <td class="text">
        {{assign var=patient value=$_sejour->_ref_patient}}
        <a href="?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={{$patient->_id}}&amp;sejour_id={{$_sejour->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
          {{$patient}}
        </span>
        </a>
      </td>

      <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
    	  {{mb_include module=system template=inc_interval_datetime from=$_sejour->_entree to=$_sejour->_sortie}}
    	</span>
      </td>

      <td class="text {{if !$_sejour->DP}}empty{{/if}}">
        {{if !$_sejour->DP}}
          <img src="images/icons/cross.png" alt="alerte" /> Aucun DP
        {{else}}
          <img src="images/icons/tick.png" alt="ok" /> {{$_sejour->DP}}
        {{/if}}
      </td>

      <td class="text {{if $_sejour->_count_actes < 1}}empty{{/if}}">
        {{if $_sejour->_count_actes > 0}}
          <img src="images/icons/tick.png" alt="{{$_sejour->_count_actes}} actes sur le séjour" />
          {{$_sejour->_count_actes}} actes
        {{else}}
          <img src="images/icons/cross.png" alt="Aucun acte sur le séjour" />
          Aucun acte
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>