<div>
<?php
    echo "<?php echo \$errore; ?>";
?>
</div> 
<div>
    <form action="subscribe.php" method="post">
        <?php        foreach ($fields as $field)    { ?>
        <div>
            <?php echo $field; ?><input type="text" name="<?php echo $field; ?>" value="<?php echo "<?php echo \$user[\"$field\"]; ?>"; ?>" />
        </div>
        <?php } ?>
        <input type="submit" value="submit" />
    </form>
</div>