<!-- $Id: print_planning.tpl,v 1.16 2006/05/03 13:22:47 mytto Exp $ -->

{literal}
<script type="text/javascript">
//<![CDATA[

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, 'Admissions');
}

//]]>
</script>
{/literal}

<table class="main">
  <tr>
    <th>
      <a href="javascript:window.print()">
        Planning du {$deb|date_format:"%A %d %b %Y à %Hh%M"}
        au {$fin|date_format:"%A %d %B %Y à %Hh%M"} ({$total} admissions)
      </a>
    </th>
  </tr>
  {foreach from=$listDays item=curr_day}
  {foreach from=$curr_day.listChirs item=curr_chir}
  {if $curr_chir.admissions}
  <tr>
    <td>
      <strong>
        {$curr_day.date_adm|date_format:"%a %d %b %Y"} 
        &mdash; Dr. {$curr_chir.user_last_name} {$curr_chir.user_first_name} : {$curr_chir.admissions|@count} admission(s)
      </strong>
    </td>
  </tr>
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th colspan="7"><strong>Admission</strong></th>
		  <th colspan="4"><strong>Intervention</strong></th>
		  <th colspan="3"><strong>Patient</strong></th>
		</tr>
		<tr>
		  <th>Heure</th>
		  <th>Type</th>
		  <th>Durée</th>
      <th>Bilan</th>
      <th>Conv.</th>
		  <th>Chambre</th>
		  <th>Remarques</th>
		  <th>Date</th>
		  <th>Heure</th>
		  <th>Dénomination</th>
		  <th>Côté</th>
		  <th>Nom / Prenom</th>
		  <th>Naissance (Age)</th>
		  <th>Remarques</th>
		</tr>
		{foreach from=$curr_chir.admissions item=curr_adm}
		<tr>
		  <td>{$curr_adm->_hour_adm}h{$curr_adm->_min_adm}</td>
		  <td>{$curr_adm->type_adm|truncate:1:""|capitalize}</td>
      <td>{$curr_adm->duree_hospi} j</td>
      <td class="text">{$curr_adm->examen|nl2br}</td>
      <td class="text">{$curr_adm->convalescence|nl2br}</td>
      <td class="text">
        {assign var="affectation" value=$curr_adm->_ref_first_affectation}
        {if $affectation->affectation_id}
        {$affectation->_ref_lit->_ref_chambre->_ref_service->nom}
        - {$affectation->_ref_lit->_ref_chambre->nom}
        - {$affectation->_ref_lit->nom}
        {else}
        Non placé
        {/if}
        ({$curr_adm->chambre})
      </td>
      <td class="text">{$curr_adm->rques}</td>
      <td>{$curr_adm->_ref_plageop->date|date_format:"%d/%m/%Y"}</td>
      <td>
        {if $curr_adm->time_operation != "00:00:00"}
        {$curr_adm->time_operation|truncate:5:""}
        {/if}
      </td>
      
      <td class="text">
      {foreach from=$curr_adm->_ext_codes_ccam item=curr_code}
      {$curr_code->libelleLong|truncate:80:"...":false} <em>({$curr_code->code})</em>
      {/foreach}
      </td>
      <td>{$curr_adm->cote|truncate:1:""|capitalize}</td>
      <td>
        <a href="javascript:printAdmission({$curr_adm->operation_id})">{$curr_adm->_ref_pat->_view}</a>
      </td>
      <td>
        <a href="javascript:printAdmission({$curr_adm->operation_id})">{$curr_adm->_ref_pat->_naissance} ({$curr_adm->_ref_pat->_age})</a>
      </td>
      <td class="text">
        <a href="javascript:printAdmission({$curr_adm->operation_id})">{$curr_adm->_ref_pat->rques}</a>
      </td>
		</tr>
		{/foreach}
	  </table>
	</td>
  </tr>
  {/if}
  {/foreach}
  {/foreach}
</table>