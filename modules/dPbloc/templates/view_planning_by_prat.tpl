{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=show_duree_preop value=$conf.dPplanningOp.COperation.show_duree_preop}}
{{assign var=curr_plage_id value=""}}
{{assign var=salle_id value=""}}
{{assign var=curr_plageop value=""}}
{{assign var="col1" value=$conf.dPbloc.CPlageOp.planning.col1}}
{{assign var="col2" value=$conf.dPbloc.CPlageOp.planning.col2}}
{{assign var="col3" value=$conf.dPbloc.CPlageOp.planning.col3}}

<table class="tbl">
  {{mb_include module=bloc template=inc_view_planning_header}}

  {{foreach from=$listDatesByPrat item=ops_by_date key=curr_date}}
    {{foreach from=$ops_by_date item=listOperations key=prat_id}}
      <tr class="clear">
        <td colspan="{{$_materiel+$_extra+$_duree+$_coordonnees+12}}">
          <h2>
            <strong>{{$curr_date|date_format:"%A %d/%m/%Y"|ucfirst}}</strong>
            &mdash;
            Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$listPrats.$prat_id}}
          </h2>
        </td>
      </tr>

      {{mb_include module=bloc template=inc_view_planning_title}}

      {{mb_include module=bloc template=inc_view_planning_content}}
    {{/foreach}}
  {{/foreach}}
</table>