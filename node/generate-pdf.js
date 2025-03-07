import puppeteer from 'puppeteer';

(async () => {
    const args = process.argv.slice(2); // Ambil argumen dari command line
    const inputFile = args[0]; // Jalur file HTML
    const outputFile = args[1]; // Jalur file PDF

    const browser = await puppeteer.launch({
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage', '--unlimited-storage', '--full-memory-crash-report'],
        cacheDirectory: 'C:\\Users\\Administrator\\.puppeteer',
        headless: true,
    });

    const page = await browser.newPage();

    try {
        // Buka halaman HTML
        await page.goto(inputFile, { waitUntil: 'networkidle0' });

        // Generate PDF
        await page.pdf({
            path: outputFile,
            format: 'A4',
            landscape: true,
            printBackground: true,
        });

        console.log('PDF berhasil dibuat:', outputFile);

    } catch (error) {
        console.error('Gagal membuat PDF:', error);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
