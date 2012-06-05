{{if $conf.dPhospi.systeme_prestations != $wanted}} 
<div class="small-warning">
La <a href="?m=hospi&tab=configure">configuration</a> du 
<strong>{{tr}}config-dPhospi-systeme_prestations{{/tr}} 
{{tr}}config-dPhospi-systeme_prestations-{{$conf.dPhospi.prestations}}{{/tr}}
</strong>
n'est pas compatible avec l'usage de ces prestations.  
</div>
{{/if}}
