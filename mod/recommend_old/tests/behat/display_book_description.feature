@mod @mod_recommend
Feature: Display the recommend description in the recommend and optionally in the course
  In order to display the the recommend description in the course
  As a teacher
  I need to enable the 'Display description on course page' setting.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "recommend" to section "1" and I fill the form with:
      | Name | Test recommend |
      | Description | A recommend about dreams! |
    And I follow "Test recommend"
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
    And I press "Save changes"

  Scenario: Description is displayed in the recommend
    Given I am on "Course 1" course homepage
    When I follow "Test recommend"
    Then I should see "A recommend about dreams!"

  Scenario: Show recommend description in the course homepage
    Given I am on "Course 1" course homepage
    And I follow "Test recommend"
    And I navigate to "Edit settings" in current page administration
    And the following fields match these values:
      | Display description on course page | |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should see "A recommend about dreams!"

  Scenario: Hide recommend description in the course homepage
    Given I am on "Course 1" course homepage
    And I follow "Test recommend"
    And I navigate to "Edit settings" in current page administration
    And the following fields match these values:
      | Display description on course page | |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should not see "A recommend about dreams!"
