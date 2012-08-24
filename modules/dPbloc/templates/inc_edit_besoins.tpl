{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  {{if !$usage}}
    Main.add(function() {
      var form = getForm("addBesoin");
      var url = new Url("system", "ajax_seek_autocomplete");
      url.addParam("object_class", "CTypeRessource");
      url.addParam("field", "_keywords");
      
      url.autoComplete(form.elements._keywords, "besoins_area", {
        minChars: 3,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field, selected){
          var id = selected.get("id");
          if (!id) {
            return;
          }
          $V(field.form.type_ressource_id, id);
          field.form.onsubmit();
        }
      });
    });
  {{/if}}
  
  onSubmitBesoins = function(form) {
    return onSubmitFormAjax(form, function() {
      reloadModal(); 
    });
  };
  
  onDelBesoin = function(besoin_id, nom) {
    var form = getForm("delBesoin");
    $V(form.besoin_ressource_id, besoin_id);
    confirmDeletion(form, {objName: nom, ajax: 1}, {onComplete: function() { reloadModal(); } });
  }
  
  onDelUsage = function(usage_id, nom) {
    var form = getForm("delUsage");
    $V(form.usage_ressource_id, usage_id);
    confirmDeletion(form, {objName: nom, ajax: 1}, {onComplete: function() { reloadModal(); } });
  }
  
  reloadModal = function() {
    getForm('delBesoin').up('div.modal').down('button.change').onclick();
  }
  
  showPlanning = function(type_ressource_id, operation_id, usage_ressource_id, besoin_ressource_id, usage) {
   var url = new Url("dPbloc", "ajax_vw_planning_ressources");
   url.addParam("besoin_ressource_id", besoin_ressource_id);
   url.addParam("usage_ressource_id", usage_ressource_id);
   url.addParam("type_ressource_id", type_ressource_id);
   url.addParam("operation_id", operation_id);
   url.addParam("usage", usage);
   url.modal();
   url.modalObject.observe("afterClose", reloadModal);
  }
</script>

<form name="delBesoin" method="post">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_besoin_ressource_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="besoin_ressource_id" />
</form>

<form name="delUsage" method="post">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_usage_ressource_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="usage_ressource_id" />
</form>

<table class="tbl">
  <tr>
    <th {{if $type == "operation_id"}} colspan="3"{{/if}}>
      {{if !$usage}}
        <div style="float: right;">
          <form name="addBesoin" method="post" onsubmit="onSubmitBesoins(this)">
            <input type="hidden" name="m" value="dPbloc" />
            <input type="hidden" name="dosql" value="do_besoin_ressource_aed" />
            <input type="text" name="_keywords" class="autocomplete" />
            <input type="hidden" name="{{$type}}" value="{{$object_id}}"/>
            <input type="hidden" name="type_ressource_id" />
          </form>
          <div id="besoins_area" style="text-align: left;" class="autocomplete">
            
          </div>
        </div>
      {{/if}}
      Liste des besoins
    </th>
    {{foreach from=$besoins item=_besoin}}
      {{assign var=type_ressource value=$_besoin->_ref_type_ressource}}
      {{assign var=_usage value=$_besoin->_ref_usage}}
      <tr>
        <td style="width: 12px; background: #{{$_besoin->_color}}">
        </td>
        <td style="width: 50%">
          <div style="float: right">
            <button type="button" class="trash notext" {{if $usage || $_usage->_id}}disabled{{/if}} title="{{tr}}Delete{{/tr}}"
              onclick="onDelBesoin('{{$_besoin->_id}}', '{{$type_ressource->libelle}}')"></button>
            {{if $type == "operation_id"}}
              <button type="button" class="modele_etiquette notext"
                onclick="showPlanning('{{$_besoin->type_ressource_id}}', '{{$object_id}}', '{{$_usage->_id}}', '{{$_besoin->_id}}', '{{$usage}}')"title="Planning"></button>
            {{/if}}
          </div>
          <strong>
            {{$type_ressource->libelle}}
          </strong>
        </td>
        {{if $type == "operation_id"}}
          <td {{if !$_usage->_id}}class="empty"{{/if}}>
            {{if $_usage->_id}}
              {{$_usage->_ref_ressource}}
              <form name="delUsage{{$_usage->_id}}" method="post">
                <input type="hidden" name="m" value="bloc" />
                <input type="hidden" name="dosql" value="do_usage_ressource_aed" />
                <button type="button" {{if !$usage}}disabled{{/if}} class="trash notext" style="float: right;"
                  onclick="onDelUsage('{{$_usage->_id}}', '{{$_usage->_ref_ressource}}')"></button>
              </form>
            {{else}}
              Non pourvu
            {{/if}}
          </td>
        {{/if}}
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty">
          {{tr}}CBesoinRessource.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  </tr>
</table>