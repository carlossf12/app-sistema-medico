<?php
require_once('../layouts/headerAdmin.php');
?>
<div id="root">
    <div id="nav" class="nav-container d-flex">
        <?php include_once('../layouts/navAdmin.php'); ?>
        <div class="nav-shadow"></div>
    </div>

    <main>
        <div class="container">
            <div class="row">
                <div class="col">
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
                                <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto add-datatable">
                                    <i data-acorn-icon="plus"></i>
                                    <span>Agregar</span>
                                </button>
                                <!-- Add New Button End -->

                                <!-- Check Button Start -->
                                <div class="btn-group ms-1 check-all-container">
                                    <div class="btn btn-outline-primary btn-custom-control p-0 ps-3 pe-2" id="datatableCheckAllButton">
                                        <span class="form-check float-end">
                                            <input type="checkbox" class="form-check-input" id="datatableCheckAll" />
                                        </span>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-offset="0,3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-submenu></button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item disabled delete-datatable" type="button">Eliminar</button>
                                    </div>
                                </div>
                                <!-- Check Button End -->
                            </div>
                            <!-- Top Buttons End -->
                        </div>
                    </div>
                    <!-- Title and Top Buttons End -->

                    <!-- Content Start -->
                    <div class="data-table-rows slim">
                        <!-- Controls Start -->
                        <div class="row">
                            <!-- Search Start -->
                            <div class="col-sm-12 col-md-5 col-lg-3 col-xxl-2 mb-1">
                                <div class="d-inline-block float-md-start me-1 mb-1 search-input-container w-100 shadow bg-foreground">
                                    <input class="form-control datatable-search" placeholder="Buscar" data-datatable="#datatableRowsAjax" />
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
                                <div class="d-inline-block me-0 me-sm-3 float-start float-md-none">
                                    <!-- Add Button Start -->
                                    <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow add-datatable" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" type="button">
                                        <i data-acorn-icon="plus"></i>
                                    </button>
                                    <!-- Add Button End -->

                                    <!-- Edit Button Start -->
                                    <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow edit-datatable disabled" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" type="button">
                                        <i data-acorn-icon="edit"></i>
                                    </button>
                                    <!-- Edit Button End -->

                                    <!-- Delete Button Start -->
                                    <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow disabled delete-datatable" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" type="button">
                                        <i data-acorn-icon="bin"></i>
                                    </button>
                                    <!-- Delete Button End -->
                                </div>
                                <div class="d-inline-block">
                                    <!-- Print Button Start -->
                                    <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow datatable-print" data-bs-delay="0" data-datatable="#datatableRowsAjax" data-bs-toggle="tooltip" data-bs-placement="top" title="Imprimir" type="button">
                                        <i data-acorn-icon="print"></i>
                                    </button>
                                    <!-- Print Button End -->

                                    <!-- Export Dropdown Start -->
                                    <div class="d-inline-block datatable-export" data-datatable="#datatableRowsAjax">
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
                                    <div class="dropdown-as-select d-inline-block datatable-length" data-datatable="#datatableRowsAjax" data-childSelector="span">
                                        <button class="btn p-0 shadow" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-offset="0,3">
                                            <span class="btn btn-foreground-alternate dropdown-toggle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-delay="0" title="Item Count">
                                                10 Areas
                                            </span>
                                        </button>
                                        <div class="dropdown-menu shadow dropdown-menu-end">
                                            <a class="dropdown-item" href="#">5 pacientes</a>
                                            <a class="dropdown-item active" href="#">10 pacientes</a>
                                            <a class="dropdown-item" href="#">20 pacientes</a>
                                        </div>
                                    </div>
                                    <!-- Length End -->
                                </div>
                            </div>
                        </div>
                        <!-- Controls End -->

                        <!-- Table Start -->
                <div class="data-table-responsive-wrapper">
                  <table id="datatableRowsAjax" class="data-table nowrap w-100">
                    <thead>
                      <tr>
                        <th class="text-muted text-small text-uppercase">Nombre</th>
                        <th class="text-muted text-small text-uppercase">Telefono</th>
                        <th class="text-muted text-small text-uppercase">Correo</th>
                        <th class="text-muted text-small text-uppercase">Expediente</th>
                        <th class="empty">&nbsp;</th>
                      </tr>
                    </thead>
                  </table>
                </div>
                <!-- Table End -->
                    </div>
                    <!-- Content End -->

                    <!-- Add Edit Modal Start -->
                    <div class="modal modal-right fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTitle">Agregar Nuevo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div id="iconsNotify" class="modal-body">
                                    <form>
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
                                        <input type="hidden" name="id_patient" value="">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="addEditConfirmButton">Agregar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add Edit Modal End -->
                </div>
            </div>
        </div>
    </main>
    <!-- Layout Footer Start -->
    <?php include_once('../layouts/footer.php'); ?>
    <!-- Layout Footer End -->
</div>
<!-- Vendor Scripts Start -->
<script src="../js/vendor/jquery-3.5.1.min.js"></script>
<script src="../js/vendor/bootstrap.bundle.min.js"></script>
<script src="../js/vendor/OverlayScrollbars.min.js"></script>
<script src="../js/vendor/autoComplete.min.js"></script>
<script src="../js/vendor/clamp.min.js"></script>

<script src="../icon/acorn-icons.js"></script>
<script src="../icon/acorn-icons-interface.js"></script>
<script src="../icon/acorn-icons-medical.js"></script>

<script src="../js/vendor/jquery.barrating.min.js"></script>

<script src="../js/vendor/bootstrap-submenu.js"></script>
<script src="../js/vendor/datatables.min.js"></script>
<script src="../js/vendor/mousetrap.min.js"></script>

<script src="../js/vendor/Chart.bundle.min.js"></script>
<script src="../js/vendor/chartjs-plugin-rounded-bar.min.js"></script>
<script src="../js/vendor/chartjs-plugin-crosshair.js"></script>
<script src="../js/vendor/fullcalendar/main.min.js"></script>

<script src="../js/cs/scrollspy.js"></script>
<script src="../js/vendor/select2.full.min.js"></script>
<script src="../js/vendor/datepicker/bootstrap-datepicker.min.js"></script>
<script src="../js/vendor/tagify.min.js"></script>

<script src="../js/vendor/bootstrap-notify.min.js"></script>

<!-- Vendor Scripts End -->

<!-- Template Base Scripts Start -->
<script src="../js/base/helpers.js"></script>
<script src="../js/base/globals.js"></script>
<script src="../js/base/nav.js"></script>
<script src="../js/base/search.js"></script>
<script src="../js/base/settings.js"></script>
<!-- Template Base Scripts End -->
<!-- Page Specific Scripts Start -->

<script src="../js/cs/charts.extend.js"></script>
<script src="../js/pages/dashboards.doctor.js"></script>
<script src="../js/cs/datatable.extend.js"></script>
<script src="../js/plugins/datatable_Pacientes_Ajax.js"></script>

<script src="../js/forms/layouts.js"></script>
<script src="../js/components/toasts.js"></script>
<script src="../js/plugins/notifies.js"></script>

<script src="../js/pages/doctors.js"></script>

<script src="../js/common.js"></script>
<script src="../js/scripts.js"></script>
<!-- Page Specific Scripts End -->
</body>

</html>