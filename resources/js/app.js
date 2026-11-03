document.querySelectorAll('[data-expand-row]').forEach((fila) => {
    const destino = fila.getAttribute('data-expand-target');
    const detalle = destino ? document.getElementById(destino) : null;
    const icono = fila.querySelector('[data-expand-icon] svg');

    if (! (fila instanceof HTMLElement) || ! (detalle instanceof HTMLElement)) {
        return;
    }

    fila.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('[data-expand-ignore]')) {
            return;
        }

        const oculto = ! detalle.classList.contains('hidden');
        detalle.classList.toggle('hidden', oculto);
        fila.classList.toggle('bg-brand-green-soft', ! oculto);
        fila.setAttribute('aria-expanded', oculto ? 'false' : 'true');
        icono?.classList.toggle('rotate-180', ! oculto);
    });
});

document.querySelectorAll('[data-optional-field]').forEach((bloque) => {
    const agregar = bloque.querySelector('[data-optional-add]');
    const extra = bloque.querySelector('[data-optional-extra]');
    const quitar = bloque.querySelector('[data-optional-remove]');
    const input = extra?.querySelector('input:not([data-optional-remove-flag])');
    const bandera = extra?.querySelector('[data-optional-remove-flag]');

    if (! (agregar instanceof HTMLButtonElement) || ! (extra instanceof HTMLElement)) {
        return;
    }

    agregar.addEventListener('click', () => {
        extra.classList.remove('hidden');
        agregar.classList.add('hidden');

        if (bandera instanceof HTMLInputElement) {
            bandera.value = '0';
        }

        input?.focus();
    });

    quitar?.addEventListener('click', () => {
        extra.classList.add('hidden');
        agregar.classList.remove('hidden');

        if (input instanceof HTMLInputElement) {
            input.value = '';
        }

        if (bandera instanceof HTMLInputElement) {
            bandera.value = '1';
        }
    });
});

const perfilesUsuarios = (() => {
    const nodo = document.querySelector('[data-perfiles-usuarios]');

    if (! (nodo instanceof HTMLElement)) {
        return {};
    }

    try {
        return JSON.parse(nodo.textContent || '{}');
    } catch {
        return {};
    }
})();

const agregarCorreo = (picker, valor) => {
    const oculto = picker.querySelector('[data-email-hidden]');
    const chips = picker.querySelector('[data-email-chips]');

    if (! (oculto instanceof HTMLInputElement) || ! (chips instanceof HTMLElement)) {
        return;
    }

    const correo = valor.trim().replace(/[;,]+$/g, '');

    if (correo === '' || ! correo.includes('@')) {
        return;
    }

    const actuales = oculto.value.split(',').map((item) => item.trim()).filter(Boolean);

    if (actuales.some((item) => item.toLowerCase() === correo.toLowerCase())) {
        return;
    }

    actuales.push(correo);
    oculto.value = actuales.join(', ');

    const chip = document.createElement('button');
    chip.type = 'button';
    chip.className = 'inline-flex items-center gap-1 rounded-full bg-brand-green-soft px-2.5 py-1 text-xs font-medium text-brand-green-dark';
    chip.dataset.emailChip = correo;
    chip.innerHTML = `${correo}<span aria-hidden="true">×</span>`;
    chip.addEventListener('click', () => {
        oculto.value = oculto.value
            .split(',')
            .map((item) => item.trim())
            .filter((item) => item.toLowerCase() !== correo.toLowerCase())
            .join(', ');
        chip.remove();
    });
    chips.appendChild(chip);
};

document.querySelectorAll('[data-email-picker]').forEach((picker) => {
    const oculto = picker.querySelector('[data-email-hidden]');
    const entrada = picker.querySelector('[data-email-input]');

    if (oculto instanceof HTMLInputElement) {
        const inicial = oculto.value;
        oculto.value = '';
        inicial.split(',').forEach((correo) => agregarCorreo(picker, correo));
    }

    if (entrada instanceof HTMLInputElement) {
        entrada.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                agregarCorreo(picker, entrada.value);
                entrada.value = '';
            }
        });
    }
});

document.querySelectorAll('[data-filter-combobox]').forEach((combo) => {
    const input = combo.querySelector('[data-combobox-input]');
    const lista = combo.querySelector('[data-combobox-list]');
    const vacio = combo.querySelector('[data-combobox-empty]');
    const opciones = Array.from(combo.querySelectorAll('[data-combobox-option]'));

    if (! (input instanceof HTMLInputElement) || ! (lista instanceof HTMLElement)) {
        return;
    }

    const normalizar = (valor) => valor.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim();

    const posicionar = () => {
        const caja = input.getBoundingClientRect();
        const minimo = Number(combo.dataset.comboboxMinWidth || 260);
        const ancho = Math.max(caja.width, Number.isFinite(minimo) ? minimo : 260);
        let izquierda = caja.left;

        if (izquierda + ancho > window.innerWidth - 8) {
            izquierda = Math.max(8, window.innerWidth - ancho - 8);
        }

        lista.style.position = 'fixed';
        lista.style.left = `${Math.round(izquierda)}px`;
        lista.style.top = `${Math.round(caja.bottom + 4)}px`;
        lista.style.width = `${Math.round(ancho)}px`;
        lista.style.zIndex = '9999';

        const alto = lista.offsetHeight;

        if (caja.bottom + 4 + alto > window.innerHeight && caja.top > alto + 4) {
            lista.style.top = `${Math.round(caja.top - alto - 4)}px`;
        }
    };

    const mostrar = () => {
        document.querySelectorAll('[data-combobox-list]').forEach((otra) => {
            if (otra !== lista) {
                otra.classList.add('hidden');
            }
        });

        const dialogo = combo.closest('dialog');
        const destino = dialogo instanceof HTMLDialogElement ? dialogo : document.body;

        if (lista.parentElement !== destino) {
            destino.appendChild(lista);
        }

        lista.classList.remove('hidden');
        posicionar();
    };

    const ocultar = () => lista.classList.add('hidden');

    const claveTraslado = () => input.name.match(/^traslados\[([^\]]+)\]\[usuario_recibe\]$/)?.[1] ?? null;

    const campoTraslado = (campo) => {
        const clave = claveTraslado();

        if (! clave) {
            return null;
        }

        const nodo = document.querySelector(`input[name="traslados[${clave}][${campo}]"]`);

        return nodo instanceof HTMLInputElement ? nodo : null;
    };

    const autocompletarPerfil = (usuario) => {
        if (claveTraslado() === null) {
            return;
        }

        const perfil = perfilesUsuarios[usuario.trim().toLowerCase()] ?? null;
        const cargo = campoTraslado('cargo_recibe');
        const area = campoTraslado('area_recibe');
        const departamento = campoTraslado('departamento_recibe');

        if (perfil) {
            if (cargo) {
                cargo.value = perfil.cargo ?? '';
            }

            if (area) {
                area.value = perfil.area ?? '';
            }

            if (departamento) {
                departamento.value = perfil.departamento ?? '';
            }

            input.dataset.autofillAplicado = '1';

            return;
        }

        if (input.dataset.autofillAplicado === '1') {
            if (cargo) {
                cargo.value = '';
            }

            if (area) {
                area.value = '';
            }

            if (departamento) {
                departamento.value = '';
            }

            delete input.dataset.autofillAplicado;
        }
    };

    const filtrar = () => {
        const termino = normalizar(input.value);
        const filtraPorMarca = input.getAttribute('data-filtra-por-marca');
        const marcaNodo = filtraPorMarca ? document.querySelector(`[name="${filtraPorMarca}"]`) : null;
        const marca = marcaNodo instanceof HTMLInputElement ? marcaNodo.value : '';
        const faltaMarca = filtraPorMarca !== null && normalizar(marca) === '';
        const avisoMarca = lista.querySelector('[data-combobox-marca-vacia]');
        let visibles = 0;

        opciones.forEach((opcion) => {
            if (faltaMarca) {
                opcion.classList.add('hidden');

                return;
            }

            const esNuevo = opcion.hasAttribute('data-combobox-custom');
            const esGestion = opcion.hasAttribute('data-combobox-manage');
            const marcaOpcion = opcion.getAttribute('data-marca');
            const coincideMarca = marcaOpcion === null || normalizar(marcaOpcion) === normalizar(marca);
            const coincide = esNuevo || esGestion || (coincideMarca && normalizar(opcion.dataset.value ?? '').includes(termino));

            opcion.classList.toggle('hidden', ! coincide);

            if (coincide) {
                visibles += 1;
            }
        });

        avisoMarca?.classList.toggle('hidden', ! faltaMarca);
        vacio?.classList.toggle('hidden', faltaMarca || visibles > 0 || termino === '');
        mostrar();
    };

    const reposicionar = () => {
        if (! lista.classList.contains('hidden')) {
            posicionar();
        }
    };

    input.addEventListener('focus', filtrar);
    input.addEventListener('input', () => {
        filtrar();
        autocompletarPerfil(input.value);
    });
    window.addEventListener('scroll', reposicionar, true);
    window.addEventListener('resize', reposicionar);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            ocultar();
        }
    });

    const elegirCorreo = (valor) => {
        const picker = combo.closest('[data-email-picker]');

        if (picker instanceof HTMLElement) {
            agregarCorreo(picker, valor);
            input.value = '';
            input.focus();
            filtrar();

            return true;
        }

        return false;
    };

    opciones.forEach((opcion) => {
        opcion.addEventListener('mousedown', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (opcion.hasAttribute('data-combobox-manage')) {
                ocultar();
                const dialogoId = opcion.getAttribute('data-combobox-manage');
                const dialogo = dialogoId ? document.getElementById(dialogoId) : null;

                if (dialogo instanceof HTMLDialogElement) {
                    dialogo.showModal();
                }

                return;
            }

            if (opcion.hasAttribute('data-combobox-custom')) {
                input.value = '';
                input.placeholder = 'Escribe el valor nuevo';
                autocompletarPerfil('');
                input.dispatchEvent(new Event('input', { bubbles: true }));
                ocultar();
                input.focus();

                return;
            }

            input.value = opcion.dataset.value ?? '';
            autocompletarPerfil(input.value);
            input.dispatchEvent(new Event('input', { bubbles: true }));

            if (input.id === 'marca') {
                const modelo = document.querySelector('#modelo');

                if (modelo instanceof HTMLInputElement && modelo.value.trim() !== '') {
                    const marca = input.value;
                    const coincide = Array.from(document.querySelectorAll('[data-combobox-option][data-marca]')).some((item) => (
                        item instanceof HTMLElement
                        && normalizar(item.getAttribute('data-marca') ?? '') === normalizar(marca)
                        && normalizar(item.dataset.value ?? '') === normalizar(modelo.value)
                    ));

                    if (! coincide) {
                        modelo.value = '';
                    }
                }
            }

            if (! elegirCorreo(input.value)) {
                ocultar();
            }
        });

        opcion.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target instanceof Node && ! combo.contains(event.target) && ! lista.contains(event.target)) {
            ocultar();
        }
    });
});

document.querySelectorAll('[data-procesador-campo]').forEach((bloque) => {
    const modelo = bloque.querySelector('[data-procesador-modelo]');
    const detalle = bloque.querySelector('[data-procesador-detalle]');

    if (! (modelo instanceof HTMLInputElement) || ! (detalle instanceof HTMLElement)) {
        return;
    }

    const actualizar = () => {
        detalle.classList.toggle('hidden', modelo.value.trim() === '');
    };

    modelo.addEventListener('input', actualizar);
    modelo.addEventListener('change', actualizar);
    actualizar();
});

const fechaAdquisicion = document.querySelector('[data-antiguedad-source]');
const antiguedadSalida = document.querySelector('[data-antiguedad-output]');

if (fechaAdquisicion instanceof HTMLInputElement && antiguedadSalida instanceof HTMLInputElement) {
    const plural = (valor, singular, plural) => `${valor} ${valor === 1 ? singular : plural}`;

    const calcularAntiguedad = (valor) => {
        if (! valor) {
            return '—';
        }

        const inicio = new Date(`${valor}T00:00:00`);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        if (Number.isNaN(inicio.getTime())) {
            return '—';
        }

        if (inicio > hoy) {
            return 'Fecha futura';
        }

        let years = hoy.getFullYear() - inicio.getFullYear();
        let months = hoy.getMonth() - inicio.getMonth();
        let days = hoy.getDate() - inicio.getDate();

        if (days < 0) {
            months -= 1;
            const previo = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
            days += previo.getDate();
        }

        if (months < 0) {
            years -= 1;
            months += 12;
        }

        const partes = [];

        if (years > 0) {
            partes.push(plural(years, 'año', 'años'));
        }

        if (months > 0) {
            partes.push(plural(months, 'mes', 'meses'));
        }

        if (days > 0 || partes.length === 0) {
            partes.push(plural(days, 'día', 'días'));
        }

        return partes.join(', ');
    };

    const actualizar = () => {
        antiguedadSalida.value = calcularAntiguedad(fechaAdquisicion.value);
    };

    fechaAdquisicion.addEventListener('change', actualizar);
    fechaAdquisicion.addEventListener('input', actualizar);
}

document.querySelectorAll('[data-whatsapp-share]').forEach((enlace) => {
    enlace.addEventListener('click', (event) => {
        if (! (enlace instanceof HTMLAnchorElement)) {
            return;
        }

        const nodo = document.querySelector('[data-whatsapp-mensaje]');
        let texto = '';

        try {
            texto = JSON.parse(nodo?.textContent || '""');
        } catch {
            texto = '';
        }

        if (typeof texto !== 'string' || texto === '') {
            return;
        }

        event.preventDefault();
        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(texto)}`, '_blank', 'noopener');
    });
});

document.querySelectorAll('[data-open-dialog]').forEach((boton) => {
    boton.addEventListener('click', () => {
        const id = boton.getAttribute('data-open-dialog');
        const dialogo = id ? document.getElementById(id) : null;

        if (dialogo instanceof HTMLDialogElement) {
            document.querySelectorAll('dialog[open]').forEach((abierto) => {
                if (abierto instanceof HTMLDialogElement) {
                    abierto.close();
                }
            });
            dialogo.showModal();
        }
    });
});

document.querySelectorAll('dialog [data-close-dialog]').forEach((boton) => {
    boton.addEventListener('click', () => {
        boton.closest('dialog')?.close();
    });
});

document.querySelectorAll('dialog').forEach((dialogo) => {
    dialogo.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('[data-combobox-list]')) {
            return;
        }

        if (event.target === dialogo) {
            dialogo.close();
        }
    });
});

document.querySelectorAll('[data-firma-form]').forEach((formulario) => {
    const editor = formulario.querySelector('[data-firma-editor]');
    const contenido = formulario.querySelector('[data-firma-contenido]');
    const nombre = formulario.querySelector('[data-firma-nombre]');
    const archivo = formulario.querySelector('[data-firma-archivo]');
    const insertar = formulario.querySelector('[data-firma-insertar]');

    if (
        ! (formulario instanceof HTMLFormElement)
        || ! (editor instanceof HTMLElement)
        || ! (contenido instanceof HTMLInputElement)
        || ! (nombre instanceof HTMLInputElement)
        || ! (archivo instanceof HTMLInputElement)
    ) {
        return;
    }

    const sincronizar = () => {
        contenido.value = editor.innerHTML.trim();
        const texto = editor.innerText.replace(/\s+/g, ' ').trim();
        editor.toggleAttribute('data-empty', texto === '' && ! editor.querySelector('img'));
    };

    const insertarImagen = (file) => {
        if (! file.type.startsWith('image/')) {
            return;
        }

        const lector = new FileReader();
        lector.onload = () => {
            const imagen = document.createElement('img');
            imagen.src = String(lector.result || '');
            imagen.alt = 'Firma';
            imagen.style.maxWidth = '320px';
            imagen.style.height = 'auto';
            imagen.style.display = 'block';
            editor.appendChild(imagen);
            sincronizar();
        };
        lector.readAsDataURL(file);
    };

    editor.addEventListener('input', sincronizar);
    editor.addEventListener('paste', (event) => {
        const items = event.clipboardData?.items;

        if (! items) {
            return;
        }

        for (const item of items) {
            if (item.type.startsWith('image/')) {
                event.preventDefault();
                const file = item.getAsFile();

                if (file) {
                    insertarImagen(file);
                }

                return;
            }
        }
    });

    insertar?.addEventListener('click', () => archivo.click());
    archivo.addEventListener('change', () => {
        const file = archivo.files?.[0];

        if (file) {
            insertarImagen(file);
            archivo.value = '';
        }
    });

    formulario.addEventListener('submit', sincronizar);

    if (editor.innerHTML.trim() === '') {
        editor.setAttribute('data-empty', 'true');
    }

    sincronizar();
});

const firmaSelect = document.querySelector('[data-firma-select]');
const firmaEliminar = document.querySelector('[data-firma-eliminar]');
const firmaEliminarBoton = document.querySelector('[data-firma-eliminar-boton]');

if (
    firmaSelect instanceof HTMLSelectElement
    && firmaEliminar instanceof HTMLFormElement
    && firmaEliminarBoton instanceof HTMLButtonElement
) {
    const actualizar = () => {
        const valor = firmaSelect.value;
        firmaEliminarBoton.disabled = valor === '';

        if (valor !== '') {
            firmaEliminar.action = `${firmaEliminar.dataset.firmaEliminarBase}/${valor}`;
        }
    };

    firmaSelect.addEventListener('change', actualizar);
    actualizar();
}

const abrirDialogos = document.querySelector('[data-abrir-dialogos]');

if (abrirDialogos instanceof HTMLElement) {
    try {
        const datos = JSON.parse(abrirDialogos.textContent || '{}');
        const id = datos.firma ? 'modal-firma' : (datos.correo ? 'modal-correo' : null);
        const dialogo = id ? document.getElementById(id) : null;

        if (dialogo instanceof HTMLDialogElement) {
            dialogo.showModal();
        }
    } catch {
        //
    }
}
