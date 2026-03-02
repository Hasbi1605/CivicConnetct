const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const SCREENSHOTS_DIR = __dirname;
const MOBILE_DIR = path.join(__dirname, "mobile");
const MOCKUPS_DIR = path.join(__dirname, "..", "mockups");

// Page mapping: number → base name
const pages = [
    {
        num: "01",
        desktop: "01-login.png",
        mobile: "01-login-mobile.png",
        label: "Login",
    },
    {
        num: "02",
        desktop: "02-beranda-feed.png",
        mobile: "02-beranda-feed-mobile.png",
        label: "Beranda - Feed",
    },
    {
        num: "03",
        desktop: "03-beranda-voting.png",
        mobile: "03-beranda-voting-mobile.png",
        label: "Beranda - Voting",
    },
    {
        num: "04",
        desktop: "04-hoax-buster.png",
        mobile: "04-hoax-buster-mobile.png",
        label: "Hoax Buster",
    },
    {
        num: "05",
        desktop: "05-hoax-buster-detail.png",
        mobile: "05-hoax-buster-detail-mobile.png",
        label: "Hoax Buster - Detail",
    },
    {
        num: "06",
        desktop: "06-lab-room.png",
        mobile: "06-lab-room-mobile.png",
        label: "LAB Room",
    },
    {
        num: "07",
        desktop: "07-lab-room-detail.png",
        mobile: "07-lab-room-detail-mobile.png",
        label: "LAB Room - Detail",
    },
    {
        num: "08",
        desktop: "08-lab-room-diskusi.png",
        mobile: "08-lab-room-diskusi-mobile.png",
        label: "LAB Room - Diskusi",
    },
    {
        num: "09",
        desktop: "09-policy-lab.png",
        mobile: "09-policy-lab-mobile.png",
        label: "Policy Lab",
    },
    {
        num: "10",
        desktop: "10-policy-brief-detail.png",
        mobile: "10-policy-brief-detail-mobile.png",
        label: "Policy Brief - Detail",
    },
    {
        num: "11",
        desktop: "11-moderasi.png",
        mobile: "11-moderasi-mobile.png",
        label: "Moderasi",
    },
    {
        num: "12",
        desktop: "12-moderasi-riwayat.png",
        mobile: "12-moderasi-riwayat-mobile.png",
        label: "Moderasi - Riwayat",
    },
    {
        num: "13",
        desktop: "13-post-detail.png",
        mobile: "13-post-detail-mobile.png",
        label: "Post Detail",
    },
    {
        num: "14",
        desktop: "14-post-komentar.png",
        mobile: "14-post-komentar-mobile.png",
        label: "Post - Komentar",
    },
    {
        num: "15",
        desktop: "15-profil.png",
        mobile: "15-profil-mobile.png",
        label: "Profil",
    },
];

function buildHTML(desktopBase64, mobileBase64, label) {
    return `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    width: 1920px;
    height: 1080px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
    overflow: hidden;
    position: relative;
  }

  /* Subtle grid pattern overlay */
  body::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0);
    background-size: 40px 40px;
  }

  /* Glow accents */
  .glow-1 {
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
    top: -100px;
    left: -100px;
  }
  .glow-2 {
    position: absolute;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
    bottom: -100px;
    right: -50px;
  }

  .title {
    color: #f1f5f9;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
    margin-bottom: 8px;
    z-index: 2;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
  }
  .subtitle {
    color: #94a3b8;
    font-size: 14px;
    font-weight: 400;
    margin-bottom: 40px;
    z-index: 2;
  }

  .devices {
    display: flex;
    align-items: flex-end;
    gap: 60px;
    z-index: 2;
  }

  /* ===== LAPTOP FRAME ===== */
  .laptop {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .laptop-screen {
    width: 980px;
    height: 612px;
    background: #000;
    border-radius: 12px 12px 0 0;
    border: 3px solid #374151;
    border-bottom: none;
    overflow: hidden;
    position: relative;
    box-shadow:
      0 -2px 20px rgba(99, 102, 241, 0.1),
      inset 0 0 0 1px rgba(255,255,255,0.05);
  }
  .laptop-screen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top left;
    display: block;
  }
  /* Camera notch */
  .laptop-screen::before {
    content: '';
    position: absolute;
    top: 6px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #1f2937;
    border: 1px solid #374151;
    z-index: 5;
  }
  .laptop-base {
    width: 1100px;
    height: 18px;
    background: linear-gradient(180deg, #374151 0%, #1f2937 100%);
    border-radius: 0 0 8px 8px;
    position: relative;
  }
  .laptop-base::before {
    content: '';
    position: absolute;
    top: 4px;
    left: 50%;
    transform: translateX(-50%);
    width: 120px;
    height: 5px;
    border-radius: 3px;
    background: #4b5563;
  }
  .laptop-shadow {
    width: 1120px;
    height: 20px;
    background: radial-gradient(ellipse, rgba(0,0,0,0.4) 0%, transparent 70%);
    margin-top: -2px;
  }

  /* ===== PHONE FRAME ===== */
  .phone {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .phone-body {
    width: 260px;
    height: 540px;
    background: linear-gradient(145deg, #1f2937, #111827);
    border-radius: 36px;
    padding: 12px;
    position: relative;
    box-shadow:
      0 25px 60px rgba(0,0,0,0.5),
      0 0 0 1px rgba(255,255,255,0.08),
      inset 0 1px 0 rgba(255,255,255,0.1);
  }
  .phone-body::before {
    content: '';
    position: absolute;
    right: -3px;
    top: 120px;
    width: 4px;
    height: 50px;
    background: #374151;
    border-radius: 0 3px 3px 0;
  }
  .phone-body::after {
    content: '';
    position: absolute;
    left: -3px;
    top: 100px;
    width: 4px;
    height: 30px;
    background: #374151;
    border-radius: 3px 0 0 3px;
    box-shadow: 0 40px 0 #374151;
  }
  .phone-screen {
    width: 100%;
    height: 100%;
    border-radius: 24px;
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
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 24px;
    border-radius: 20px;
    background: #000;
    z-index: 5;
  }
  .phone-shadow {
    width: 200px;
    height: 20px;
    background: radial-gradient(ellipse, rgba(0,0,0,0.35) 0%, transparent 70%);
    margin-top: 5px;
  }

  /* Label tags */
  .label-desktop, .label-mobile {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 5px 16px;
    border-radius: 20px;
    margin-bottom: 14px;
  }
  .label-desktop {
    color: #a5b4fc;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.25);
  }
  .label-mobile {
    color: #6ee7b7;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.25);
  }
</style>
</head>
<body>
  <div class="glow-1"></div>
  <div class="glow-2"></div>

  <div class="title">CIVIC Connect — ${label}</div>
  <div class="subtitle">Responsive Preview · Desktop & Mobile</div>

  <div class="devices">
    <div class="laptop">
      <span class="label-desktop">Desktop</span>
      <div class="laptop-screen">
        <img src="data:image/png;base64,${desktopBase64}" />
      </div>
      <div class="laptop-base"></div>
      <div class="laptop-shadow"></div>
    </div>

    <div class="phone">
      <span class="label-mobile">Mobile</span>
      <div class="phone-body">
        <div class="phone-screen">
          <img src="data:image/png;base64,${mobileBase64}" />
        </div>
      </div>
      <div class="phone-shadow"></div>
    </div>
  </div>
</body>
</html>`;
}

(async () => {
    if (!fs.existsSync(MOCKUPS_DIR))
        fs.mkdirSync(MOCKUPS_DIR, { recursive: true });

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        deviceScaleFactor: 2,
    });

    let done = 0;
    for (const pg of pages) {
        const desktopPath = path.join(SCREENSHOTS_DIR, pg.desktop);
        const mobilePath = path.join(MOBILE_DIR, pg.mobile);

        if (!fs.existsSync(desktopPath)) {
            console.warn(
                `⚠ Desktop screenshot not found: ${pg.desktop}, skipping`,
            );
            continue;
        }
        if (!fs.existsSync(mobilePath)) {
            console.warn(
                `⚠ Mobile screenshot not found: ${pg.mobile}, skipping`,
            );
            continue;
        }

        const desktopBase64 = fs.readFileSync(desktopPath).toString("base64");
        const mobileBase64 = fs.readFileSync(mobilePath).toString("base64");

        const html = buildHTML(desktopBase64, mobileBase64, pg.label);
        const page = await context.newPage();
        await page.setContent(html, { waitUntil: "networkidle" });
        await page.waitForTimeout(500);

        const outputFile = path.join(
            MOCKUPS_DIR,
            `${pg.num}-${pg.label
                .toLowerCase()
                .replace(/\s+/g, "-")
                .replace(/[^a-z0-9-]/g, "")}-mockup.png`,
        );
        await page.screenshot({ path: outputFile, type: "png" });
        await page.close();

        done++;
        console.log(`${done}/${pages.length} ✓ ${pg.label}`);
    }

    await browser.close();
    console.log(`\nDone! ${done} mockups saved to ${MOCKUPS_DIR}`);
})().catch((e) => {
    console.error(e);
    process.exit(1);
});
