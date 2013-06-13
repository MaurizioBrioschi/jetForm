<div>
<?php
    echo "<?php echo \$errore; ?>";
?>
</div> 
<div>
    <form action="subscribe.php" method="post">
        <?php        foreach ($fields as $field)    { ?>
        <div>
            <?php echo $field; ?><input
                <?php if($field=='privacy1'||$field=='privacy2') { ?>
                         name="<?php echo $field; ?>" type="checkbox" value="1" <?php echo "<?php if(\$user[\"$field\"]>0) echo \"checked\"; ?>";?> />
                <?php }else { ?>              
                type="text" name="<?php echo $field; ?>" value="<?php echo "<?php echo \$user[\"$field\"]; ?>"; ?>" />
                <?php } ?>
        </div>
        <?php } ?>
        <input type="submit" value="submit" />
    </form>
</div>