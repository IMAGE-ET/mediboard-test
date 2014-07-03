{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <th>{{tr}}Classname{{/tr}}</th>
    <th>{{tr}}Action{{/tr}}</th>
  </tr>

  <tr>
    <td>
      {{tr}}CPlageOp{{/tr}}
    </td>
    <td>
      <script type="text/javascript">

        CPlageOp = {
          reaffect: function(mode_real) {
            new Url('bloc', 'httpreq_reaffect_plagesop') .
              addParam("mode_real", mode_real) .
              requestModal(400);
          },

          purgeEmpty: function(form) {
            var url = new Url('bloc', 'purge_empty_plagesop');

            if (form) {
              url.addNotNullElement(form.purge);
              url.addNotNullElement(form.max  );
              url.addElement(form.auto);
            }

            var modal = Control.Modal.stack.last();
            if (modal) {
              url.requestUpdate(modal.container.down('.content'));
            }
            else {
              url.requestModal(600);
            }

            return false;
          },

          edit: function(plageop_id, bloc_id, date) {
            var url = new Url('bloc', 'inc_edit_planning');
            url.addParam('plageop_id', plageop_id);
            url.addParam('bloc_id', bloc_id);
            url.addParam('date', date);
            url.requestModal(800);
          },

          auto: function() {
            var form = document.PurgeEmpty;
            if (form && $(form.auto.checked)) {
              CPlageOp.purgeEmpty(form);
            }
          }
        }

      </script>

      <div>
        <button class="search" onclick="CPlageOp.reaffect(0)">Tester les plages à réattribuer</button>
        <button class="change" onclick="CPlageOp.reaffect(1)">Réattribuer les plages</button>
      </div>

      <div>
        <button class="search" onclick="CPlageOp.purgeEmpty()">{{tr}}mod-bloc-tab-purge_empty_plagesop{{/tr}}</button>
      </div>

    </td>
  </tr>


</table>