{include file="`$smartyMail`header.tpl"}
<strong>RESET PASSWORD FOR ACCOUNT <b>{$myUser->email}</b>:</strong>
<br />
This email was sent from out website to notify you that administrator had been reset your password for your account. Your new account information:<br />
<br />
Email: {$myUser->email}<br />
Password: {$newpass}<br />

{include file="`$smartyMail`footer.tpl"}