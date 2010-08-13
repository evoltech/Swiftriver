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
                <option value="sweeper" selected="selected">Sweeper</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-row">
            <input type="submit" value="Add this user" onclick="ValidateAndTryRegister()" />
        </div>
    </div>
</div>