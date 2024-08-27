// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    // Validar el campo de precio para permitir solo 2 decimales
    document.querySelectorAll('input[name="precio_evento"]').forEach(function (input) {
        input.addEventListener('input', function () {
            let value = this.value;
            if (value) {
                value = value.replace(/[^0-9.]/g, ''); // Eliminar caracteres no numéricos excepto punto
                let parts = value.split('.');
                if (parts.length > 2) {
                    parts = [parts[0] + '.' + parts.slice(1).join('')];
                }
                if (parts[1]) {
                    parts[1] = parts[1].substring(0, 2); // Limitar a 2 decimales
                }
                this.value = parts.join('.');
            }
        });
    });

    // Validar el campo de número de tarjeta para permitir solo números
    document.querySelectorAll('input[name="tarjeta"]').forEach(function (input) {
        input.addEventListener('input', function () {
            // Eliminar caracteres no numéricos
            let value = this.value.replace(/\D/g, '');
            // Limitar a 16 dígitos
            if (value.length > 16) {
                value = value.slice(0, 16);
            }
            // Formatear en bloques de 4 dígitos
            this.value = value.replace(/(.{4})/g, '$1 ').trim();
        });
    });

    // Validar el campo de fecha de vencimiento
    document.querySelectorAll('input[name="fecha_vencimiento"]').forEach(function (input) {
        input.addEventListener('input', function () {
            let value = this.value;
            if (value) {
                value = value.replace(/[^0-9/]/g, ''); // Eliminar caracteres no numéricos y '/'
                // Formatear la fecha en MM/YY
                if (value.length > 2 && value[2] !== '/') {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }
                this.value = value.slice(0, 5); // Limitar a MM/YY
            }
        });
    });

    // Validar el campo de CVV para permitir solo 3 dígitos
    document.querySelectorAll('input[name="cvv"]').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, ''); // Eliminar caracteres no numéricos
            this.value = this.value.slice(0, 3); // Limitar a 3 dígitos
        });
    });

    document.querySelectorAll('input[name="precio"]').forEach(function (input) {
        input.addEventListener('input', function () {
            let value = this.value;
            if (value) {
                value = value.replace(/[^0-9.]/g, ''); // Eliminar caracteres no numéricos excepto punto
                let parts = value.split('.');
                if (parts.length > 2) {
                    parts = [parts[0] + '.' + parts.slice(1).join('')];
                }
                if (parts[1]) {
                    parts[1] = parts[1].substring(0, 2); // Limitar a 2 decimales
                }
                this.value = parts.join('.');
            }
        });
    });
});
