{{assign var=class value=CConstantesMedicales}}

<div class="small-info">
  Les configurations de base sont à gauche, il est possible de définir des configurations par établissement,
  et de rédefinir chaque élément aussi pour chaque service.
</div>

<script type="text/javascript">
  Main.add(function(){
    Configuration.edit(
      'dPpatients',
      [
        'constantes / CService CGroups.group_id',
        'constantes / CFunctions CGroups.group_id',
        'constantes / CBlocOperatoire CGroups.group_id',
        'constantes / CGroups'
      ],
      'configuration-CConstantesMedicales'
    );
  });
</script>
<div id="configuration-CConstantesMedicales"></div>
