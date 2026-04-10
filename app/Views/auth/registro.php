<?= $this->extend('layaouts/main') ?>

<?= $this->section('content') ?>
<div class="login-page">
    <div class="container-fluid d-flex justify-content-center py-5 rounded">
        <div class="row mx-auto">
            
            <!--Lado izquierdo-->
                <div class="col-md-6 login-left d-flex  justify-content-center">
                    <!--Título-->
                    <div style="width: 100%; max-width: 400px;">
                        <h1 class="fw-bold mb-4">TimeTurner</h1>
                        <h2 class="mb-4">Registro</h2>

                        <form action="<?= base_url('registro') ?>" method="post">

                            <!-- Input del nombre -->
                            <div class="mb-3 position-relative">
                                <label class="form-label">Nombre</label>
                                <span class="material-symbols-outlined input-icon">person</span>
                                <input type="text" name="nombre" class="form-control ps-5" placeholder="Mar">
                            </div>

                            <!-- Input del apellido -->
                            <div class="mb-3 position-relative">
                                <label class="form-label">Apellidos</label>
                                <span class="material-symbols-outlined input-icon">person</span>
                                <input type="text" name="apellidos" class="form-control ps-5" placeholder="Marun">
                            </div>

                            <!--Input del email-->
                            <div class="mb-3 position-relative">
                                <label class="form-label">Email</label>
                                <span class="material-symbols-outlined input-icon">mail</span>
                                <input type="email" name="email" class="form-control ps-5" placeholder="mar@outlock.com">
                            </div>
                            
                            <!--Input de contraseña-->
                            <div class="mb-3 position-relative">
                                <label class="form-label">Contraseña</label>
                                <span class="material-symbols-outlined input-icon">lock</span>
                                <input type="password" name="contraseña" class="form-control ps-5">
                            </div>
                            <div class="mb-3 position-relative">
                                <label class="form-label">Confirma contraseña</label>
                                <span class="material-symbols-outlined input-icon">lock</span>
                                <input type="password" name="ccontraseña" class="form-control ps-5">
                            </div>

                            <!--botón-->
                            <button type="submit" class="btn btn-primary w-100">Registrarse</button>

                        </form>

                    </div>

                </div>

            <!--Lado derecho-->
                <div class="col-md-6 login-right d-none d-md-flex align-items-center justify-content-center">
                    <div class="text-center px-5">
                        <!--Logo-->
                        <div class="mb-4">
                            <img src="/assets/imagen/logo.jpg" class="img-fluid  rounded" style="max-width: 200px;" alt="Logo Timeturner">
                        </div>

                        <!--Título-->
                        <h2 class="fw-bold mb-3">
                            TimeTurner
                        </h2>

                        <!--Texto-->
                        <p>
                            La mejor forma de poder gestionar tus horarios de trabajo.
                        </p>
                    </div>
                </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>