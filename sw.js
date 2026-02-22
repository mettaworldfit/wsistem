const CACHE_STATIC_NAME = "static-v3";
const CACHE_DYNAMIC_NAME = "dynamic-v1";
const CACHE_INMUTABLE_NAME = "inmutable-v3";
const CACHE_DYNAMIC_LIMIT = 20;

function limpiarCache(cacheName, numeroItems) {


    caches.open(cacheName)
        .then(cache => {

            return cache.keys()
                .then(keys => {

                    if (keys.length > numeroItems) {
                        cache.delete(keys[0])
                            .then(limpiarCache(cacheName, numeroItems));
                    }
                });
        });
}


self.addEventListener("install", (e) => {
    const cacheProm = caches.open(CACHE_STATIC_NAME)
        .then((cache) => {
            return cache.addAll([

                "public/style.css",
                "public/imagen/sistem/icon.ico",
                "public/imagen/img/img1.jpg",
                "public/imagen/img/img2.jpg",
                "public/imagen/img/img3.jpg",
                "public/imagen/sistem/no-imagen.png",
                "public/login.css",
                "public/vendor/jquery/jquery.js",
                "public/fonts/Cabin-Regular.ttf",
                "public/fonts/Nunito-Regular.ttf",
                "public/fonts/Barlow-Regular.ttf",
                "public/font-awesome/all.min.css",
                "public/font-awesome/all.min.js"
            ]);
        });

    const cacheInmutable = caches.open(CACHE_INMUTABLE_NAME).then((cache) => {
        return cache.addAll([

            "public/vendor/chartjs/chart.umd.js",
            "public/vendor/alertify/alertify.min.js",
            "public/vendor/alertify/css/alertify.min.css",
            "public/vendor/alertify/css/themes/default.min.css",

            "public/vendor/mdtoast/mdtoast.min.js",
            "public/vendor/mdtoast/mdtoast.min.css",
            "public/vendor/bootstrap4/popper.min.js",
            "public/vendor/bootstrap4/bootstrap.min.js",
            "public/vendor/bootstrap4/bootstrap.min.css",
            "public/vendor/datatable/dataTables.bootstrap4.min.css",
            "public/vendor/datatable/jquery.dataTables.min.js",
            "public/vendor/datatable/dataTables.bootstrap4.min.js",
            "public/vendor/select2/select2.min.css",
            "public/vendor/select2/select2.full.min.js",
            "public/vendor/qz/qz-tray.js",
            'https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js'

        ]);
    });

    e.waitUntil(Promise.all([cacheProm, cacheInmutable]));
});


self.addEventListener('activate', e => {


    const respuesta = caches.keys().then(keys => {

        keys.forEach(key => {

            // static-v1
            if (key !== CACHE_STATIC_NAME && key.includes('static')) {
                return caches.delete(key);
            }

        });

    });

    e.waitUntil(respuesta);

});


self.addEventListener("fetch", (event) => {

    const request = event.request;
    const contentType = request.headers.get('Content-Type');


    // Manejamos el resto de las solicitudes con cache firts

    const resp = caches.match(request).then((response) => {

        // Responder solicitud es encontrada en el cache
        if (response) {

            return response.clone();

        } else {

            // No se encontro la peticion en el cache, buscar en internet
            return fetch(request).then((response) => {

                // Almacenamos la respuesta en caché excluyendo peticiones POST
                if (!request.method.includes("POST") && !event.request.destination === 'document') { //ojo

                    caches.open(CACHE_DYNAMIC_NAME).then((cache) => {
                        cache.put(request, response.clone());
                        limpiarCache(CACHE_DYNAMIC_NAME, CACHE_DYNAMIC_LIMIT); // Limpiar versiones anteriores del cache Dynamic
                    });

                }

                return response.clone();
            });

        }
    })


    event.respondWith(resp);


});