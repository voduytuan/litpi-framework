{include file="`$smartyMail`header.tpl"}

{if $myUser->fullname != ''}<p>Hi {$myUser->fullname},</p>{/if}
<p>Your request to recovery password at {$datecreated}</p>
<p>Account:</p>
<p>&nbsp;&nbsp;Email: <b>{$myUser->email}</b></p>
<p>ACTIVATED CODE: <big><b>{$activatedCode}</b></big></p>

{if $activatedCode neq ''}
	<p>Click this link <a href="{$conf.rooturl}site/forgotpass/reset?email={$myUser->email}&amp;code={$activatedCode}">{$conf.rooturl}site/forgotpass/reset?email={$myUser->email}&amp;code={$activatedCode}</a> and type your new password to reset your password.</p>
{/if}

{include file="`$smartyMail`footer.tpl"}