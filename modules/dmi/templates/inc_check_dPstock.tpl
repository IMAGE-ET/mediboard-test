{{* $Id: $ *}}
{{*{{assign var=dPstock value=$modules.dPstock}}*}}
{{*{{if $dPstock->mod_active}}*}}
{{if @$modules.dPstock->mod_active == 0}}
<div class="big-warning">
  Attention le module dPstock n'est pas installé
</div>
{{/if}}