<?php
require_once('../../backend/db/config.php');

if (isset($_POST['submit'])) {
    // Obtener los valores del formulario
    $name = $_POST['name'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $rol = 3;
    $status = 1;
    $img = $_FILES['image']['name'];
    $data_create = date('Y-m-d H:i:s');



    // Verificar si el correo electrónico ya existe
    $email_check_query = $conn->prepare("SELECT email FROM users WHERE email = :email");
    $email_check_query->bindParam(':email', $email);
    $email_check_query->execute();

    if ($email_check_query->rowCount() > 0) {
        // El correo electrónico ya existe, muestra un mensaje de error
        echo "El correo electrónico ya está registrado en la base de datos.";
    } else {
        // El correo electrónico no existe, procede con la inserción
        if ($_FILES['image']['size'] > 0) {
            $img = file_get_contents($_FILES['image']['tmp_name']);
        } else {
            $img = null;
        }

        // Insertar el registro en la tabla doctors
        $stmt = $conn->prepare("INSERT INTO patients (name, first_name, last_name, phone, email, img, status, data_create)  
                                        VALUES (:nombre, :first_name, :last_name, :phone, :email, :image, :status, :data_create)");
        $stmt->bindParam(':nombre', $name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':image', $img, PDO::PARAM_LOB);
        $stmt->bindParam(':data_create', $data_create);

        // Ejecutar la consulta de inserción en la tabla doctors
        if ($stmt->execute()) {
            // Insertar el registro en la tabla users
            $user_stmt = $conn->prepare("INSERT INTO users (name, email, password, rol) VALUES (:name, :email, :password, :rol)");
            $user_stmt->bindParam(':name', $name);
            $user_stmt->bindParam(':email', $email);
            $user_stmt->bindParam(':password', $password);
            $user_stmt->bindParam(':rol', $rol);

            if ($user_stmt->execute()) {
                // Ambas inserciones fueron exitosas
                echo "El registro se insertó correctamente.";
            } else {
                // La inserción en la tabla users falló
                echo "Error al insertar en la tabla users: " . $user_stmt->errorInfo()[2];
            }
        } else {
            // La inserción en la tabla patients falló
            echo "Error al insertar en la tabla patients: " . $stmt->errorInfo()[2];
        }
    }
}

if (isset($_POST['editar']) && $_POST['editar'] === 'editDoctor') {
    // Obtener los valores del formulario
    $id_patient = $_POST['id_patient'];
    $name = $_POST['name'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar y convertir la contraseña en caso de que no esté en formato MD5
    if (strlen($password) != 32) {
        $password = md5($password);
    }

    // Actualizar los datos en la tabla patients
    $sqlDoctors = "UPDATE patients SET name = :name, first_name = :first_name, last_name = :last_name, phone = :phone WHERE id_patient = :id_patient";
    $stmtDoctors = $conn->prepare($sqlDoctors);
    $stmtDoctors->bindParam(':name', $name);
    $stmtDoctors->bindParam(':first_name', $first_name);
    $stmtDoctors->bindParam(':last_name', $last_name);
    $stmtDoctors->bindParam(':phone', $phone);
    $stmtDoctors->bindParam(':id_patient', $id_patient);

    // Actualizar los datos en la tabla users
    $sqlUsers = "UPDATE users SET name = :name, password = :password WHERE email = :email";
    $stmtUsers = $conn->prepare($sqlUsers);
    $stmtUsers->bindParam(':name', $name);
    $stmtUsers->bindParam(':email', $email);
    $stmtUsers->bindParam(':password', $password);

    // Ejecutar ambas consultas en una transacción para mantener la coherencia de los datos
    try {
        $conn->beginTransaction();

        if ($stmtDoctors->execute() && $stmtUsers->execute()) {
            $conn->commit();
            echo "Actualización exitosa";
        } else {
            $conn->rollBack();
            echo "Error al actualizar";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// Eliminar un registro de la tabla doctors

if (isset($_POST['eliminar']) && $_POST['eliminar'] === 'deleteDoctor') {
    // Obtener los valores del formulario
    $id_patient = $_POST['id_patient'];
    $email = $_POST['email'];

    // Realizar la eliminación en la tabla doctors
    $sqlDeleteDoctor = "DELETE FROM patients WHERE id_patient = :id_patient";
    $stmtDeleteDoctor = $conn->prepare($sqlDeleteDoctor);
    $stmtDeleteDoctor->bindParam(':id_patient', $id_patient);

    // Realizar la eliminación en la tabla users a través del correo electrónico
    $sqlDeleteUser = "DELETE FROM users WHERE email = :email";
    $stmtDeleteUser = $conn->prepare($sqlDeleteUser);
    $stmtDeleteUser->bindParam(':email', $email);

    try {
        $conn->beginTransaction();

        // Ejecutar ambas consultas en una transacción para mantener la coherencia de los datos
        if ($stmtDeleteDoctor->execute() && $stmtDeleteUser->execute()) {
            $conn->commit();
            echo "Eliminación exitosa";
        } else {
            $conn->rollBack();
            echo "Error al eliminar";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}


require_once('../layouts/headerAdmin.php');
?>
<div id="root">
    <div id="nav" class="nav-container d-flex">
        <?php include_once('../layouts/navAdmin.php'); ?>
        <div class="nav-shadow"></div>
    </div>

    <main>
        <div class="container">
            <!-- Title and Top Buttons Start -->
            <div class="page-title-container">
                <div class="row">
                    <!-- Title Start -->
                    <div class="col-12 col-md-7">
                        <a href="Dashboards.Admin.php" class="muted-link pb-1 d-inline-block breadcrumb-back">
                            <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
                            <span class="text-small align-middle">Home</span>
                        </a>
                        <h1 class="mb-0 pb-0 display-4" id="title">Pacientes</h1>
                    </div>
                    <!-- Title End -->
                    <!-- Top Buttons Start -->
                    <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                        <!-- Add New Button Start -->
                        <button class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                            <i data-acorn-icon="plus"></i>
                            <span>Agregar</span>
                        </button>
                        <!-- Add New Button End -->
                    </div>
                    <!-- Top Buttons End -->
                </div>
            </div>
            <!-- Title and Top Buttons End -->

            <!-- Controls Start -->
            <div class="row mb-2">
                <!-- Search Start -->
                <div class="col-sm-12 col-md-5 col-lg-3 col-xxl-2 mb-1">
                    <div class="d-inline-block float-md-start me-1 mb-1 search-input-container w-100 shadow bg-foreground">
                        <input class="form-control" placeholder="Buscar" />
                        <span class="search-magnifier-icon">
                            <i data-acorn-icon="search"></i>
                        </span>
                        <span class="search-delete-icon d-none">
                            <i data-acorn-icon="close"></i>
                        </span>
                    </div>
                </div>
                <!-- Search End -->

                <div class="col-sm-12 col-md-7 col-lg-9 col-xxl-10 text-end mb-1">
                    <div class="d-inline-block">
                        <!-- Export Dropdown Start -->
                        <div class="d-inline-block">
                            <button class="btn p-0" data-bs-toggle="dropdown" type="button" data-bs-offset="0,3">
                                <span class="btn btn-icon btn-icon-only btn-foreground-alternate shadow dropdown" data-bs-delay="0" data-bs-placement="top" data-bs-toggle="tooltip" title="Export">
                                    <i data-acorn-icon="download"></i>
                                </span>
                            </button>
                            <div class="dropdown-menu shadow dropdown-menu-end">
                                <button class="dropdown-item export-copy" type="button">Copy</button>
                                <button class="dropdown-item export-excel" type="button">Excel</button>
                                <button class="dropdown-item export-cvs" type="button">Cvs</button>
                            </div>
                        </div>
                        <!-- Export Dropdown End -->

                        <!-- Length Start -->
                        <div class="dropdown-as-select d-inline-block ms-1" data-childSelector="span">
                            <button class="btn p-0 shadow" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-offset="0,3">
                                <span class="btn btn-foreground-alternate dropdown-toggle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-delay="0" title="Item Count">
                                    10 Items
                                </span>
                            </button>
                            <div class="dropdown-menu shadow dropdown-menu-end">
                                <a class="dropdown-item" href="#">5 Items</a>
                                <a class="dropdown-item active" href="#">10 Items</a>
                                <a class="dropdown-item" href="#">20 Items</a>
                            </div>
                        </div>
                        <!-- Length End -->
                    </div>
                </div>
            </div>
            <!-- Controls End -->

            <!-- Doctors Start -->
            <div>
                <div class="card">
                    <div class="card-body">
                        <table id="myTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Expediente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <?php
                            $sql = "SELECT p.id_patient,
                        p.name,
                        p.first_name,
                        p.last_name,
                        p.phone,
                        p.email,
                        p.img,
                        u.password
                    from patients p
                    inner join users u
                    on p.email = u.email
                    where u.rol = 3
                    order by p.id_patient desc";
                            $select_user = $conn->query($sql);
                            if ($select_user !== false && $select_user->rowCount() > 0) {
                                while ($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="sw-6 me-1 mb-1 d-inline-block">
                                                    <a href="Patients.Detail.php?id_patient=<?php echo $fetch_user['id_patient']; ?>">
                                                    <img src="<?php echo !empty($fetch_user['img']) ? 'data:image/jpeg;base64,' . base64_encode($fetch_user['img']) : '../img/profile/user.jpeg'; ?>" class="img-fluid rounded-xl" alt="thumb">
                                                    </a>
                                                </div>
                                            </td>
                                            <td><?php echo $fetch_user['name']; ?> <?php echo $fetch_user['first_name']; ?> <?php echo $fetch_user['last_name']; ?> </td>
                                            <td><?php echo $fetch_user['phone']; ?></td>
                                            <td><?php echo $fetch_user['email']; ?></td>
                                            <td>
                                                <a type="button" class="btn btn-outline-primary btn-ms mb-1 btn-icon btn-icon-start" href="Patients.Detail.php?id_patient=<?php echo $fetch_user['id_patient']; ?>">
                                                    <i data-acorn-icon="clipboard"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-primary btn-ms mb-1 btn-icon btn-icon-start" data-bs-toggle="offcanvas" data-bs-target="#editModal<?php echo $fetch_user['id_patient']; ?>">
                                                    <i data-acorn-icon="edit-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-ms mb-1 btn-icon btn-icon-start" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?php echo $fetch_user['id_patient']; ?>">
                                                    <i data-acorn-icon="bin"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <!-- Confirm Delete Modal Start -->
                                    <div class="modal fade" id="confirmDeleteModal<?php echo $fetch_user['id_patient']; ?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de que deseas eliminar a <?php echo $fetch_user['name']; ?> <?php echo $fetch_user['first_name']; ?> <?php echo $fetch_user['last_name']; ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="post" action="">
                                                        <input type="hidden" name="id_patient" value="<?php echo $fetch_user['id_patient']; ?>">
                                                        <input type="hidden" name="email" value="<?php echo $fetch_user['email']; ?>">
                                                        <button type="submit" class="btn btn-danger" name="eliminar" value="deleteDoctor">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- fin del modal de confirmación de eliminación -->
                                    <!-- Offcanvas Start -->
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="editModal<?php echo $fetch_user['id_patient']; ?>" aria-labelledby="editModalLabel" style="visibility: visible;margin-top: 0px;" role="dialog">
                                        <div class="offcanvas-header">
                                            <h5 id="editModalLabel">Editar Paciente</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <form method="post" action="" enctype="multipart/form-data">
                                                <div style="padding-left: 40px;">
                                                    <img src="<?php echo !empty($fetch_user['img']) ? 'data:image/jpeg;base64,' . base64_encode($fetch_user['img']) : '../img/profile/user.jpeg'; ?>" alt="Imagen vista previa" class="img-fluid rounded mb-1 me-1 sh-19" alt="Responsive image" />
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="name" placeholder="nombre" value="<?php echo $fetch_user['name']; ?>" />
                                                    <label>Nombre</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="first_name" placeholder="ApellidoPaterno" value="<?php echo $fetch_user['first_name']; ?>" />
                                                    <label>Apellido Paterno</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="last_name" placeholder="ApellidoMaterno" value="<?php echo $fetch_user['last_name']; ?>" />
                                                    <label>Apellido Materno</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="phone" placeholder="Telefono" value="<?php echo $fetch_user['phone']; ?>" />
                                                    <label>Telefono</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="email" class="form-control" name="email" placeholder="Correo" value="<?php echo $fetch_user['email']; ?>" disabled />
                                                    <label>Correo</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="password" class="form-control" name="password" placeholder="Contrasena" value="<?php echo $fetch_user['password']; ?>" />
                                                    <label>Contrasena</label>
                                                </div>
                                                <input type="hidden" name="id_patient" value="<?php echo $fetch_user['id_patient']; ?>">
                                                <input type="hidden" name="email" value="<?php echo $fetch_user['email']; ?>">
                                                <div class="modal-footer" style="border-right-style: solid;padding-right: 50px;">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="offcanvas">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary" name="editar" value="editDoctor">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- Fin del modal de edición -->
                            <?php
                                };
                            };
                            ?>
                            <tfoot>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Expediente</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Doctors End -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">Agregar Medico</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div id="iconsNotify" class="offcanvas-body">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div style="padding-left: 40px;">
                            <img id="imagePreview" src="#" alt="Imagen vista previa" class="img-fluid rounded mb-1 me-1 sh-19" alt="Responsive image" />
                        </div>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" name="image" id="imageInput" accept="image/*" onchange="previewImage(event)" />
                            <label>Foto</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="name" placeholder="nombre" />
                            <label>Nombre</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="first_name" placeholder="ApellidoPaterno" />
                            <label>Apellido Paterno</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="last_name" placeholder="ApellidoMaterno" />
                            <label>Apellido Materno</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="phone" placeholder="Descripción" />
                            <label>Telefono</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Correo" />
                            <label>Correo</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Contrasena" />
                            <label>Contrasena</label>
                        </div>
                        <div class="modal-footer" style="border-right-style: solid;padding-right: 100px;">
                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="offcanvas">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="submit" value="register now">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!-- Layout Footer Start -->
    <?php include_once('../layouts/footer.php'); ?>
    <!-- Layout Footer End -->
</div>
<?php require_once('../layouts/scripts.php'); ?>