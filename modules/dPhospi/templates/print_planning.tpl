<!-- $Id$ -->

<script type="text/javascript">

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, 'Admissions');
}

</script>

<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Planning du {{$filter->_date_min|date_format:"%A %d %b %Y à %Hh%M"}}
        au {{$filter->_date_max|date_format:"%A %d %B %Y à %Hh%M"}} 
        : {{$total}} séjour(s)
        <br />
        Filtrés sur : {{mb_label class=CSejour field=$filter->_horodatage}}
      </a>
    </th>
  </tr>
  {{foreach from=$listDays key=key_day item=curr_day}}
  {{foreach from=$curr_day key=key_prat item=curr_prat}}
  {{assign var="praticien" value=$curr_prat.praticien}}
  <tr>
    <td>
      <strong>
        {{$key_day|date_format:"%a %d %b %Y"}} 
        &mdash; Dr {{$praticien->_view}} : 
        {{mb_label class=CSejour field=$filter->_horodatage}}
        x {{$curr_prat.sejours|@count}} 
    </td>
  </tr>
  <tr>
    <td>
	    <table class="tbl">
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
          <td>{{$curr_sejour->$horodatage|date_format:$dPconfig.time}}</td>
          <td>
            {{if !$curr_sejour->facturable}}
            <strong>NF</strong>
            {{/if}}
            
            {{$curr_sejour->type|truncate:1:""|capitalize}}
          </td>
          <td>{{$curr_sejour->_duree_prevue}} j</td>
          <td class="text">{{$curr_sejour->convalescence|nl2br}}</td>
          <td class="text">
            {{assign var="affectation" value=$curr_sejour->_ref_first_affectation}}
            {{if $affectation->affectation_id}}
              {{$affectation->_ref_lit->_view}}
            {{else}}
              Non placé
            {{/if}}
            ({{tr}}chambre_seule.{{$curr_sejour->chambre_seule}}{{/tr}})
          </td>
          <td class="text">{{$curr_sejour->rques}}</td>
          <td>
            {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
              {{$curr_operation->_datetime|date_format:"%d/%m/%Y"}}
              {{if $curr_operation->time_operation != "00:00:00"}}
                à {{$curr_operation->time_operation|date_format:$dPconfig.time}}
              {{/if}}
              <br />
            {{/foreach}}
          </td>
          <td class="text">
            {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
              <ul style="padding-left: 0px;">
              {{if $curr_operation->libelle}}
                <em>[{{$curr_operation->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
                <em>{{$curr_code->code}}</em>
                {{if $filter->_ccam_libelle}}
                  : {{$curr_code->libelleLong|truncate:60:"...":false}}
                  <br/>
                {{else}}
                  ;
                {{/if}}
              {{/foreach}}
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
          <td>
            <a href="#" onclick="printAdmission({{$curr_sejour->_id}})">
              {{$patient->_view}}
            </a>
          </td>
          <td>
            <a href="#" onclick="printAdmission({{$curr_sejour->_id}})">
              {{mb_value object=$patient field="naissance"}} ({{$patient->_age}} ans)
            </a>
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
            <a href="#" onclick="printAdmission({{$curr_sejour->sejour_id}})">
              {{$patient->rques}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>