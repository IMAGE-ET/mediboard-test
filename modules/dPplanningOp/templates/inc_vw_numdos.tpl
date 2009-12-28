{{if @$hide_empty}}
  {{if $num_dossier}}[{{$num_dossier}}]{{/if}}
{{else}}
  [{{$num_dossier|default:"-"}}]
{{/if}}