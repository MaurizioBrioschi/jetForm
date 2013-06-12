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