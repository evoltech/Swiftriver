<?php include_once("header.php"); ?>
<?php
    $htaccessFile = dirname(__FILE__)."/../web/.htaccess";
    $bootstrapFile = dirname(__FILE__)."/../web/application/bootstrap.php";

     $checks = array();
     /*
     $check->check = "Now, I need to check to see if you have an .htaccess file in place.";
     $check->result = (file_exists($htaccessFile));
     $check->message = $check->result
                         ? "Excellent, it's there."
                         : "Oops, you don't seem to have the .htaccess file in the root ".
                           "folder of the install. This can happen sometimes when ".
                           "you take the code from the GitHub repo but shouldn't if you ".
                           "got a packaged download. It's fine though, you just need to ".
                           "go to http://github.com/ushahidi/Swiftriver/issues, download ".
                           "the .htaccess file and place it in the root folder.";
     $checks[] = $check;
     unset($check);
     */
     $check->check = "Now we need to make sure that the .htaccess file is writable.";
     $check->result = is_writable($htaccessFile);
     $check->message = $check->result
                       ? "No problems here, I can open it and write to it."
                       : "Oops, that's a shame, I don't seem to be able to open it ".
                         "for write access (I tried is_writable()). Can you check ".
                         "the file access permissions? Make them 755 or above.";
                         
     $checks[] = $check;
     
     unset($check);

     $check->check = "Now, I need to check to see if you have a bootstrap.php file in place.";
     $check->result = (file_exists($bootstrapFile));
     $check->message = $check->result
                         ? "Excellent, it's there."
                         : "Oops, you don't seem to have the bootstrap.php file in the ".
                           "web/application folder. This is going to be a real issue. " .
                           "I think the best thing to do is contact SwiftRiver and report ".
                           "at http://github.com/ushahidi/Swiftriver/issues.";
     $checks[] = $check;
     unset($check);

     $check->check = "Now we need to make sure that the bootstrap.php file is writable.";
     $check->result = is_writable($bootstrapFile);
     $check->message = $check->result
                       ? "No problems here, I can open it and write to it."
                       : "Oops, that's a shame, I don't seem to be able to open it ".
                         "for write access (I tried is_writable()). Can you check ".
                         "the file access permissions? Make them 755 or above.";

     $checks[] = $check;

     unset($check);

     //check directory acces to key directories
     $check->check = "So now we need to make sure that the directories need are indeed ".
                     "writable. I'll check them all at once to save time ... they are: ".
                     "[myroot]/Core/Configuration/ConfigurationFiles, [myroot]/Core/Modules ".
                     "and [myroot]/web/application/cache";
     $check->result = is_writable(dirname(__FILE__)."/../core/Configuration/ConfigurationFiles") &&
                      is_writable(dirname(__FILE__)."/../core/Modules") &&
                      is_writable(dirname(__FILE__)."/../web/application/cache");
     $check->message = $check->result
                        ? "Great news...they are all willing to accept my changes!"
                        : "Oops, at least one of the above directories wouldn't let me ".
                          "write (I tried is_writable()). Can you check the permissions ".
                          "for each of them?";
     $checks[] = $check;

     unset($check);

?>
<div id="php-checks">
<script language="javascript" type="text/javascript">
    var data = {"checks":<?php echo(json_encode($checks)); ?>};
    var checks = data.checks;
    var counter = 0;
    $(document).ready(function(){
        DoMessage("check");
    });

    function DoMessage(messageType) {
        if(messageType == "check") {
            ClearMessages()
            DoWriteMessage(
                "div#messages div.check",
                checks[counter].check,
                GetTime(checks[counter].check)
            );
            setTimeout(
                "DoMessage('wait')",
                (GetTime(checks[counter].check)* 1000) + 500
            );
        } else if (messageType == "wait") {
            DoWriteMessage(
                "div#messages div.wait",
                " . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .",
                GetTime(" . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .")
            );
            setTimeout(
                "DoMessage('message')",
                (GetTime(" . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .")*1000) + 500
            );
        } else if (messageType == "message") {
            var time = GetTime(checks[counter].message);
            DoWriteMessage("div#messages div.message", checks[counter].message, time);
            if(checks[counter].result != 1) {
                setTimeout('DoMessage("fail")', (time * 1000) + 500);
            }else if(counter < checks.length - 1) {
                counter++;
                setTimeout('DoMessage("check")', (time * 1000) + 500);
            } else {
                setTimeout('DoMessage("sucess")', (time * 1000) + 500);
            }
        } else if (messageType == "sucess") {
            ClearMessages()
            DoWriteMessage(
                "div#messages div.message",
                "Ok, happy days, thats all the checks in this step passed.",
                5);
            setTimeout('$("div#messages div.action").show();', 5500);
        } else if (messageType == "fail") {
            setTimeout('$("div#messages div.fail").show();', 1000);
        }
    }

    function ClearMessages() {
        $("div#messages div:not(.action, .fail)").each(function() {
            $(this).html("");
        });
    }
</script>
    <img id="logo-callout" src="assets/images/logo-callout.png" />
    <div id="baloon">
        <div class="top">&nbsp;</div>
        <div class="mid">
            <div id="messages">
                <div class="check"></div>
                <div class="wait"></div>
                <div class="message"></div>
                <div class="action" style="display:none;">
                    <p>Ok, the last bit now, lets go and finish up.</p>
                    <form action="step-urlrewrite-setup.php" method="GET">
                        <input type="submit" value="Let's Go..." class="button" />
                    </form>
                </div>
                <div class="fail" style="display:none;">
                    <p>Sorry about that! One of the tests we carried out failed.</p>
                    <p>Can you try to fix this problem and start the installation again?</p>
                </div>
            </div>
        </div>
        <div class="bottom">&nbsp;</div>
    </div>
</div>
<?php include_once("footer.php"); ?>