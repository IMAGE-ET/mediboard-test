{{if $suppr}}
  <div class="small-success">{{$suppr}} patients supprimés</div>
{{/if}}
{{if $error}}
  <div class="small-error">{{$error}} patients non supprimés</div>
{{/if}}
<div class="small-info">{{$nb_patients}} patients dans la base</div>