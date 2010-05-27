<?php include_once("header.php"); ?>
<?php
    function ChangeHtaccessFile($rewrite) {
        try {
            $htaccessFile = file(dirname(__FILE__)."/../web/.htaccess");
            $handle = fopen(dirname(__FILE__)."/../web/.htaccess", "w");
            foreach($htaccessFile as $lineNumber => $line) {
                if(strpos(" ".$line, "RewriteBase") != 0) {
                    $lineToWrite = ($rewrite == "")
                        ? "RewriteBase /web/ \n"
                        : "RewriteBase " . $rewrite . "/web/ \n";
                    fwrite($handle, $lineToWrite);
                } else {
                    fwrite($handle, $line);
                }
            }
            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }

    function ChangeBootstrapFile($rewrite) {
        try {
            $bootstrapFile = file(dirname(__FILE__)."/../web/application/bootstrap.php");
            $handle = fopen(dirname(__FILE__)."/../web/application/bootstrap.php", "w");
            foreach($bootstrapFile as $lineNumber => $line) {
                if(strpos(" " . $line, "'base_url'") != 0) {
                    $lineToWrite = ($rewrite == "")
                        ? "'base_url' => '/web/', \n"
                        : "'base_url' => '" . $rewrite . "/web/', \n";
                    fwrite($handle, $lineToWrite);
                } else {
                    fwrite($handle, $line);
                }
            }
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }

    function ChangeIndexFile($rewrite) {
        try {
            $bootstrapFile = file(dirname(__FILE__)."/../index.php");
            $handle = fopen(dirname(__FILE__)."/../index.php", "w");
            foreach($bootstrapFile as $lineNumber => $line) {
                if(strpos(" " . $line, "header") != 0) {
                    $lineToWrite = ($rewrite == "")
                        ? "header(\"Location: web/\"); \n"
                        : "header(\"Location: " . substr($rewrite, 1) . "web/\"); \n";
                    fwrite($handle, $lineToWrite);
                } else {
                    fwrite($handle, $line);
                }
            }
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }

    $rewriteBase = substr($_SERVER["REQUEST_URI"],0,stripos($request_uri,'/installer/'));

    $checks = array();

    $check->check = "First off I'm going to make a change to the .htaccess file.";
    $check->result = ChangeHtaccessFile($rewriteBase);
    $check->message = $check->result
                     ? "Fine, did it,"
                     : "Oh thats a shame, somethign went wrong while trying to write to the " .
                       ".htaccess file, is it there? are the permissions set so I can write to it?";

    $checks[] = $check;
    unset($check);

    $check->check = "No I'm goign to try and change the bootstrap.php file.";
    $check->result = ChangeBootstrapFile($rewriteBase);
    $check->message = $check->result
                   ? "No problems here, I did it."
                   : "Oh no, I had a few probelms while trying to write to the " .
                     "bootstrap.php file, is it there? are the permissions set so I can write to it?";

    $checks[] = $check;

    unset($check);

    $check->check = "Finally, lets turn the installer off and start the app going.";
    $check->result = ChangeIndexFile($rewriteBase);
    $check->message = $check->result
                   ? "No problems here, I did it."
                   : "Oh no, I had a few probelms while trying to write to the " .
                     "index.php file in the root?, is it there? are the permissions set so I can write to it?";

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
                "Ok, we are finished, that was fun wasn't it? Well you havent seen anything yet, you wait till we start the application going.",
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
                    <p>Ok, were done! Lets go play.</p>
                    <form action="../index.php" method="GET">
                        <input type="submit" value="lets go ..." class="button" />
                    </form>
                </div>
                <div class="fail" style="display:none;">
                    <p>Sorry about that! One of the tests I carried out failed.</p>
                    <p>Can you try to fix this problem and start the installation again?</p>
                </div>
            </div>
        </div>
        <div class="bottom">&nbsp;</div>
    </div>
</div>
<?php include_once("footer.php"); ?>