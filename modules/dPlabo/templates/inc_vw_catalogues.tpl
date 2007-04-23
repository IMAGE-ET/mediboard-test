<script type="text/javascript">

PairEffect.initGroup('tree-content');
  
</script>

<form name="editCatalogue" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_catalogue_aed" />
  <input type="hidden" name="catalogue_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>

{{assign var="catalogue_id" value=$catalogue->_id}}
{{foreach from=$listCatalogues item="_catalogue"}}
{{include file="tree_catalogues.tpl"}}
{{/foreach}}