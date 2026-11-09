document.addEventListener('DOMContentLoaded', async () => {
    const app = document.getElementById('catalogo-app');
    const estado = document.getElementById('estado-empresa');

    if (!app || !estado) {
        return;
    }

    const elementos = {
        nombre: document.getElementById('empresa-nombre'),
        descripcion: document.getElementById('empresa-descripcion'),
        mision: document.getElementById('empresa-mision'),
        vision: document.getElementById('empresa-vision'),
        telefono: document.getElementById('empresa-telefono'),
        direccion: document.getElementById('empresa-direccion'),
        email: document.getElementById('empresa-email'),
        horario: document.getElementById('empresa-horario'),
    };

    try {
        const respuesta = await fetch(app.dataset.apiEmpresa, {
            headers: { Accept: 'application/json' },
        });

        if (respuesta.status === 404) {
            Object.values(elementos).forEach((elemento) => {
                if (elemento) {
                    elemento.textContent = '';
                }
            });
            estado.textContent = 'La información de la empresa aún no está configurada.';
            return;
        }

        if (!respuesta.ok) {
            throw new Error(`La consulta de empresa respondió HTTP ${respuesta.status}.`);
        }

        const empresa = await respuesta.json();
        const asignarTexto = (elemento, valor, reemplazo = 'Información no disponible.') => {
            if (elemento) {
                elemento.textContent = valor?.trim() || reemplazo;
            }
        };

        asignarTexto(elementos.nombre, empresa.nombre);
        asignarTexto(elementos.descripcion, empresa.descripcion, '');
        asignarTexto(elementos.mision, empresa.mision);
        asignarTexto(elementos.vision, empresa.vision);
        asignarTexto(elementos.telefono, empresa.telefono, 'No disponible');
        asignarTexto(elementos.direccion, empresa.direccion);
        asignarTexto(elementos.email, empresa.email, 'No disponible');

        const horario = [empresa.horario_normal, empresa.horario_especial]
            .filter((valor) => typeof valor === 'string' && valor.trim())
            .join(' · ');
        asignarTexto(elementos.horario, horario);
    } catch (error) {
        console.error('Error al cargar la información de la empresa:', error);
        estado.textContent = 'No fue posible cargar la información de la pastelería.';
    }
});
