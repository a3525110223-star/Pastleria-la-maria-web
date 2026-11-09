document.addEventListener('DOMContentLoaded', () => {
    const formularios = document.querySelectorAll('form');

    formularios.forEach((formulario) => {
        const confirmacion = formulario.querySelector('[name="password_confirmation"]');
        const contrasena = formulario.querySelector('[name="password"]');

        const actualizarConfirmacion = () => {
            if (confirmacion && contrasena) {
                confirmacion.setCustomValidity(
                    confirmacion.value && confirmacion.value !== contrasena.value
                        ? 'Las contraseñas no coinciden. Verifícalas.'
                        : '',
                );
            }
        };

        const nombreDelCampo = (campo) => {
            const etiqueta = campo.labels?.[0]?.textContent
                .replace(/\*/g, '')
                .trim();

            return etiqueta
                ? etiqueta.charAt(0).toLowerCase() + etiqueta.slice(1)
                : 'campo';
        };

        const mensajeDeValidacion = (campo) => {
            if (campo.validity.customError) {
                return campo.validationMessage;
            }

            if (campo.validity.valueMissing) {
                if (campo.type === 'checkbox') {
                    return `Marca ${nombreDelCampo(campo)} para continuar.`;
                }

                if (campo.type === 'file') {
                    return `Selecciona un archivo para ${nombreDelCampo(campo)}.`;
                }

                if (campo.tagName === 'SELECT') {
                    return `Selecciona una opción para ${nombreDelCampo(campo)}.`;
                }

                return `Completa el campo ${nombreDelCampo(campo)}.`;
            }

            if (campo.validity.typeMismatch) {
                if (campo.type === 'email') {
                    return 'Escribe un correo electrónico válido.';
                }

                if (campo.type === 'url') {
                    return 'Escribe una dirección web válida.';
                }
            }

            if (campo.validity.patternMismatch) {
                return 'Revisa el formato de este campo.';
            }

            if (campo.validity.tooShort) {
                return `Escribe al menos ${campo.minLength} caracteres en ${nombreDelCampo(campo)}.`;
            }

            if (campo.validity.tooLong) {
                return `Escribe como máximo ${campo.maxLength} caracteres en ${nombreDelCampo(campo)}.`;
            }

            if (campo.validity.rangeUnderflow) {
                return `El valor de ${nombreDelCampo(campo)} debe ser igual o mayor que ${campo.min}.`;
            }

            if (campo.validity.rangeOverflow) {
                return `El valor de ${nombreDelCampo(campo)} debe ser igual o menor que ${campo.max}.`;
            }

            if (campo.validity.stepMismatch || campo.validity.badInput) {
                return 'Escribe un número válido.';
            }

            return '';
        };

        formulario.addEventListener('invalid', (evento) => {
            const campo = evento.target;

            if (campo instanceof HTMLInputElement
                || campo instanceof HTMLSelectElement
                || campo instanceof HTMLTextAreaElement) {
                campo.setCustomValidity(mensajeDeValidacion(campo));
            }
        }, true);

        formulario.addEventListener('input', (evento) => {
            if (evento.target instanceof HTMLInputElement
                || evento.target instanceof HTMLSelectElement
                || evento.target instanceof HTMLTextAreaElement) {
                evento.target.setCustomValidity('');
            }

            actualizarConfirmacion();
        });

        formulario.addEventListener('change', (evento) => {
            if (evento.target instanceof HTMLInputElement
                || evento.target instanceof HTMLSelectElement
                || evento.target instanceof HTMLTextAreaElement) {
                evento.target.setCustomValidity('');
            }

            actualizarConfirmacion();
        });

        actualizarConfirmacion();
    });
});
