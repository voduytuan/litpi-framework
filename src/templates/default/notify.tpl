{if count($notifySuccess) > 0}
<div class="notify-bar notify-bar-success">
	<div class="notify-bar-button{if $hidenotifyclose} hide{/if}"><a href="javascript:void(0);" onclick="javascript:$(this).parent().parent().fadeOut();" title="close"><img src="{$imageDir}notify/close-btn.png" border="0" alt="close" /></a></div>
	<div class="notify-bar-text">
		{if $notifySuccess|@is_array}
			{foreach item=notifySuccessItem from=$notifySuccess name="notifysuccess"}
				<p>{$notifySuccessItem}</p>
				{if !$smarty.foreach.notifysuccess.last}<div class="notify-bar-text-sep"></div>{/if}
			{/foreach}
		{else}
			<p>{$notifySuccess}</p>
		{/if}
	</div>
</div>
{/if}

{if count($notifyError) > 0}
<div class="notify-bar notify-bar-error">
	<div class="notify-bar-button{if $hidenotifyclose} hide{/if}"><a href="javascript:void(0);" onclick="javascript:$(this).parent().parent().fadeOut();" title="close"><img src="{$imageDir}notify/close-btn.png" border="0" alt="close" /></a></div>
	<div class="notify-bar-text">
		{if $notifyError|@is_array}
			{foreach item=notifyErrorItem from=$notifyError name="notifyerror"}
				<p>{$notifyErrorItem}</p>
				{if !$smarty.foreach.notifyerror.last}<div class="notify-bar-text-sep"></div>{/if}
			{/foreach}
		{else}
			<p>{$notifyError}</p>
		{/if}
	</div>
</div>
{/if}

{if count($notifyWarning) > 0}
<div class="notify-bar notify-bar-warning">
	<div class="notify-bar-button{if $hidenotifyclose} hide{/if}"><a href="javascript:void(0);" onclick="javascript:$(this).parent().parent().fadeOut();" title="close"><img src="{$imageDir}notify/close-btn.png" border="0" alt="close" /></a></div>
	<div class="notify-bar-text">
		{if $notifyWarning|@is_array}
			{foreach item=notifyWarningItem from=$notifyWarning name="notifywarning"}
				<p>{$notifyWarningItem}</p>
				{if !$smarty.foreach.notifywarning.last}<div class="notify-bar-text-sep"></div>{/if}
			{/foreach}
		{else}
			<p>{$notifyWarning}</p>
		{/if}
	</div>
</div>
{/if}

{if count($notifyInformation) > 0}
<div class="notify-bar notify-bar-info">
	<div class="notify-bar-button{if $hidenotifyclose} hide{/if}"><a href="javascript:void(0);" onclick="javascript:$(this).parent().parent().fadeOut();" title="close"><img src="{$imageDir}notify/close-btn.png" border="0" alt="close" /></a></div>
	<div class="notify-bar-text">
		{if $notifyInformation|@is_array}
			{foreach item=notifyInformationItem from=$notifyInformation name="notifyinformation"}
				<p>{$notifyInformationItem}</p>
				{if !$smarty.foreach.notifyinformation.last}<div class="notify-bar-text-sep"></div>{/if}
			{/foreach}
		{else}
			<p>{$notifyInformation}</p>
		{/if}
	</div>
</div>
{/if}