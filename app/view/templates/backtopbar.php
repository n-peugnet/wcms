<header id="topbar">

<span id="search" class="hidephone">
<form action="<?= $this->url('search') ?>" method="post">
<input type="text" list="searchdatalist" name="id" id="search" placeholder="page id" required <?= $tab !== 'edit' && !$user->isvisitor() ? 'autofocus' : '' ?>>
<input type="submit" name="action" value="read">
<?= $user->iseditor() ? '<input type="submit" name="action" value="edit">' : '' ?>

<?php if($user->iseditor()) { ?>
<datalist id="searchdatalist">
    <?php foreach ($pagelist as $id) { ?>
        <option value="<?= $id ?>"><?= $id ?></option>
    <?php } ?>
</datalist>
<?php } ?>

</form>
</span>



<?php if($user->iseditor()) { ?>

<span id="menu">
<a href="<?= $this->url('home') ?>" <?= $tab == 'home' ? 'class="actualpage"' : '' ?>>
    <img src="<?= Wcms\Model::iconpath() ?>home.png" alt="" class="icon">
    <span class="hidephone">home</span>
</a>
<a href="<?= $this->url('media') ?>" <?= $tab == 'media' ? 'class="actualpage"' : '' ?>>
    <img src="<?= Wcms\Model::iconpath() ?>media.png" alt="" class="icon">
    <span class="hidephone">media</span>
</a>
<?php
if($user->isadmin()) {
?>
<a href="<?= $this->url('admin') ?>" <?= $tab == 'admin' ? 'class="actualpage"' : '' ?>>
    <img src="<?= Wcms\Model::iconpath() ?>admin.png" alt="" class="icon">
    <span class="hidephone">admin</span>

</a>
<?php
}
?>
<a href="<?= $this->url('info') ?>"  <?= $tab == 'info' ? 'class="actualpage"' : '' ?>>
    <img src="<?= Wcms\Model::iconpath() ?>info.png" alt="" class="icon">
    <span class="hidephone">info</span>
</a>
</span>





<?php } ?>



<span id="user">

<?php if($user->isvisitor()) { ?>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="password" name="pass" id="loginpass" placeholder="password" autofocus>
<input type="hidden" name="route" value="home">
<input type="hidden" name="rememberme" value="0">
<input type="checkbox" name="rememberme" id="rememberme" value="1">
<label for="rememberme">Remember me</label>
<input type="submit" name="log" value="login">
</form>


<?php } else { ?>  

<span>

<a href="<?= $this->url('user') ?>" <?= $tab == 'user' ? 'class="actualpage"' : '' ?>>
    <img src="<?= Wcms\Model::iconpath() ?>user.png" alt="" class="icon">
    <span class="hidephone"><?= $user->id() ?></span>
</a>
<i><?= $user->level() ?></i>
</span>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="submit" name="log" value="logout" >
<?php if($tab === 'edit') { ?>
    <input type="hidden" name="route" value="pageread/">
    <input type="hidden" name="id" value="<?= $pageid ?>">
<?php } ?>

</form>



</span>




<?php } ?>

</header>