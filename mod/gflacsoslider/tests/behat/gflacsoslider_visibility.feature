@mod @mod_extendedlabel
Feature: Check extendedlabel visibility works
  In order to check extendedlabel visibility works
  As a teacher
  I should create extendedlabel activity

  @javascript
  Scenario: Hidden extendedlabel activity should be show as hidden.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
    Given I log in as "admin"
    And I follow "Test"
    And I turn editing mode on
    When I add a "extendedlabel" to section "1" and I fill the form with:
      | extendedlabel text | Swanky extendedlabel |
      | Visible | Hide |
    Then "Swanky extendedlabel" activity should be hidden

  @javascript
  Scenario: Visible extendedlabel activity should be shown as visible.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
    Given I log in as "admin"
    And I follow "Test"
    And I turn editing mode on
    When I add a "extendedlabel" to section "1" and I fill the form with:
      | extendedlabel text | Swanky extendedlabel |
      | Visible | Show |
    Then "Swanky extendedlabel" activity should be visible
