<section class="grid-100 grid-parent">
    <article class="grid-100">
        <h2>Connexion</h2>
        <?php echo validation_errors(); ?>
        <?php echo form_open('login'); ?>
            <label for="username">Utilisateur :</label>
            <input type="text" size="20" id="username" name="username"/>
            <br/>
            <label for="password">Mot de passe :</label>
            <input type="password" size="20" id="passowrd" name="password"/>
            <br/>
            <input type="submit" value="Se connecter"/>
        <?php echo form_close(); ?>
    </article>
</section>