{{if !"dPfacturation"|module_active}}
<div class="small-warning">
  Veuiller activer le module <strong>Facturation</strong> pour utiliser les factures
</div>
{{else}}
  {{mb_include module=facturation template=inc_vw_facturation}}
{{/if}}