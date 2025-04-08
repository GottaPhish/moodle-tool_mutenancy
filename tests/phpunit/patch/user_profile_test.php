<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong
// phpcs:disable moodle.Commenting.DocblockDescription.Missing

namespace tool_mutenancy\phpunit\patch;

/**
 * Multi-tenancy tests for upstream modifications.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_profile_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * @covers \profile_field_base::is_visible
     */
    public function test_profile_field_base_is_visible(): void {
        global $CFG;
        require_once("$CFG->dirroot/user/profile/lib.php");

        $field1 = $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'text',
            'shortname' => 'a',
            'name' => 'A',
            'visible' => PROFILE_VISIBLE_PRIVATE,
        ]);
        $field2 = $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'text',
            'shortname' => 'b',
            'name' => 'B',
            'visible' => PROFILE_VISIBLE_NONE,
        ]);

        $user0 = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $text1 = profile_get_user_field('text', $field1->id, $user0->id);
        $text2 = profile_get_user_field('text', $field2->id, $user0->id);
        $this->assertTrue($text1->is_visible());
        $this->assertTrue($text2->is_visible());

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();

        $manager1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $user1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        \tool_mutenancy\local\manager::add($tenant1->id, $manager1->id);
        $user2 = $this->getDataGenerator()->create_user(['tenantid' => $tenant2->id]);

        $this->setAdminUser();
        $text1 = profile_get_user_field('text', $field1->id, $user0->id);
        $text2 = profile_get_user_field('text', $field2->id, $user0->id);
        $this->assertTrue($text1->is_visible());
        $this->assertTrue($text2->is_visible());
        $text1 = profile_get_user_field('text', $field1->id, $user1->id);
        $text2 = profile_get_user_field('text', $field2->id, $user1->id);
        $this->assertTrue($text1->is_visible());
        $this->assertTrue($text2->is_visible());
        $text1 = profile_get_user_field('text', $field1->id, $user2->id);
        $text2 = profile_get_user_field('text', $field2->id, $user2->id);
        $this->assertTrue($text1->is_visible());
        $this->assertTrue($text2->is_visible());

        $this->setUser($manager1);
        $text1 = profile_get_user_field('text', $field1->id, $user0->id);
        $text2 = profile_get_user_field('text', $field2->id, $user0->id);
        $this->assertFalse($text1->is_visible());
        $this->assertFalse($text2->is_visible());
        $text1 = profile_get_user_field('text', $field1->id, $user1->id);
        $text2 = profile_get_user_field('text', $field2->id, $user1->id);
        $this->assertTrue($text1->is_visible());
        $this->assertTrue($text2->is_visible());
        $text1 = profile_get_user_field('text', $field1->id, $user2->id);
        $text2 = profile_get_user_field('text', $field2->id, $user2->id);
        $this->assertFalse($text1->is_visible());
        $this->assertFalse($text2->is_visible());
    }

    /**
     * @covers \profile_field_base::is_editable
     */
    public function test_profile_field_base_is_editable(): void {
        global $CFG;
        require_once("$CFG->dirroot/user/profile/lib.php");

        $field1 = $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'text',
            'shortname' => 'a',
            'name' => 'A',
            'visible' => PROFILE_VISIBLE_PRIVATE,
        ]);
        $field2 = $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'text',
            'shortname' => 'b',
            'name' => 'B',
            'visible' => PROFILE_VISIBLE_NONE,
        ]);

        $user0 = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $text1 = profile_get_user_field('text', $field1->id, $user0->id);
        $text2 = profile_get_user_field('text', $field2->id, $user0->id);
        $this->assertTrue($text1->is_editable());
        $this->assertTrue($text2->is_editable());

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();

        $manager1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $user1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        \tool_mutenancy\local\manager::add($tenant1->id, $manager1->id);
        $user2 = $this->getDataGenerator()->create_user(['tenantid' => $tenant2->id]);

        $this->setAdminUser();
        $text1 = profile_get_user_field('text', $field1->id, $user0->id);
        $text2 = profile_get_user_field('text', $field2->id, $user0->id);
        $this->assertTrue($text1->is_editable());
        $this->assertTrue($text2->is_editable());
        $text1 = profile_get_user_field('text', $field1->id, $user1->id);
        $text2 = profile_get_user_field('text', $field2->id, $user1->id);
        $this->assertTrue($text1->is_editable());
        $this->assertTrue($text2->is_editable());
        $text1 = profile_get_user_field('text', $field1->id, $user2->id);
        $text2 = profile_get_user_field('text', $field2->id, $user2->id);
        $this->assertTrue($text1->is_editable());
        $this->assertTrue($text2->is_editable());

        $this->setUser($manager1);
        $text1 = profile_get_user_field('text', $field1->id, $user0->id);
        $text2 = profile_get_user_field('text', $field2->id, $user0->id);
        $this->assertFalse($text1->is_editable());
        $this->assertFalse($text2->is_editable());
        $text1 = profile_get_user_field('text', $field1->id, $user1->id);
        $text2 = profile_get_user_field('text', $field2->id, $user1->id);
        $this->assertTrue($text1->is_editable());
        $this->assertTrue($text2->is_editable());
        $text1 = profile_get_user_field('text', $field1->id, $user2->id);
        $text2 = profile_get_user_field('text', $field2->id, $user2->id);
        $this->assertFalse($text1->is_editable());
        $this->assertFalse($text2->is_editable());
    }
}
