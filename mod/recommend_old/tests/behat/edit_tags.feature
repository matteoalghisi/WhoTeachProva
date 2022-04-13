@mod @mod_recommend @core_tag @javascript
Feature: Edited recommend chapters handle tags correctly
  In order to get recommend chapters properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "recommend" to section "1" and I fill the form with:
      | Name | Test recommend |
      | Description | A recommend about dreams! |
    And I log out

  Scenario: recommend chapter edition of custom tags works as expected
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test recommend"
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | Example, Chapter, Cool |
    And I press "Save changes"
    Then I should see "Example" in the ".recommend-tags" "css_element"
    And I should see "Chapter" in the ".recommend-tags" "css_element"
    And I should see "Cool" in the ".recommend-tags" "css_element"
    And I press "Turn editing on"
    And I follow "Edit chapter \"1. Dummy first chapter\""
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Chapter" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: recommend chapter edition of standard tags works as expected
    Given I log in as "admin"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    And I follow "Add standard tags"
    And I set the field "Enter comma-separated list of new tags" to "OT1, OT2, OT3"
    And I press "Continue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test recommend"
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | OT1, OT3 |
    And I press "Save changes"
    Then I should see "OT1" in the ".recommend-tags" "css_element"
    And I should see "OT3" in the ".recommend-tags" "css_element"
    And I should not see "OT2" in the ".recommend-tags" "css_element"
    And I press "Turn editing on"
    And I follow "Edit chapter \"1. Dummy first chapter\""
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"
