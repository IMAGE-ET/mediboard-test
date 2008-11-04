<script type="text/javascript">
Main.add(function() {
	Calendar.regField("Filter", "_date_sortie");	
} );
</script>

<!-- Filter -->
<form name="Filter" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="do" value="0" />
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">Export de documents vers le e-Cap</th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_num_dossier}}</th>
    <td>{{mb_field object=$filter field=_num_dossier}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_date_sortie}}</th>
    <td class="date">{{mb_field object=$filter field=_date_sortie form=Filter}}</td>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="tick" type="submit" onclick="this.form.do.value = 'export';">
        Exporter les documents
      </button>
      <button class="tick" type="submit" onclick="this.form.do.value = 'test';">
        Tester le service web
      </button>
    </td>
  </tr>
</table>

</form>

{{if $do == "export"}}
{{include file=inc_list_export_documents.tpl}}
{{elseif $do == "test"}}

{{else}}
<div class="big-info">
  Il est nécessaire de valider l'export pour le réaliser.
  Merci de cliquer sur <strong>Exporter les documents</strong> après avoir choisi :
  <dl>
    <dt>soit une <em>date</em></dt>
    <dd>Pour exporter les dpcuments de tous les séjours ayant une sortie réelle ce jour.</dd>
    <dt>soit un <em>numéro de dossier</em></dt>
    <dd>Pour exporter les documents spécifiques à un séjour en particulier.</dd>
  </dl>
</div>
{{/if}}

