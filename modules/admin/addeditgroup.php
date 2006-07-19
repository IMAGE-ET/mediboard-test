<?php // Unused in Mediboard. To be removed.
?>

<?php
//add or edit a system user
if(empty($user_id))$user_id = 0;

?>
<SCRIPT language="javascript">
function submitIt(){
    var form = document.changeuser;
    if(form.user_username.value.length < 3)
    {
        alert("Please enter a valid user name");
        form.user_username.focus();
    }
    else if(form.user_password.value.length < 4)
    {
        alert("Please enter a valid password\n(greater than 4 chars).");
        form.user_password.focus();
    }
    else if(form.user_password.value !=  form.user_password2.value)
    {
        alert("Your passwords do not match).");
        form.user_password.focus();
    }
    else if(form.user_email.value.length < 4)
    {
        alert("Your email is invalid, please try again.");
        form.user_email.focus();
    }
    else if(form.user_birthday.value.length > 0)
    {
        dar =form.user_birthday.value.split("-");
        if(dar.length < 3)
        {
            alert("Please enter a valid Birthday date\nformat: (YYYY-MM_DD)\nor leave the field blank");
            form.user_birthday.focus();
        }
        else if(isNaN(parseInt(dar[0])) || isNaN(parseInt(dar[1])) || isNaN(parseInt(dar[2])))
        {
            alert("Please enter a valid Birthday date\nformat: (YYYY-MM_DD)\nor leave the field blank");
            form.user_birthday.focus();
        }
        else if(parseInt(dar[1]) < 1 || parseInt(dar[1]) > 12)
        {
            alert("Please enter a valid Birthday date\nformat: (YYYY-MM_DD)\nor leave the field blank");
            form.user_birthday.focus();
        }
        else if(parseInt(dar[2]) < 1 || parseInt(dar[2]) > 31)
        {
            alert("Please enter a valid Birthday date\nformat: (YYYY-MM_DD)\nor leave the field blank");
            form.user_birthday.focus();
        }
        else if(parseInt(dar[0]) < 1900 || parseInt(dar[0]) > 2020)
        {
            alert("Please enter a valid Birthday date\nformat: (YYYY-MM_DD)\nor leave the field blank");
            form.user_birthday.focus();
        }   
        else
        {
        form.submit();
        }
    }
    else
    {
    form.submit();
    }
    
}
</script>
<?php //------------------------Begin HTML -------------------------------?>
<table width="95%" border="0" cellpadding="0" cellspacing="1">
    <tr>
    <td valign="top"><img src="./images/icons/admin.gif" alt="" border="0" width="42" height="42" /></td>
        
        <td nowrap><h1>
        <?php if(!$prow["user_id"]){ echo "Add User";}else{echo "View/Edit User";}?></h1></td>
        <td valign="top" align="right" width="100%">&nbsp;</td>
    </tr>
</table>

<table width="95%" border="0" bgcolor="#f4efe3" cellpadding="0" cellspacing="1" height="400">
<form name="changeuser" action="./index.php?m=admin&amp;a=dosql" method="post">
    <input type="hidden" name="user_id" value="<?php echo intval($prow["user_id"]);?>" />
    <tr height="20">
        <td valign="top" bgcolor="#878676" colspan="2">
        <font color="white"><strong><em>Adding new user to the system</em></strong></font>
        </td>
    </tr>
    <tr>
        <td align="right" width="230">Username:</td>
        <td>
            <input type="text" class="text" name="user_username" value="<?php echo $prow["user_username"];?>" maxlength="255" /> 
            <span class="smallNorm">(required)</span>
        </td>
    </tr>
    <tr>
        <td align="right">Password:</td>
        <td><input type="password" class="text" name="user_password" value="<?php echo $prow["user_password"];?>" maxlength="20" /></td>
    </tr>
    <tr>
        <td align="right">Password2:</td>
        <td><input type="password" class="text" name="user_password2" value="<?php echo $prow["user_password"];?>" maxlength="20" /></td>
    </tr>
    <tr>
        <td align="right">First Name:</td>
        <td>
            <input type="text" class="text" name="user_first_name" value="<?php echo $prow["user_first_name"];?>" maxlength="50" /> 
            <input type="text" class="text" name="user_last_name" value="<?php echo $prow["user_last_name"];?>" maxlength="50" />
        </td>
    </tr>
    <tr><td align="right">Email:</td><td><input type="text" class="text" name="user_email" value="<?php echo $prow["user_email"];?>" maxlength="255" /> </td></tr>
    <tr><td align="right">Phone:</td><td><input type="text" class="text" name="user_phone" value="<?php echo $prow["user_phone"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Home Phone:</td><td><input type="text" class="text" name="user_home_phone" value="<?php echo $prow["user_home_phone"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Mobile:</td><td><input type="text" class="text" name="user_mobile" value="<?php echo $prow["user_mobile"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Address1:</td><td><input type="text" class="text" name="user_address1" value="<?php echo $prow["user_address1"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Address2:</td><td><input type="text" class="text" name="user_address2" value="<?php echo $prow["user_address2"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">City:</td><td><input type="text" class="text" name="user_city" value="<?php echo $prow["user_city"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">State:</td><td><input type="text" class="text" name="user_state" value="<?php echo $prow["user_state"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Zip:</td><td><input type="text" class="text" name="user_zip" value="<?php echo $prow["user_zip"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Country:</td><td><input type="text" class="text" name="user_country" value="<?php echo $prow["user_country"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">ICQ#:</td><td><input type="text" class="text" name="user_icq" value="<?php echo $prow["user_icq"];?>" maxlength="50" /> AOL Nick: <input type="text" class="text" name="user_aol" value="<?php echo $prow["user_aol"];?>" maxlength="50" /> </td></tr>
    <tr><td align="right">Birthday:</td><td><input type="text" class="text" name="user_birthday" value="<?php echo substr($prow["user_birthday"],0,10);?>" maxlength="50" /> format(YYYY-MM-DD)</td></tr>
    <tr><td align="left">&nbsp; &nbsp; &nbsp;<button type="button" onClick="javascript:history.back(-1);">back</button></td><td align="right"><button type="submit" onClick="submitIt()">submit</button>&nbsp; &nbsp; &nbsp;</td></tr>
</table>
