{{if !"dPprescription"|module_active && $mode == "plannable"}}
  <div class="small-warning">
    <div>Le module <strong>Param. Prescription</strong> n'est pas installé ou activé.</div>
    <div>La prescription de rééducation n'est donc pas possible.</div>
  </div>
  {{mb_return}}
{{/if}}

<script>
Main.add(function() {
  Control.Tabs.setTabCount.curry('board-sejours-{{$mode}}', '{{$sejours|@count}}');
})	
</script>
<table class="tbl">
  <tr>
    <th colspan="3">
      {{mb_title class=CSejour field=patient_id}} /
      {{mb_title class=CPatient field=_age}}
		</th>
    <th>{{mb_title class=CSejour field=libelle}}</th>
    <th class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th class="narrow">{{mb_title class=CSejour field=sortie}}</th>
    <th class="narrow" colspan="2"><label title="Evenements planifiés par le rééducateur (cette semaine - pendant tout le séjour)">Evt.</label></th>
  </tr>
	
  {{foreach from=$sejours item=_sejour}}
    {{assign var=patient value=$_sejour->_ref_patient}}
    {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
    <tr {{if $_sejour->_count_evenements_ssr_week}} style="font-weight: bold;" {{/if}}>
      <td class="text {{if !$bilan->_encours}}arretee{{/if}}">
        {{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
        <a href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id={{$_sejour->_id}}#planification">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
            {{mb_value object=$_sejour field=patient_id}}
          </span>
        </a>
      </td>
      <td class="narrow">
        {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
          {{assign var=prescription_id value=$prescription->_id}}
          {{mb_include module=system template=inc_icon_alerts object=$prescription callback="function() {BoardSejours.updateTab('$mode');}" nb_alerts=$prescription->_count_alertes}}
        {{else}}
          {{if $prescription->_count_fast_recent_modif}}
            <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
            {{mb_include module=system template=inc_vw_counter_tip count=$prescription->_count_fast_recent_modif}}
          {{/if}}
        {{/if}}
      </td>
      <td class="narrow">
        {{mb_value object=$patient field=_age}}
      </td>
      <td class="text">
        {{if $bilan->hospit_de_jour}}
          <img style="float: right;"title="{{mb_value object=$bilan field=_demi_journees}}" src="modules/ssr/images/dj-{{$bilan->_demi_journees}}.png" />
        {{/if}}
        {{mb_value object=$_sejour field=libelle}}
      </td>
      <td>{{mb_value object=$_sejour field=entree format=$conf.date}}</td>
      <td>{{mb_value object=$_sejour field=sortie format=$conf.date}}</td>

      <td style="text-align: right;">
        {{assign var=count_evenements value=$_sejour->_count_evenements_ssr_week}}
        {{$count_evenements|nozero}}
      </td>

      <td style="text-align: right;">
        {{assign var=count_evenements value=$_sejour->_count_evenements_ssr}}
        {{$count_evenements|nozero}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
