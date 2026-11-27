// @ts-check
const { test, expect } = require('@playwright/test')

/**
 * Acceptance tests for the PHP 7.4 (Rector downgraded) package.
 *
 * `.github/workflows/php74.yml` installs the package on WordPress under PHP 7.4 and seeds the design
 * settings below before these tests run, so the assertions here are tied to those seeded values.
 */
const ADMIN_USER = process.env.WP_ADMIN_USER || 'admin'
const ADMIN_PASSWORD = process.env.WP_ADMIN_PASSWORD || 'password'

const LOGIN_PATH = '/wp-login.php'
const ADMIN_PATH = '/wp-admin/'
const SETTINGS_PATH = '/wp-admin/options-general.php?page=custom-login'
const EXTENSIONS_PATH = '/wp-admin/options-general.php?page=custom-login/extensions'

// Settings sections, as registered with WpSettingsApi (`Factory::getSection()`).
const DESIGN_SECTION = 'custom_login_design'
const SECTION_ID_PREFIX = 'Dwnload_WP_Settings_Api__'

// Seeded by the workflow (`wp option update custom_login_design ...`).
const SEEDED = {
  labelColor: 'rgb(255, 0, 0)', // #ff0000
  formWidth: '400px',
  customHtmlSelector: '#ci-html',
  customJsBodyClass: 'ci-custom-js',
}

// Changed through the settings UI.
const UPDATED_FORM_WIDTH = 360

/**
 * Fail a test on any uncaught JavaScript error.
 * @param {import('@playwright/test').Page} page
 * @returns {string[]}
 */
function collectPageErrors (page) {
  const errors = []
  page.on('pageerror', (error) => errors.push(`${page.url()}: ${error.message}`))

  return errors
}

/**
 * Log in through the wp-login.php form.
 * @param {import('@playwright/test').Page} page
 */
async function logIn (page) {
  await page.goto(LOGIN_PATH)
  await page.locator('#user_login').fill(ADMIN_USER)
  await page.locator('#user_pass').fill(ADMIN_PASSWORD)
  await Promise.all([
    page.waitForURL((url) => url.pathname.startsWith(ADMIN_PATH)),
    page.locator('#wp-submit').click(),
  ])
}

test.describe.configure({ mode: 'serial' })

test.describe('Custom Login (PHP 7.4 package)', () => {
  /** @type {string[]} */
  let pageErrors = []

  test.beforeEach(({ page }) => {
    pageErrors = collectPageErrors(page)
  })

  test.afterEach(() => {
    expect(pageErrors, 'uncaught JavaScript errors').toEqual([])
  })

  test('login screen renders the saved design, HTML and JavaScript', async ({ page }) => {
    await page.goto(LOGIN_PATH)

    await expect(page.locator('meta[name="generator"][content^="Custom Login"]')).toHaveCount(1)
    await expect(page.locator('.login label').first()).toHaveCSS('color', SEEDED.labelColor)
    await expect(page.locator('#login')).toHaveCSS('width', SEEDED.formWidth)
    await expect(page.locator(SEEDED.customHtmlSelector)).toBeVisible()
    // The custom javascript setting ran (and jQuery was enqueued for it).
    await expect(page.locator('body')).toHaveClass(new RegExp(`\\b${SEEDED.customJsBodyClass}\\b`))
  })

  test('an administrator can log in through the customized form', async ({ page }) => {
    await logIn(page)

    await expect(page.locator('#wpadminbar')).toBeVisible()
  })

  test('saving a design setting in the admin updates the login screen', async ({ page }) => {
    await logIn(page)
    await page.goto(SETTINGS_PATH)

    // Open the "Design" tab, change the form width and save that section.
    await page.locator(`.${SECTION_ID_PREFIX}menu a[data-tab-id="${SECTION_ID_PREFIX}${DESIGN_SECTION}"]`).click()
    const form = page.locator(`#${SECTION_ID_PREFIX}${DESIGN_SECTION} form`)
    await expect(form).toBeVisible()
    await form.locator(`input[name="${DESIGN_SECTION}[login_form_width]"]`).fill(String(UPDATED_FORM_WIDTH))
    await Promise.all([
      page.waitForURL((url) => url.searchParams.get('settings-updated') === 'true'),
      form.locator('p.submit [type="submit"]').click(),
    ])
    await expect(page.locator('#setting-error-settings_updated')).toBeVisible()

    // Logged out, the login screen uses the new width.
    await page.context().clearCookies()
    await page.goto(LOGIN_PATH)
    await expect(page.locator('#login')).toHaveCSS('width', `${UPDATED_FORM_WIDTH}px`)
    await expect(page.locator('.login label').first()).toHaveCSS('color', SEEDED.labelColor)
  })

  test('the extensions page lists the available extensions', async ({ page }) => {
    await logIn(page)
    await page.goto(EXTENSIONS_PATH)

    await expect(page.getByRole('heading', { name: 'Available Custom Login Extensions' })).toBeVisible()
    await expect(page.locator('.addon-container').first()).toBeVisible()
  })
})
