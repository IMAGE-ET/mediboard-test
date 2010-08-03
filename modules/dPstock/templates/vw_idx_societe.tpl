{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
refreshSocietesList = function(){
  var url = new Url("dPstock", "httpreq_vw_societes_list");
  url.addFormData("filterSociete");
  url.requestUpdate("list-societe");
  return false;
}

changePageSociete = function (page){
  $V(getForm("filterSociete").start, page);
}

Main.add(function(){
  refreshSocietesList();
});

editSociete = function(societe_id) {
  var url = new Url("dPstock", "httpreq_vw_societe_form");
  if (!Object.isUndefined(societe_id))
    url.addParam("societe_id", societe_id);
  url.requestUpdate("edit-societe");
}

Main.add(editSociete);
</script>

{{mb_include_script module=dPstock script=barcode}}

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="filterSociete" method="get" action="" onsubmit="return refreshSocietesList()">
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <input type="text" name="keywords" value="" onchange="$V(this.form.start, 0)" />
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        
        <label>
          <input type="checkbox" name="suppliers" value="1" {{if $suppliers}}checked="checked"{{/if}}
                 onchange="$V(this.form.start, 0); this.form.onsubmit()" />
          Distributeurs
        </label>
        
        <label>
          <input type="checkbox" name="manufacturers" value="1" {{if $manufacturers}}checked="checked"{{/if}}
                 onchange="$V(this.form.start, 0); this.form.onsubmit()" />
          Fabricants
        </label>
        
        <label>
          <input type="checkbox" name="inactive" value="1" {{if $inactive}}checked="checked"{{/if}}
                 onchange="$V(this.form.start, 0); this.form.onsubmit()" />
          Sociétés inactives
        </label>
      </form>
    </td>
    <td id="edit-societe" class="halfPane" rowspan="2"></td>
  </tr>
  <tr>
    <td id="list-societe"></td>
  </tr>
</table>