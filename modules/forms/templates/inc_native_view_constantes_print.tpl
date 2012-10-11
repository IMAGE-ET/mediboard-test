<style type="text/css">
table.tbl.constantes td {
  white-space: nowrap;
}
</style>

{{assign var=cst value=$object->loadListConstantesMedicales()}}
{{assign var=grid value="CConstantesMedicales::buildGrid"|static_call:$object->_list_constantes_medicales:false}}

{{mb_include module=patients template=print_constantes constantes_medicales_grid=$grid}}
