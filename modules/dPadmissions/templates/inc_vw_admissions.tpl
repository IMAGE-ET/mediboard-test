<script type="text/javascript">

regRedirectPopupCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");

</script>

<table class="tbl">
  <tr>
    <th colspan="7">
      {$date|date_format:"%A %d %B %Y"}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
      <br /> 
      <em>
      {if $selAdmis == "n"}Admissions non effectuées
      {elseif $selSaisis == "n"}Dossiers non préparés
      {else}Toutes les admissions
      {/if}
      {if $selTri == "nom"}triées par nom
      {elseif $selTri == "heure"}triées par heure
      {/if}
      </em>
    </th>
  </tr>
  <tr>
    <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;selAdmis=0&amp;selTri=nom">Nom</a></th>
    <th>Chirurgien</th>
    <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;selAdmis=0&amp;selTri=heure">Heure</a></th>
    <th>Chambre</th>
    <th>Admis</th>
    <th>
      <form name="editAllAdmFrm" action="index.php" method="post">
      <input type="hidden" name="m" value="{$m}" />
      <input type="hidden" name="dosql" value="do_edit_admis" />
      <input type="hidden" name="id" value="{$date}" />
      <input type="hidden" name="mode" value="allsaisie" />
      <input type="hidden" name="value" value="o" />
      Saisis
      <button type="submit">
        <img src="modules/{$m}/images/tick.png" alt="Admis" />
      </button>
      </form>
    </th>
    <th>DH</th>
  </tr>
  {foreach from=$today item=curr_adm}
  <tr id="admission{$curr_adm->operation_id}">
  {include file="inc_vw_admission_line.tpl"}
  </tr>
  {/foreach}
</table>