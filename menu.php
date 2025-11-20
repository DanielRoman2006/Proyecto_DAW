<?php
include 'conexion.php';
session_start();

$usuario_autenticado = isset($_SESSION['usuario']) || isset($_SESSION['nombre']);

$sql = "SELECT id_producto, nombre, descripcion, precio, imagen_url FROM productos WHERE disponible = 1";
$resultado = $conn->query($sql);

if ($usuario_autenticado) {
    include 'encabezado_con_sesion.php';
} else {
    include 'encabezado_sin_sesion.php';
}
?>

<style>
main {
    padding: 20px 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.cards-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin: 0 auto;
    padding: 0 10px;
    max-width: 1200px;
}

.card {
    background-color: #fff3e0;
    border-radius: 1rem;
    width: 14rem;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    justify-content: space-between;
    min-height: 22rem;
    box-shadow: 0 4px 8px rgba(255,111,0,0.3);
    transition: transform 0.3s ease;
    color: #4a2c0f;
    cursor: pointer; 
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(255,111,0,0.5);
}

.image_container {
    background-color: #ffcc80;
    border-radius: 0.5rem;
    height: 8rem;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image_container img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
    border-radius: 0.5rem;
    transition: transform 0.3s ease;
}

.image_container:hover img {
    transform: scale(1.05); 
}

.title {
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: capitalize;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.descripcion {
    font-size: 0.9rem;
    height: 3rem;
    overflow: hidden;
    text-overflow: ellipsis;
}

.price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #bf360c;
}

.cart-button {
    cursor: pointer;
    background: linear-gradient(0deg, #ff6f00 50%, #fff7f0 125%);
    border-radius: 0.5rem;
    border: 2px solid rgba(255,111,0,0.5);
    box-shadow: inset 0 0 0.25rem 1px #4a2c0f;
    color: #4a2c0f;
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    user-select: none;
    transition: 0.3s;
    position: relative; 
    z-index: 10;
}

.cart-button:hover {
    background-color: #bf360c;
    border-color: #bf360c;
    color: white;
}

/* Modal action button (detail view) - visually distinct and slightly larger font */
.modal-add-button {
    background: linear-gradient(0deg, #ff6f00 60%, #fff7f0 100%);
    border: 2px solid rgba(255,111,0,0.5);
    color: #4a2c0f;
    padding: 0.55rem 0.9rem;
    font-weight: 700;
    font-size: 0.95rem;
    border-radius: 0.6rem;
    display: inline-block;
}
.modal-add-button:hover { background-color:#bf360c; color:white; }

.carrusel-img {
    width: 100%;
    height: 1000px;
    object-fit: cover;
    display: inline-block;
}

.validation-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.validation-modal-content {
    background-color: #fff3e0;
    margin: 15% auto;
    padding: 2rem;
    border-radius: 1rem;
    width: 90%;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(255,111,0,0.3);
    color: #4a2c0f;
}

.validation-modal-close, .validation-modal-action {
    background: #ff6f00;
    border: none;
    border-radius: 0.5rem;
    padding: 0.8rem 1.5rem;
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.3s;
    margin: 0.5rem;
}

.validation-modal-close:hover, .validation-modal-action:hover {
    background: #bf360c;
}

#detalleModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    overflow: auto;
}

.detalle-modal-content {
    background-color: #fff3e0;
    margin: 5% auto;
    padding: 2rem;
    border-radius: 1rem;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 40px rgba(255,111,0,0.5);
    color: #4a2c0f;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    animation: fadeIn 0.3s ease-out;
}

.detalle-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #ffcc80;
    padding-bottom: 1rem;
}

.detalle-modal-close {
    color: #4a2c0f;
    font-size: 2.5rem;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
    transition: color 0.2s;
    padding: 0 0.5rem;
}

.detalle-modal-close:hover,
.detalle-modal-close:focus {
    color: #bf360c;
}

.detalle-modal-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detalle-modal-body img {
    max-width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: contain;
    border-radius: 0.75rem;
    margin: 0 auto;
    border: 3px solid #ffcc80;
}

.detalle-modal-price {
    font-size: 1.8rem;
    font-weight: 700;
    color: #bf360c;
}

.detalle-modal-title {
    font-size: 1.8rem;
    font-weight: 700;
    text-transform: capitalize;
    margin: 0;
}

.detalle-modal-descripcion {
    font-size: 1.1rem;
    line-height: 1.5;
    text-align: justify;
}

.detalle-modal-footer {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid #ffcc80;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<main class="container">
<div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner rounded-4">
        <div class="carousel-item active">
            <img src="imagenes/carrusel1.png" class="d-block w-100 carrusel-img">
        </div>
        <div class="carousel-item">
            <img src="imagenes/carrusel2.png" class="d-block w-100 carrusel-img">
        </div>
        <div class="carousel-item">
            <img src="imagenes/carrusel3.png" class="d-block w-100 carrusel-img">
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<section class="text-center mb-4">
    <h2 style="color:#ff6f00;">¿Con hambre? Nosotros cocinamos por ti</h2>
    <p style="color:#5d4037;">Descubre nuestros platillos del día y haz tu pedido antes del recreo.</p>
</section>

<h1 class="text-center mb-4" style="color:#bf360c;">Menú del Día</h1>

<div class="row g-3 justify-content-center">
<?php
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre = htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars($fila['descripcion'], ENT_QUOTES, 'UTF-8');
        $precio = number_format($fila['precio'], 2);
        $imagen_url = htmlspecialchars($fila['imagen_url'], ENT_QUOTES, 'UTF-8');
        $id_producto = htmlspecialchars($fila['id_producto'], ENT_QUOTES, 'UTF-8');
?>
      <div class="col-6 col-sm-4 col-md-3 col-lg-2 d-flex justify-content-center">
        <div class="card" 
             onclick="mostrarDetallesProducto('<?= $nombre ?>', '<?= $descripcion ?>', '<?= $precio ?>', '<?= $imagen_url ?>', '<?= $id_producto ?>')">
          <div class="image_container">
            <img src="<?= $imagen_url ?>" alt="<?= $nombre ?>">
          </div>
          <div class="title"><?= $nombre ?></div>
          <div class="descripcion"><?= $descripcion ?></div>
          <div class="price">$<?= $precio ?></div>
                    <button class="cart-button" onclick="event.stopPropagation(); addToCart(<?= $id_producto ?>);" aria-label="Agregar al carrito" title="Agregar al carrito">
                        Agregar
                    </button>
        </div>
      </div>
<?php
    }
} else {
    echo "<p class='text-center'>No hay productos disponibles.</p>";
}
?>
</div>
</main>

<div id="alertaSesion" class="validation-modal">
    <div class="validation-modal-content">
        <h3>Inicia Sesión</h3>
        <p>Debes iniciar sesión para comprar productos.</p>
        <button class="validation-modal-close" onclick="cerrarAlertaSesion()">Cerrar</button>
    </div>
</div>

<div id="alertaPedidoAgregado" class="validation-modal">
    <div class="validation-modal-content">
        <h3>¡Pedido Agregado!</h3>
        <p>Tu producto se ha añadido a tu carrito de pedidos.</p>
        <div class="d-flex justify-content-center mt-3">
            <button class="validation-modal-close" onclick="cerrarAlertaPedidoAgregado()">Seguir Comprando</button>
            <a href="carrito.php" class="validation-modal-action text-decoration-none">Ver Pedidos</a>
        </div>
    </div>
</div>

<div id="detalleModal" class="validation-modal">
    <div class="detalle-modal-content">
        <div class="detalle-modal-header">
            <h2 class="detalle-modal-title" id="detalle-nombre"></h2>
            <span class="detalle-modal-close" onclick="cerrarDetalleModal()">&times;</span>
        </div>
        <div class="detalle-modal-body">
            <img id="detalle-imagen" src="" alt="Imagen del Producto">
            <p class="detalle-modal-descripcion" id="detalle-descripcion"></p>
            <p class="detalle-modal-price" id="detalle-precio"></p>
        </div>
        <div class="detalle-modal-footer">
            <button class="modal-add-button" id="btn-agregar-detalle" onclick="manejarBotonAgregarDetalle()">
                Agregar al Carrito
            </button>
        </div>
    </div>
</div>

<script>
let productoSeleccionadoId = null; 
const IS_AUTHENTICATED = <?= $usuario_autenticado ? 'true' : 'false' ?>;

function abrirAlertaSesion() {
    document.getElementById("alertaSesion").style.display = "block";
}
function cerrarAlertaSesion() {
    document.getElementById("alertaSesion").style.display = "none";
}
function abrirAlertaPedidoAgregado() {
    document.getElementById("alertaPedidoAgregado").style.display = "block";
}
function cerrarAlertaPedidoAgregado() {
    document.getElementById("alertaPedidoAgregado").style.display = "none";
}
function cerrarDetalleModal() {
    document.getElementById("detalleModal").style.display = "none";
}

function mostrarDetallesProducto(nombre, descripcion, precio, imagenUrl, id_producto) {
    const modal = document.getElementById("detalleModal");
    document.getElementById("detalle-nombre").textContent = nombre;
    document.getElementById("detalle-descripcion").textContent = descripcion;
    document.getElementById("detalle-precio").textContent = `$${precio}`; 
    document.getElementById("detalle-imagen").src = imagenUrl;
    document.getElementById("detalle-imagen").alt = "Imagen de " + nombre;
    productoSeleccionadoId = id_producto;
    modal.style.display = "block";
}

function manejarBotonAgregarDetalle() {
    cerrarDetalleModal();
    if (productoSeleccionadoId) {
        agregarACarrito(productoSeleccionadoId);
    }
}

function agregarACarrito(id_producto) {
    if (!IS_AUTHENTICATED) {
        abrirAlertaSesion();
        return;
    }

    const formData = new URLSearchParams();
    formData.append('action', 'add');
    formData.append('product_id', id_producto);
    formData.append('qty', 1);

    fetch('cart_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: formData.toString()
    }).then(resp => resp.json())
    .then(data => {
        if (data && data.success) {
            console.log('Added to cart, new count:', data.cart_count);
            abrirAlertaPedidoAgregado();

            const badge = document.getElementById('cart-count-badge');
            if (badge && typeof data.cart_count !== 'undefined') badge.textContent = data.cart_count;
        } else {
            console.error('Cart API error', data);
            alert(data && data.message ? ('Error: ' + data.message) : 'Error al agregar al carrito');
        }
    }).catch(err => {
        console.error('Fetch error', err);
        alert('Error al agregar al carrito');
    });
}

function addToCart(id_producto) {
    agregarACarrito(id_producto);
}

window.onclick = function(event) {
    const detalleModal = document.getElementById("detalleModal");
    const alertaSesionModal = document.getElementById("alertaSesion");
    const alertaPedidoAgregadoModal = document.getElementById("alertaPedidoAgregado");

    if (event.target == detalleModal) {
        detalleModal.style.display = "none";
    }
    if (event.target == alertaSesionModal) {
        alertaSesionModal.style.display = "none";
    }
    if (event.target == alertaPedidoAgregadoModal) {
        alertaPedidoAgregadoModal.style.display = "none";
    }
}
</script>
<?php
include 'pie.html';
?>
