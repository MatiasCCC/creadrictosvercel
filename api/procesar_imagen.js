import { readFile } from 'fs/promises';
import path from 'path';
import rembg from 'rembg';
import sharp from 'sharp';

export default async function handler(req, res) {
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Método no permitido' });
    }

    try {
        // Recibe la imagen desde el frontend
        const fileBuffer = await req.arrayBuffer();
        const inputBuffer = Buffer.from(fileBuffer);

        // Elimina el fondo de la imagen usando rembg
        const imageWithoutBackground = await rembg.remove(inputBuffer);

        // Cargar una imagen de fondo personalizada (por ejemplo, "fondo.jpg" en el proyecto)
        const backgroundBuffer = await readFile(path.join(process.cwd(), 'public', 'https://factoryfy.es/wp-content/uploads/diseno-logo-farmacia-mosaico.jpg'));

        // Combina la imagen segmentada con el nuevo fondo usando sharp
        const finalImage = await sharp(backgroundBuffer)
            .composite([{ input: imageWithoutBackground, blend: 'over' }])
            .toBuffer();

        // Enviar la imagen procesada al cliente
        res.setHeader('Content-Type', 'image/png');
        res.status(200).send(finalImage);
    } catch (error) {
        console.error("Error al procesar la imagen:", error);
        res.status(500).json({ error: 'Error al procesar la imagen' });
    }
}
