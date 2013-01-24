{{assign var=class value=CConstantesMedicales}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />

  <tr>
    {{mb_include module=system template=inc_config_enum var=unite_ta values=cmHg|mmHg}}
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

  <tr>
    <th class="category" colspan="2">
    	Configurations par service / établissement
    </th>
  </tr>
</table>

</form>

<div class="small-info">
	Les configurations de base sont à gauche, il est possible de définir des configurations par établissement,
	et de rédefinir chaque élément aussi pour chaque service.
</div>

<script type="text/javascript">
  Main.add(function(){
    Configuration.edit('dPpatients', 'CService CGroups.group_id', 'configuration-CConstantesMedicales');
  });
</script>
<div id="configuration-CConstantesMedicales"></div>
