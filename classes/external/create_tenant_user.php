<?php
namespace tool_mutenancy;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\user as tenant_user;

class create_tenant_user extends \core_external\external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tenantid' => new external_value(PARAM_INT, 'Tenant ID'),
            'username' => new external_value(PARAM_USERNAME, 'Username (lowercase, unique)'),
            'password' => new external_value(PARAM_RAW, 'Plain text password'),
            'firstname' => new external_value(PARAM_NOTAGS, 'First name'),
            'lastname' => new external_value(PARAM_NOTAGS, 'Last name'),
            'email' => new external_value(PARAM_EMAIL, 'Email address'),
            'idnumber' => new external_value(PARAM_RAW_TRIMMED, 'User ID number', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'city' => new external_value(PARAM_TEXT, 'City', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'country' => new external_value(PARAM_ALPHA, 'ISO country code', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'lang' => new external_value(PARAM_ALPHANUMEXT, 'Preferred language', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'timezone' => new external_value(PARAM_TEXT, 'Time zone', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'forcepasswordchange' => new external_value(PARAM_BOOL, 'Require password change on next login', VALUE_DEFAULT, 0),
        ]);
    }

    public static function execute(
        int $tenantid,
        string $username,
        string $password,
        string $firstname,
        string $lastname,
        string $email,
        ?string $idnumber = null,
        ?string $city = null,
        ?string $country = null,
        ?string $lang = null,
        ?string $timezone = null,
        int $forcepasswordchange = 0
    ): array {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $params = self::validate_parameters(self::execute_parameters(), [
            'tenantid' => $tenantid,
            'username' => $username,
            'password' => $password,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'idnumber' => $idnumber,
            'city' => $city,
            'country' => $country,
            'lang' => $lang,
            'timezone' => $timezone,
            'forcepasswordchange' => $forcepasswordchange,
        ]);

        if (!tenancy::is_active()) {
            throw new \core\exception\invalid_parameter_exception('Multi-tenancy is not active');
        }

        $tenantcontext = \context_tenant::instance($params['tenantid']);
        self::validate_context($tenantcontext);
        require_capability('tool/mutenancy:membercreate', $tenantcontext);
        require_capability('moodle/user:create', $tenantcontext);

        $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $params['tenantid']], '*', MUST_EXIST);

        if ($tenant->memberlimit) {
            $currentcount = $DB->count_records('user', ['deleted' => 0, 'tenantid' => $tenant->id]);
            if ($currentcount >= $tenant->memberlimit) {
                throw new \core\exception\invalid_parameter_exception('Tenant member limit reached');
            }
        }

        if (\core_user::get_user_by_username(\core_text::strtolower($params['username']), 'id', null, IGNORE_MISSING)) {
            throw new \core\exception\invalid_parameter_exception('Username already exists');
        }
        if (\core_user::get_user_by_email(\core_text::strtolower($params['email']))) {
            throw new \core\exception\invalid_parameter_exception('Email address already exists');
        }

        $user = (object)[
            'tenantid' => $tenant->id,
            'username' => \core_text::strtolower($params['username']),
            'password' => $params['password'],
            'firstname' => $params['firstname'],
            'lastname' => $params['lastname'],
            'email' => \core_text::strtolower($params['email']),
            'auth' => 'manual',
            'confirmed' => 1,
            'mnethostid' => $CFG->mnet_localhost_id,
        ];

        if ($params['idnumber'] !== null) {
            $user->idnumber = $params['idnumber'];
        }
        if ($params['city'] !== null) {
            $user->city = $params['city'];
        }
        if ($params['country'] !== null) {
            $user->country = $params['country'];
        }
        if ($params['lang'] !== null) {
            $user->lang = $params['lang'];
        }
        if ($params['timezone'] !== null) {
            $user->timezone = $params['timezone'];
        }

        $userid = user_create_user($user, true, true);

        if (!empty($params['forcepasswordchange'])) {
            set_user_preference('auth_forcepasswordchange', 1, $userid);
        }

        tenant_user::sync($tenant->id, userid: $userid);

        $created = \core_user::get_user($userid, '*', MUST_EXIST);

        return [
            'id' => $created->id,
            'username' => $created->username,
            'firstname' => $created->firstname,
            'lastname' => $created->lastname,
            'email' => $created->email,
            'idnumber' => isset($created->idnumber) ? $created->idnumber : null,
            'tenantid' => (int)$created->tenantid,
        ];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'User ID'),
            'username' => new external_value(PARAM_USERNAME, 'Username'),
            'firstname' => new external_value(PARAM_NOTAGS, 'First name'),
            'lastname' => new external_value(PARAM_NOTAGS, 'Last name'),
            'email' => new external_value(PARAM_EMAIL, 'Email'),
            'idnumber' => new external_value(PARAM_RAW_TRIMMED, 'User ID number', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'tenantid' => new external_value(PARAM_INT, 'Tenant ID'),
        ]);
    }
}
