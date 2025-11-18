<?php

include 'encabezado.html';
?>

<style>
    .aviso-privacidad-container {
        padding: 40px 20px;
        max-width: 900px;
        margin: 0 auto;
        background-color: #fffaf0; 
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(255, 111, 0, 0.2);
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .aviso-privacidad-container h2 {
        color: #bf360c; 
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
        border-bottom: 3px solid #ffcc80;
        padding-bottom: 10px;
    }

    .aviso-privacidad-container h3 {
        color: #ff6f00;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .aviso-privacidad-container p, 
    .aviso-privacidad-container ul {
        color: #4a2c0f; 
        line-height: 1.6;
        margin-bottom: 1rem;
        text-align: justify;
    }
    
    .aviso-privacidad-container ul {
        list-style-type: disc;
        padding-left: 20px;
    }

    .aviso-privacidad-container strong {
        color: #bf360c;
    }
</style>

<main class="container">
    <div class="aviso-privacidad-container">

        <h2>Aviso de Privacidad de la Plataforma de la Cafetería UPQROO</h2>
        
        <p>
            Este aviso describe la forma en que el sitio web de la Cafetería de la Universidad Politécnica de Quintana Roo (en adelante, "la Plataforma") recaba y utiliza los datos personales de sus usuarios.
        </p>

        <hr>

        <h3>1. Responsable del Tratamiento de Datos</h3>
        <p>
            La Plataforma es responsabilidad del equipo de desarrollo del 24BM de la <strong>Universidad Politécnica de Quintana Roo (UPQROO)</strong>, y se utiliza para gestionar los pedidos de la cafetería universitaria.
        </p>

        <hr>

        <h3>2. Datos Personales Recabados</h3>
        <p>
            Para los fines establecidos en este Aviso, la Plataforma puede recabar los siguientes datos personales, dependiendo del uso que se le dé:
        </p>
        <ul>
            <li>
                <strong>Identificación y Contacto:</strong> Nombre completo, correo electrónico institucional (matrícula o número de empleado, si se utiliza para el registro).
            </li>
            <li>
                <strong>Información de Pedido:</strong> Detalle de los productos solicitados, hora y fecha del pedido, y el estado de la entrega.
            </li>
            <li>
                <strong>Datos de Pago (Si aplica):</strong> La Plataforma <strong>no almacena directamente</strong> datos sensibles como números de tarjeta de crédito. La información de pago se procesa a través de plataformas de terceros (sistemas de TPV local o pasarelas de pago), siendo estas las responsables de la seguridad y confidencialidad de dichos datos.
            </li>
        </ul>

        <hr>

        <h3>3. Finalidades del Tratamiento de Datos</h3>
        <p>
            Los datos personales recabados serán utilizados para las siguientes finalidades esenciales:
        </p>
        <ul>
            <li><strong>Gestión de Pedidos:</strong> Procesar, dar seguimiento y entregar los pedidos de alimentos y bebidas realizados.</li>
            <li><strong>Comunicación:</strong> Enviar notificaciones sobre el estado de su pedido (ej. "Listo para recoger").</li>
            <li><strong>Mejora del Servicio:</strong> Analizar las preferencias de consumo para optimizar el menú y la eficiencia operativa de la cafetería.</li>
            <li><strong>Seguridad y Verificación:</strong> Confirmar la identidad del usuario al momento de la recolección del pedido.</li>
        </ul>

        <hr>

        <h3>4. Medidas de Seguridad</h3>
        <p>
            La Plataforma implementa medidas de seguridad administrativas, técnicas y físicas para proteger sus datos personales contra daño, pérdida, alteración, destrucción o el uso, acceso o tratamiento no autorizado.
        </p>
        <p>
            La transmisión de datos sensibles se realiza bajo el protocolo HTTPS y el acceso a la información está restringido solo al personal autorizado de la cafetería y administración.
        </p>

        <hr>

        <h3>5. Derechos ARCO</h3>
        <p>
            Usted tiene derecho a ejercer sus derechos de <strong>Acceso</strong>, <strong>Rectificación</strong>, <strong>Cancelación</strong> y <strong>Oposición</strong> respecto al tratamiento de sus datos personales.
        </p>
        <p>
            Para ejercer cualquiera de estos derechos, deberá enviar una solicitud por escrito a la dirección de correo electrónico <strong>202400272@upqroo.edu.mx</strong>. La solicitud deberá especificar el derecho que desea ejercer.
        </p>

        <hr>

        <h3>6. Cambios al Aviso de Privacidad</h3>
        <p>
            Este Aviso de Privacidad puede ser modificado en cualquier momento. Cualquier cambio será publicado en la Plataforma en esta misma sección. Se recomienda revisar esta sección periódicamente.
        </p>

        <p class="text-center" style="font-style: italic; margin-top: 2rem;">
            Última Actualización: 17 de noviembre de 2025.
        </p>
    </div>
</main>

<?php
include 'pie.html';
?>