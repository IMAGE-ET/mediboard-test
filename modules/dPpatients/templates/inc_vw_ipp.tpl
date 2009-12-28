{{if @$hide_empty}}
  {{if $ipp}}[{{$ipp}}]{{/if}}
{{else}}
  [{{$ipp|default:"-"}}]
{{/if}}