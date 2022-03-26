<?php require_once $helper->layouts('head'); ?>

<body class="text-center d-flex justify-content-center align-items-center vh-100">

<main class="form-signin">
    <form id="login-form">
        <img class="mb-4" src="<?php echo $helper->url('resources/images/logo.svg') ?>" alt="">
        <h6 class="h5 mb-3 fw-normal text-ims">Por favor, ingrese sus datos <br> para iniciar sesión</h6>
        <div class="form-floating">
            <input type="email" class="form-control" id="floatingInput" placeholder="Usuario">
            <label for="floatingInput">Usuario</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" placeholder="Contraseña">
            <label for="floatingPassword">Contraseña</label>
        </div>
        <button class="w-100 mt-4 btn btn-lg btn-primary" type="submit">Iniciar Sesión</button>
    </form>
</main>

<?php  require_once $helper->layouts('footer'); ?>