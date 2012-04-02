<!-- $Id$ -->

<table class="tbl">
  <tr class="clear">
    <th colspan="16">
      <h1>
        <a href="#" onclick="window.print()">
          Planning du {{$filter->_date_min|date_format:$conf.datetime}}
          au {{$filter->_date_max|date_format:$conf.datetime}} 
          : {{$total}} séjour(s)
          <br />
          Filtrés sur : {{mb_label class=CSejour field=$filter->_horodatage}}
        </a>
      </h1>
    </th>
  </tr>
  {{foreach from=$listDays key=key_day item=curr_day}}
  {{foreach from=$curr_day key=key_prat item=curr_prat}}
  {{assign var="praticien" value=$curr_prat.praticien}}
  <tr class="clear">
    <td colspan="16">
      <h2>
        <strong>
          {{$key_day|date_format:"%a %d %b %Y"}} 
          - Dr {{$praticien->_view}}
        </strong>
        - {{mb_label class=CSejour field=$filter->_horodatage}}
        x {{$curr_prat.sejours|@count}}
      </h2> 
    </td>
  </tr>
  <tr>
    <th colspan="6"><strong>Séjour</strong></th>
    <th colspan="5"><strong>Intervention(s)</strong></th>
    <th colspan="5"><strong>Patient</strong></th>
  </tr>
  <tr>
    <th>{{mb_title class=CSejour field=$filter->_horodatage}}</th>
    <th>Type</th>
    <th>Dur.</th>
    <th>Conv.</th>
    <th>Chambre</th>
    <th>Remarques</th>
    <th>Date</th>
    <th>Dénomination</th>
    <th>Côté</th>
    <th>Bilan</th>
    <th>Remarques</th>
    <th>Nom / Prenom</th>
    <th>Naissance (Age)</th>
    {{if $filter->_coordonnees}}
    <th>Adresse</th>
    <th>Tel</th>
    {{/if}}
    <th>Remarques</th>
  </tr>
  {{assign var=horodatage value=$filter->_horodatage}}
  {{foreach from=$curr_prat.sejours item=curr_sejour}}
  <tr>
    <td>{{$curr_sejour->$horodatage|date_format:$conf.time}}</td>
    <td>
      {{if !$curr_sejour->facturable}}
      <strong>NF</strong>
      {{/if}}
      
      {{$curr_sejour->type|truncate:1:""|capitalize}}
    </td>
    <td>{{$curr_sejour->_duree_prevue}} j</td>
    <td class="text">{{$curr_sejour->convalescence|nl2br}}</td>
    <td class="text">
      {{mb_include module=hospi template=inc_placement_sejour sejour=$curr_sejour}}
      
      ({{tr}}chambre_seule.{{$curr_sejour->chambre_seule}}{{/tr}})
    </td>
    <td class="text">{{$curr_sejour->rques}}</td>
    <td class="text">
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
        {{$curr_operation->_datetime|date_format:"%d/%m/%Y"}}
        {{if $curr_operation->time_operation != "00:00:00"}}
          à {{$curr_operation->time_operation|date_format:$conf.time}}
        {{/if}}
        <br />
      {{/foreach}}
    </td>
    <td class="text">
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
        <ul style="padding-left: 0px;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_operation->_guid}}');">
        {{if $curr_operation->libelle}}
          <em>[{{$curr_operation->libelle}}]</em>
          <br />
        {{else}}
        {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
          <em>{{$curr_code->code}}</em>
          {{if $filter->_ccam_libelle}}
            : {{$curr_code->libelleLong|truncate:60:"...":false}}
            <br/>
          {{else}}
            ;
          {{/if}}
        </span>
        {{/foreach}}
        {{/if}}
        </ul>
      {{/foreach}}
    </td>
    <td>
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
        {{$curr_operation->cote|truncate:1:""|capitalize}}
        <br />
      {{/foreach}}
    </td>
    <td class="text">
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
        {{$curr_operation->examen|nl2br}}
        <br />
      {{/foreach}}
    </td>
    <td class="text">
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
        {{$curr_operation->rques|nl2br}}
        <br />
      {{/foreach}}
    </td>
    
    {{assign var=patient value=$curr_sejour->_ref_patient}}
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
        {{$patient->_view}}
      </span>
    </td>
    <td class="text">
      {{mb_value object=$patient field="naissance"}} ({{$patient->_age}} ans)
    </td>
    
    {{if $filter->_coordonnees}}
    <td>
    	{{mb_value object=$patient field=adresse}}
    	<br />
    	{{mb_value object=$patient field=cp}} 
    	{{mb_value object=$patient field=ville}}
    </td>
    <td>
      {{mb_value object=$patient field=tel}}
      <br />
      {{mb_value object=$patient field=tel2}}
    </td>
    {{/if}}
    
    <td class="text">
      {{$patient->rques}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</table>