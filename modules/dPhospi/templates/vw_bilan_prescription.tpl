{{if @$modules.soins}}
<div class="small-warning">
Le module dédié aux soins est maintenant présent dans Mediboard.
Cet onglet y est déjà disponible et disparaitra prochainement du module <strong>Hospitalisation</strong>.
Pour l'utiliser, veuillez d'ores et déjà aller dans le module nommé <strong>Soins</strong>
</div>
{{/if}}
{{include file="../../soins/templates/vw_bilan_prescription.tpl"}}