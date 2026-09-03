import path from 'path'
import fs from 'fs'
import { glob } from 'glob'
import { src, dest, watch, series } from 'gulp'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'
import concat from 'gulp-concat'
import terser from 'gulp-terser'
import sharp from 'sharp'
import rename from 'gulp-rename'

const sass = gulpSass(dartSass)

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    img: 'src/img/**/*'
}

// ==========================
// CSS
// ==========================
export function css(done) {
    src(paths.scss, { sourcemaps: true })
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(dest('./build/css', { sourcemaps: '.' }))
    done()
}

// ==========================
// JS
// ==========================
export function js(done) {
    src(paths.js)
        .pipe(concat('app.js'))
        .pipe(terser())
        .pipe(rename({ suffix: '.min' }))
        .pipe(dest('./build/js'))
    done()
}

// ==========================
// 🖼️ IMÁGENES (FIX PNG)
// ==========================
export async function imagenes(done) {
    const srcDir = './src/img'
    const buildDir = './build/img'
    const images = await glob(paths.img)

    images.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file))
        const outputSubDir = path.join(buildDir, relativePath)

        procesarImagenes(file, outputSubDir)
    })

    done()
}

function procesarImagenes(file, outputSubDir) {
    if (!fs.existsSync(outputSubDir)) {
        fs.mkdirSync(outputSubDir, { recursive: true })
    }

    const baseName = path.basename(file, path.extname(file))
    const extName = path.extname(file).toLowerCase()

    const options = { quality: 80 }

    // ✅ SVG, ICO y WEBMANIFEST → copiar tal cual (sin procesar)
    if (extName === '.svg' || extName === '.ico' || extName === '.webmanifest') {
        fs.copyFileSync(file, path.join(outputSubDir, `${baseName}${extName}`))
        return
    }

    // ✅ PNG → mantener PNG (SIN convertir, SIN redimensionar)
    if (extName === '.png') {
        const outputPNG = path.join(outputSubDir, `${baseName}.png`)

        sharp(file)
            .png({ quality: 80, compressionLevel: 9 })
            .toFile(outputPNG)

        return
    }

    // ✅ JPG / JPEG → generar múltiples formatos (SIN redimensionar)
    if (extName === '.jpg' || extName === '.jpeg') {

        const outputJPG = path.join(outputSubDir, `${baseName}.jpg`)
        const outputWebp = path.join(outputSubDir, `${baseName}.webp`)
        const outputAvif = path.join(outputSubDir, `${baseName}.avif`)

        sharp(file)
            .jpeg(options)
            .toFile(outputJPG)

        sharp(file)
            .webp(options)
            .toFile(outputWebp)

        sharp(file)
            .avif({ quality: 50 })
            .toFile(outputAvif)
    }
}

// ==========================
// WATCH
// ==========================
export function dev() {
    watch(paths.scss, css)
    watch(paths.js, js)
    watch('src/img/**/*', imagenes)
}

// ==========================
// DEFAULT
// ==========================
export default series(js, css, imagenes, dev)