/**
 *
 * AuthLogin
 *
 * Pages.Authentication.Login page content scripts. Initialized from scripts.js file.
 *
 *
 */

class AuthLogin {
  constructor() {
    // Initialization of the page plugins
    this._initForm();
  }

  // Form validation
  _initForm() {
    const form = document.getElementById('loginForm');
    if (!form) {
      return;
    }
    const validateOptions = {
      rules: {
        email: {
          required: true,
          email: true,
        },
        password: {
          required: true,
        },
      },
      messages: {
        email: {
          email: '¡dirección de correo electrónico incorrecto!',
          required: '¡Por favor, introduzca su correo electrónico!',
        },
        password: {
          required: '¡Por favor, introduzca su contraseña!',
        },
      },
    };
    jQuery(form).validate(validateOptions);
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      event.stopPropagation();
      if (jQuery(form).valid()) {
        const formValues = {
          email: form.querySelector('[name="email"]').value,
          password: form.querySelector('[name="password"]').value,
        };
        console.log(formValues);

        // Realizar una petición AJAX a login.php
        jQuery.ajax({
          url: './../backend/php/login.php',
          method: 'POST',
          data: formValues,
          success: function (response) {
            // Aquí puedes manejar la respuesta del servidor si es necesario
            console.log('Respuesta del servidor:', response);
            // response = parseInt(response, 10);

            // Mostrar la notificación utilizando jQuery Notify
            if (response == 0) {
              jQuery.notify(
                {title: 'Contraseña incorrecta', message: '¡Por favor, inténtelo otra vez.!', icon: 'cs-info-hexagon'},
                {
                  type: 'danger',
                  delay: 5000,
                },
              );
            } else if (response == 1) {
              window.location.href = 'admin/Dashboards.Admin.php';
            } else if (response == 2) {
              window.location.href = 'medico/Dashboards.Doctor.php';
            } else if (response == 3) {
              window.location.href = 'paciente/Dashboards.Patient.php';
            } else {
              jQuery.notify(
                {title: response, message: '¡Por favor, inténtelo otra vez!', icon: 'cs-user'},
                {
                  type: 'warning',
                  delay: 5000,
                },
              );
            }
          },
          error: function (xhr, status, error) {
            // Manejar errores si los hay
            console.error('Error en la petición AJAX:', error);
          },
        });
      }
    });
  }
}
