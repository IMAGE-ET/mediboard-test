{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=cim10 script=code_cim}}

<script type="text/javascript">
function tagCallback(){
  location.reload();
}
</script>

<table class="main tbl">
  <tr>
    <td colspan="4">
      <form action="?" name="selectLang" method="get">
        {{include file="inc_select_lang.tpl"}}

        <input type="hidden" name="m" value="cim10" />
        <input type="hidden" name="tab" value="vw_idx_favoris" />

        <label for="tag_id">Tag</label>
        <select name="tag_id" onchange="this.form.submit()" class="taglist">
          <option value=""> &mdash; {{tr}}All{{/tr}} </option>
        {{mb_include module=ccam template=inc_favoris_tag_select depth=0 show_empty=true}}
        </select>

        {{if $can->admin}}
          <button style="float: right;" class="tag-edit" type="button" onclick="Tag.manage('CFavoriCIM10')">
            Gérer les tags
          </button>
        {{/if}}
      </form>
    </td>
  </tr>

  {{foreach from=$fusionCim item=curr_code key=curr_key name="fusion"}}
  <tr>
    <td class="narrow">
      {{if $can->edit && $curr_code->_favoris_id}}
        <form name="delFavoris-{{$curr_key}}" action="?" method="post"
              onsubmit="return onSubmitFormAjax(this, function(){location.reload()})">
          {{mb_class class=CFavoriCIM10}}
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="favoris_id" value="{{$curr_code->_favoris_id}}" />
          <button class="trash notext compact" type="submit">Retirer de mes favoris</button>
        </form>
      {{/if}}
    </td>

    <td style="font-weight: bold;">
      <a href="#1" onclick="CodeCIM.show('{{$curr_code->code}}'); return false;">{{$curr_code->code}}</a>
    </td>
    <td class="text">
      {{if $curr_code->_favoris_id && $can->edit}}
        <form name="favoris-tag-{{$curr_code->_favoris_id}}" action="?" method="post" style="float: right;">
          {{if $curr_code->_favoris_id}}
            {{mb_include module=system
              template=inc_tag_binder_widget
              object=$curr_code->_ref_favori
              show_button=false
              form_name="favoris-tag-`$curr_code->_favoris_id`"
              callback="tagCallback"
            }}
          {{/if}}
        </form>
      {{/if}}

      <a href="#1" onclick="CodeCIM.show('{{$curr_code->code}}'); return false;">{{$curr_code->libelle}}</a>
    </td>
    <td>{{if $curr_code->occ==0}}Favoris{{else}}{{$curr_code->occ}} acte(s){{/if}}</td>
  </tr>
    {{foreachelse}}
  <tr>
    <td class="empty" colspan="4">{{tr}}CFavoriCIM10.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>