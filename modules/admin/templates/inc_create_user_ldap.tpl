{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  window.ldap_user_id='{{$user->_id}}'; 
  window.ldap_user_actif='{{$user->_user_actif}}';
  window.ldap_user_deb_activite='{{$user->_user_deb_activite}}'; 
  window.ldap_user_fin_activite='{{$user->_user_fin_activite}}'; 
  window.no_association='{{$association}}';
</script>