<div id="admin-menu" class="clearfix">
    <ul>
        <?php if($loggedIn) : ?>
            <li><a href="javascript:LogOut();">Log Out</a></li>
        <?php else : ?>
            <li><a href="javascript:LogIn();">Log In</a></li>
        <?php endif; ?>
    </ul>
</div>
