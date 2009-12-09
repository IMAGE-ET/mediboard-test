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
    urlCat.requestUpdate('CataloguesView');
    urlExam.requestUpdate('CataloguesExamensView');
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
    urlPack.requestUpdate('PacksView');
    urlExam.requestUpdate('PacksExamensView');
  },
  dropExamenCat: function(sExamen_id, pack_id) {
    oFormBase = document.editPackItem;
    aExamen_id = sExamen_id.split("-");
    if(aExamen_id[0] == "examenCat") {
      oFormBase.examen_labo_id.value       = aExamen_id[1];
      oFormBase.pack_examens_labo_id.value = pack_id;
      submitFormAjax(oFormBase, 'systemMsg', { onComplete: Pack.select });
      return true;
    } else {
      return false;
    }
  },
  dropExamen: function(sExamen_id, pack_id) {
    oFormBase = document.editPackItem;
    aExamen_id = sExamen_id.split("-");
    if(aExamen_id[0] == "examenPack" || aExamen_id[0] == "examenCat") {
      oFormBase.examen_labo_id.value       = aExamen_id[1];
      oFormBase.pack_examens_labo_id.value = pack_id;
      submitFormAjax(oFormBase, 'systemMsg', { onComplete: Pack.select });
      return true;
    } else {
      return false;
    }
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


Main.add(function() {
  Pack.select();
  Catalogue.select();
  ViewPort.SetAvlHeight('PacksView'            , 0.4);
  ViewPort.SetAvlHeight('PacksExamensView'     , 1);
  ViewPort.SetAvlHeight('CataloguesView'       , 0.4);
  ViewPort.SetAvlHeight('CataloguesExamensView', 1);

  // Debugage du scroll de la div de la liste des prescriptions
  Position.includeScrollOffsets = true;
  Event.observe('PacksView', 'scroll', function(event) { Position.prepare(); });
  
  // Pour éviter de dropper en dessous du tableau de la liste des analyses
  Droppables.add('viewport-PacksExamensView', oDragOptions );
});

// Recherche des analyses
function search(){
  var oForm = document.frmRecherche;
  var url = new Url();
  url.setModuleAction("dPlabo", "httpreq_search_exam");
  url.addParam("recherche", oForm.search.value);
  url.requestUpdate("CataloguesExamensView");
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
  <tbody class="viewported">
  <tr>
    <td class="viewport" id="viewport-PacksView">
      <div id="PacksView"></div>
    </td>
    <td class="viewport">
      <div id="CataloguesView"></div>
    </td>
  </tr>
  <tr>
    <td class="viewport" id="viewport-PacksExamensView">
      <div id="PacksExamensView"></div>
    </td>
    <td class="viewport">
      <div id="CataloguesExamensView"></div>
    </td>
  </tr>
  </tbody>
</table>