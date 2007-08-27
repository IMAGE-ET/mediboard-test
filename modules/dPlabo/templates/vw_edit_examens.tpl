{{* $Id$ *}}

<script type="text/javascript">

var Analyse = {
  createSibling: function(oForm) {
    if (!checkForm(oForm)) {
      return false;
    }
    
    var oEditForm = document.editExamen;
    Console.debug(Form.toObject(oForm), "Sibling Form");
    Console.debug(Form.toObject(oEditForm), "Edit Form");
    oEditForm.examen_labo_id.value = "";
    oEditForm.catalogue_labo_id.value = oForm.catalogue_labo_id.value;
    oEditForm.submit();
    
    return false;
  }
}

function pageMain() {
  regFieldCalendar('editExamen', 'deb_application');
  regFieldCalendar('editExamen', 'fin_application');

  var oAccord = new Rico.Accordion($('accordionExamen'), { 
    panelHeight: 320,
    showDelay: 50, 
    showSteps: 3 
  } );
}
</script>

<table class="main">
  <tr>
    <td style="width: 320px;">
    
      <!-- S�lection du catalogue -->
      <form name="selectCatalogue" action="index.php" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="catalogue_labo_id" title="Selectionner le catalogue que vous d�sirez afficher">
        Catalogue courant
      </label>
      <select name="catalogue_labo_id" onchange="this.form.submit()">
        <option value="0">&mdash; Choisir un catalogue</option>
        {{assign var="selected_id" value=$catalogue->_id}}
        {{assign var="exclude_id" value=""}}
        {{foreach from=$listCatalogues item="_catalogue"}}
        {{include file="options_catalogues.tpl"}}
        {{/foreach}}
      </select>

      </form>
      
      <!-- Liste des analyses pour le catalogue courant -->
      {{assign var="examens" value=$catalogue->_ref_examens_labo}}
      {{assign var="examen_id" value=$examen->_id}}
      {{include file="list_examens.tpl"}}
    </td>
    
    <td>
      
      <!-- Edition de l'analyse s�lectionn� -->
      {{if $can->edit}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id=0">
        Ajouter une nouvelle analyse
      </a>
      
      <form name="editExamen" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      
      <input type="hidden" name="dosql" value="do_examen_aed" />
      <input type="hidden" name="examen_labo_id" value="{{$examen->_id}}" />
      <input type="hidden" name="_locked" value="{{$examen->_locked}}" />
      <input type="hidden" name="del" value="0" />
      
      <table class="form">
        <tr>
          {{if $examen->_id}}
          <th class="title modify" colspan="2">
            <div class="idsante400" id="{{$examen->_class_name}}-{{$examen->_id}}" ></div>
            <a style="float:right;" href="#nothing" onclick="view_log('{{$examen->_class_name}}', {{$examen->_id}})">
              <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
            </a>
            Modification de l'examen {{$examen->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un examen</th>
          {{/if}}
        </tr>
      </table>

      <div class="accordionMain" id="accordionExamen">
      
        <div id="acc_infos">
          <div  class="accordionTabTitleBar" id="IdentiteHeader">
            {{tr}}mod-dPlabo-inc-acc_infos{{/tr}}
          </div>
          <div class="accordionTabContentBox" id="IdentiteContent"  >
          {{include file="inc_examen/acc_infos.tpl"}}
          </div>
        </div>
        
        <div id="acc_realisation">
          <div  class="accordionTabTitleBar" id="IdentiteHeader">
            {{tr}}mod-dPlabo-inc-acc_realisation{{/tr}}
          </div>
          <div class="accordionTabContentBox" id="IdentiteContent"  >
          {{include file="inc_examen/acc_realisation.tpl"}}
          </div>
        </div>
        
        <div id="acc_conservation">
          <div  class="accordionTabTitleBar" id="IdentiteHeader">
            {{tr}}mod-dPlabo-inc-acc_conservation{{/tr}}
          </div>
          <div class="accordionTabContentBox" id="IdentiteContent"  >
          {{include file="inc_examen/acc_conservation.tpl"}}
          </div>
        </div>
        
      </div>

      
            
      <table class="form">
        <tr>
          <td class="button" colspan="2" id="button">
            <button class="submit" type="submit">Valider</button>
            {{if $examen->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l \'examen',objName:'{{$examen->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      
      </form>
      {{/if}}
      
      <!-- Liste des packs associ�s -->
      {{if $examen->_id}}
      <table class="tbl">
        <tr>
          <th class="title">Packs d'analyses associ�s</th>
        </tr>
        <tr>
          <th>Nom du pack</th>
        </tr>
        {{foreach from=$examen->_ref_packs_labo item=_pack}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_packs&amp;pack_examens_labo_id={{$_pack->_id}}">
              {{$_pack->_view}}
            </a>
          </td>
        </tr>
        {{foreachelse}}
        <tr><td><em>Analyse pr�sente dans aucun pack</em></td></tr>
        {{/foreach}}
      </table>
      {{/if}}

      <!-- Equivalents dans d'autres catalogues -->
      {{if $examen->_id}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="10">Equivalents dans d'autres catalogues</th>
        </tr>

        <tr>
          <td colspan="2">
            
            <form name="createSibling" action="#nowhere" method="get" onsubmit="return Analyse.createSibling(this)">
              
              <label for="catalogue_labo_id" title="Choisir un catalogue pour cr�er un �quivalent">
                Cr�er un �quivalent dans</label>
              <select class="notNull ref class|CCatalogueLabo" name="catalogue_labo_id">
                <option value="">&mdash; Choisir un catalogue</option>
                {{assign var="selected_id" value=$examen->catalogue_labo_id}}
                {{assign var="exclude_id" value=$examen->_ref_root_catalogue->_id}}
                {{foreach from=$listCatalogues item="_catalogue"}}
                {{include file="options_catalogues.tpl"}}
                {{/foreach}}
              </select>
              <button class="new">Cr�er</button>
            </form>
  
          <td>
        </tr>
        
        <tr>
          <th>Analyse</th>
          <th>Catalogue</th>
        </tr>
        {{foreach from=$examen->_ref_siblings item=_sibling}}
        <tr>
          <td>
            <a href="?m=dPlabo&amp;tab=vw_edit_examens&amp;examen_labo_id={{$_sibling->_id}}">
              {{$_sibling->_view}}
            </a>
          </td>
          <td>
            {{foreach from=$_sibling->_ref_catalogues item=_catalogue}}
            <strong>{{tr}}CExamen-catalogue-{{$_catalogue->_level}}{{/tr}} :</strong>
            {{$_catalogue->_view}}
            <br />
            {{/foreach}}
          </td>
        </tr>
        {{foreachelse}}
        <tr><td colspan="2"><em>Absent des autres catalogues</em></td></tr>
        {{/foreach}}
      </table>
      {{/if}}

    </td>
  </tr>
</table>