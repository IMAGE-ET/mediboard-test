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
      {{tr}}CUser{{/tr}}
    </td>
    <td>
      <script type="text/javascript">
        CUser = {
          checkSiblings: function () {
            new Url('admin', 'check_siblings').requestModal(400);
          }
        }
      </script>
      <button class="tick" onclick="CUser.checkSiblings()">{{tr}}mod-admin-tab-check_siblings{{/tr}}</button>
    </td>
  </tr>


  <tr>
    <td>
      {{tr}}CUserLog{{/tr}}
    </td>
    <td>
      <script type="text/javascript">
        CUserLog = {
          sanitize: function(form) {
            var url = new Url('admin', 'sanitize_userlogs');

            if (form) {
              url.addNotNullElement(form.execute);
              url.addNotNullElement(form.offset);
              url.addNotNullElement(form.step);
              url.addElement(form.auto);
            }

            var modal = Control.Modal.stack.last();
            if (modal) {
              url.requestUpdate(modal.container.down('.content'));
            }
            else {
              url.requestModal(900);
            }

            return false;
          },

          auto: function() {
            var form = getForm("Sanitize");
            if ($V(form.auto) == 1) {
              CUserLog.sanitize(form);
            }
          }
        }
      </script>
      <button class="tick" onclick="CUserLog.sanitize()">{{tr}}mod-admin-tab-sanitize_userlogs{{/tr}}</button>
    </td>
  </tr>

</table>