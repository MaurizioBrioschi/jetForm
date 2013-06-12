<?php
 
                include('../include.php');

                $user = array(); //arry che identifica lo user
$user["name"] = isset($_POST["name"]) ? $_POST["name"]: '';
$user["surname"] = isset($_POST["surname"]) ? $_POST["surname"]: '';
$user["email"] = isset($_POST["email"]) ? $_POST["email"]: '';
$user["address"] = isset($_POST["address"]) ? $_POST["address"]: '';
$validator = new ValidatorForm($repository->getConnection(),array("email"=>$user["email"]));

    $errore = "";

    if($validator->getIsFormValid())    {

            //inserisco lo user a database

            $user_id= $repository->genericInsert("users",$user);
header('Location: confirm.html');

                   exit;   
}else{

        $errore = $validator->getErrorMessage();

    }

    ?>
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