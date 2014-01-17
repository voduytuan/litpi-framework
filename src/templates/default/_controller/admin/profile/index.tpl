<div class="header-row">
	<div class="header-row-wrapper">
		<header>
			<h1 class="header-main" id="page-header" rel="menu_user_list">
				<i class="fa fa-user"></i>
				<span class="breadcrumb"><a href="{$conf.rooturl_admin}profile" title="">{$me->fullname}</a> /</span>
				Change Profile
			</h1>
			<div class="header-right pull-right">
				<a class="btn btn-success pull-right" href="javascript:void(0);" onclick="$('#myform').submit();">{$lang.default.formUpdateSubmit}</a>
				<a class="btn btn-warning" href="{$conf.rooturl_admin}profile/changepassword">Change Password</a>
			</div>
		</header>
	</div>
</div>


<div class="col-md-12">
	<div class="col-md-2" id="avatarform">
		<div class="form-avatar">
			<input type="hidden" name="favatar" id="favatar" />
			<div class="form-avatar-image"><img src="{$me->getImage()}" alt="avatar placeholder" /></div>
			<div class="form-avatar-buttons"{if $me->avatar != ''} style="display:none;"{/if}>
				<a href="{$conf.rooturl_admin}profile/avatarupload" rel="shadowbox;width=960px;height=640px" title="">Upload Avatar</a>
			</div>
			<div class="form-avatar-buttons-alt"{if $me->avatar == ''} style="display:none;"{/if}>
	        	<a href="{$conf.rooturl_admin}profile/avatareditor" rel="shadowbox;width=960px;height=640px" title="">{$lang.controller.editavatar}</a>
				<a href="javascript:delm('{$conf.rooturl_admin}profile?deleteavatar={$smarty.session.avatarDeleteToken}')" title="">{$lang.controller.removeavatar}</a>

				<div class="removeavatar-notification"></div>

			</div>
	        <div class="clear"></div>
		</div><!-- end class:form-avatar -->
	</div>

	<div class="col-md-10">
		{include file="notify.tpl" notifyError=$error notifySuccess=$success}

		<form id="myform" name="form1" method="post" action="" class="form-horizontal">

			<div class="form-group">
				<label for="ffullname" class="col-md-2 control-label">{$lang.controller.fullname}</label>
				<div class="col-md-10">
					<input type="text" name="ffullname" id="ffullname"  value="{$formData.ffullname}" class="form-control" />
				</div>
			</div>

			<div class="form-group">
				<label for="fphone" class="col-md-2 control-label">{$lang.controller.phone1}</label>
				<div class="col-md-2">
					<input type="text" name="fphone" id="fphone" value="{$formData.fphone}" class="form-control" />
				</div>
			</div>

			<div class="form-group">
				<label for="faddress" class="col-md-2 control-label">{$lang.controller.address}</label>
				<div class="col-md-10">
					<div>
						<span class="col-md-6 inner-left"><input type="text" name="faddress" id="faddress" value="{$formData.faddress}" class="form-control" /></span>
						<select  name="fregion" id="fregion" class="col-md-6 form-control inline">
							<option value="0">- - Region - -</option>
							{foreach item=region key=regionid from=$setting.region}
								<option {if $regionid == $formData.fregion}selected="selected" {/if} value="{$regionid}">{$region}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="fgender" class="col-md-2 control-label">{$lang.controller.gender}</label>
				<div class="col-md-3">
					<select id="fgender" name="fgender">
						<option value="">- - - -</option>
						<option value="1" {if $formData.fgender == '1'}selected="selected"{/if}>Male</option>
	                    <option value="2" {if $formData.fgender == '2'}selected="selected"{/if}>Female</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="fbirthday" class="col-md-2 control-label">{$lang.controller.birthday}</label>
				<div class="col-md-2">
					<input type="text" name="fbirthday" id="fbirthday" title="DD/MM/YYYY" placeholder="DD/MM/YYYY" value="{$formData.fbirthday}" class="form-control" />
				</div>
			</div>

			<div class="form-group">
				<label for="fwebsite" class="col-md-2 control-label">{$lang.controller.website}</label>
				<div class="col-md-10">
					<input type="text" name="fwebsite" id="fwebsite" value="{$formData.fwebsite}" class="form-control" />
				</div>
			</div>

			<div class="form-group">
				<label for="fwebsite" class="col-md-2 control-label">{$lang.controller.bio}</label>
				<div class="col-md-10">
					<textarea name="fbio" id="fbio" class="wide" rows="5">{$formData.fbio}</textarea>
				</div>
			</div>

			<div class="col-md-12 inner-right">
				<input type="hidden" name="fsubmit" value="1" />
				<input type="submit" name="fsubmit" value="{$lang.default.formUpdateSubmit}" class="btn btn-success pull-right" />
			</div>
	  </form>
	</div>
</div>



