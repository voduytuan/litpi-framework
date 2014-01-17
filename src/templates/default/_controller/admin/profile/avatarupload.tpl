<!DOCTYPE html>
<html lang="en">
  <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>{$pageTitle}</title>

        <!-- Bootstrap Stylesheet -->
        <link rel="stylesheet" href="{$currentTemplate}bootstrap/css/bootstrap.min.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="{$currentTemplate}bootstrap/css/FortAwesome/css/font-awesome.css" type="text/css" media="screen" />

        <!-- Customized Admin Stylesheet -->
        <link type="text/css" rel="stylesheet" href="{$currentTemplate}min/?g=cssadmin&ver={$setting.site.cssversion}" media="screen" />

        <!-- jQuery -->
        <script type="text/javascript" src="{$currentTemplate}js/admin/jquery.js"></script>

        <!-- Bootstrap Js -->
        <script type="text/javascript" src="{$currentTemplate}bootstrap/js/bootstrap.min.js"></script>

        <!-- customized admin -->
        <script src="{$currentTemplate}min/?g=jsadmin&ver={$setting.site.jsversion}"></script>


        <script type="text/javascript">
        var rooturl = "{$conf.rooturl}";
        var rooturl_admin = "{$conf.rooturl_admin}";
        var module = "{$module}";
        var currentTemplate = "{$currentTemplate}";

        var delConfirm = "Are You Sure?";
        var delPromptYes = "Type YES to continue";

        var imageDir = "{$imageDir}";
        </script>

    </head>

    <body>
        <div class="text-center" style="padding-top:50px;">
        <h1>{$lang.controller.avatarUploadTitle}</h1>
        <form action="" id="uploadform" method="post" enctype="multipart/form-data" class="form-inline">
            <input type="hidden" name="fsubmit" value="1" />
            <br /><br />
            <div class="center-block">
                <input type="file" name="fimage" style="background:#eee;padding:5px;" id="fimage" class="inline form-control" onchange="$('#loader').show();$('#submitbutton').hide();this.form.submit();" />
                <input class="btn btn-success" type="submit" id="submitbutton" value="Upload"  />
            </div>
            <br /><br />
            <h1 id="loader" style="display:none;">Uploading...<img src="{$imageDir}admin/spinner.gif" alt="" /></h1>
        </form>

        {include file="notify.tpl" notifyError=$error notifySuccess=$success}
        </div>
    </body>
</html>