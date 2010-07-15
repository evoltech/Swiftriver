<div id="login">
    <div class="form">
        <div class="alert" style="display:none;">
            <ul></ul>
        </div>
        <div class="form-row">
            <label for="username">Your username:</label>
            <input type="text" name="username" />
        </div>
        <div class="form-row">
            <label for="password">Your password:</label>
            <input type="password" name="password" />
        </div>
        <div class="form-row">
            <input type="submit" value="login" onclick="ValidateAndTryLogin()" />
        </div>
    </div>
</div>
<script type="text/javascript" language="javascript">
    function ValidateAndTryLogin() {
        $("div#login div.alert").slideUp("slow");
        $("div#login div.alert ul").children().remove();

        var username = $("div#login input[name=username]").val();
        var password = $("div#login input[name=password]").val();

        if(username == "" || password == "") {
            $("div#login div.alert ul").append("<li>You have to enter both a username and password</li>");
            $("div#login div.alert").slideDown();
            return;
        }

        $.post(
            "<?php echo(url::base()); ?>config/user/login",
            { "username" : username, "password" : password },
            function(data) {
                if(data.result) {
                    Shadowbox.close();
                    window.location = "<?php echo(url::base()); ?>";
                    return;
                }
                $("div#login div.alert ul").append("<li>The username and password you entered didn't match any in the data base, please try again.</li>");
                $("div#login div.alert").slideDown();
            },
            "json"
        );
    }
</script>