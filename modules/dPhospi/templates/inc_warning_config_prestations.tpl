{{if "dPhospi prestations systeme_prestations"|conf:"CGroups-$g" != $wanted}}
<div class="small-warning">
La <a href="?m=hospi&tab=configure">configuration</a> du 
<strong>{{tr}}config-dPhospi-prestations-systeme_prestations{{/tr}}
{{tr}}config-dPhospi-prestations-systeme_prestations-{{"dPhospi prestations systeme_prestations"|conf:"CGroups-$g"}}{{/tr}}
</strong>
n'est pas compatible avec l'usage de ces prestations.  
</div>
{{/if}}
