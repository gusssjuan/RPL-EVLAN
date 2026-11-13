import { access, unlink } from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

const dir = path.resolve('public/images');
const names = ['hero', 'tujuan', 'login', 'register'];
const widths = [640, 1280];

for (const name of names) {
    const source = path.join(dir, `${name}.png`);

    try {
        await access(source);
    } catch {
        console.warn(`Lewati ${source}: berkas tidak ditemukan.`);
        continue;
    }

    for (const width of widths) {
        const target = path.join(dir, `${name}-${width}.webp`);
        const info = await sharp(source)
            .resize({ width, withoutEnlargement: true })
            .webp({ quality: 80 })
            .toFile(target);

        console.log(`${path.relative(process.cwd(), target)} ${info.width}x${info.height} ${Math.round(info.size / 1024)} KB`);
    }
}
