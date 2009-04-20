{{if $etat == "vide" || $etat == "ko"}}
  <div class='small-warning'>
  {{if $etat == "vide"}}Configurations absentes de la mémoire partagé.{{/if}}
  {{if $etat == "ko"}}Configurations pas à jour.{{/if}}
    <button class="tick" type="button" onclick="checkSHM('{{$name}}','create');">Mettre à jour</button>
  </div>
{{/if}}

{{if $etat == "ok"}}
<div class='small-info'>Configurations à jour.</div>
{{/if}}