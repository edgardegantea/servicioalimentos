/**
 * Lógica del Punto de Venta (POS)
 */
let cart = [];

// 1. Agregar Producto al Carrito
function addToCart(id, name, price) {
    // Verificar si ya existe para aumentar cantidad
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.push({ id: id, name: name, price: parseFloat(price), qty: 1 });
    }
    
    renderCart();
    playBeep(); // Feedback auditivo opcional
}

// 2. Renderizar HTML del Ticket
function renderCart() {
    const container = document.getElementById('cart-container');
    const totalEl = document.getElementById('cart-total');
    
    container.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted mt-5">
                <i class="fa-solid fa-basket-shopping fa-3x mb-3 opacity-25"></i>
                <p>Selecciona productos</p>
            </div>`;
        totalEl.innerText = '$0.00';
        return;
    }

    cart.forEach((item, index) => {
        let subtotal = item.price * item.qty;
        total += subtotal;

        const row = document.createElement('div');
        row.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'mb-3', 'border-bottom', 'pb-2');
        row.innerHTML = `
            <div>
                <div class="fw-bold">${item.name}</div>
                <small class="text-muted">$${item.price.toFixed(2)} x ${item.qty}</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="fw-bold me-3">$${subtotal.toFixed(2)}</span>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });

    totalEl.innerText = '$' + total.toFixed(2);
}

// 3. Eliminar Item
function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

// 4. Enviar Venta al Servidor (AJAX)
async function processSale() {
    if (cart.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    const btn = document.getElementById('btn-checkout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';

    // Obtener Token CSRF (Seguridad CodeIgniter)
    const csrfName = document.querySelector('.csrf-token').getAttribute('name');
    const csrfHash = document.querySelector('.csrf-token').getAttribute('content');

    // Calcular totales finales
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

    const payload = {
        items: cart,
        subtotal: total,
        total: total,
        [csrfName]: csrfHash // Incluir token en payload si se envía como form data, o headers
    };

    try {
        const response = await fetch('/pos/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash // Importante para CI4
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(`¡Venta Exitosa!\nFolio: ${result.folio}`);
            cart = []; // Limpiar carrito
            renderCart();
        } else {
            alert('Error: ' + result.message);
        }

    } catch (error) {
        console.error(error);
        alert('Error de conexión');
    } finally {
        btn.disabled = false;
        btn.innerText = 'COBRAR';
    }
}

// Sonidito beep simple (opcional)
function playBeep() {
    // Implementación simple o dejar vacío
}