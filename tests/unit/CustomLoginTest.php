<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use TheFrosty\CustomLogin\CustomLogin;

/**
 * Class CustomLoginTest
 * @package TheFrosty\Tests\CustomLogin
 */
class CustomLoginTest extends TestCase
{

    private CustomLogin $custom_login;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        $this->custom_login = new CustomLogin();
        $this->reflection = $this->getReflection($this->custom_login);
    }

    /**
     * Teardown.
     */
    public function tearDown(): void
    {
        unset($this->custom_login);
    }

    /**
     * Test CustomLogin.
     */
    public function testCustomLogin(): void
    {
        $this->assertInstanceOf(CustomLogin::class, $this->custom_login);
        $this->assertCount(3, $this->reflection->getConstants());
    }
}
