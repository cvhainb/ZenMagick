<h1>Hello world!</h1>

<?php if (ZMMessages::instance()->hasMessages()) { ?>
    <ul id="messages">
    <?php foreach (ZMMessages::instance()->getMessages() as $message) { ?>
        <li class="<?php echo $message->getType() ?>"><?php echo $message->getText() ?></li>
    <?php } ?>
    </ul>
<?php } ?>

<form action="#" method="POST">
  <p>Tell me your name?</p>
  <p>
    <input type="text" name="myname" value="">
    <input type="submit" value="submit">
  </p>
</form>
<?php if (isset($name)) { ?>
  <p>Your name is: <?php echo $name ?>.</p>
<?php } ?>
<p>Context is: <?php echo $request->getContext() ?></p>
