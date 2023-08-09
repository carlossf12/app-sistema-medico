<?php
require_once('../../backend/db/config.php');

if (isset($_POST['submit'])) {
  // Obtener los valores del formulario
  $name = $_POST['name'];
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $specialty_id = $_POST['specialty'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $password = md5($_POST['password']);
  $rol = 2;
  $img = $_FILES['image']['name'];
  $data_create = date('Y-m-d H:i:s');

  // Verificar si el correo electrónico ya existe
  $email_check_query = $conn->prepare("SELECT id_doctor FROM doctors WHERE email = :email");
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
    $stmt = $conn->prepare("INSERT INTO doctors (name, first_name, last_name, phone, email, specialty, img, data_create) VALUES (:nombre, :first_name, :last_name, :phone, :email, :specialty, :image, :data_create)");
    $stmt->bindParam(':nombre', $name);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':specialty', $specialty_id);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':email', $email);
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
      // La inserción en la tabla doctors falló
      echo "Error al insertar en la tabla doctors: " . $stmt->errorInfo()[2];
    }
  }
}

if (isset($_POST['editar']) && $_POST['editar'] === 'editDoctor') {
  // Obtener los valores del formulario
  $doctorId = $_POST['doctor_id'];
  $name = $_POST['name'];
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $specialty = $_POST['specialty'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Verificar y convertir la contraseña en caso de que no esté en formato MD5
  if (strlen($password) != 32) {
    $password = md5($password);
  }

  // Actualizar los datos en la tabla doctors
  $sqlDoctors = "UPDATE doctors SET name = :name, first_name = :first_name, last_name = :last_name, specialty = :specialty, phone = :phone WHERE id_doctor = :doctor_id";
  $stmtDoctors = $conn->prepare($sqlDoctors);
  $stmtDoctors->bindParam(':name', $name);
  $stmtDoctors->bindParam(':first_name', $first_name);
  $stmtDoctors->bindParam(':last_name', $last_name);
  $stmtDoctors->bindParam(':specialty', $specialty);
  $stmtDoctors->bindParam(':phone', $phone);
  $stmtDoctors->bindParam(':doctor_id', $doctorId);

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
  $doctorId = $_POST['doctor_id'];
  $email = $_POST['email'];

  // Realizar la eliminación en la tabla doctors
  $sqlDeleteDoctor = "DELETE FROM doctors WHERE id_doctor = :doctor_id";
  $stmtDeleteDoctor = $conn->prepare($sqlDeleteDoctor);
  $stmtDeleteDoctor->bindParam(':doctor_id', $doctorId);

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
            <h1 class="mb-0 pb-0 display-4" id="title">Doctores</h1>
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

      <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 row-cols-xxl-4 g-4 mb-5">
        <?php
        $sql = "SELECT d.id_doctor,
                        d.name,
                        d.first_name,
                        d.last_name,
                        d.phone,
                        d.email,
                        s.name as specialty,
                        s.id_specialty,
                        d.img,
                        u.password
                    from doctors d
                    inner join specialty s 
                    on d.specialty = s.id_specialty 
                    inner join users u
                    on d.email = u.email
                    where u.rol = 2
                    order by d.id_doctor desc";
        $select_doc = $conn->query($sql);
        if ($select_doc !== false && $select_doc->rowCount() > 0) {
          while ($fetch_doc = $select_doc->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <div class="col">
              <div class="card h-100">
                <a href="Doctors.Detail.html">
                  <img src="<?php echo !empty($fetch_doc['img']) ? 'data:image/jpeg;base64,' . base64_encode($fetch_doc['img']) : '../img/profile/profile-large-4.webp'; ?>" class="card-img-top sh-30" alt="card image" />
                </a>
                <div class="card-body" style="padding-left: 20px;padding-right: 20px;">
                  <a href="Doctors.Detail.html"><?php echo $fetch_doc['name']; ?></a>
                  <div class="text-muted mb-1"><?php echo $fetch_doc['first_name']; ?> <?php echo $fetch_doc['last_name']; ?></div>
                  <div class="mb-3">
                    <div class="br-wrapper br-theme-cs-icon d-inline-block">
                      <select class="rating" name="rating" autocomplete="off" data-readonly="true" data-initial-rating="5">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                      </select>
                    </div>
                    <div class="text-muted d-inline-block text-small align-text-top">(12)</div>
                  </div>
                  <div class="row g-0 align-items-center mb-2">
                    <div class="col-auto">
                      <div class="sw-3 d-flex justify-content-center align-items-center">
                        <i data-acorn-icon="health" class="text-primary"></i>
                      </div>
                    </div>
                    <div class="col ps-3">
                      <div class="d-flex align-items-center lh-1-25"><?php echo $fetch_doc['specialty']; ?></div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center mb-2">
                    <div class="col-auto">
                      <div class="sw-3 d-flex justify-content-center align-items-center">
                        <i data-acorn-icon="phone" class="text-primary"></i>
                      </div>
                    </div>
                    <div class="col ps-3">
                      <div class="d-flex align-items-center lh-1-25"><?php echo $fetch_doc['phone']; ?></div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center mb-2" style="padding-bottom: 30px;">
                    <div class="col-auto">
                      <div class="sw-3 d-flex justify-content-center align-items-center">
                        <i data-acorn-icon="email" class="text-primary"></i>
                      </div>
                    </div>
                    <div class="col ps-3">
                      <div class="d-flex align-items-center lh-1-25"><?php echo $fetch_doc['email']; ?></div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-outline-primary btn-ms mb-1 btn-icon btn-icon-start" data-bs-toggle="offcanvas" data-bs-target="#editModal<?php echo $fetch_doc['id_doctor']; ?>">
                    <i data-acorn-icon="edit-square"></i>
                    <span>Editar</span>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-ms mb-1 btn-icon btn-icon-start" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?php echo $fetch_doc['id_doctor']; ?>">
                    <i data-acorn-icon="bin"></i>
                    <span>Eliminar</span>
                  </button>
                </div>
              </div>
            </div>
            <!-- Confirm Delete Modal Start -->
            <div class="modal fade" id="confirmDeleteModal<?php echo $fetch_doc['id_doctor']; ?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar a <?php echo $fetch_doc['name']; ?> <?php echo $fetch_doc['first_name']; ?> <?php echo $fetch_doc['last_name']; ?>?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="post" action="">
                      <input type="hidden" name="doctor_id" value="<?php echo $fetch_doc['id_doctor']; ?>">
                      <input type="hidden" name="email" value="<?php echo $fetch_doc['email']; ?>">
                      <button type="submit" class="btn btn-danger" name="eliminar" value="deleteDoctor">Eliminar</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- fin del modal de confirmación de eliminación -->
            <!-- Offcanvas Start -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="editModal<?php echo $fetch_doc['id_doctor']; ?>" aria-labelledby="editModalLabel" style="visibility: visible;margin-top: 0px;" role="dialog">
              <div class="offcanvas-header">
                <h5 id="editModalLabel">Editar Médico</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <form method="post" action="" enctype="multipart/form-data">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" placeholder="nombre" value="<?php echo $fetch_doc['name']; ?>" />
                    <label>Nombre</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="first_name" placeholder="ApellidoPaterno" value="<?php echo $fetch_doc['first_name']; ?>" />
                    <label>Apellido Paterno</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="last_name" placeholder="ApellidoMaterno" value="<?php echo $fetch_doc['last_name']; ?>" />
                    <label>Apellido Materno</label>
                  </div>
                  <div class="mb-3 w-100">
                    <label class="form-label">Especialidad</label>
                    <select name="specialty" id="selectBasic">
                      <option label="&nbsp;"></option>
                      <?php
                      $sql = "SELECT s.id_specialty,
                          s.name
                  from specialty s";
                      $select_spe = $conn->query($sql);
                      if ($select_spe !== false && $select_spe->rowCount() > 0) {
                        while ($fetch_spe = $select_spe->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                          <option value="<?php echo $fetch_spe['id_specialty']; ?>" <?php if ($fetch_spe['id_specialty'] == $fetch_doc['id_specialty']) echo 'selected'; ?>><?php echo $fetch_spe['name']; ?></option>
                      <?php
                        };
                      };
                      ?>
                    </select>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="phone" placeholder="Telefono" value="<?php echo $fetch_doc['phone']; ?>" />
                    <label>Telefono</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Correo" value="<?php echo $fetch_doc['email']; ?>" disabled />
                    <label>Correo</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Contrasena" value="<?php echo $fetch_doc['password']; ?>" />
                    <label>Contrasena</label>
                  </div>
                  <input type="hidden" name="doctor_id" value="<?php echo $fetch_doc['id_doctor']; ?>">
                  <input type="hidden" name="email" value="<?php echo $fetch_doc['email']; ?>">
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
            <div class="mb-3 w-100">
              <label class="form-label">Especialidad</label>
              <select name="specialty" id="selectBasic">
                <option label="&nbsp;"></option>
                <?php
                $sql = "SELECT s.id_specialty,
                                s.name
                        from specialty s";
                $select_spe = $conn->query($sql);
                if ($select_spe !== false && $select_spe->rowCount() > 0) {
                  while ($fetch_spe = $select_spe->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <option value="<?php echo $fetch_spe['id_specialty']; ?>"><?php echo $fetch_spe['name']; ?></option>
                    <?php
                  };
                };
                    ?>>
              </select>
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