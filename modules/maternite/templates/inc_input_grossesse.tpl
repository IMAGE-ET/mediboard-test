{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=maternite script=grossesse ajax=true}}

{{if $object->_class == "CPatient"}}
  {{assign var=grossesse value=$object->_ref_last_grossesse}}
{{else}}
  {{assign var=grossesse value=$object->_ref_grossesse}}
{{/if}}
{{mb_default var=submit value=0}}
{{mb_default var=large_icon value=0}}
{{mb_default var=modify_grossesse value=1}}

{{if !$grossesse}}
  {{mb_return}}
{{/if}}

<script>
  Main.add(function() {
    Grossesse.parturiente_id = '{{$patient->_id}}';
    Grossesse.submit = '{{$submit}}';
    Grossesse.large_icon = '{{$large_icon}}';
    Grossesse.modify_grossesse = '{{$modify_grossesse}}';
    Grossesse.formTo = $('grossesse_id').form;
    Grossesse.duree_sejour = '{{$conf.maternite.duree_sejour}}';
    
    {{if $submit}}
      Grossesse.submit = {{$submit}};
    {{/if}}
  });
</script>

<input type="hidden" name="_grossesse_id" value="{{$grossesse->_id}}"/>
<input type="hidden" name="grossesse_id" value="{{$grossesse->_id}}" id="grossesse_id" onchange="$V(this.form._grossesse_id, this.value);"/>
<input type="hidden" name="_patient_sexe" value="" onchange="Grossesse.toggleGrossesse(this.value, this.form)"/>
<input type="hidden" name="_large_icon" value="{{$large_icon}}" />

<span id="view_grossesse">
  {{if $grossesse->_id}}
      <img onmouseover="ObjectTooltip.createEx(this, '{{$grossesse->_guid}}')" {{if !$grossesse->active}}class="opacity-50"{{/if}}
           src="style/mediboard/images/icons/grossesse.png" style="{{if $large_icon}}width: 30px;{{/if}} background-color: rgb(255, 215, 247);"/>
  {{elseif $modify_grossesse && (!$patient->_id || $patient->sexe == "f")}}
    <div class="empty" style="display:inline">{{tr}}CGrossesse.none_linked{{/tr}}</div>
  {{/if}}
</span>

{{if $modify_grossesse && (!$patient->_id || $patient->sexe == "f")}}
  <button id="button_grossesse" type="button" class="edit notext button_grossesse" {{if !$patient->_id || $patient->_annees < 12}}disabled{{/if}}
    onclick="Grossesse.viewGrossesses('{{$patient->_id}}', '{{$object->_guid}}', this.form)"></button>
{{/if}}
  