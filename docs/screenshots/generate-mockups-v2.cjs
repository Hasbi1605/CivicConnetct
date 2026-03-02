const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const SCREENSHOTS_DIR = __dirname;
const MOBILE_DIR = path.join(__dirname, "mobile");
const MOCKUPS_DIR = path.join(__dirname, "..", "mockups-v2");

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
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    width: 1920px;
    height: 1080px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  /* Container for both devices */
  .scene {
    position: relative;
    width: 1500px;
    height: 920px;
  }

  /* ===== MacBook LAPTOP with thick bezel ===== */
  .laptop {
    position: absolute;
    left: 20px;
    top: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    filter: drop-shadow(0 20px 50px rgba(0,0,0,0.12));
  }

  /* Outer bezel (silver aluminum) */
  .laptop-bezel {
    width: 960px;
    background: linear-gradient(180deg, #e8e8ea 0%, #d4d4d8 100%);
    border-radius: 16px 16px 0 0;
    padding: 24px 24px 20px 24px;
    border: 1px solid #c4c4c8;
    border-bottom: none;
    position: relative;
  }

  /* Camera on bezel */
  .laptop-camera {
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3a3a3c;
    border: 1px solid #2a2a2c;
  }
  /* Camera lens glare */
  .laptop-camera::after {
    content: '';
    position: absolute;
    top: 1px;
    left: 2px;
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
  }

  /* Inner screen area */
  .laptop-display {
    width: 100%;
    height: 570px;
    background: #000;
    border-radius: 4px;
    overflow: hidden;
  }

  .laptop-display img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top left;
    display: block;
  }

  /* Bottom chin / hinge connector */
  .laptop-chin {
    width: 960px;
    height: 8px;
    background: linear-gradient(180deg, #d4d4d8, #c8c8cc);
    border-left: 1px solid #c4c4c8;
    border-right: 1px solid #c4c4c8;
  }

  /* Keyboard base / bottom half */
  .laptop-base {
    width: 1080px;
    height: 16px;
    background: linear-gradient(180deg, #d8d8dc 0%, #c8c8cc 60%, #bcbcc0 100%);
    border-radius: 0 0 8px 8px;
    border: 1px solid #b8b8bc;
    border-top: none;
    position: relative;
  }
  /* Trackpad notch indent */
  .laptop-base::before {
    content: '';
    position: absolute;
    top: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 110px;
    height: 5px;
    border-radius: 0 0 4px 4px;
    background: #b0b0b4;
  }

  /* Shadow under laptop */
  .laptop-shadow {
    width: 1100px;
    height: 30px;
    background: radial-gradient(ellipse at center, rgba(0,0,0,0.10) 0%, transparent 70%);
    margin-top: -2px;
  }

  /* ===== iPhone PHONE ===== */
  .phone {
    position: absolute;
    right: 100px;
    bottom: 20px;
    z-index: 10;
    filter: drop-shadow(-10px 20px 40px rgba(0,0,0,0.20));
  }

  .phone-body {
    width: 275px;
    height: 570px;
    background: linear-gradient(160deg, #2c2c2e, #1c1c1e);
    border-radius: 40px;
    padding: 11px;
    position: relative;
    border: 1px solid #3a3a3c;
  }

  /* Right side button (power) */
  .phone-body::before {
    content: '';
    position: absolute;
    right: -3px;
    top: 130px;
    width: 3px;
    height: 44px;
    background: #3a3a3c;
    border-radius: 0 3px 3px 0;
  }
  /* Left side buttons (volume) */
  .phone-body::after {
    content: '';
    position: absolute;
    left: -3px;
    top: 110px;
    width: 3px;
    height: 26px;
    background: #3a3a3c;
    border-radius: 3px 0 0 3px;
    box-shadow: 0 36px 0 #3a3a3c;
  }

  .phone-screen {
    width: 100%;
    height: 100%;
    border-radius: 30px;
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
    width: 76px;
    height: 22px;
    border-radius: 16px;
    background: #000;
    z-index: 5;
  }

  /* Home indicator bar */
  .phone-screen::after {
    content: '';
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    border-radius: 2px;
    background: rgba(255,255,255,0.25);
    z-index: 5;
  }

</style>
</head>
<body>
  <div class="scene">
    <!-- MacBook Laptop -->
    <div class="laptop">
      <div class="laptop-bezel">
        <div class="laptop-camera"></div>
        <div class="laptop-display">
          <img src="data:image/png;base64,${desktopBase64}" />
        </div>
      </div>
      <div class="laptop-chin"></div>
      <div class="laptop-base"></div>
      <div class="laptop-shadow"></div>
    </div>

    <!-- iPhone -->
    <div class="phone">
      <div class="phone-body">
        <div class="phone-screen">
          <img src="data:image/png;base64,${mobileBase64}" />
        </div>
      </div>
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
