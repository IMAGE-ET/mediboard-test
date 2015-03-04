{{* $id: $
  * @param $object CMbObject Target Object for documents
  * @param $modelesByOwner array|CCompteRendu sorted by owner
  * @param $packs array|CPack  List of packs
  * @param $praticien CMediuser Owner of modeles
  *}}

{{mb_script module=dPhospi script=modele_etiquette ajax=true}}  
{{assign var=object_class value=$object->_class}}
{{assign var=object_id value=$object->_id}}
{{unique_id var=unique_id}}

{{if $nb_modeles_etiquettes}}
  <form name="download_etiq_{{$object->_class}}_{{$object->_id}}_{{$unique_id}}" style="display: none;" action="?" target="_blank" method="get" class="prepared">
    <input type="hidden" name="m" value="dPhospi" />
    <input type="hidden" name="a" value="print_etiquettes" />
    <input type="hidden" name="object_class" value="{{$object->_class}}" />
    <input type="hidden" name="object_id" value="{{$object->_id}}" />
    <input type="hidden" name="modele_etiquette_id" />
    <input type="hidden" name="suppressHeaders" value="1" />
    <input type="hidden" name="dialog" value="1" />
  </form>
{{/if}}

{{if $can_create_docs}}
  <form name="unmergePack_{{$object->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="compteRendu" />
    <input type="hidden" name="dosql" value="do_pack_multi_aed" />
    <input type="hidden" name="pack_id" value="" />
    <input type="hidden" name="object_class" value="{{$object->_class}}" />
    <input type="hidden" name="object_id" value="{{$object->_id}}" />
    <input type="hidden" name="callback" value="Document.afterUnmerge" />
  </form>

  <script>
    Main.add(function() {
      ObjectTooltip.modes.locker = {
        module: "compteRendu",
        action: "ajax_show_locker",
        sClass: "tooltip"
      };

      var form = getForm('DocumentAdd-{{$unique_id}}-{{$object->_guid}}');
      var url;

      url = new Url("dPcompteRendu", "ajax_modele_autocomplete");
      url.addParam("user_id", "{{$praticien->_id}}");
      url.addParam("function_id", "{{$praticien->function_id}}");
      url.addParam("object_class", '{{$object_class}}');
      url.addParam("object_id", '{{$object_id}}');
      url.autoComplete(form.keywords_modele, '', {
        minChars: 2,
        afterUpdateElement: createDoc,
        dropdown: true,
        width: "250px"
      });

      url = new Url("dPcompteRendu", "ajax_pack_autocomplete");
      url.addParam("user_id", "{{$praticien->_id}}");
      url.addParam("function_id", "{{$praticien->function_id}}");
      url.addParam("object_class", '{{$object_class}}');
      url.addParam("object_id", '{{$object_id}}');
      url.autoComplete(form.keywords_pack, '', {
        minChars: 2,
        afterUpdateElement: createPack,
        dropdown: true,
        width: "250px"
      });

      ModeleEtiquette.nb_printers  = {{$nb_printers|@json}};

      function createDoc(input, selected) {
        $V(input, '');
        var id = selected.down(".id").innerHTML;
        var object_class = null;

        if (id == 0) {
          object_class = '{{$object->_class}}';
        }

        if (selected.select(".fast_edit").length) {
          Document.fastMode('{{$object_class}}', id, '{{$object_id}}', '{{$unique_id}}');
        } else {
          Document.create(id, '{{$object_id}}', null, object_class, null);
        }
      }

      function createPack(input, selected) {
        $V(input, '');
        if (selected.select(".fast_edit").length) {
          Document.fastModePack(selected.down(".id").innerHTML, '{{$object_id}}', '{{$object_class}}', '{{$unique_id}}', selected.select(".merge_docs").length ? selected.get("modeles_ids") : null);
        }
        else if (selected.select(".merge_docs").length){
          var form = getForm("unmergePack_{{$object->_guid}}");
          $V(form.pack_id, selected.down(".id").innerHTML);
          form.onsubmit();
        }
        else {
          Document.createPack(selected.down(".id").innerHTML, '{{$object_id}}');
        }
      }
    });
  
    //Création via ModeleSelector
    modeleSelector[{{$object_id}}] = new ModeleSelector("DocumentAdd-{{$unique_id}}-{{$object->_guid}}", null, "_modele_id", "_object_id", "_fast_edit");
  </script>
  
  {{if $can->admin}}
    <form name="DeleteAll-{{$object->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcompteRendu" />
      <input type="hidden" name="dosql" value="do_compte_rendu_multi_delete" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="object_guid" value="{{$object->_guid}}">
       
      <button class="trash" type="button" style="float: right;" onclick="Document.removeAll(this, '{{$object->_guid}}')">
        {{tr}}Delete-all{{/tr}}
      </button>
    </form>
  {{/if}}
  
  <form name="DocumentAdd-{{$unique_id}}-{{$object->_guid}}" action="?m={{$m}}" method="post" class="prepared">
    {{if $can_create_docs}}
      <input type="text" value="&mdash; Modèle" name="keywords_modele" class="autocomplete str" autocomplete="off" onclick="this.value = ''; this.onclick=null;" style="width: 5em;" />
      <input type="text" value="&mdash; Pack" name="keywords_pack" class="autocomplete str" autocomplete="off" onclick="this.value = ''; this.onclick=null;" style="width: 4em;"/>
    
  
      <button type="button" class="search notext" onclick="modeleSelector[{{$object_id}}].pop('{{$object_id}}','{{$object_class}}','{{$praticien->_id}}')">
        {{if $praticien->_can->edit}}
        Tous
        {{else}}
        Modèles disponibles
        {{/if}}
      </button>
    {{/if}}
  
    <!-- Impression de tous les modèles disponibles pour l'objet -->
    <button type="button" class="print notext" onclick="Document.printSelDocs('{{$object_id}}', '{{$object_class}}');">
      {{tr}}Print{{/tr}}
    </button>

    <input type="hidden" name="_fast_edit" value="" />
    <input type="hidden" name="_modele_id" value="" />
    <input type="hidden" name="_object_id" value=""
           onchange="var fast_edit = $V(this.form._fast_edit);
             if (fast_edit == '1') {
               Document.fastMode('{{$object_class}}', this.form._modele_id.value, '{{$object_id}}');
             }
             else {
               Document.create(this.form._modele_id.value, this.value,'{{$object_id}}','{{$object_class}}');
             }
             $V(this, '', false);
             $V(this.form._fast_edit, '');
             $V(this.form._modele_id, ''); "/>
  </form>
{{/if}}

{{if $nb_modeles_etiquettes > 0}}
  <button type="button" class="modele_etiquette"
    {{if $nb_modeles_etiquettes == 1}}
      onclick="ModeleEtiquette.print('{{$object_class}}', '{{$object_id}}', null, '{{$unique_id}}')"
    {{else}}
      onclick="ModeleEtiquette.chooseModele('{{$object_class}}', '{{$object_id}}', '{{$unique_id}}')"
    {{/if}}>Etiquettes</button>
{{/if}}

{{if $object->_nb_cancelled_docs && $mode != "hide"}}
  <button class="hslip" style="float: right;" data-show="" onclick="Document.showCancelled(this, $('docs_{{$object->_class}}{{$object->_id}}'))">
    Afficher / Masquer {{$object->_nb_cancelled_docs}} document(s) annulé(s)
  </button>
{{/if}}

<table class="form" id="docs_{{$object_class}}{{$object_id}}">
  {{mb_include module="compteRendu" template="inc_widget_list_documents"}}
</table>