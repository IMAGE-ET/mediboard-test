{{$msgSystem|smarty:nodefaults}}

{{if $demandeSynchro}}
  <button class="tick" onclick="synchrovalid();">Remplacer</button>
  <button class="cancel" onclick="recupvalue();">Récupérer</button>
{{elseif $del || $tmp_repas_id}}
  <script type='text/javascript'>
    synchro_repas('{{$object->affectation_id}}','{{$object->typerepas_id}}','{{$del}}','{{$object->repas_id}}');
  </script>
{{/if}}

{{if $callBack}}
  <script type='text/javascript'>
    {{$callBack}}({{$idValue}});
  </script>
{{/if}}