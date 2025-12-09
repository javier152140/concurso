document.addEventListener('DOMContentLoaded', function() {
    // 1. Obtener elementos del DOM
    const form = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    // 2. Agregar event listener al formulario
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Detener el envío estándar del formulario

            // 3. Recoger valores
            const nombre = document.getElementById('nombre').value;
            const email = document.getElementById('email').value;
            const mensaje = document.getElementById('mensaje').value;
            
            // Expresión regular para validar email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // 4. Validación de datos (Funcionalidad)
            if (nombre.trim() === '' || email.trim() === '' || mensaje.trim() === '') {
                formMessage.textContent = '❌ Error: Todos los campos son obligatorios.';
                formMessage.style.color = 'red';
                return;
            }

            if (!emailRegex.test(email)) {
                formMessage.textContent = '❌ Error: Por favor, introduce un correo electrónico válido.';
                formMessage.style.color = 'red';
                return;
            }

            // 5. Simulación de envío exitoso
            // En un proyecto real, aquí se usaría 'fetch()' para enviar datos a un servidor.
            
            formMessage.textContent = '✅ Mensaje enviado con éxito. ¡Pronto te contactaremos!';
            formMessage.style.color = 'green';
            form.reset(); // Limpiar el formulario
            
            // Para fines de demostración, mostramos los datos en la consola (Base de datos / datos)
            console.log('--- Nuevo Mensaje Enviado (Simulación) ---');
            console.log(`Nombre: ${nombre}`);
            console.log(`Email: ${email}`);
            console.log(`Mensaje: ${mensaje}`);
        });
    } else {
        console.error('El formulario de contacto no se encontró.');
    }
});