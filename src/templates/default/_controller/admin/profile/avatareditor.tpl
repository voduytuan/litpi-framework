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

        <style type="text/css">
        {literal}

            body{background-color:#fff;}
            input{border:1px solid #999999;}
            .captureCell{background:#eeeeee;font-weight:bold;font-size:18px;}
            .note{font-size:12px; color:#ff0000;font-weight:normal;}
            #previewCanvas{width:200px;height:200px; border:0px solid #ccc; overflow:hidden;}
            #previewCanvas50{width:50px;height:50px; border:0px solid #ccc; overflow:hidden;}
            #previewCanvas30{width:30px;height:30px; border:0px solid #ccc; overflow:hidden;}
            #previewCanvas img{}

        {/literal}
        </style>

        <!-- jQuery -->
        <script type="text/javascript" src="{$currentTemplate}js/admin/jquery.js"></script>

        <!-- Bootstrap Js -->
        <script type="text/javascript" src="{$currentTemplate}bootstrap/js/bootstrap.min.js"></script>

        <!-- customized admin -->
        <script src="{$currentTemplate}min/?g=jsadmin&ver={$setting.site.jsversion}"></script>

        <script type="text/javascript" src="{$currentTemplate}js/admin/jquery.imgareaselect.min.js" ></script>


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
<br /><br />
    {include file="notify.tpl" notifyError=$error notifySuccess=$success}
	<form method="post" onsubmit="return submitCropImage();" style="padding:20px;">
    <table style="margin:auto;padding:0px;">
        <tr>
            <td align="left" class="captureCell">{$lang.controller.avatarEditorOriginal}: <span class="note">{$lang.controller.avatarEditorHelp}</span></td>
            <td width="30"></td>
            <td></td>
        </tr>
        <tr>
            <td align="left" valign="top" class="captureCell">


                <input type="hidden" name="x1" value="" id="x1" />
                <input type="hidden" name="y1" value="" id="y1" />
                <input type="hidden" name="x2" value="" id="x2" />
                <input type="hidden" name="y2" value="" id="y2" />
                <input type="hidden" name="w" value="" id="w" />
                <input type="hidden" name="h" value="" id="h" />

                <img style="margin-bottom:10px;" src="{$conf.rooturl}{$fullImagePath}" id="thumbnail" alt="Original image" />
                    <br />
                    <div style="font-size:12px;font-weight:normal;">{$imagewidth} x {$imageheight}</div>
                </td>
            <td></td>
            <td align="right" valign="top">
                <div id="previewCanvas"><img src="{$conf.rooturl}{$fullImagePath}" ></div>
                <div>200x200</div>
                <br />
                <div id="previewCanvas50"><img src="{$conf.rooturl}{$fullImagePath}" ></div>
                <div>50x50</div>
                <br />
                <div id="previewCanvas30"><img src="{$conf.rooturl}{$fullImagePath}" ></div>
                <div>30x30</div>
                <br /><br /><br />
                <input type="submit" value="{$lang.controller.avatarEditorSave}" name="fsavethumbnail" class="btn btn-success" />
            </td>

        </tr>
    </table>
    </form>
    <script type="text/javascript">
        var imagefile = "{$fullImagePath}";
        var originalWidth = '{$imagewidth}';
        var originalHeight = '{$imageheight}';

		{literal}
        function selectEndImage(imageurl)
        {
            //$("#selectedImage", window.opener.document).attr("src",imageurl).show();
            //$("#capturedImageName", window.opener.document).val(imageurl);
            window.close();
        }

        function selectChange(img, selection)
        {
            $("#x1").val(selection.x1);
            $("#y1").val(selection.y1);
            $("#x2").val(selection.x2);
            $("#y2").val(selection.y2);
            $("#w").val(selection.width);
            $("#h").val(selection.height);

            /**
            * process image preview
            */

            var defaultWidth = 200;
            var defaultHeight = 200;

            //resize if width & height is not default size
            var marginLeft = selection.x1;
            var marginTop = selection.y1;
            var newImageWidth = originalWidth;
            var newImageHeight = originalHeight;

            if(selection.width != defaultWidth || selection.height != defaultHeight)
            {
                //tinh ty le giua selection va default width (because it is a square selection, doen not neeed to check the height)
                var ratio = defaultWidth / selection.width;

                //calculate new value of image
                newImageWidth = parseInt(ratio*newImageWidth);
                newImageHeight = parseInt(ratio*newImageHeight);
                marginLeft = parseInt(ratio*marginLeft);
                marginTop = parseInt(ratio*marginTop);
            }

            //set the position of image in preview canvas
            $("#previewCanvas img").css("width", newImageWidth + "px").css("height", newImageHeight + "px")
                                    .css("margin-top", "-" + marginTop + "px").css("margin-left", "-" + marginLeft + "px");

            /**
            * process image preview 50px
            */
            var defaultWidth = 50;
            var defaultHeight = 50;

            //resize if width & height is not default size
            var marginLeft = selection.x1;
            var marginTop = selection.y1;
            var newImageWidth = originalWidth;
            var newImageHeight = originalHeight;

            if(selection.width != defaultWidth || selection.height != defaultHeight)
            {
                //tinh ty le giua selection va default width (because it is a square selection, doen not neeed to check the height)
                var ratio = defaultWidth / selection.width;

                //calculate new value of image
                newImageWidth = parseInt(ratio*newImageWidth);
                newImageHeight = parseInt(ratio*newImageHeight);
                marginLeft = parseInt(ratio*marginLeft);
                marginTop = parseInt(ratio*marginTop);
            }

            //set the position of image in preview canvas
            $("#previewCanvas50 img").css("width", newImageWidth + "px").css("height", newImageHeight + "px")
                                    .css("margin-top", "-" + marginTop + "px").css("margin-left", "-" + marginLeft + "px");

            /**
            * process image preview 30px
            */
            var defaultWidth = 30;
            var defaultHeight = 30;

            //resize if width & height is not default size
            var marginLeft = selection.x1;
            var marginTop = selection.y1;
            var newImageWidth = originalWidth;
            var newImageHeight = originalHeight;

            if(selection.width != defaultWidth || selection.height != defaultHeight)
            {
                //tinh ty le giua selection va default width (because it is a square selection, doen not neeed to check the height)
                var ratio = defaultWidth / selection.width;

                //calculate new value of image
                newImageWidth = parseInt(ratio*newImageWidth);
                newImageHeight = parseInt(ratio*newImageHeight);
                marginLeft = parseInt(ratio*marginLeft);
                marginTop = parseInt(ratio*marginTop);
            }

            //set the position of image in preview canvas
            $("#previewCanvas30 img").css("width", newImageWidth + "px").css("height", newImageHeight + "px")
                                    .css("margin-top", "-" + marginTop + "px").css("margin-left", "-" + marginLeft + "px");

        }

        function submitCropImage()
        {
            var x1 = $("#x1").val();
            var y1 = $("#y1").val();
            var x2 = $("#x2").val();
            var y2 = $("#y2").val();
            var w = $("#w").val();
            var h = $("#h").val();
            if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
                alert("Select the region of photo first!");
                return false;
            }else{
                return true;
            }
        }


        $(window).load(function () {

            $("#thumbnail").imgAreaSelect({ onSelectChange: selectChange, aspectRatio : "1:1", x1:0, y1:0, x2:200, y2:200 });
            selectChange(new Object(), {x1:0, y1:0, x2:200, x2:200, width:200, height:200});

			//Update image to main window
			self.parent.$(".form-avatar-image img").attr("src", "{/literal}{$conf.rooturl}{$setting.avatar.imageDirectory}{$me->mediumImage()}{literal}?" + (new Date()).getTime());
			self.parent.$(".form-avatar-heading").hide();
			self.parent.$(".form-avatar-buttons").hide();
			self.parent.$(".form-avatar-text").hide();
			self.parent.$(".form-avatar-heading-alt").show();
			self.parent.$(".form-avatar-buttons-alt").show();
        });



		{/literal}
    </script>

    </body>
</html>