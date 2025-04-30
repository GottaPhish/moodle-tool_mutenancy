@tool @tool_mutenancy @MuTMS
Feature: Tenant roles
  Background:
    Given unnecessary Admin bookmarks block gets deleted

  Scenario: Tenant roles are created during multi-tenancy activation
    Given I skip tests if multi-tenancy is activated
    And I log in as "admin"

    When I navigate to "Users > Permissions > Define roles" in site administration
    Then I should not see "Tenant manager"
    And I should not see "Tenant user"

    When I navigate to "Tenants" in site administration
    And I press "Activate multi-tenancy"
    And I press dialog form button "Activate multi-tenancy"
    And I navigate to "Users > Permissions > Define roles" in site administration
    Then the following should exist in the "roles" table:
      | Role           | Description                       | Short name    |
      | Tenant user    | Tenant user role gets assigned    | tenantuser    |
      | Tenant manager | Tenant manager role gets assigned | tenantmanager |

    When I follow "Tenant user"
    Then I should see "ARCHETYPE: Tenant user"

    And I follow "Back to the list of all roles"

    When I follow "Tenant manager"
    Then I should see "ARCHETYPE: Tenant manager"
