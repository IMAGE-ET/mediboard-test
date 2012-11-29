{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=ccam script=code_ccam}}

<script type="text/javascript">
function tagCallback(){
  location.reload();
}
</script>

<table class="main tbl">
  <tr>
    <td colspan="5">
      {{if $can->admin}}
        <button style="float: right;" class="tag-edit" type="button" onclick="Tag.manage('CFavoriCCAM')">
          Gérer les tags
        </button>
      {{/if}}

      <form name="selClass" action="?" method="get">
        <input type="hidden" name="m" value="ccam" />
        <input type="hidden" name="tab" value="vw_idx_favoris" />

        {{mb_label object=$favoris field="filter_class"}}
        {{mb_field object=$favoris field="filter_class" emptyLabel="All" onchange="this.form.submit()"}}

        <label for="tag_id">Tag</label>
        <select name="tag_id" onchange="this.form.submit()" class="taglist">
          <option value=""> &mdash; {{tr}}All{{/tr}} </option>
          {{mb_include module=ccam template=inc_favoris_tag_select depth=0 show_empty=true}}
        </select>
      </form>
    </td>
  </tr>
  
  {{foreach from=$fusion item=curr_chap key=key_chap}}
  <tr>
    <th colspan="5">
      {{$curr_chap.nom}}
    </th>
  </tr>
  <tbody>
    {{foreach from=$curr_chap.codes item=curr_code key=key_code}}
      <tr>
        <td class="narrow">
          {{if $curr_code->favoris_id && $can->edit}}
            <form name="FavorisDel-{{$curr_code->favoris_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, function(){location.reload()})">
              <input type="hidden" name="m" value="ccam" />
              <input type="hidden" name="dosql" value="do_favoris_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="favoris_id" value="{{$curr_code->favoris_id}}" />
              <button class="trash notext compact" type="submit">
                Retirer de mes favoris
              </button>
            </form>
          {{/if}}
        </td>

        <td style="background-color: #{{$curr_code->couleur}}; font-weight: bold;">
          <a href="#1" onclick="CodeCCAM.show('{{$curr_code->code}}', '{{$curr_code->class}}'); return false;">{{$curr_code->code}}</a>
        </td>
        <td>
          {{if $curr_code->favoris_id && $can->edit}}
            <form name="favoris-tag-{{$curr_code->favoris_id}}" action="?" method="post" style="float: right;">
              {{if $curr_code->favoris_id}}
                {{mb_include module=system
                             template=inc_tag_binder_widget
                             object=$curr_code->_ref_favori
                             show_button=false
                             form_name="favoris-tag-`$curr_code->favoris_id`"
                             callback="tagCallback"}}
              {{/if}}
            </form>
          {{/if}}

          <a href="#1" onclick="CodeCCAM.show('{{$curr_code->code}}', '{{$curr_code->class}}'); return false;"> {{$curr_code->libelleLong}}</a>
        </td>
        <td>{{tr}}CFavoriCCAM.filter_class.{{$curr_code->class}}{{/tr}}</td>
        <td>
          {{if $curr_code->occ==0}}
            Favoris
          {{else}}
            {{$curr_code->occ}} acte(s)
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
  </tbody>
  {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CFavoriCCAM.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>