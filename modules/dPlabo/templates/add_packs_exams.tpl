<script type="text/javascript">

var Catalogue = {
  select : function(iCatalogue) {
    if(isNaN(iCatalogue)) {
      iCatalogue = 0;
      oForm = $('currCatalogue');
      if(oForm) {
        iCatalogue = oForm.catalogue_labo_id.value;
      }
    }
    var url = new Url;
    url.setModuleAction("dPlabo", "httpreq_vw_catalogues");
    if(iCatalogue) {
      url.addParam("catalogue_labo_id", iCatalogue);
    }
    url.requestUpdate('CataloguesView', { waitingText : null });
  }
}

var Pack = {
  select : function reloadPacks(pack_id) {
    if(isNaN(pack_id)) {
      pack_id = 0;
      oForm = $('newPackItem');
      if(oForm) {
        pack_id = oForm.pack_examens_labo_id.value;
      }
    }
    var url = new Url;
    url.setModuleAction("dPlabo", "httpreq_vw_packs");
    if(pack_id) {
      url.addParam("pack_examens_labo_id", pack_id);
    }
    url.requestUpdate('PacksView', { waitingText : null });
  },
  dropExamen: function(sExamen_id, pack_id) {
    oFormBase = $('newPackItem');
    aExamen_id = sExamen_id.split("-");
    oFormBase.examen_labo_id.value       = aExamen_id[1];
    oFormBase.pack_examens_labo_id.value = pack_id;
    submitFormAjax(oFormBase, 'systemMsg', { onComplete: Pack.select });
    return true;
  },
  delExamen: function(oForm) {
    oFormBase = $('newPackItem');
    oFormBase.pack_examens_labo_id.value = oForm.pack_examens_labo_id.value;
    submitFormAjax(oForm, 'systemMsg', { onComplete: Pack.select });
    return true;
  }
}

var oDragOptions = {
  revert: true,
  ghosting: true,
  starteffect : function(element) { 
    Element.classNames(element).add("dragged");
    new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 }); 
  },
  reverteffect: function(element, top_offset, left_offset) {
    var dur = Math.sqrt(Math.abs(top_offset^2)+Math.abs(left_offset^2))*0.02;
    element._revert = new Effect.Move(element, { 
      x: -left_offset, 
      y: -top_offset, 
      duration: dur,
      afterFinish : function (effect) { 
        Element.classNames(effect.element.id).remove("dragged");
      }
    } );
  },
  endeffect: function(element) { 
    new Effect.Opacity(element, { duration:0.2, from:0.7, to:1.0 } ); 
  }       
}

function pageMain() {
  Catalogue.select();
  Pack.select();
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
    <td id="CataloguesView">
    </td>
    <td id="PacksView">
    </td>
  </tr>
</table>