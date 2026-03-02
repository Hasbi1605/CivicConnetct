/**
 * V4 — Desktop Diagonal Showcase
 * All 15 desktop screenshots in browser-window frames arranged in a rotated grid.
 * Outputs a single composite image.
 */
const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const SCREENSHOTS_DIR = __dirname;
const OUTPUT_DIR = path.join(__dirname, "..", "mockups-v4");

const desktopFiles = [
    "01-login.png",
    "02-beranda-feed.png",
    "03-beranda-voting.png",
    "04-hoax-buster.png",
    "05-hoax-buster-detail.png",
    "06-lab-room.png",
    "07-lab-room-detail.png",
    "08-lab-room-diskusi.png",
    "09-policy-lab.png",
    "10-policy-brief-detail.png",
    "11-moderasi.png",
    "12-moderasi-riwayat.png",
    "13-post-detail.png",
    "14-post-komentar.png",
    "15-profil.png",
];

// Layout constants
const CANVAS_W = 3840;
const CANVAS_H = 2400;
const COLS = 5;
const ROWS = 5;
const CARD_W = 620;
const CARD_H = 420;
const GAP_X = 50;
const GAP_Y = 50;
const ROW_OFFSET = 335; // stagger offset for odd rows
const ROTATION = -15; // degrees
const TITLEBAR_H = 32;

function buildHTML(base64Images) {
    const totalCards = COLS * ROWS; // 25 cards, cycle 15 images
    let cards = "";

    for (let i = 0; i < totalCards; i++) {
        const imgIdx = i % base64Images.length;
        const row = Math.floor(i / COLS);
        const col = i % COLS;
        const x = col * (CARD_W + GAP_X) + (row % 2 === 1 ? ROW_OFFSET : 0);
        const y = row * (CARD_H + GAP_Y);

        cards += `
        <div class="browser-card" style="left:${x}px; top:${y}px;">
          <div class="browser-titlebar">
            <span class="dot red"></span>
            <span class="dot yellow"></span>
            <span class="dot green"></span>
            <div class="url-bar">civic-connect.test</div>
          </div>
          <div class="browser-content">
            <img src="data:image/png;base64,${base64Images[imgIdx]}" />
          </div>
        </div>`;
    }

    const gridW = COLS * (CARD_W + GAP_X) + ROW_OFFSET;
    const gridH = ROWS * (CARD_H + GAP_Y);

    return `<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap');

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    width: ${CANVAS_W}px;
    height: ${CANVAS_H}px;
    background: #c8ced4;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', -apple-system, sans-serif;
  }

  .grid {
    position: relative;
    width: ${gridW}px;
    height: ${gridH}px;
    transform: rotate(${ROTATION}deg);
    flex-shrink: 0;
  }

  .browser-card {
    position: absolute;
    width: ${CARD_W}px;
    height: ${CARD_H}px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.18), 0 3px 10px rgba(0,0,0,0.10);
    background: #fff;
  }

  .browser-titlebar {
    height: ${TITLEBAR_H}px;
    background: #1e293b;
    display: flex;
    align-items: center;
    padding: 0 12px;
    gap: 6px;
  }

  .dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .dot.red    { background: #ff5f57; }
  .dot.yellow { background: #febc2e; }
  .dot.green  { background: #28c840; }

  .url-bar {
    margin-left: 12px;
    background: #334155;
    border-radius: 4px;
    padding: 3px 14px;
    font-size: 10px;
    color: #94a3b8;
    letter-spacing: 0.3px;
    flex: 1;
    max-width: 240px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .browser-content {
    width: 100%;
    height: calc(100% - ${TITLEBAR_H}px);
    overflow: hidden;
    background: #f8fafc;
  }

  .browser-content img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top left;
    display: block;
  }
</style>
</head>
<body>
  <div class="grid">${cards}</div>
</body>
</html>`;
}

(async () => {
    if (!fs.existsSync(OUTPUT_DIR))
        fs.mkdirSync(OUTPUT_DIR, { recursive: true });

    console.log("Loading desktop screenshots...");
    const base64Images = desktopFiles.map((f) => {
        const p = path.join(SCREENSHOTS_DIR, f);
        if (!fs.existsSync(p)) {
            console.error(`Missing: ${p}`);
            process.exit(1);
        }
        return fs.readFileSync(p).toString("base64");
    });

    console.log("Rendering composite mockup...");
    const html = buildHTML(base64Images);

    const browser = await chromium.launch();
    const page = await browser.newPage({
        viewport: { width: CANVAS_W, height: CANVAS_H, deviceScaleFactor: 1 },
    });
    await page.setContent(html, { waitUntil: "networkidle" });
    const outPath = path.join(OUTPUT_DIR, "civic-connect-desktop-showcase.png");
    await page.screenshot({ path: outPath, type: "png" });
    await browser.close();

    console.log(`Done! Saved to ${outPath}`);
})();
