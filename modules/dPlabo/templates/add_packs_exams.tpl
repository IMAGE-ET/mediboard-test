<script type="text/javascript">

var Catalogue = {
  select : function(iCatalogue_id) {
    var url = new Url;
    url.setModuleAction("system", "httpreq_vw_complete_object");
    url.addParam("object_class" , "CCatalogueLabo");
    url.addParam("object_id"    , iCatalogue_id);
    url.requestUpdate('CatalogueView');
  }
}

function reloadPacks() {
    var url = new Url;
    url.setModuleAction("dPlabo", "httpreq_vw_packs");
    url.requestUpdate('PacksView');
}

function dragDropExamen(examen_id, pack_id) {
  oForm = $('newPackItem');
  oForm.examen_labo_id.value       = examen_id.substring(7);
  oForm.pack_examens_labo_id.value = pack_id;
  //Console.debug(oForm.examen_labo_id.value);
  //Console.debug(oForm.pack_examens_labo_id.value);
  submitFormAjax(oForm, 'systemMsg', { onComplete: reloadPacks });
  return true;
}

function pageMain() {
  PairEffect.initGroup('tree-content', {
    bStoreInCookie: false,
    bStartVisible: true
  } );
  reloadPacks();
}

</script>

<table class="main">
  <tr>
    <th class="halfPane">
      Catalogues
    </td>
    <th class="halfPane">
      Packs
    </td>
  </tr>
  <tr>
    <td>
      {{assign var="catalogue_id" value=0}}
      {{foreach from=$listCatalogues item="_catalogue"}}
      {{include file="tree_catalogues.tpl"}}
      {{/foreach}}
    </td>
    <td id="PacksView" rowspan="2">
    </td>
  </tr>
  <tr>
    <td id="CatalogueView">
    </td>
  </tr>
</table>