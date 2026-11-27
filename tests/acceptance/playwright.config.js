// @ts-check
const { defineConfig, devices } = require('@playwright/test')

/**
 * Browser acceptance tests for the PHP 7.4 package, run by `.github/workflows/php74.yml`
 * against a WordPress site served by PHP 7.4 (see SITE_URL).
 */
module.exports = defineConfig({
  testDir: '.',
  // The tests share one WordPress install and change its settings, so run them in order.
  fullyParallel: false,
  workers: 1,
  forbidOnly: !!process.env.CI,
  retries: 0,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL: process.env.SITE_URL || 'http://127.0.0.1:8080',
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  ],
})
