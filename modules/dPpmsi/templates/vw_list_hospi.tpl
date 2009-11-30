<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="8">
      Liste des {{$listSejours|@count}} personne(s) hospitalisée(s) au {{$date|date_format:$dPconfig.longdate}}
      
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=CSejour field=facture}}</th>
    <th>{{mb_title class=CSejour field=_num_dossier}}</th>
    <th>{{mb_label class=CSejour field=praticien_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>
    	{{mb_title class=CSejour field=_entree}} / 
			{{mb_title class=CSejour field=_sortie}}
		</th>
    <th>GHM</th>
    <th>Bornes</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
  {{assign var="GHM" value=$_sejour->_ref_GHM}}
  <tr>
    <td>
      {{if $_sejour->_ref_hprim_files|@count}}
       <img src="images/icons/tick.png" alt="ok" />
      {{else}}
      <img src="images/icons/cross.png" alt="alerte" />
      {{/if}}
    </td>
    <td>
      <strong onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
        [{{$_sejour->_num_dossier}}]
      </strong>
    </td>

    <td class="text">
    	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
    </td>

    <td class="text">
      {{assign var=patient value=$_sejour->_ref_patient}}
      <a href="?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={{$patient->_id}}" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        [{{$patient->_IPP}}]
        {{$patient}}
      </a>
    </td>

    <td>
    	{{mb_include module=system template=inc_interval_datetime from=$_sejour->_entree to=$_sejour->_sortie}}
    </td>
    
    <td class="text" {{if !$GHM->_CM}}style="background-color:#fdd"{{/if}}>
      {{$GHM->_GHM}}
      {{if $GHM->_DP}}: {{$GHM->_GHM_nom}}{{/if}}
    </td>
  
    <td class="text">
      {{if $GHM->_DP}}
        {{if $GHM->_borne_basse > $GHM->_duree}}
          <img src="images/icons/cross.png" alt="alerte" /> 
					Séjour trop court
        {{elseif $GHM->_borne_haute < $GHM->_duree}}
          <img src="images/icons/cross.png" alt="alerte" /> 
					Séjour trop long
        {{else}}
          <img src="images/icons/tick.png" alt="ok" />
        {{/if}}
      {{else}}
      -
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>