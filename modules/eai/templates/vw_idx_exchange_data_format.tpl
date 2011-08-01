{{*
 * View exchange data format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=exchange_data_format}}

<script type="text/javascript">
  Main.add(function () {
    var exchange_class = Url.hashParams().exchange_class;
    if (exchange_class) {
    	ExchangeDataFormat.refreshExchanges(exchange_class, Url.hashParams().exchange_type, Url.hashParams().exchange_group_id);
    }
  });
</script>

<table class="main">
  <tr>
    <td style="width:5%" rowspan="6" id="exchange_data_format">
      {{mb_include template=inc_exchange_data_format}}
    </td>
    <td class="halfPane" id="exchanges">
    </td> 
  </tr>
</table>