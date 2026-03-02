const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

(async () => {
    const mobileDir = path.join(__dirname, "mobile");
    if (!fs.existsSync(mobileDir)) fs.mkdirSync(mobileDir, { recursive: true });

    const browser = await chromium.launch({ headless: true });

    // Mobile context (iPhone-like viewport)
    // Note: isMobile causes Chromium to inflate viewport (375→460px),
    // so we use plain viewport + mobile userAgent instead
    const context = await browser.newContext({
        viewport: { width: 375, height: 812 },
        deviceScaleFactor: 2,
        userAgent:
            "Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1",
    });
    const page = await context.newPage();

    // Login first
    await page.goto("http://127.0.0.1:8000/login");
    await page.fill('input[name="email"]', "demo@civic.id");
    await page.fill('input[name="password"]', "password");
    await page.click('button[type="submit"]');
    await page.waitForURL("http://127.0.0.1:8000/");
    await page.waitForTimeout(1000);

    // 1. Login page (separate context)
    const loginCtx = await browser.newContext({
        viewport: { width: 375, height: 812 },
        deviceScaleFactor: 2,
    });
    const loginPage = await loginCtx.newPage();
    await loginPage.goto("http://127.0.0.1:8000/login");
    await loginPage.waitForTimeout(800);
    await loginPage.screenshot({
        path: path.join(mobileDir, "01-login-mobile.png"),
        fullPage: false,
    });
    await loginCtx.close();
    console.log("1/15 login");

    // 2. Home/feed
    await page.goto("http://127.0.0.1:8000/");
    await page.waitForTimeout(1500);
    await page.screenshot({
        path: path.join(mobileDir, "02-beranda-feed-mobile.png"),
        fullPage: false,
    });
    console.log("2/15 beranda");

    // 3. Home - scroll to see voting/post interactions
    await page.evaluate(() => window.scrollBy(0, 600));
    await page.waitForTimeout(500);
    await page.screenshot({
        path: path.join(mobileDir, "03-beranda-voting-mobile.png"),
        fullPage: false,
    });
    console.log("3/15 beranda-voting");

    // 4. Hoax Buster
    await page.goto("http://127.0.0.1:8000/hoax-buster");
    await page.waitForTimeout(1500);
    await page.screenshot({
        path: path.join(mobileDir, "04-hoax-buster-mobile.png"),
        fullPage: false,
    });
    console.log("4/15 hoax-buster");

    // 5. Hoax Buster detail
    const claimLink = await page.$('a[href*="hoax-buster/"]');
    if (claimLink) {
        await claimLink.click();
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(mobileDir, "05-hoax-buster-detail-mobile.png"),
            fullPage: false,
        });
        console.log("5/15 hoax-detail");
    }

    // 6. LAB Room
    await page.goto("http://127.0.0.1:8000/lab-room");
    await page.waitForTimeout(1500);
    await page.screenshot({
        path: path.join(mobileDir, "06-lab-room-mobile.png"),
        fullPage: false,
    });
    console.log("6/15 lab-room");

    // 7. LAB Room detail
    const roomLink = await page.$('a[href*="lab-room/"]');
    if (roomLink) {
        await roomLink.click();
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(mobileDir, "07-lab-room-detail-mobile.png"),
            fullPage: false,
        });
        console.log("7/15 lab-detail");

        await page.evaluate(() => window.scrollBy(0, 500));
        await page.waitForTimeout(500);
        await page.screenshot({
            path: path.join(mobileDir, "08-lab-room-diskusi-mobile.png"),
            fullPage: false,
        });
        console.log("8/15 lab-diskusi");
    }

    // 9. Policy Lab
    await page.goto("http://127.0.0.1:8000/policy-lab");
    await page.waitForTimeout(1500);
    await page.screenshot({
        path: path.join(mobileDir, "09-policy-lab-mobile.png"),
        fullPage: false,
    });
    console.log("9/15 policy-lab");

    // 10. Policy Brief detail
    const briefLink = await page.$('a[href*="policy-lab/"]');
    if (briefLink) {
        await briefLink.click();
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(mobileDir, "10-policy-brief-detail-mobile.png"),
            fullPage: false,
        });
        console.log("10/15 brief-detail");
    }

    // 11. Moderation panel
    await page.goto("http://127.0.0.1:8000/moderation");
    await page.waitForTimeout(1500);
    await page.screenshot({
        path: path.join(mobileDir, "11-moderasi-mobile.png"),
        fullPage: false,
    });
    console.log("11/15 moderasi");

    await page.evaluate(() => window.scrollBy(0, 800));
    await page.waitForTimeout(500);
    await page.screenshot({
        path: path.join(mobileDir, "12-moderasi-riwayat-mobile.png"),
        fullPage: false,
    });
    console.log("12/15 moderasi-riwayat");

    // 13. Post detail
    await page.goto("http://127.0.0.1:8000/");
    await page.waitForTimeout(1000);
    const postLink = await page.$('a[href*="posts/"]');
    if (postLink) {
        await postLink.click();
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(mobileDir, "13-post-detail-mobile.png"),
            fullPage: false,
        });
        console.log("13/15 post-detail");

        await page.evaluate(() => window.scrollBy(0, 500));
        await page.waitForTimeout(500);
        await page.screenshot({
            path: path.join(mobileDir, "14-post-komentar-mobile.png"),
            fullPage: false,
        });
        console.log("14/15 post-komentar");
    }

    // 15. Profile
    await page.goto("http://127.0.0.1:8000/profile");
    await page.waitForTimeout(1000);
    await page.screenshot({
        path: path.join(mobileDir, "15-profil-mobile.png"),
        fullPage: false,
    });
    console.log("15/15 profil");

    console.log("All mobile screenshots captured!");
    await browser.close();
})().catch((e) => {
    console.error(e);
    process.exit(1);
});
