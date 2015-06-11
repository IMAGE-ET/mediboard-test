{{* $Id: $ *}}

{{*
  * @package    Mediboard
  * @subpackage admissions
  * @version    $Revision: $
  * @author     SARL OpenXtrem
  * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{mb_default var=edit value=0}}

{{if "dPhospi prestations systeme_prestations"|conf:"CGroups-$g" == "standard"}}
  {{if !$edit}}
    {{if $sejour->chambre_seule}}
      <div>{{mb_label object=$sejour field=chambre_seule}}</div>
    {{/if}}
    {{mb_value object=$sejour field=prestation_id tooltip=1}}
    {{mb_return}}
  {{/if}}
  
  <div>
    <form name="Chambre-{{$sejour->_guid}}" method="post" class="prepared" onsubmit="return onSubmitFormAjax(this);">
      {{mb_class object=$sejour}}
      {{mb_key   object=$sejour}}
      {{mb_field object=$sejour field=chambre_seule typeEnum=checkbox onchange="this.form.onsubmit();"}}
      {{mb_label object=$sejour field=chambre_seule typeEnum=checkbox}}
    </form>
  </div>
    
  {{if $prestations}}
  <div>
    <form name="Prestations-{{$sejour->_guid}}" method="post" class="prepared" onsubmit="return onSubmitFormAjax(this);">
      {{mb_class object=$sejour}}
      {{mb_key   object=$sejour}}
      {{mb_field object=$sejour field=prestation_id choose=CPrestation options=$prestations onchange="this.form.onsubmit();"}}
    </form>
  </div>
  {{/if}}
{{/if}}

{{if "dPhospi prestations systeme_prestations"|conf:"CGroups-$g" == "expert"}}
  {{if !$edit}}
    Prestations
    {{mb_return}}
  {{/if}}
  
  {{assign var=opacity value=""}}
  {{assign var=class   value=help}}
  {{if array_key_exists("items_liaisons", $sejour->_count)}}
    {{assign var=class value=search}}
    {{if !$sejour->_count.items_liaisons}}
      {{assign var=opacity value=opacity-60}}
    {{/if}}
  {{/if}}
  
  <button type="button" class="{{$class}} {{$opacity}}" onclick="Prestations.edit('{{$sejour->_id}}')">
    Prestations
  </button>
{{/if}}