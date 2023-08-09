<?php
if (isset($_GET['id_patient'])) {
  $id_patient = $_GET['id_patient'];
} else {
  header('Location: Patientes.php');
}
require_once('../../backend/db/config.php');

if (isset($_POST['submit'])) {
  // Obtener los valores del formulario  
  $id_paciente = $id_patient;
  $file = $_FILES['image']['name'];
  $name = $_POST['name'];


  // Insertar el registro en la tabla doctors
  $stmt = $conn->prepare("INSERT INTO expedientes (id_paciente, file, description)  
                                      VALUES (:id_patient, :file, :description)");
  $stmt->bindParam(':id_patient', $id_paciente);
  $stmt->bindParam(':file', $file);
  $stmt->bindParam(':description', $name);

  if ($stmt->execute()) {
    echo "El registro se insertó correctamente.";
  } else {
    // La inserción en la tabla patients falló
    echo "Error al insertar en la tabla patients: " . $stmt->errorInfo()[2];
  }
};

// Actualizar un registro de la tabla Pacientes
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

// Actualizar un registro de la tabla expedientes
if (isset($_POST['editardoc']) && $_POST['editardoc'] === 'editDoc') {
  // Obtener los valores del formulario
  $id_expediente = $_POST['id_expediente'];
  $description = $_POST['name'];
  $data_update = date("Y-m-d H:i:s");

  // Actualizar los datos en la tabla patients
  $sqlexpedient = "UPDATE expedientes SET description = :description, data_update = :data_update WHERE id_expediente = :id_expediente";
  $stmtexp = $conn->prepare($sqlexpedient);
  $stmtexp->bindParam(':description', $description);
  $stmtexp->bindParam(':data_update', $data_update);
  $stmtexp->bindParam(':id_expediente', $id_expediente);

  if($stmtexp->execute()){
    echo "Actualización exitosa";
  } else {
    echo "Error al actualizar";
  }

}

// Eliminar un registro de la tabla expedientes
if (isset($_POST['eliminarDoc']) && $_POST['eliminarDoc'] === 'deleteDoc') {
  // Obtener los valores del formulario
  $id_expediente = $_POST['id_expediente'];

  // Realizar la eliminación en la tabla doctors
  $sqlDeleteDoc = "DELETE FROM expedientes WHERE id_expediente = :id_expediente";
  $stmtDeleteDoc = $conn->prepare($sqlDeleteDoc);
  $stmtDeleteDoc->bindParam(':id_expediente', $id_expediente);

  if($stmtDeleteDoc->execute()){
    echo "Eliminación exitosa";
  } else {
    echo "Error al eliminar";
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
            <a href="Dashboards.Patient.php" class="muted-link pb-1 d-inline-block breadcrumb-back">
              <i data-acorn-icon="chevron-left" data-acorn-size="13"></i>
              <span class="text-small align-middle">Home</span>
            </a>
            <h1 class="mb-0 pb-0 display-4" id="title">Detalles de Paciente</h1>
          </div>
          <!-- Title End -->
        </div>
      </div>
      <!-- Title and Top Buttons End -->

      <div class="row gx-5">
        <div class="col-xl-4">
          <!-- Profile Start -->
          <h2 class="small-title">Perfil</h2>
          <div class="card mb-5">
            <div class="card-body">
              <div class="mb-4 d-flex align-items-center flex-column">
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
                      where p.id_patient = $id_patient";
                $select_user = $conn->query($sql);
                if ($select_user !== false && $select_user->rowCount() > 0) {
                  while ($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="sw-13 position-relative mb-3">
                      <img src="<?php echo !empty($fetch_user['img']) ? 'data:image/jpeg;base64,' . base64_encode($fetch_user['img']) : '../img/profile/user.jpeg'; ?>" class="img-fluid rounded-xl" alt="thumb" />
                    </div>
                    <div class="h5 mb-0" style="padding-bottom: 10px;"><?php echo $fetch_user['name']; ?> <?php echo $fetch_user['first_name']; ?> <?php echo $fetch_user['last_name']; ?></div>
                    <div class="row g-0 align-items-center mb-2">
                      <div class="col-auto">
                        <div class="sw-3 d-flex justify-content-center align-items-center">
                          <i data-acorn-icon="user" class="text-primary"></i>
                        </div>
                      </div>
                      <div class="col ps-3">
                        <div class="d-flex align-items-center lh-1-25"><?php echo $fetch_user['phone']; ?></div>
                      </div>
                    </div>
                    <div class="row g-0 align-items-center mb-2">
                      <div class="col-auto">
                        <div class="sw-3 d-flex justify-content-center align-items-center">
                          <i data-acorn-icon="email" class="text-primary"></i>
                        </div>
                      </div>
                      <div class="col ps-3">
                        <div class="d-flex align-items-center lh-1-25"><?php echo $fetch_user['email']; ?></div>
                      </div>
                    </div>
                    <div class="d-flex flex-row justify-content-center w-100 w-sm-50 w-xl-100">
                      <button type="button" class="btn btn-outline-primary btn-ms mb-1 btn-icon btn-icon-start" style="margin-right: 10px;" data-bs-toggle="offcanvas" data-bs-target="#editModal<?php echo $fetch_user['id_patient']; ?>">
                        <i data-acorn-icon="edit-square"></i>
                        <span>Editar</span>
                      </button>
                      <button type="button" class="btn btn-outline-danger btn-ms mb-1 btn-icon btn-icon-start" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?php echo $fetch_user['id_patient']; ?>">
                        <i data-acorn-icon="bin"></i>
                        <span>Eliminar</span>
                      </button>
                    </div>
              </div>
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
                        <button type="submit" class="btn btn-danger" name="eliminar" value="deleteDoc">Eliminar</button>
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
            </div>
          </div>
          <!-- Profile End -->

          <!-- Honors and Awards Start -->
          <h2 class="small-title">Medicos</h2>
          <div class="card mb-5">
            <div class="card-body mb-n3">
              <div class="mb-3">
                <div>Lasker Award</div>
                <div class="text-muted">2014</div>
              </div>
              <div class="mb-3">
                <div>Florey Medal</div>
                <div class="text-muted">2012</div>
              </div>
              <div class="mb-3">
                <div>Outstanding Physician-Clinician Award</div>
                <div class="text-muted">2008</div>
              </div>
              <div class="mb-3">
                <div>Medical School Scholarship, University of Sydney</div>
                <div class="text-muted">2005</div>
              </div>
              <div class="mb-3">
                <div>Valedictorian University of Canberra</div>
                <div class="text-muted">2001</div>
              </div>
            </div>
          </div>
          <!-- Honors and Awards End -->
        </div>

        <div class="col-xl-8">
          <!-- Publications Start -->

          <div class="d-flex justify-content-between align-items-center w-100 w-sm-50 w-xl-100">
            <h2 class="small-title">Expediente Medico</h2>
            <div class="ms-auto">
              <button class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                <i data-acorn-icon="plus"></i>
                <span>Agregar</span>
              </button>
            </div>
          </div>

          <div class="card">
            <div class="card-body mb-n3">
              <div class="card mb-2 bg-transparent no-shadow d-none d-md-block">
                <div class="row g-0 sh-3">
                  <div class="col">
                    <div class="card-body pt-0 pb-0 h-100">
                      <div class="row g-0 h-100 align-content-center">
                        <div class="col-6 col-md-4 d-flex align-items-center text-alternate text-medium text-muted text-small">Nombre</div>
                        <div class="col-6 col-md-3 d-flex align-items-center text-alternate text-medium text-muted text-small">Fecha</div>
                        <div class="col-6 col-md-3 d-flex align-items-center text-alternate text-medium text-muted text-small">
                          Acciones
                        </div>
                        <div class="col-6 col-md-2 d-flex align-items-center justify-content-md-end text-alternate text-medium text-muted text-small">
                          Descargar
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-5">
                <?php
                $sql = "SELECT e.id_expediente,
                                e.id_paciente,
                                e.file,
                                e.description,
                                DATE(e.data_create) as fecha_creacion
                            from expedientes e
                            where e.id_paciente = $id_patient
                            order by e.id_expediente desc";
                $select_expe = $conn->query($sql);
                if ($select_expe !== false && $select_expe->rowCount() > 0) {
                  while ($fetch_ex = $select_expe->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="card mb-2 sh-11 sh-md-8">
                      <div class="card-body pt-0 pb-0 h-100">
                        <div class="row g-0 h-100 align-content-center">
                          <div class="col-11 col-md-4 d-flex align-items-center mb-1 mb-md-0 order-1 order-md-1">
                            <a href="Results.Detail.html" class="body-link text-truncate">
                              <i data-acorn-icon="file-text" class="sw-2 me-2 text-alternate" data-acorn-size="17"></i>
                              <span class="align-middle"><?php echo $fetch_ex['description']; ?></span>
                            </a>
                          </div>
                          <div class="col-12 col-md-3 d-flex align-items-center text-muted order-3 order-md-2"><?php echo $fetch_ex['fecha_creacion']; ?></div>
                          <div class="col-12 col-md-3 d-flex align-items-center text-muted order-3 order-md-2">
                            <button type="button" class="btn btn-outline-primary btn-ms mb-1 btn-icon btn-icon-start" style="margin-right: 15px;" data-bs-toggle="offcanvas" data-bs-target="#EditModal<?php echo $fetch_ex['id_paciente']; ?>">
                              <i data-acorn-icon="edit-square"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-ms mb-1 btn-icon btn-icon-start" data-bs-toggle="modal" data-bs-target="#DeleteModal<?php echo $fetch_ex['id_paciente']; ?>">
                              <i data-acorn-icon="bin"></i>
                            </button>
                          </div>
                          <div class="col-1 col-md-2 d-flex align-items-center text-muted text-medium justify-content-end order-2 order-md-3">
                            <button class="btn btn-icon btn-icon-only btn-link btn-sm p-1" type="button">
                              <i data-acorn-icon="arrow-bottom"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Confirm Delete Modal Start -->
                    <div class="modal fade" id="DeleteModal<?php echo $fetch_ex['id_paciente']; ?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar a <?php echo $fetch_ex['description']; ?>?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form method="post" action="">
                              <input type="hidden" name="id_expediente" value="<?php echo $fetch_ex['id_expediente']; ?>">
                              <button type="submit" class="btn btn-danger" name="eliminarDoc" value="deleteDoc">Eliminar</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- fin del modal de confirmación de eliminación -->
                    <!-- Offcanvas Start -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="EditModal<?php echo $fetch_ex['id_paciente']; ?>" aria-labelledby="editModalLabel" style="visibility: visible;margin-top: 0px;" role="dialog">
                      <div class="offcanvas-header">
                        <h5 id="editModalLabel">Editar Expediente</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                      </div>
                      <div class="offcanvas-body">
                        <form method="post" action="" enctype="multipart/form-data">
                          <div style="padding-left: 40px;">
                            <img src="<?php echo !empty($fetch_user['img']) ? 'data:image/jpeg;base64,' . base64_encode($fetch_user['img']) : '../img/profile/user.jpeg'; ?>" alt="Imagen vista previa" class="img-fluid rounded mb-1 me-1 sh-19" alt="Responsive image" />
                          </div>
                          <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="name" placeholder="nombre" value="<?php echo $fetch_ex['description']; ?>" />
                            <label>Nombre</label>
                          </div>
                          <input type="hidden" name="id_expediente" value="<?php echo $fetch_ex['id_expediente']; ?>">
                          <div class="modal-footer" style="border-right-style: solid;padding-right: 50px;">
                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="offcanvas">Cancelar</button>
                            <button type="submit" class="btn btn-primary" name="editardoc" value="editDoc">Actualizar</button>
                          </div>
                        </form>
                      </div>
                    </div>
                <?php
                  };
                };
                ?>
                <nav>
                  <ul class="pagination semibordered justify-content-center">
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                        <i data-acorn-icon="chevron-left"></i>
                      </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                      <a class="page-link" href="#">
                        <i data-acorn-icon="chevron-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
            <!-- Publications End -->
            <!-- Expediente start -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
              <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Agregar Medico</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div id="iconsNotify" class="offcanvas-body">
                <form method="post" action="" enctype="multipart/form-data">
                  <div style="padding-left: 40px;">
                    <img id="imagePreview1" src="#" alt="Imagen vista previa" class="img-fluid rounded mb-1 me-1 sh-19" />
                  </div>
                  <div class="form-floating mb-3">
                    <input type="file" class="form-control" name="image" id="imageInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewFile(event)" />
                    <label>Seleccionar Archivo</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" placeholder="nombre" />
                    <label>Nombre</label>
                  </div>
                  <div class="modal-footer" style="border-right-style: solid;padding-right: 100px;">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="offcanvas">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="submit" value="register now">Agregar</button>
                  </div>
                </form>
              </div>
            </div>
            <!-- Expediente End -->

            <!-- Experience Start -->
            <!--
          <h2 class="small-title">Experience</h2>
          <div class="card mb-5">
            <div class="card-body">
              <div class="row g-0">
                <div class="col-auto sw-1 d-flex flex-column justify-content-center align-items-center position-relative me-4">
                  <div class="w-100 d-flex sh-1"></div>
                  <div class="rounded-xl shadow d-flex flex-shrink-0 justify-content-center align-items-center">
                    <div class="bg-gradient-light sw-1 sh-1 rounded-xl position-relative"></div>
                  </div>
                  <div class="w-100 d-flex h-100 justify-content-center position-relative">
                    <div class="line-w-1 bg-separator h-100 position-absolute"></div>
                  </div>
                </div>
                <div class="col mb-4">
                  <div class="h-100">
                    <div class="d-flex flex-column justify-content-start">
                      <div class="d-flex flex-column">
                        <a href="#" class="heading stretched-link">Consultant Cardiologist</a>
                        <div class="text-alternate">Lismore Base Hospital</div>
                        <div class="text-alternate">2015-Present</div>
                        <div class="text-muted mt-1">
                          <div>Lemon drops cotton candy bear claw oat cake tootsie roll halvah.</div>
                          <div>Bonbon pie lollipop fruitcake bonbon chocolate cake gummies.</div>
                          <div>Donut biscuit chocolate cake pie topping.</div>
                          <div>Cake cookie chocolate.</div>
                          <div>Caramels jujubes.</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row g-0">
                <div class="col-auto sw-1 d-flex flex-column justify-content-center align-items-center position-relative me-4">
                  <div class="w-100 d-flex sh-1 position-relative justify-content-center">
                    <div class="line-w-1 bg-separator h-100 position-absolute"></div>
                  </div>
                  <div class="rounded-xl shadow d-flex flex-shrink-0 justify-content-center align-items-center">
                    <div class="bg-gradient-light sw-1 sh-1 rounded-xl position-relative"></div>
                  </div>
                  <div class="w-100 d-flex h-100 justify-content-center position-relative">
                    <div class="line-w-1 bg-separator h-100 position-absolute"></div>
                  </div>
                </div>
                <div class="col mb-4">
                  <div class="h-100">
                    <div class="d-flex flex-column justify-content-start">
                      <div class="d-flex flex-column">
                        <a href="#" class="heading stretched-link">Cardiologist</a>
                        <div class="text-alternate">Gosford Hospital</div>
                        <div class="text-alternate">2011-2014</div>
                        <div class="text-muted mt-1">
                          <div>Chocolate cake apple pie bear claw wafer cupcake topping topping oat cake.</div>
                          <div>Jelly-o sugar plum fruitcake.</div>
                          <div>Lemon drops tart gummies cake fruitcake.</div>
                          <div>Caramels cookie gummies sweet bear claw jelly-o.</div>
                          <div>Danish dragée toffee bonbon.</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row g-0">
                <div class="col-auto sw-1 d-flex flex-column justify-content-center align-items-center position-relative me-4">
                  <div class="w-100 d-flex sh-1 position-relative justify-content-center">
                    <div class="line-w-1 bg-separator h-100 position-absolute"></div>
                  </div>
                  <div class="rounded-xl shadow d-flex flex-shrink-0 justify-content-center align-items-center">
                    <div class="bg-gradient-light sw-1 sh-1 rounded-xl position-relative"></div>
                  </div>
                  <div class="w-100 d-flex h-100 justify-content-center position-relative">
                    <div class="line-w-1 bg-separator h-100 position-absolute"></div>
                  </div>
                </div>
                <div class="col mb-4">
                  <div class="h-100">
                    <div class="d-flex flex-column justify-content-start">
                      <div class="d-flex flex-column">
                        <a href="#" class="heading stretched-link">Cardiology Registrar</a>
                        <div class="text-alternate">Flinders Medical Centre</div>
                        <div class="text-alternate">2008-2010</div>
                        <div class="text-muted mt-1">
                          <div>Apple pie icing gingerbread candy canes marzipan halvah sugar plum tart marzipan.</div>
                          <div>Sesame snaps chocolate apple pie chocolate cake chupa chups.</div>
                          <div>Lemon drops cotton candy bear claw oat cake tootsie roll halvah.</div>
                          <div>Bonbon pie lollipop fruitcake bonbon chocolate cake gummies.</div>
                          <div>Plum fruitcake bonbon.</div>
                          <div>Chupa chups bonbon.</div>
                          <div>Danish dragée toffee bonbon.</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row g-0">
                <div class="col-auto sw-1 d-flex flex-column justify-content-center align-items-center position-relative me-4">
                  <div class="w-100 d-flex sh-1 position-relative justify-content-center">
                    <div class="line-w-1 bg-separator h-100 position-absolute"></div>
                  </div>
                  <div class="rounded-xl shadow d-flex flex-shrink-0 justify-content-center align-items-center">
                    <div class="bg-gradient-light sw-1 sh-1 rounded-xl position-relative"></div>
                  </div>
                  <div class="w-100 d-flex h-100 justify-content-center position-relative"></div>
                </div>
                <div class="col">
                  <div class="h-100">
                    <div class="d-flex flex-column justify-content-start">
                      <div class="d-flex flex-column">
                        <a href="#" class="heading stretched-link pt-0">Basic Physician Trainee</a>
                        <div class="text-alternate">The Royal Melbourne Hospital City</div>
                        <div class="text-alternate">2005-2007</div>
                        <div class="text-muted mt-1">
                          <div>Chocolate apple pie powder.</div>
                          <div>Tart chupa chups bonbon.</div>
                          <div>Jelly-o marshmallow cake.</div>
                          <div>Caramels jujubes.</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
-->
            <!-- Experience End -->
          </div>
        </div>
      </div>
  </main>
  <!-- Layout Footer Start -->
  <?php include_once('../layouts/footer.php'); ?>
  <!-- Layout Footer End -->
</div>
<?php require_once('../layouts/scripts.php'); ?>