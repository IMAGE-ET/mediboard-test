{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<table class="main">
	{{if $prat->_id}}
  <tr>
    <td>
      <form name="filters" action="?" method="get" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPboard" />

      <table class="form">
      
        <tr>
          <th colspan="4" class="category">Statistiques de consultation</th>
        </tr>

        <tr>
          <td>{{mb_label object=$filterConsultation field="_date_min"}}</td>
          <td>{{mb_field object=$filterConsultation field="_date_min" form="filters" register=true canNull="false"}} </td>
          <td>{{mb_label object=$filterConsultation field="_date_max"}}</td>
          <td>{{mb_field object=$filterConsultation field="_date_max" form="filters" register=true canNull="false"}} </td>
        </tr>

        <tr>
          <td colspan="4" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>

        <tr>
          <td colspan="4" class="button">
            <img title="Nombre de consultations" src='?m=dPstats&amp;a=graph_consultations&amp;suppressHeaders=1&amp;debut={{$filterConsultation->_date_min}}&amp;fin={{$filterConsultation->_date_max}}&amp;prat_id={{$filterConsultation->praticien_id}}' />
          </td>
        </tr>
        
      </table>
      
      </form>
    </td>
  </tr>
  {{/if}}
</table>