<?php

include 'encabezado.html';
?>

<style>
    .sobre-nosotros-container {
        padding: 40px 20px;
        max-width: 900px;
        margin: 0 auto;
        background-color: #fffaf0;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(255, 111, 0, 0.2);
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .sobre-nosotros-container h2 {
        color: #bf360c; 
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
        border-bottom: 3px solid #ffcc80;
        padding-bottom: 10px;
    }

    .sobre-nosotros-container h3 {
        color: #ff6f00; 
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .sobre-nosotros-container p {
        color: #4a2c0f; 
        line-height: 1.6;
        margin-bottom: 1rem;
        text-align: justify;
    }
    
    .logo-upqroo {
        max-width: 150px;
        height: auto;
        display: block;
        margin: 0 auto 2rem auto;
    }

    .beneficios-list {
        list-style-type: none;
        padding-left: 0;
    }

    .beneficios-list li {
        background-color: #ffcc80;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 5px rgba(255, 111, 0, 0.1);
        color: #4a2c0f;
    }

    .beneficios-list li strong {
        color: #bf360c;
    }
</style>

<main class="container">
    <div class="sobre-nosotros-container">
        

        <h2>Acerca de Nosotros: Facilitando tu Día Universitario</h2>
        
        <p>
            Bienvenido a la plataforma de pedidos en línea diseñada exclusivamente para la <strong>Cafetería de la Universidad Politécnica de Quintana Roo (UPQROO)</strong>.
            Nuestra función principal es simple pero vital: <strong>digitalizar el proceso de pedido de alimentos</strong> para que tú, como estudiante o personal de la universidad,
            puedas aprovechar al máximo tu tiempo.
        </p>

        <hr>

        <h3>Nuestra Misión: Eficiencia y Comodidad</h3>
        <p>
            Sabemos que el tiempo de receso es limitado. Por eso, esta plataforma fue creada para eliminar las largas filas y la espera innecesaria.
            Ahora puedes <strong>explorar el menú del día</strong>, ver los precios y la disponibilidad de los productos, y realizar tu pedido desde tu teléfono o computadora,
            todo esto antes de que toque la campana. 
        </p>

        <h3>¿Cómo Beneficia a la Comunidad UPQROO?</h3>

        <ul class="beneficios-list">
            <li>
                <strong>Ahorro de Tiempo:</strong> Realiza el pedido en clase o mientras caminas. Recógelo en el momento justo, ¡sin esperas!
            </li>
            <li>
                <strong>Transparencia:</strong> Consulta los precios y los detalles de los platillos en cualquier momento.
            </li>
            <li>
                <strong>Comodidad:</strong> Evita el estrés de las aglomeraciones. Más tiempo para disfrutar tu comida y convivir.
            </li>
            <li>
                <strong>Organización para la Cafetería:</strong> Ayudamos al personal de la cafetería a gestionar mejor la demanda y preparar los pedidos con anticipación.
            </li>
        </ul>
        
        <hr>

        <p class="text-center" style="font-style: italic;">
            Somos tu puente digital hacia la comida de la cafetería. ¡Pide en línea y disfruta más de tu día en la UPQROO!
        </p>
    </div>
</main>

<?php
include 'pie.html';
?>