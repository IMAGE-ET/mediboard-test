<script type="text/javascript">

var Catalogue = {
  select : function(iCatalogue) {
    if(isNaN(iCatalogue)) {
      iCatalogue = 0;
      oForm = document.editCatalogue;
      if(oForm) {
        iCatalogue = oForm.catalogue_labo_id.value;
      }
    }
    var urlCat  = new Url;
    var urlExam = new Url;
    urlCat.setModuleAction("dPlabo", "httpreq_vw_catalogues");
    urlExam.setModuleAction("dPlabo", "httpreq_vw_examens_catalogues");
    if(iCatalogue) {
      urlCat.addParam("catalogue_labo_id", iCatalogue);
      urlExam.addParam("catalogue_labo_id", iCatalogue);
    }
    urlCat.requestUpdate('CataloguesView', { waitingText : null });
    urlExam.requestUpdate('CataloguesExamensView', { waitingText : null });
  }
}

var Pack = {
  select : function(pack_id) {
    if(isNaN(pack_id)) {
      pack_id = 0;
      oForm = document.editPackItem;
      if(oForm) {
        pack_id = oForm.pack_examens_labo_id.value;
      }
    }
    var urlPack = new Url;
    var urlExam = new Url;
    urlPack.setModuleAction("dPlabo", "httpreq_vw_packs");
    urlExam.setModuleAction("dPlabo", "httpreq_vw_examens_packs");
    if(pack_id) {
      urlPack.addParam("pack_examens_labo_id", pack_id);
      urlExam.addParam("pack_examens_labo_id", pack_id);
    }
    urlPack.requestUpdate('PacksView', { waitingText : null });
    urlExam.requestUpdate('PacksExamensView', { waitingText : null });
  },
  dropExamen: function(sExamen_id, pack_id) {
    oFormBase = document.editPackItem;
    aExamen_id = sExamen_id.split("-");
    oFormBase.examen_labo_id.value       = aExamen_id[1];
    oFormBase.pack_examens_labo_id.value = pack_id;
    submitFormAjax(oFormBase, 'systemMsg', { onComplete: Pack.select });
    return true;
  },
  delExamen: function(oForm) {
    oFormBase = document.editPackItem;
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

var ElementManipulator = {
  SetViewportAvlHeight: function (sDivId, iPct) {
    var oDiv = $(sDivId);
    if (!oDiv) {
      return;
    }
    var fYDivPos   = 0;
    var fNavHeight = 0;
    var fDivHeight = 0;
  
    // Position Top de la div, hauteur de la fenetre,
    // puis calcul de la taille de la div
    fYDivPos   = Position.cumulativeOffset(oDiv)[1];
    fNavHeight = window.getInnerDimensions().y;
    fDivHeight = fNavHeight - fYDivPos;
    oDiv.style.overflow = "auto";
    oDiv.style.height = (fDivHeight * iPct) +"px";
  }
}

function pageMain() {
  Pack.select();
  Catalogue.select();
  ElementManipulator.SetViewportAvlHeight('PacksView', 0.5);
  ElementManipulator.SetViewportAvlHeight('PacksExamensView', 1);
  ElementManipulator.SetViewportAvlHeight('CataloguesView', 0.5);
  ElementManipulator.SetViewportAvlHeight('CataloguesExamensView', 1);
}

</script>

<table class="main">
  <tr>
    <th class="halfPane">
      Packs
    </th>
    <th class="halfPane">
      Catalogues
    </th>
  </tr>
  <tr>
    <td>
      <div id="PacksView"></div>
    </td>
    <td>
      <div id="CataloguesView"></div>
    </td>
  </tr>
  <tr>
    <td>
      <div id="PacksExamensView"></div>
    </td>
    <td>
      <div id="CataloguesExamensView"></div>
    </td>
  </tr>
</table>