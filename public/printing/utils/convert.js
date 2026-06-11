export function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

/**
 * Convierte una imagen desde una URL a Base64 sin el prefijo `data:image/png;base64,`.
 * 
 * @param {string} imageUrl - La URL de la imagen a convertir.
 * @returns {Promise<string>} - Promesa que devuelve la cadena Base64 sin el prefijo.
 */
export function convertImageUrlToBase64(imageUrl) {
    return new Promise((resolve, reject) => {
        const image = new Image();

        // IMPORTANTE para evitar canvas tainted
        image.crossOrigin = 'anonymous';

        image.onload = () => {
            try {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = image.width;
                canvas.height = image.height;

                ctx.drawImage(image, 0, 0);

                const base64 = canvas
                    .toDataURL('image/png')
                    .split(',')[1];

                resolve(base64);
            } catch (e) {
                reject(e);
            }
        };

        image.onerror = () => reject('No se pudo cargar la imagen');

        // evita cache viejo
        image.src = imageUrl + '?v=' + Date.now();
    });
}