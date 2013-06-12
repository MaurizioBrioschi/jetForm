<?php
 
                include('../include.php');

                $user = array(); //arry che identifica lo user
$user["nome"] = isset($_POST["nome"]) ? $_POST["nome"]: '';
$user["cognome"] = isset($_POST["cognome"]) ? $_POST["cognome"]: '';
$user["email"] = isset($_POST["email"]) ? $_POST["email"]: '';
$user["indirizzo"] = isset($_POST["indirizzo"]) ? $_POST["indirizzo"]: '';
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
    <form action="iscrizione.php" method="post">
                <div>
            nome<input type="text" name="nome" value="<?php echo $user["nome"]; ?>" />
        </div>
                <div>
            cognome<input type="text" name="cognome" value="<?php echo $user["cognome"]; ?>" />
        </div>
                <div>
            email<input type="text" name="email" value="<?php echo $user["email"]; ?>" />
        </div>
                <div>
            indirizzo<input type="text" name="indirizzo" value="<?php echo $user["indirizzo"]; ?>" />
        </div>
                <input type="submit" value="submit" />
    </form>
</div>