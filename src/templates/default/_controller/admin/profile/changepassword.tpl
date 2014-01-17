<div class="header-row">
	<div class="header-row-wrapper">
		<header>
			<h1 class="header-main" id="page-header" rel="menu_user_list">
				<i class="fa fa-user"></i>
				<span class="breadcrumb"><a href="{$conf.rooturl_admin}profile" title="">{$me->fullname}</a> /</span>
				Change Password
			</h1>
			<div class="header-right pull-right">
				<a class="btn btn-default" href="{$conf.rooturl_admin}profile">Cancel</a>
				<a class="btn btn-success" href="javascript:void(0)" onclick="$('#myform').submit();">{$lang.default.formUpdateSubmit}</a>
			</div>
		</header>
	</div>
</div>


<form id="myform" action="" method="post" class="form-horizontal">

	{include file="notify.tpl" notifyError=$error notifySuccess=$success notifyWarning=$warning}

	{if $me->canChangePassword()}
		<div class="row section clear">
			<div class="col-md-3 section-summary">
				<h1>Information</h1>
				<p>Type your old &amp; new password to change password</p>
			</div>
			<div class="col-md-9 section-content">
				<div class="col-md-6 ssb clear inner-left">
					<label for="foldpass">{$lang.controller.oldpass}</label>
					<input type="password" name="foldpass" id="foldpass" class="form-control" />
				</div>

				<div class="col-md-6 ssb clear inner-left">
					<label for="fnewpass1">{$lang.controller.newpass1}</label>
					<input type="password" name="fnewpass1" id="fnewpass1" class="form-control" />
				</div>

				<div class="col-md-6 ssb clear inner-left">
					<label for="fnewpass2">{$lang.controller.newpass2}</label>
					<input type="password" name="fnewpass2" id="fnewpass2" class="form-control" />
				</div>
			</div>
		</div>
	{else}
		<div class="row section clear">
			<div class="col-md-3 section-summary">
				<h1>Information</h1>
				<p>Use this form to create password for your account</p>
			</div>
			<div class="col-md-9 section-content">
				<div class="col-md-6 ssb clear inner-left">
					<label>{$lang.controller.email}</label>
					<input type="text" name="femail" id="femail" {if $me->email != ''}readonly="readonly" class="disabled" disabled="disabled"{/if} value="{$formData.femail}" />
				</div>

				<div class="col-md-6 ssb clear inner-left">
					<label for="fnewpass1">{$lang.controller.newpass1}</label>
					<input type="password" name="fnewpass1" id="fnewpass1" class="form-control" />
				</div>

				<div class="col-md-6 ssb clear inner-left">
					<label for="fnewpass2">{$lang.controller.newpass2}</label>
					<input type="password" name="fnewpass2" id="fnewpass2" class="form-control" />
				</div>
			</div>
		</div>
	{/if}

	<div class="row section buttons">
		<input type="hidden" name="fsubmitpassword" value="1" />
		<input type="submit" name="fsubmitpassword" value="{$lang.default.formUpdateSubmit}" class="btn btn-success" />
	</div>
</form>