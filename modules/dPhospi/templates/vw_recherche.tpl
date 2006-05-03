<script language="JavaScript" type="text/javascript">
{literal}

function popPlanning(date) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_affectations");
  url.addParam("date", date);
  url.popup(700, 550, 'Planning');
}

function pageMain() {
  {/literal}
  regRedirectFlatCal("{$date_recherche}", "index.php?m={$m}&tab={$tab}&date_recherche=", null, true);
  {literal}
}

{/literal}
</script>

<table class="main">
  <tr>
    {if $typeVue}
    <td>
      <form name="chossePrat" action="?m={$m}" method="get">
      <input type="hidden" name="m" value="{$m}" />
      <select name="selPrat" onchange="submit()">
      <option value="0" {if $selPrat == 0}selected="selected"{/if}>&mdash; Selectionner un praticien &mdash;</option>
      {foreach from=$listPrat item=curr_prat}
        <option value="{$curr_prat->user_id}" {if $selPrat == $curr_prat->user_id}selected="selected"{/if}>
          {$curr_prat->_view}
        </option>
      {/foreach}
      </select>
      </form>
    </td>
    {else}
    <td class="Pane">
      <strong><a href="javascript:popPlanning('{$date_recherche}')">Etat des services</a></strong>
    </td>
    {/if}
    <td style="text-align: right;">
      <form name="typeVue" action="?m={$m}" method="get">
      <input type="hidden" name="m" value="{$m}" />
      <select name="typeVue" onchange="submit()">
        <option value="0" {if $typeVue == 0}selected="selected"{/if}>Afficher les lits disponible</option>
        <option value="1" {if $typeVue == 1}selected="selected"{/if}>Afficher les patients d'un chirurgien</option>
      </select>
      </form>
    </td>
  </tr>
  <tr>
    <td><div id="calendar-container"></div></td>
    <td class="greedyPane">
      <table class="tbl">
        {if $typeVue == 0}
        <tr>
          <th class="title" colspan="4">
            {$date_recherche|date_format:"%A %d %B %Y à %H h %M"} : {$libre|@count} lit(s) disponible(s)
          </th>
        </tr>
        <tr>
          <th>Service</th>
          <th>Chambre</th>
          <th>Lit</th>
          <th>Fin de disponibilité</th>
        </tr>
        {foreach from=$libre item=curr_lit}
        <tr>
          <td>{$curr_lit.service}</td>
          <td>{$curr_lit.chambre}</td>
          <td>{$curr_lit.lit}</td>
          <td>{$curr_lit.limite|date_format:"%A %d %B %Y à %H h %M"}
        </tr>
        {/foreach}
        {else}
        <tr>
          <th class="title" colspan="7">
            {if $selPrat}
              Dr. {$listPrat.$selPrat->_view} -
            {/if}
            {$date_recherche|date_format:"%A %d %B %Y"} : {$listAff|@count} patient(s)
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>CCAM</th>
          <th>Service</th>
          <th>Chambre</th>
          <th>Lit</th>
          <th>Entrée</th>
          <th>Sortie prévue</th>
        </tr>
        {foreach from=$listAff item=curr_aff}
        <tr>
          <td>{$curr_aff->_ref_operation->_ref_pat->_view}</td>
          <td class="text">
            {foreach from=$curr_aff->_ref_operation->_ext_codes_ccam item=curr_code}
            <strong>{$curr_code->code}</strong> : {$curr_code->libelleLong}
            <br />
            {/foreach}    
          </td>
          <td>{$curr_aff->_ref_lit->_ref_chambre->_ref_service->nom}</td>
          <td>{$curr_aff->_ref_lit->_ref_chambre->nom}</td>
          <td>{$curr_aff->_ref_lit->nom}</td>
          <td>{$curr_aff->entree|date_format:"%A %d %B %Y à %H h %M"}
          <td>{$curr_aff->sortie|date_format:"%A %d %B %Y à %H h %M"}
        </tr>
        {/foreach}
        {/if}
      </table>
    </td>
  </tr>
</table>