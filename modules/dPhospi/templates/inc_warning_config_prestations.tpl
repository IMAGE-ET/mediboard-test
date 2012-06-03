{{if $conf.dPhospi.prestations != $wanted}} 
<div class="small-warning">
La <a href="?m=hospi&tab=configure">configuration</a> du 
<strong>{{tr}}config-dPhospi-prestations{{/tr}} 
{{tr}}config-dPhospi-prestations-{{$conf.dPhospi.prestations}}{{/tr}}
</strong>
n'est pas compatible avec l'usage de ces prestations.  
</div>
{{/if}}
