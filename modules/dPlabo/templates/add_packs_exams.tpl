<script type="text/javascript">

var Catalogue = {
  iCurrent: 0,
  eHeader: null,
  findCurrent: function(iCatalogue) {
    this.iCurrent = iCatalogue;
    this.eHeader = $("catalogue-" + iCatalogue + "-header");
  },
  
  select : function(iCatalogue) {
    if (this.eHeader) {
      Element.classNames(this.eHeader).remove("selected");
    } 
    this.findCurrent(iCatalogue);
    Element.classNames(this.eHeader).add("selected");

    var url = new Url;
    url.setModuleAction("system", "httpreq_vw_complete_object");
    url.addParam("object_class" , "CCatalogueLabo");
    url.addParam("object_id"    , iCatalogue);
    url.requestUpdate('CatalogueView');
  }
}

function reloadPacks() {
    var url = new Url;
    url.setModuleAction("dPlabo", "httpreq_vw_packs");
    url.requestUpdate('PacksView');
}

function pageMain() {
  PairEffect.initGroup('tree-content');
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