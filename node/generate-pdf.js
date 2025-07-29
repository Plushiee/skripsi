import puppeteer from 'puppeteer';

(async () => {
    const args = process.argv.slice(2); // Ambil argumen dari command line
    const inputFile = args[0]; // Jalur file HTML
    const outputFile = args[1]; // Jalur file PDF

    const browser = await puppeteer.launch({
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--unlimited-storage',
            '--full-memory-crash-report'
        ],
        cacheDirectory: 'C:\\Users\\Administrator\\.puppeteer',
        headless: true,
    });

    const page = await browser.newPage();

    try {
        await page.goto(inputFile, {
            waitUntil: 'networkidle0',
            timeout: 0 // Hindari timeout jika grafik lambat
        });

        // ⏳ Tunggu sampai semua canvas punya tinggi > 0 (grafik selesai render)
        await page.waitForFunction(() => {
            const canvases = document.querySelectorAll('canvas');
            if (canvases.length === 0) return false;

            return Array.from(canvases).every(canvas => {
                return canvas.offsetHeight > 0 && canvas.offsetWidth > 0;
            });
        }, {
            timeout: 10000 // Tunggu maksimal 10 detik
        });

        await new Promise(resolve => setTimeout(resolve, 1000));
        // 1 detik ekstra untuk stabilisasi

        await page.pdf({
            path: outputFile,
            format: 'A4',
            landscape: true,
            printBackground: true,
        });

        console.log('OK: PDF berhasil dibuat:', outputFile);

    } catch (error) {
        console.error('ERR: Gagal membuat PDF:', error);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
