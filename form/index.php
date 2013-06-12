<div>
<?php echo $errore; ?></div> 
<div>
    <form action="subscribe.php" method="post">
                <div>
            name<input type="text" name="name" value="<?php echo $user["name"]; ?>" />
        </div>
                <div>
            surname<input type="text" name="surname" value="<?php echo $user["surname"]; ?>" />
        </div>
                <div>
            email<input type="text" name="email" value="<?php echo $user["email"]; ?>" />
        </div>
                <div>
            address<input type="text" name="address" value="<?php echo $user["address"]; ?>" />
        </div>
                <input type="submit" value="submit" />
    </form>
</div>