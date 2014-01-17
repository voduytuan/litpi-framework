<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="{$currentTemplate}images/favicon.ico"/>
<title>{if $pageTitle != ''}{$pageTitle} | {$setting.site.heading}{else}{$setting.site.defaultPageTitle}{/if}</title>
<meta name="author" content="Vo Duy Tuan, tuanmaster2002@yahoo.com" />
<meta name="keywords" content="{$pageKeyword|default:$setting.site.defaultPageKeyword}" />
<meta name="description" content="{$pageDescription|default:$setting.site.defaultPageDescription}" />

<style type="text/css">
{literal}
body{background-color:#fff; font-family: Arial, Tahoma, Geneva, sans-serif; font-size:11px;}
{/literal}
</style>

<script type="text/javascript">
    function insertPhoto()
    {literal}{{/literal}
        self.parent.$(".form-avatar-image img").attr("src", "{$me->getImage()}?t={$smarty.now}" + (new Date()).getTime());
		self.parent.$("#panelleft .headingbox .avatarimg").attr("src", "{$me->getImage(1)}?t={$smarty.now}" + (new Date()).getTime()); 
        self.parent.$(".form-avatar-heading").hide();
        self.parent.$(".form-avatar-buttons").hide();
        self.parent.$(".form-avatar-text").hide();
        self.parent.$(".form-avatar-heading-alt").show();
        self.parent.$(".form-avatar-buttons-alt").show();  
        
        self.parent.Shadowbox.close();
     {literal}}{/literal}
    
    insertPhoto();
    

</script>

</head>

<body>
    
    <div>
        PROCESS OK!
    </div>
	<p class="closewindow"><a href="javascript:void(0)" onclick="self.parent.Shadowbox.close();">{$lang.default.closewindow}</a></p>
	<script  type="text/javascript" src="{$currentTemplate}min/?g=jquery"></script>
</body>
</html>