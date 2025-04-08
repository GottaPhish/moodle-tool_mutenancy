<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong
// phpcs:disable moodle.Commenting.DocblockDescription.Missing

namespace tool_mutenancy\phpunit\patch;

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\config;

/**
 * Multi-tenancy upstream patch test.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \core\output\renderer_base
 */
final class core_oputput_renderer_base_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * @covers ::get_logo_url
     */
    public function test_get_logo_url(): void {
        global $OUTPUT;

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();

        $syscontext = \context_system::instance();
        $tenantcontext2 = \context_tenant::instance($tenant2->id);

        $themerev = theme_get_revision();

        config::override($tenant2->id, 'logo', '/mylogo.gif', 'core_admin');

        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertFalse($result);

        tenancy::switch($tenant1->id);
        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertFalse($result);

        tenancy::switch($tenant2->id);
        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$tenantcontext2->id/core_admin/logo/100x50/$themerev/mylogo.gif",
            $result->out(false));

        set_config('logo', '/default.jpg', 'core_admin');

        tenancy::switch(null);
        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$syscontext->id/core_admin/logo/100x50/$themerev/default.jpg",
            $result->out(false));

        tenancy::switch($tenant1->id);
        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$syscontext->id/core_admin/logo/100x50/$themerev/default.jpg",
            $result->out(false));

        tenancy::switch($tenant2->id);
        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$tenantcontext2->id/core_admin/logo/100x50/$themerev/mylogo.gif",
            $result->out(false));

        config::override($tenant2->id, 'logo', '', 'core_admin');
        $result = $OUTPUT->get_logo_url(100, 50);
        $this->assertFalse($result);
    }

    /**
     * @covers ::get_compact_logo_url
     */
    public function test_get_compact_logo_url(): void {
        global $OUTPUT;

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();

        $syscontext = \context_system::instance();
        $tenantcontext2 = \context_tenant::instance($tenant2->id);

        $themerev = theme_get_revision();

        config::override($tenant2->id, 'logocompact', '/mylogocompact.gif', 'core_admin');

        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertFalse($result);

        tenancy::switch($tenant1->id);
        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertFalse($result);

        tenancy::switch($tenant2->id);
        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$tenantcontext2->id/core_admin/logocompact/100x50/$themerev/mylogocompact.gif",
            $result->out(false));

        set_config('logocompact', '/default.jpg', 'core_admin');

        tenancy::switch(null);
        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$syscontext->id/core_admin/logocompact/100x50/$themerev/default.jpg",
            $result->out(false));

        tenancy::switch($tenant1->id);
        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$syscontext->id/core_admin/logocompact/100x50/$themerev/default.jpg",
            $result->out(false));

        tenancy::switch($tenant2->id);
        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertInstanceOf(\moodle_url::class, $result);
        $this->assertSame(
            "https://www.example.com/moodle/pluginfile.php/$tenantcontext2->id/core_admin/logocompact/100x50/$themerev/mylogocompact.gif",
            $result->out(false));

        config::override($tenant2->id, 'logocompact', '', 'core_admin');
        $result = $OUTPUT->get_compact_logo_url(100, 50);
        $this->assertFalse($result);
    }
}
