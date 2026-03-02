/**
 * V3 — Mobile Diagonal Showcase
 * All 15 mobile screenshots in phone frames arranged in a rotated grid.
 * Outputs a single composite image.
 */
const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const MOBILE_DIR = path.join(__dirname, "mobile");
const OUTPUT_DIR = path.join(__dirname, "..", "mockups-v3");

const mobileFiles = [
    "01-login-mobile.png",
    "02-beranda-feed-mobile.png",
    "03-beranda-voting-mobile.png",
    "04-hoax-buster-mobile.png",
    "05-hoax-buster-detail-mobile.png",
    "06-lab-room-mobile.png",
    "07-lab-room-detail-mobile.png",
    "08-lab-room-diskusi-mobile.png",
    "09-policy-lab-mobile.png",
    "10-policy-brief-detail-mobile.png",
    "11-moderasi-mobile.png",
    "12-moderasi-riwayat-mobile.png",
    "13-post-detail-mobile.png",
    "14-post-komentar-mobile.png",
    "15-profil-mobile.png",
];

// Layout constants
const CANVAS_W = 3840;
const CANVAS_H = 2400;
const COLS = 6;
const ROWS = 4;
const PHONE_W = 380;
const PHONE_H = 780;
const GAP_X = 55;
const GAP_Y = 55;
const ROW_OFFSET = 217; // stagger offset for odd rows
const ROTATION = -18; // degrees

function buildHTML(base64Images) {
    // Generate 24 phone cards (cycle through 15 images)
    const totalCards = COLS * ROWS;
    let cards = "";

    for (let i = 0; i < totalCards; i++) {
        const imgIdx = i % base64Images.length;
        const row = Math.floor(i / COLS);
        const col = i % COLS;
        const x = col * (PHONE_W + GAP_X) + (row % 2 === 1 ? ROW_OFFSET : 0);
        const y = row * (PHONE_H + GAP_Y);

        cards += `
        <div class="phone-card" style="left:${x}px; top:${y}px;">
          <div class="phone-body">
            <div class="phone-screen">
              <img src="data:image/png;base64,${base64Images[imgIdx]}" />
            </div>
          </div>
        </div>`;
    }

    const gridW = COLS * (PHONE_W + GAP_X) + ROW_OFFSET;
    const gridH = ROWS * (PHONE_H + GAP_Y);

    return `<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    width: ${CANVAS_W}px;
    height: ${CANVAS_H}px;
    background: #e8ecf1;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .grid {
    position: relative;
    width: ${gridW}px;
    height: ${gridH}px;
    transform: rotate(${ROTATION}deg);
    flex-shrink: 0;
  }

  .phone-card {
    position: absolute;
    width: ${PHONE_W}px;
    height: ${PHONE_H}px;
  }

  .phone-body {
    width: 100%;
    height: 100%;
    background: linear-gradient(160deg, #2c2c2e, #1c1c1e);
    border-radius: 42px;
    padding: 11px;
    position: relative;
    border: 1px solid #3a3a3c;
    box-shadow: 0 12px 35px rgba(0,0,0,0.22), 0 4px 12px rgba(0,0,0,0.12);
  }

  /* Power button */
  .phone-body::before {
    content: '';
    position: absolute;
    right: -2.5px;
    top: 130px;
    width: 2.5px;
    height: 42px;
    background: #3a3a3c;
    border-radius: 0 2px 2px 0;
  }
  /* Volume buttons */
  .phone-body::after {
    content: '';
    position: absolute;
    left: -2.5px;
    top: 110px;
    width: 2.5px;
    height: 26px;
    background: #3a3a3c;
    border-radius: 2px 0 0 2px;
    box-shadow: 0 36px 0 #3a3a3c;
  }

  .phone-screen {
    width: 100%;
    height: 100%;
    border-radius: 32px;
    overflow: hidden;
    background: #000;
    position: relative;
  }

  .phone-screen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top left;
    display: block;
  }

  /* Dynamic Island */
  .phone-screen::before {
    content: '';
    position: absolute;
    top: 9px;
    left: 50%;
    transform: translateX(-50%);
    width: 72px;
    height: 21px;
    border-radius: 14px;
    background: #000;
    z-index: 5;
  }

  /* Home indicator bar */
  .phone-screen::after {
    content: '';
    position: absolute;
    bottom: 7px;
    left: 50%;
    transform: translateX(-50%);
    width: 92px;
    height: 4px;
    border-radius: 2px;
    background: rgba(255,255,255,0.25);
    z-index: 5;
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

    console.log("Loading mobile screenshots...");
    const base64Images = mobileFiles.map((f) => {
        const p = path.join(MOBILE_DIR, f);
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
    const outPath = path.join(OUTPUT_DIR, "civic-connect-mobile-showcase.png");
    await page.screenshot({ path: outPath, type: "png" });
    await browser.close();

    console.log(`Done! Saved to ${outPath}`);
})();
