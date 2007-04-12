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
    url.requestUpdate('CatalogueView', { waitingText : null });
  }
}

function reloadPacks(pack_id) {
    if(isNaN(pack_id)) {
      oForm = $('newPackItem');
      if(oForm) {
        pack_id = oForm.pack_examens_labo_id.value;
      }
    }
    var url = new Url;
    url.setModuleAction("dPlabo", "httpreq_vw_packs");
    url.addParam("pack_examens_labo_id", pack_id);
    url.requestUpdate('PacksView', { waitingText : null });
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
    </th>
    <th class="halfPane">
      Packs
    </th>
  </tr>
  <tr>
    <td>
      {{assign var="catalogue_id" value=0}}
      {{foreach from=$listCatalogues item="_catalogue"}}
      {{include file="tree_catalogues.tpl"}}
      {{/foreach}}
      <div id="CatalogueView">
      </div>
    </td>
    <td id="PacksView">
    </td>
  </tr>
</table>