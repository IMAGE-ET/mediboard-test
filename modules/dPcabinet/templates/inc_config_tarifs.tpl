<form name="editConfig-tarifs" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  
  <table class="form">
    <tr>
      <th class="category" colspan="2">{{tr}}CTarif{{/tr}}</th>
    </tr>
    
    {{assign var="class" value="Tarifs"}}
    {{mb_include module=system template=inc_config_bool var=show_tarifs_etab}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<form name="recalculTarifs" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_tarif_aed" />
  <input type="hidden" name="reloadAlltarifs" value="1" />
  <table class="form">
  <tr>
    <td class="button" colspan="2">
      <button class="reboot" type="submit">Recalculer l'ensemble des tarifs</button>
    </td>
  </tr>
  </table>
</form>

<form name="modifTaux2014" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_tarif_aed" />
  <input type="hidden" name="modifTauxVingPct" value="1" />
  <table class="form">
  <tr>
    <td class="button" colspan="2">
      <button class="change" type="submit">Passer tous les tarifs ayant un taux de TVA de 19,6% à 20%</button>
    </td>
  </tr>
  </table>
</form>
