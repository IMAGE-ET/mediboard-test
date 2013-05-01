{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
editCEtabExterne = function(etab_id){
  var url = new Url("etablissement", "ajax_etab_externe");
  url.addParam("etab_id", etab_id);
  url.requestUpdate('group_externe'); 
};

changePage = function(page){
  $V(getForm("filter-etab_externes").elements.start, page);
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="filter-etab_externes" method="get" action="?">
        <input type="hidden" name="m" value="dPetablissement" />
        <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
        <input type="hidden" name="start" value="{{$current_page}}" onchange="this.form.submit()" />
        
        <input type="text" name="keywords" value="{{$keywords}}" onchange="this.form.start.value = 0" />
        <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
      </form>
      {{mb_include module=system template=inc_pagination total=$total_etab_externes current=$current_page change_page="changePage" step=40}}
      
      <table class="tbl">
        {{foreach from=$list_etab_externes item=_etab}}
        <tr id="{{$_etab->_guid}}-row">
          <td>
            <a href="#" onclick="editCEtabExterne('{{$_etab->_id}}')">
              {{$_etab->nom}}
            </a>
          </td>
          <td>
            {{$_etab->cp}}
            {{$_etab->ville}}
          </td>
          <td>
            {{$_etab->finess}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td class="empty" colspan="3">{{tr}}CEtabExterne.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    
    <td class="halfPane" id="group_externe">
      {{mb_include module=etablissement template=inc_etab_externe}}
    </td>
  </tr>
</table>
