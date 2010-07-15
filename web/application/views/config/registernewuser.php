<div id="register-new-user">
    <div class="form">
        <div class="alert" style="display:none;">
            <ul></ul>
        </div>
        <div class="form-row">
            <label for="username">Their username:</label>
            <input type="text" name="username" />
        </div>
        <div class="form-row">
            <label for="password">Their password:</label>
            <input type="text" name="password" />
        </div>
        <div class="form-row">
            <label for="role">Their role:</label>
            <select name="role">
                <option value="user" selected="selected">User</option>
                <option value="sweeper">Sweeper</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-row">
            <input type="submit" value="Add this user" onclick="ValidateAndTryRegister()" />
        </div>
    </div>
</div>
<script type="text/javascript" language="javascript">
    function ValidateAndTryRegister() {
        $("div#register-new-user div.alert").slideUp("slow");
        $("div#register-new-user div.alert ul").children().remove();

        var username = $("div#register-new-user input[name=username]").val();
        var password = $("div#register-new-user input[name=password]").val();
        var role = $("div#register-new-user select[name=role] option:selected").val();

        if(username == "" || password == "") {
            $("div#register-new-user div.alert ul").append("<li>You have to enter both a username and password</li>");
            $("div#register-new-user div.alert").slideDown();
            return;
        }

        $.post(
            "<?php echo(url::base()); ?>config/user/register",
            { "username" : username, "password" : password, "role" :role },
            function(data) {
                Shadowbox.close();
                return;
            },
            "json"
        );
    }
</script>
